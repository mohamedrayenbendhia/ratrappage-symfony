

# 🔐 Symfony User Management System

## 📋 Description
Système complet de gestion d'utilisateurs avec authentification, autorisation basée sur des rôles, réinitialisation de mot de passe, vérification d'email, intégration reCAPTCHA et notifications SMS/Email.

## 🛠️ Technologies & Bundles Utilisés

### Framework & Core
- **Symfony 6.x** - Framework PHP
- **Doctrine ORM** - Gestion de base de données
- **Twig** - Moteur de templates

### Bundles de Sécurité
- **symfony/security-bundle** - Authentification et autorisation
- **symfonycasts/reset-password-bundle** - Réinitialisation de mot de passe
- **symfonycasts/verify-email-bundle** - Vérification d'email
- **scheb/2fa-bundle** - Authentification à deux facteurs

### Validation & Formulaires
- **symfony/form** - Gestion des formulaires
- **symfony/validator** - Validation des données
- **google/recaptcha** - Protection reCAPTCHA

### Communication
- **symfony/mailer** - Envoi d'emails
- **twilio/sdk** - Notifications SMS

### Développement
- **symfony/maker-bundle** - Générateurs de code
- **sensio/framework-extra-bundle** - Annotations et fonctionnalités supplémentaires

## 🎯 Fonctionnalités Principales

### 🔑 Authentification & Autorisation
- **Login/Logout** avec gestion de sessions
- **Inscription** avec validation complète
- **Système de rôles** (SUPER_ADMIN, ADMIN, USER)
- **Authentification à 2 facteurs** (2FA) par email
- **Vérification d'email** obligatoire

### 👥 Gestion des Utilisateurs (CRUD)
- **Création** d'utilisateurs par les admins
- **Lecture** avec filtrage par rôles et exclusion de l'utilisateur connecté
- **Modification** avec restrictions de permissions
- **Suppression** avec confirmation
- **Blocage/Déblocage** d'utilisateurs
- **Photo de profil** via URL dans la page profile

### 🖼️ Gestion des Profils
- **Page profile centrée** avec design moderne
- **Upload de photo de profil** par URL
- **Prévisualisation** en temps réel de l'image
- **Mise à jour** dynamique de la photo dans l'en-tête
- **Formulaire responsive** avec validation côté client

### 🔒 Sécurité Avancée
- **Réinitialisation de mot de passe** sécurisée
- **Protection reCAPTCHA** sur les formulaires sensibles
- **Validation de tokens CSRF**
- **Hachage sécurisé** des mots de passe

### 📧 Notifications
- **Emails** de vérification et réinitialisation
- **SMS** via Twilio pour notifications importantes
- **Templates** personnalisés pour tous les emails

## 🏗️ Architecture du Projet

### 📁 Structure des Fichiers

```
src/
├── Controller/
│   ├── AdminController.php          # Gestion espace admin
│   ├── ClientController.php         # Espace client (USER)
│   ├── RegistrationController.php   # Inscription utilisateurs
│   ├── ResetPasswordController.php  # Réinitialisation mot de passe
│   ├── SecurityController.php       # Login/Logout
│   └── UserController.php           # CRUD utilisateurs
├── Entity/
│   ├── User.php                     # Entité utilisateur principal
│   └── ResetPasswordRequest.php     # Tokens de réinitialisation
├── Form/
│   ├── LoginFormType.php            # Formulaire de connexion
│   ├── RegistrationFormType.php     # Formulaire d'inscription
│   ├── UserType.php                 # Formulaire CRUD utilisateur
│   ├── ProfileType.php              # Modification profil
│   ├── ChangePasswordFormType.php   # Changement mot de passe
│   └── ResetPasswordRequestFormType.php # Demande reset password
├── Repository/
│   ├── UserRepository.php           # Requêtes personnalisées
│   └── ResetPasswordRequestRepository.php
├── Security/
│   ├── AppAuthenticator.php         # Authentificateur principal
│   └── EmailVerifier.php            # Vérification emails
├── Service/
│   └── SmsService.php               # Service notifications SMS
└── EventListener/
    └── LoginListener.php            # Événements de connexion

templates/
├── base.html.twig                   # Template de base
├── admin/
│   ├── index.html.twig              # Dashboard admin
│   └── please-verify-email.html.twig
├── client/
│   └── dashboard.html.twig          # Espace client
├── security/
│   └── login.html.twig              # Page de connexion
├── registration/
│   ├── register.html.twig           # Page d'inscription
│   └── confirmation_email.html.twig
├── reset_password/
│   ├── request.html.twig            # Demande reset
│   ├── check_email.html.twig        # Vérification email
│   └── reset.html.twig              # Nouveau mot de passe
└── user/
    ├── index.html.twig              # Liste utilisateurs
    ├── new.html.twig                # Création utilisateur
    ├── edit.html.twig               # Modification utilisateur
    └── _form.html.twig              # Formulaire réutilisable

config/
├── packages/
│   ├── security.yaml                # Configuration sécurité
│   ├── doctrine.yaml                # Configuration BDD
│   ├── mailer.yaml                  # Configuration emails
│   ├── recaptcha.yaml               # Configuration reCAPTCHA
│   └── scheb_2fa.yaml               # Configuration 2FA
└── routes.yaml                      # Routes principales
```

