<?php

namespace App\Service;

use Twilio\Rest\Client;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SmsService
{
    private ?Client $twilioClient;
    private string $fromNumber;
    private string $toNumber;

    public function __construct(ParameterBagInterface $params)
    {
        $accountSid = $params->get('twilio_account_sid');
        $authToken = $params->get('twilio_auth_token');
        $this->fromNumber = $params->get('twilio_from_number');
        $this->toNumber = $params->get('twilio_to_number');
        
        // Vérifier si nous avons de vraies credentials ou des valeurs par défaut
        if ($accountSid === 'ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' || $authToken === 'your_default_token') {
            // Mode simulation - ne pas créer le client Twilio
            $this->twilioClient = null;
        } else {
            $this->twilioClient = new Client($accountSid, $authToken);
        }
    }

    public function sendRegistrationSms(string $userName, ?string $phoneNumber = null): bool
    {
        try {
            $message = sprintf(
                "Welcome %s! Your registration has been successful. Thank you for joining our platform!",
                $userName
            );

            // Utiliser le numéro de l'utilisateur ou le numéro par défaut
            $toNumber = $phoneNumber ?: $this->toNumber;
            
            // Debug: Log des informations
            error_log("SMS Debug - From: {$this->fromNumber}, To: {$toNumber}, Message: {$message}");
            
            // Vérifier que nous avons un numéro valide
            if (empty($toNumber)) {
                error_log('Erreur SMS: Aucun numéro de destination défini');
                return false;
            }

            // Si pas de client Twilio (mode simulation)
            if ($this->twilioClient === null) {
                error_log('Mode simulation SMS - Message qui aurait été envoyé: ' . $message);
                return true; // Simuler le succès
            }

            $this->twilioClient->messages->create(
                $toNumber,
                [
                    'from' => $this->fromNumber,
                    'body' => $message
                ]
            );

            return true;
        } catch (\Exception $e) {
            error_log('Erreur lors de l\'envoi du SMS: ' . $e->getMessage());
            return false;
        }
    }

    public function sendCustomSms(string $message): bool
    {
        // Mode simulation si pas de client Twilio configuré
        if ($this->twilioClient === null) {
            error_log('SMS en mode simulation (sendCustomSms): ' . $message);
            return true;
        }

        try {
            $this->twilioClient->messages->create(
                $this->toNumber,
                [
                    'from' => $this->fromNumber,
                    'body' => $message
                ]
            );

            return true;
        } catch (\Exception $e) {
            error_log('Erreur envoi SMS: ' . $e->getMessage());
            return false;
        }
    }
}
