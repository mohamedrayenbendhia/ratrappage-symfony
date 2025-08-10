<?php
/**
 * Script de test SMS autonome
 * Utilisation: php test_sms_standalone.php
 */

require_once __DIR__ . '/vendor/autoload.php';

use Twilio\Rest\Client;
use Symfony\Component\Dotenv\Dotenv;

echo "=== TEST SMS STANDALONE ===\n";

// Charger les variables d'environnement
if (file_exists(__DIR__ . '/.env.local')) {
    $dotenv = new Dotenv();
    $dotenv->load(__DIR__ . '/.env.local');
    echo "✓ Fichier .env.local chargé\n";
} else {
    echo "⚠ Aucun fichier .env.local trouvé\n";
}

// Configuration Twilio
$accountSid = $_ENV['TWILIO_SID'] ?? 'default_value';
$authToken = $_ENV['TWILIO_AUTH_TOKEN'] ?? 'default_value';
$fromNumber = $_ENV['TWILIO_FROM_NUMBER'] ?? '+1234567890';
$toNumber = $_ENV['TWILIO_TO_NUMBER'] ?? '+21655667940';

echo "\n=== CONFIGURATION ===\n";
echo "Account SID: " . $accountSid . "\n";
echo "Auth Token: " . $authToken . "\n";
echo "From Number: " . $fromNumber . "\n";
echo "To Number: " . $toNumber . "\n";

// Vérifier si on a de vraies clés
if ($accountSid === 'default_value' || $authToken === 'default_value') {
    echo "\n❌ SIMULATION MODE: Pas de vraies clés Twilio configurées\n";
    echo "Message qui aurait été envoyé: 'Test SMS depuis script autonome à " . date('Y-m-d H:i:s') . "'\n";
    echo "Pour utiliser de vraies clés, créez un fichier .env.local avec:\n";
    echo "TWILIO_SID=votre_sid\n";
    echo "TWILIO_AUTH_TOKEN=votre_token\n";
    echo "TWILIO_FROM_NUMBER=votre_numero\n";
    echo "TWILIO_TO_NUMBER=numero_destinataire\n";
    exit(0);
}

echo "\n=== TEST ENVOI SMS ===\n";

try {
    // Créer le client Twilio
    $client = new Client($accountSid, $authToken);
    echo "✓ Client Twilio créé\n";

    // Message de test
    $message = "Test SMS depuis script autonome à " . date('Y-m-d H:i:s');

    // Envoyer le SMS
    $messageInstance = $client->messages->create(
        $toNumber,
        [
            'from' => $fromNumber,
            'body' => $message
        ]
    );

    echo "✅ SMS ENVOYÉ AVEC SUCCÈS !\n";
    echo "SID du message: " . $messageInstance->sid . "\n";
    echo "Statut: " . $messageInstance->status . "\n";
    echo "Message: " . $message . "\n";

} catch (\Twilio\Exceptions\TwilioException $e) {
    echo "❌ ERREUR TWILIO: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";
} catch (\Exception $e) {
    echo "❌ ERREUR GÉNÉRALE: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DU TEST ===\n";
?>