## 🚀 Installation & Configuration

### Prérequis
- **PHP 8.1+**
- **Composer**
- **MySQL/MariaDB**
- **Compte SMTP** (Gmail, Mailtrap, etc.)
- **Compte Twilio** (pour SMS)
- **Clés reCAPTCHA Google**

### 1. Clonage du Projet
```bash
git clone 
cd AuthUserManagement
```

### 2. Installation des Dépendances
```bash
composer install
```

### 3. Configuration de l'Environnement
Créer un fichier `.env.local` et configurer :

```env
# Base de données
DATABASE_URL="mysql://username:password@127.0.0.1:3306/ratrappage"

# Mailer SMTP
MAILER_DSN=smtp://username:password@smtp.gmail.com:587

# Twilio (SMS)
TWILIO_SID=your_twilio_sid
TWILIO_TOKEN=your_twilio_token
TWILIO_FROM=your_twilio_phone_number

# reCAPTCHA
RECAPTCHA_SITE_KEY=your_site_key
RECAPTCHA_SECRET_KEY=your_secret_key
```

### 4. Configuration de la Base de Données
```bash
# Créer la base de données
php bin/console doctrine:database:create

# Appliquer les migrations
php bin/console doctrine:migrations:migrate

# Ou utiliser schema:update si problème avec migrations
php bin/console doctrine:schema:update --force
```

### 5. Création du Super Admin
Exécuter cette requête SQL directement dans votre base de données :

```sql
INSERT INTO user (email, roles, password, name, phone_number, is_blocked, is_verified, created_at) 
VALUES (
    'superadmin@admin.com', 
    '["ROLE_SUPER_ADMIN"]', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
    'Super Admin', 
    '12345678', 
    0, 
    1, 
    NOW()
);
```

**Identifiants Super Admin :**
- Email : `superadmin@admin.com`
- Mot de passe : `password`

### 6. Lancement du Serveur
```bash
# Vider le cache
php bin/console cache:clear

# Démarrer le serveur
symfony serve
# Ou
php -S localhost:8000 -t public/
```

## 🔐 Système de Rôles et Permissions

### ROLE_SUPER_ADMIN
- **Accès complet** à toutes les fonctionnalités
- **Création** d'utilisateurs ADMIN et USER
- **Modification/Suppression** de tous les utilisateurs
- **Gestion** des permissions et rôles

### ROLE_ADMIN
- **Accès** aux fonctionnalités d'administration
- **Visualisation** uniquement des utilisateurs avec ROLE_USER
- **Création/Modification** uniquement des ROLE_USER
- **Impossibilité** de gérer d'autres admins

### ROLE_USER (Client)
- **Accès** à l'espace client personnel
- **Modification** de son propre profil avec photo
- **Aucun accès** aux fonctionnalités d'administration

## 🔧 Fonctionnalités Techniques Avancées

