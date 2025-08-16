<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository, PaginatorInterface $paginator): Response
    {
        // Vérifier les permissions
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $currentUser = $this->getUser();
        
        // Récupérer les paramètres de recherche
        $search = $request->query->get('search', '');
        $roleFilter = $request->query->get('role', '');
        
        // Créer la requête selon les permissions
        if (in_array('ROLE_SUPER_ADMIN', $currentUser->getRoles())) {
            // SUPER_ADMIN voit tous les utilisateurs sauf lui-même
            $queryBuilder = $userRepository->createSearchQueryBuilder($currentUser->getId(), $search, $roleFilter);
        } elseif (in_array('ROLE_ADMIN', $currentUser->getRoles())) {
            // ADMIN voit seulement les clients (ROLE_USER) sauf lui-même
            $queryBuilder = $userRepository->createSearchQueryBuilder($currentUser->getId(), $search, $roleFilter, 'ROLE_USER');
        }
        
        // Pagination
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            7 // 7 utilisateurs par page
        );
        
        return $this->render('user/index.html.twig', [
            'pagination' => $pagination,
            'search' => $search,
            'roleFilter' => $roleFilter,
        ]);
    }


    #[Route('/profile', name: 'app_user_profile', methods: ['GET', 'POST'])]
    public function profile(
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $userPasswordHasher,
        SluggerInterface $slugger
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(\App\Form\ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // Vérifier que l'utilisateur est toujours connecté
            $currentUser = $this->getUser();
            if (!$currentUser) {
                $this->addFlash('error', 'Your session has expired. Please log in again.');
                return $this->redirectToRoute('app_login');
            }

            // Validation simple sans exceptions
            $formData = $form->getData();
            $errors = [];
            
            if (empty(trim($formData->getName()))) {
                $errors[] = 'Name is required.';
            }
            
            if (empty(trim($formData->getEmail()))) {
                $errors[] = 'Email is required.';
            }
            
            if (empty(trim($formData->getPhoneNumber()))) {
                $errors[] = 'Phone number is required.';
            }

            // Validation du mot de passe si fourni
            $plainPassword = $form->get('plainPassword')->getData();
            if (!empty($plainPassword) && strlen($plainPassword) < 8) {
                $errors[] = 'Password must be at least 8 characters.';
            }

            // Si il y a des erreurs, les afficher sans impact sur la session
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    $this->addFlash('warning', $error);
                }
                // Forcer la re-création du formulaire avec l'utilisateur original
                $form = $this->createForm(\App\Form\ProfileType::class, $user);
                return $this->render('user/profile.html.twig', [
                    'user' => $user,
                    'form' => $form,
                ]);
            }

            if ($form->isValid()) {
                try {
                    // Gérer l'upload de l'image
                    $imageFile = $form->get('imageFile')->getData();
                    if ($imageFile) {
                        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                        $safeFilename = $slugger->slug($originalFilename);
                        $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                        try {
                            $uploadsDirectory = $this->getParameter('kernel.project_dir').'/public/uploads/profile';
                            if (!is_dir($uploadsDirectory)) {
                                mkdir($uploadsDirectory, 0755, true);
                            }
                            $imageFile->move($uploadsDirectory, $newFilename);
                            $user->setImage('/uploads/profile/'.$newFilename);
                        } catch (FileException $e) {
                            $this->addFlash('error', 'Error uploading image: ' . $e->getMessage());
                            return $this->render('user/profile.html.twig', [
                                'user' => $user,
                                'form' => $form,
                            ]);
                        }
                    }
                    
                    $plainPassword = $form->get('plainPassword')->getData();
                    
                    // Only update password if a new one is provided
                    if (!empty($plainPassword)) {
                        $user->setPassword(
                            $userPasswordHasher->hashPassword($user, $plainPassword)
                        );
                        $this->addFlash('success', 'Profile and password updated successfully.');
                    } else {
                        $this->addFlash('success', 'Profile updated successfully.');
                    }
                    
                    $entityManager->flush();
                    
                    return $this->redirectToRoute('app_user_profile');
                    
                } catch (\Exception $e) {
                    $this->addFlash('warning', 'An error occurred while updating your profile. Please try again.');
                    error_log('Profile update error: ' . $e->getMessage());
                    
                    // Re-créer le formulaire avec l'utilisateur original en cas d'erreur
                    $form = $this->createForm(\App\Form\ProfileType::class, $user);
                }
            } else {
                // Formulaire soumis mais invalide selon Symfony - traiter comme un avertissement
                $this->addFlash('warning', 'Please check your entries and try again.');
                
                // Re-créer le formulaire avec l'utilisateur original
                $form = $this->createForm(\App\Form\ProfileType::class, $user);
            }
        }

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                // Hash the password before saving
                $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
                $user->setPassword($hashedPassword);
                
                // Gérer le rôle sélectionné
                $selectedRole = $form->get('userRole')->getData();
                $currentUser = $this->getUser();
                
                // Vérifier les permissions de création de rôle
                if ($selectedRole === 'ROLE_ADMIN' && !in_array('ROLE_SUPER_ADMIN', $currentUser->getRoles())) {
                    $this->addFlash('error', 'Vous n\'avez pas les droits pour créer un administrateur.');
                    return $this->renderForm('user/new.html.twig', [
                        'user' => $user,
                        'form' => $form,
                    ]);
                }
                
                if ($selectedRole === 'ROLE_SUPER_ADMIN' && !in_array('ROLE_SUPER_ADMIN', $currentUser->getRoles())) {
                    $this->addFlash('error', 'Vous n\'avez pas les droits pour créer un super administrateur.');
                    return $this->renderForm('user/new.html.twig', [
                        'user' => $user,
                        'form' => $form,
                    ]);
                }
                
                $user->setRoles([$selectedRole]);
                $user->setIsVerified(true); // Les utilisateurs créés par admin sont automatiquement vérifiés
                
                $entityManager->persist($user);
                $entityManager->flush();
                
                $this->addFlash('success', 'User created successfully.');
                return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('error', 'Password is required.');
            }
        } else if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Please correct the errors in the form.');
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $currentUser = $this->getUser();
        
        // Vérifier si l'admin peut modifier cet utilisateur
        if (in_array('ROLE_ADMIN', $currentUser->getRoles()) && !in_array('ROLE_SUPER_ADMIN', $currentUser->getRoles())) {
            // Un ADMIN normal ne peut modifier que les clients (ROLE_USER)
            if (!in_array('ROLE_USER', $user->getRoles())) {
                throw $this->createAccessDeniedException('Vous ne pouvez modifier que les clients.');
            }
        }
        
        $form = $this->createForm(UserType::class, $user, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            
            // Only update password if a new one is provided
            if (!empty($plainPassword)) {
                $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
                $user->setPassword($hashedPassword);
            }
            
            // Gérer le changement de rôle (seulement pour SUPER_ADMIN)
            if ($form->has('userRole')) {
                $selectedRole = $form->get('userRole')->getData();
                if (in_array('ROLE_SUPER_ADMIN', $currentUser->getRoles())) {
                    $user->setRoles([$selectedRole]);
                }
            }
            
            $entityManager->flush();
            $this->addFlash('success', 'User updated successfully.');

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        } else if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Please correct the errors in the form.');
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'User deleted successfully.');
        } else {
            $this->addFlash('error', 'Invalid CSRF token.');
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/block', name: 'app_user_block', methods: ['POST'])]
    public function block(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('block'.$user->getId(), $request->request->get('_token'))) {
            $user->setIsBlocked(true);
            $entityManager->flush();
            $this->addFlash('danger', 'User has been blocked.');
        }
        return $this->redirectToRoute('app_user_index');
    }

    #[Route('/{id}/unblock', name: 'app_user_unblock', methods: ['POST'])]
    public function unblock(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('unblock'.$user->getId(), $request->request->get('_token'))) {
            $user->setIsBlocked(false);
            $entityManager->flush();
            $this->addFlash('success', 'User has been unblocked.');
        }
        return $this->redirectToRoute('app_user_index');
    }
}
