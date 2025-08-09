<?php

namespace App\Service;

use Twilio\Rest\Client;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SmsService
{
    private Client $twilioClient;
    private string $fromNumber;
    private string $toNumber;

    public function __construct(ParameterBagInterface $params)
    {
        $accountSid = $params->get('twilio_account_sid');
        $authToken = $params->get('twilio_auth_token');
        $this->fromNumber = $params->get('twilio_from_number');
        $this->toNumber = $params->get('twilio_to_number');
        
        $this->twilioClient = new Client($accountSid, $authToken);
    }

    public function sendRegistrationSms(string $userName): bool
    {
        try {
            $message = sprintf(
                "Welcome %s ! Your registration has been successful. Thank you for joining our platform.!",
                $userName
            );

            $this->twilioClient->messages->create(
                $this->toNumber,
                [
                    'from' => $this->fromNumber,
                    'body' => $message
                ]
            );

            return true;
        } catch (\Exception $e) {
            // Log l'erreur si nécessaire
            error_log('Erreur envoi SMS: ' . $e->getMessage());
            return false;
        }
    }

    public function sendCustomSms(string $message): bool
    {
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