### Exclusion de l'Utilisateur Connecté
Pour éviter la redondance, l'utilisateur connecté ne voit pas son propre compte dans la liste des utilisateurs :

**Implémentation dans UserController :**
```php
// SUPER_ADMIN voit tous les utilisateurs sauf lui-même
$users = $userRepository->findAllExceptCurrentUser($currentUser->getId());

// ADMIN voit seulement les clients sauf lui-même  
$users = $userRepository->findUsersByRoleExceptCurrent('ROLE_USER', $currentUser->getId());
```

**Nouvelles méthodes dans UserRepository :**
```php
public function findAllExceptCurrentUser(int $currentUserId): array
public function findUsersByRoleExceptCurrent(string $role, int $currentUserId): array
```

### Gestion des Photos de Profil
- **Stockage URL** dans la base de données (champ `image` de type string)
- **Prévisualisation JavaScript** en temps réel
- **Validation côté client** avec fallback en cas d'erreur
- **Interface centrée** avec design moderne responsive

## 🛣️ Routes Principales

```
/ (app_login)                    - Page de connexion
/register (app_register)         - Inscription
/logout (app_logout)             - Déconnexion

/admin (app_admin)               - Dashboard admin
/client (app_client_dashboard)   - Espace client

/user (app_user_index)           - Liste utilisateurs
/user/new (app_user_new)         - Créer utilisateur
/user/{id}/edit (app_user_edit)  - Modifier utilisateur
/user/{id} (app_user_delete)     - Supprimer utilisateur

/reset-password (app_forgot_password_request) - Demande reset
/reset-password/reset (app_reset_password)    - Reset password
```

## 🔧 Commandes Utiles

```bash
# Vider le cache
php bin/console cache:clear

# Voir les routes
php bin/console debug:router

# Voir les utilisateurs
php bin/console doctrine:query:sql "SELECT * FROM user"

# Générer un nouveau contrôleur
php bin/console make:controller

# Générer un CRUD complet
php bin/console make:crud

# Créer une migration
php bin/console make:migration

# Appliquer les migrations
php bin/console doctrine:migrations:migrate
```

## 📝 Fonctionnalités Détaillées

### 🔐 Contrôle de Saisie & Validation
- **Validation email** avec contraintes Symfony
- **Validation mot de passe** (longueur minimum)
- **Validation téléphone** (8 chiffres exactement)
- **Protection CSRF** sur tous les formulaires
- **reCAPTCHA** sur inscription et login

### 📧 Système de Mailing
- **Emails de vérification** après inscription
- **Emails de réinitialisation** de mot de passe
- **Templates Twig** personnalisés
- **Configuration SMTP** flexible

### 📱 Notifications SMS (Twilio)
- **Codes de vérification** 2FA
- **Notifications** importantes
- **Configuration** via variables d'environnement

### 🔄 Reset Password
- **Tokens sécurisés** avec expiration
- **Liens uniques** par utilisateur
- **Validation** côté serveur
- **Emails automatiques**

## 🐛 Dépannage

### Problèmes Courants

1. **Erreur de migration**
   ```bash
   php bin/console doctrine:schema:update --force
   ```

2. **Problème de cache**
   ```bash
   php bin/console cache:clear --env=dev
   ```

3. **Erreur de permissions**
   ```bash
   chmod -R 775 var/
   ```

4. **Test des emails**
   - Utiliser Mailtrap pour tester en développement
   - Vérifier la configuration SMTP dans `.env.local`

## 📚 Documentation Additionnelle

- [Symfony Security](https://symfony.com/doc/current/security.html)
- [Doctrine ORM](https://www.doctrine-project.org/projects/orm.html)
- [Twig Templates](https://twig.symfony.com/doc/3.x/)
- [SymfonyCasts Bundles](https://symfonycasts.com/)

## 👨‍💻 Développé par
**rayen ben dhia ** - Système de gestion d'utilisateurs avec authentification avancée

---
*Ce projet démontre l'implémentation complète d'un système d'authentification et d'autorisation robuste avec Symfony.*
