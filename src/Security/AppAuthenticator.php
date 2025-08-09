<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use ReCaptcha\ReCaptcha;
use Symfony\Component\HttpFoundation\RequestStack;

class AppAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private UrlGeneratorInterface $urlGenerator;
    private string $recaptchaSecretKey;
    private RequestStack $requestStack;
    private \App\Repository\UserRepository $userRepository;

    public function __construct(UrlGeneratorInterface $urlGenerator, RequestStack $requestStack, \App\Repository\UserRepository $userRepository, #[\Symfony\Component\DependencyInjection\Attribute\Autowire('%env(GOOGLE_RECAPTCHA_SECRET_KEY)%')] string $recaptchaSecretKey)
    {
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
        $this->userRepository = $userRepository;
        $this->recaptchaSecretKey = $recaptchaSecretKey;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');
        $request->getSession()->set(Security::LAST_USERNAME, $email);

        $recaptchaResponse = $request->request->get('g-recaptcha-response');
        if (empty($recaptchaResponse)) {
            $session = $this->requestStack->getSession();
            $session->getFlashBag()->add('error', ' reCAPTCHA is required.');
            return new Passport(
                new UserBadge(''),
                new PasswordCredentials(''),
                [new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token'))]
            );
        }
        $recaptcha = new ReCaptcha($this->recaptchaSecretKey);
        $resp = $recaptcha->verify($recaptchaResponse, $request->getClientIp());
        if (!$resp->isSuccess()) {
            $session = $this->requestStack->getSession();
            $session->getFlashBag()->add('error', 'Le reCAPTCHA est invalide.');
            return new Passport(
                new UserBadge(''),
                new PasswordCredentials(''),
                [new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token'))]
            );
        }

        // Check if user is blocked
        $user = $this->userRepository->findOneBy(['email' => $email]);
        if ($user && method_exists($user, 'isBlocked') && $user->isBlocked()) {
            $session = $this->requestStack->getSession();
            $session->getFlashBag()->add('error', "You're blocked.");
            return new Passport(
                new UserBadge(''),
                new PasswordCredentials(''),
                [new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token'))]
            );
        }

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Redirection selon le rôle de l'utilisateur (ordre hiérarchique)
        $user = $token->getUser();
        $roles = $user->getRoles();

        // Vérifier d'abord les rôles les plus élevés
        if (in_array('ROLE_SUPER_ADMIN', $roles)) {
            return new RedirectResponse($this->urlGenerator->generate('app_user_index'));
        } 
        
        if (in_array('ROLE_ADMIN', $roles)) {
            return new RedirectResponse($this->urlGenerator->generate('app_user_index'));
        }
        
        // Si l'utilisateur n'a que ROLE_USER ou un tableau vide, il va vers le dashboard client
        return new RedirectResponse($this->urlGenerator->generate('app_client_dashboard'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
