<?php
/**
 * Script pour créer le fichier .env.local avec vos clés Twilio
 * Utilisation: php create_env_local.php
 */

echo "=== CONFIGURATION TWILIO ===\n";
echo "Ce script va créer le fichier .env.local avec vos clés Twilio\n\n";

// Demander les informations
echo "Entrez votre Account SID Twilio: ";
$accountSid = trim(fgets(STDIN));

echo "Entrez votre Auth Token Twilio: ";
$authToken = trim(fgets(STDIN));

echo "Entrez votre numéro Twilio (avec indicatif, ex: +33123456789): ";
$fromNumber = trim(fgets(STDIN));

echo "Entrez le numéro de destination (avec indicatif, ex: +33123456789): ";
$toNumber = trim(fgets(STDIN));

// Créer le contenu du fichier
$envContent = "# Configuration Twilio pour SMS
TWILIO_SID=$accountSid
TWILIO_AUTH_TOKEN=$authToken
TWILIO_FROM_NUMBER=$fromNumber
TWILIO_TO_NUMBER=$toNumber
";

// Écrire le fichier
file_put_contents(__DIR__ . '/.env.local', $envContent);

echo "\n✅ Fichier .env.local créé avec succès !\n";
echo "Vous pouvez maintenant tester l'envoi de SMS réels.\n";
echo "\nPour tester :\n";
echo "1. php test_sms_standalone.php\n";
echo "2. Ou allez sur http://127.0.0.1:8000/test/sms\n";
?>
