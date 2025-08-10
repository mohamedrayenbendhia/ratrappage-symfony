<?php

namespace App\Controller;

use App\Service\SmsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestSmsController extends AbstractController
{
    private SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    #[Route('/test/sms', name: 'test_sms')]
    public function testSms(): Response
    {
        $results = [];
        
        // Test 1: SMS de bienvenue
        try {
            $results['test1'] = [
                'name' => 'Test SMS de bienvenue',
                'success' => $this->smsService->sendRegistrationSms('TestUser'),
                'message' => 'SMS de bienvenue envoyé'
            ];
        } catch (\Exception $e) {
            $results['test1'] = [
                'name' => 'Test SMS de bienvenue',
                'success' => false,
                'error' => $e->getMessage()
            ];
        }

        // Test 2: SMS personnalisé
        try {
            $results['test2'] = [
                'name' => 'Test SMS personnalisé',
                'success' => $this->smsService->sendCustomSms('Ceci est un test de SMS personnalisé'),
                'message' => 'SMS personnalisé envoyé'
            ];
        } catch (\Exception $e) {
            $results['test2'] = [
                'name' => 'Test SMS personnalisé',
                'success' => false,
                'error' => $e->getMessage()
            ];
        }

        // Test 3: Vérification de la configuration
        $config = [
            'twilio_sid' => $this->getParameter('twilio_account_sid'),
            'twilio_token' => $this->getParameter('twilio_auth_token'),
            'twilio_from' => $this->getParameter('twilio_from_number'),
            'twilio_to' => $this->getParameter('twilio_to_number'),
        ];

        return $this->render('test/sms_test.html.twig', [
            'results' => $results,
            'config' => $config
        ]);
    }

    #[Route('/test/sms/send', name: 'test_sms_send')]
    public function sendTestSms(): Response
    {
        try {
            $success = $this->smsService->sendCustomSms('Test SMS envoyé depuis le contrôleur de test à ' . date('H:i:s'));
            
            if ($success) {
                $this->addFlash('success', 'SMS de test envoyé avec succès !');
            } else {
                $this->addFlash('error', 'Échec de l\'envoi du SMS de test');
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'envoi: ' . $e->getMessage());
        }

        return $this->redirectToRoute('test_sms');
    }
}
