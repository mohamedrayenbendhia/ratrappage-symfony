# 🔐 Symfony User Management & Rating System

## 📋 Description
Système complet de gestion d'utilisateurs avec authentification, autorisation basée sur des rôles, système de rating/avis entre utilisateurs, réinitialisation de mot de passe, vérification d'email, intégration reCAPTCHA et notifications SMS/Email.

## 🛠️ Technologies & Bundles Utilisés

### Framework & Core
- **Symfony 6.x** - Framework PHP moderne avec composants modulaires
- **Doctrine ORM** - Gestion avancée de base de données avec relations et migrations
- **Twig** - Moteur de templates avec héritage, macros et fonctions avancées

### Bundles de Sécurité & Authentification
- **symfony/security-bundle** - Authentification et autorisation avec roles hiérarchiques
- **symfonycasts/reset-password-bundle** - Réinitialisation sécurisée avec tokens temporaires

### Validation & Formulaires
- **symfony/form** - Gestion avancée des formulaires avec types personnalisés
- **symfony/validator** - Validation côté serveur avec contraintes personnalisées
- **google/recaptcha** - Protection anti-bot avec reCAPTCHA v2

### Communication & Notifications
- **symfony/mailer** - Envoi d'emails avec templates HTML/text
- **twilio/sdk** - API SMS pour notifications en temps réel

### Upload & Gestion de Fichiers
- **symfony/string** - Manipulation sécurisée des chaînes et slugification
- **Gestion d'upload** - Système d'upload d'images avec validation et stockage sécurisé

### Développement & Debug
- **symfony/maker-bundle** - Générateurs de code pour productivité
- **sensio/framework-extra-bundle** - Annotations et fonctionnalités supplémentaires
- **symfony/profiler-pack** - Outils de debug et profiling en développement

## 🎯 Fonctionnalités Principales

### 🔑 Authentification & Autorisation
- **Login/Logout** avec gestion de sessions persistantes et remember-me
- **Inscription** avec validation multi-niveau côté client et serveur
- **Système de rôles hiérarchique** (SUPER_ADMIN > ADMIN > USER)
- **Authentification à 2 facteurs** (2FA) par email avec codes temporaires
- **Vérification d'email** obligatoire avec tokens sécurisés et expiration

### 👥 Gestion des Utilisateurs (CRUD Complet)
- **Création** d'utilisateurs par les admins avec attribution de rôles
- **Lecture** avec filtrage avancé par rôles et exclusion intelligente
- **Modification** avec restrictions de permissions strictes par rôle
- **Suppression** avec confirmation et vérifications de sécurité
- **Blocage/Déblocage** d'utilisateurs en temps réel
- **Gestion de photos de profil** avec upload sécurisé et prévisualisation

### ⭐ Système de Rating & Avis (NOUVEAU)
- **Donner des avis** à d'autres utilisateurs avec système d'étoiles (1-5)
- **Commentaires optionnels** pour détailler l'expérience
- **Recevoir des ratings** avec calcul automatique de moyenne
- **Dashboard client** avec statistiques personnelles en temps réel
- **Historique complet** des avis donnés et reçus avec dates
- **Protection anti-spam** (un seul avis par utilisateur par cible)
- **Suppression** de ses propres avis avec confirmation
- **Affichage conditionnel** selon les permissions utilisateur

### 🖼️ Gestion des Profils Avancée
- **Page profile centrée** avec design moderne responsive
- **Upload de photo de profil** avec validation (JPEG, PNG, GIF, max 5MB)
- **Prévisualisation** en temps réel avec JavaScript natif
- **Validation côté client et serveur** (taille, format, sécurité)
- **Mise à jour** dynamique de l'interface sans rechargement
- **Formulaire novalidate** pour validation serveur uniquement

### 🔒 Sécurité Multicouche
- **Hashage BCrypt** pour les mots de passe avec salt automatique
- **Protection CSRF** sur tous les formulaires sensibles
- **Validation stricte** des uploads avec vérification MIME
- **Sessions sécurisées** avec cookies HTTPOnly et SameSite
- **Protection contre l'énumération** d'utilisateurs
- **Redirections sécurisées** selon les rôles utilisateur

## 🏗️ Architecture du Projet & Fichiers Modifiés

### 📁 Structure Complète des Fichiers

```
src/
├── Controller/
│   ├── AdminController.php          # [MODIFIÉ] Gestion espace admin + redirections
│   ├── ClientController.php         # [NOUVEAU] Espace client complet avec rating
│   ├── RegistrationController.php   # [EXISTANT] Inscription utilisateurs
│   ├── ResetPasswordController.php  # [EXISTANT] Réinitialisation mot de passe
│   ├── SecurityController.php       # [MODIFIÉ] Login/Logout + redirections par rôle
│   └── UserController.php           # [MODIFIÉ] CRUD + exclusion utilisateur connecté
├── Entity/
│   ├── User.php                     # [MODIFIÉ] Entité principale + champ image + types nullable
│   ├── Rating.php                   # [NOUVEAU] Entité pour système d'avis
│   └── ResetPasswordRequest.php     # [EXISTANT] Tokens de réinitialisation
├── Form/
│   ├── LoginFormType.php            # [EXISTANT] Formulaire de connexion
│   ├── RegistrationFormType.php     # [EXISTANT] Formulaire d'inscription
│   ├── UserType.php                 # [EXISTANT] Formulaire CRUD utilisateur
│   ├── ProfileType.php              # [MODIFIÉ] Upload d'image + validation serveur
│   ├── RatingType.php               # [NOUVEAU] Formulaire pour donner des avis
│   ├── ChangePasswordFormType.php   # [EXISTANT] Changement mot de passe
│   └── ResetPasswordRequestFormType.php # [EXISTANT] Demande reset password
├── Repository/
│   ├── UserRepository.php           # [MODIFIÉ] Requêtes d'exclusion + filtrage par rôle
│   ├── RatingRepository.php         # [NOUVEAU] Requêtes pour système de rating
│   └── ResetPasswordRequestRepository.php # [EXISTANT]
├── Security/
│   ├── AppAuthenticator.php         # [MODIFIÉ] Redirections conditionnelles par rôle
│   └── EmailVerifier.php            # [EXISTANT] Vérification emails
├── Service/
│   └── SmsService.php               # [EXISTANT] Service notifications SMS
└── EventListener/
    └── LoginListener.php            # [EXISTANT] Événements de connexion

templates/
├── base.html.twig                   # [EXISTANT] Template de base
├── admin/
│   ├── index.html.twig              # [EXISTANT] Dashboard admin
│   └── please-verify-email.html.twig # [EXISTANT]
├── client/                          # [NOUVEAU DOSSIER] Espace client complet
│   ├── dashboard.html.twig          # [NOUVEAU] Dashboard avec statistiques
│   ├── rating.html.twig             # [NOUVEAU] Page pour donner des avis
│   └── ratings_received.html.twig   # [NOUVEAU] Page des avis reçus
├── security/
│   └── login.html.twig              # [EXISTANT] Page de connexion
├── registration/
│   ├── register.html.twig           # [EXISTANT] Page d'inscription
│   └── confirmation_email.html.twig # [EXISTANT]
├── reset_password/
│   ├── request.html.twig            # [EXISTANT] Demande reset
│   ├── check_email.html.twig        # [EXISTANT] Vérification email
│   └── reset.html.twig              # [EXISTANT] Nouveau mot de passe
└── user/
    ├── index.html.twig              # [EXISTANT] Liste utilisateurs
    ├── profile.html.twig            # [MODIFIÉ] Page profil avec upload image
    ├── new.html.twig                # [EXISTANT] Création utilisateur
    ├── edit.html.twig               # [EXISTANT] Modification utilisateur
    └── _form.html.twig              # [EXISTANT] Formulaire réutilisable

config/
├── packages/
│   ├── security.yaml                # [MODIFIÉ] Règles d'accès pour routes client
│   ├── framework.yaml               # [MODIFIÉ] Configuration session étendue
│   ├── doctrine.yaml                # [EXISTANT] Configuration BDD
│   ├── mailer.yaml                  # [EXISTANT] Configuration emails
│   ├── recaptcha.yaml               # [EXISTANT] Configuration reCAPTCHA
│   └── scheb_2fa.yaml               # [EXISTANT] Configuration 2FA
└── routes.yaml                      # [EXISTANT] Routes principales

public/
└── uploads/
    └── profile/                     # [NOUVEAU] Dossier pour photos de profil
```

## 🔧 Modifications Détaillées par Fichier

### 🆕 Nouveaux Fichiers Créés

#### `src/Entity/Rating.php`
**Fonctionnalité** : Entité pour gérer le système d'avis entre utilisateurs
**Modifications apportées** :
```php
// Relations bidirectionnelles
#[ORM\ManyToOne(targetEntity: User::class)]
private ?User $rater = null;    // Celui qui donne l'avis

#[ORM\ManyToOne(targetEntity: User::class)]  
private ?User $rated = null;    // Celui qui reçoit l'avis

// Validation des étoiles
#[Assert\Range(min: 1, max: 5)]
private ?int $stars = null;

// Commentaire optionnel
#[ORM\Column(type: Types::TEXT, nullable: true)]
private ?string $comment = null;

// Horodatage automatique
public function __construct() {
    $this->createdAt = new \DateTime();
}
```

#### `src/Repository/RatingRepository.php`
**Fonctionnalité** : Requêtes spécialisées pour le système de rating
**Méthodes créées** :
```php
// Avis donnés par un utilisateur
public function findRatingsByRater(User $rater): array

// Avis reçus par un utilisateur  
public function findRatingsForUser(User $rated): array

// Utilisateurs disponibles pour notation
public function findUsersToRate(User $currentUser): array

// Vérification si déjà noté
public function hasUserRated(User $rater, User $rated): bool

// Calcul moyenne des notes
public function getAverageRating(User $user): float

// Compte total des avis
public function countRatingsForUser(User $user): int
```

#### `src/Form/RatingType.php`
**Fonctionnalité** : Formulaire pour donner des avis
**Configuration** :
```php
// Sélection de l'utilisateur à noter
->add('rated', EntityType::class, [
    'class' => User::class,
    'choice_label' => function(User $user) {
        return $user->getName() . ' (' . $user->getEmail() . ')';
    },
    'choices' => $options['available_users']
])

// Système d'étoiles 1-5
->add('stars', ChoiceType::class, [
    'choices' => [
        '1 Star' => 1, '2 Stars' => 2, '3 Stars' => 3,
        '4 Stars' => 4, '5 Stars' => 5
    ]
])
```

#### `src/Controller/ClientController.php`
**Fonctionnalité** : Contrôleur complet pour l'espace client
**Méthodes implémentées** :
```php
// Dashboard avec statistiques
public function dashboard(RatingRepository $ratingRepository): Response

// Donner des avis + historique  
public function rating(Request $request, ...): Response

// Voir les avis reçus avec moyennes
public function ratingsReceived(RatingRepository $ratingRepository): Response

// Gestion du profil avec upload
public function profile(Request $request, ...): Response

// Supprimer ses propres avis
public function deleteRating(Rating $rating, ...): Response
```

#### Templates Client (`templates/client/`)

**`dashboard.html.twig`** : 
- Interface moderne avec CSS Grid et Flexbox
- Statistiques en temps réel (moyenne, total avis)
- Cartes d'action pour navigation rapide
- Activité récente avec limitations d'affichage

**`rating.html.twig`** :
- Formulaire Ajax pour soumission d'avis
- Historique des avis donnés avec actions
- Protection contre les doublons
- Interface responsive avec prévisualisation

**`ratings_received.html.twig`** :
- Affichage des avis reçus avec avatars
- Calcul et affichage de statistiques
- Filtrage et tri des avis
- Interface de lecture optimisée

### 🔄 Fichiers Modifiés

#### `src/Entity/User.php`
**Modifications critiques** :
```php
// AVANT - Types stricts causant erreurs null
public function setName(string $name): static
public function setEmail(string $email): static  
public function setPhoneNumber(string $phoneNumber): static

// APRÈS - Types nullable pour formulaires
public function setName(?string $name): static
public function setEmail(?string $email): static
public function setPhoneNumber(?string $phoneNumber): static

// Ajout champ image pour photos de profil
#[ORM\Column(length: 255, nullable: true)]
private ?string $image = null;
```

#### `src/Controller/UserController.php`
**Corrections majeures** :
```php
// AVANT - Hashage incorrect causant déconnexions
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

// APRÈS - Hashage Symfony correct
$user->setPassword(
    $userPasswordHasher->hashPassword($user, $plainPassword)
);

// Amélioration upload avec Slugger
$safeFilename = $slugger->slug($originalFilename);
$newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
```

#### `src/Repository/UserRepository.php`
**Nouvelles méthodes d'exclusion** :
```php
// Exclut l'utilisateur connecté (objet User)
public function findUsersExceptCurrent(User $currentUser): array

// Version avec ID numérique  
public function findAllExceptCurrentUser(int $currentUserId): array

// Filtrage par rôle + exclusion utilisateur
public function findUsersByRoleExceptCurrent(string $role, int $currentUserId): array
```

#### `src/Security/AppAuthenticator.php`
**Logique de redirection améliorée** :
```php
// Redirection hiérarchique selon les rôles
if (in_array('ROLE_SUPER_ADMIN', $roles)) {
    return new RedirectResponse($this->urlGenerator->generate('app_user_index'));
} elseif (in_array('ROLE_ADMIN', $roles)) {
    return new RedirectResponse($this->urlGenerator->generate('app_user_index'));
} else {
    // ROLE_USER (client) vers dashboard
    return new RedirectResponse($this->urlGenerator->generate('app_client_dashboard'));
}
```

#### `src/Controller/SecurityController.php`
**Correction des boucles de redirection** :
```php
// Vérification si utilisateur déjà connecté
if ($this->getUser()) {
    $roles = $this->getUser()->getRoles();
    
    if (in_array('ROLE_SUPER_ADMIN', $roles) || in_array('ROLE_ADMIN', $roles)) {
        return $this->redirectToRoute('app_user_index');
    } else {
        return $this->redirectToRoute('app_client_dashboard');
    }
}
```

#### `templates/user/profile.html.twig`
**Améliorations interface** :
```html
<!-- Ajout novalidate pour validation serveur -->
{{ form_start(form, {'attr': {'enctype': 'multipart/form-data', 'novalidate': 'novalidate'}}) }}

<!-- Affichage des erreurs par champ -->
{{ form_errors(form.name) }}
{{ form_errors(form.email) }}
{{ form_errors(form.phoneNumber) }}

<!-- Bouton conditionnel selon rôle -->
{% if app.user and 'ROLE_USER' in app.user.roles and 'ROLE_ADMIN' not in app.user.roles %}
    <a href="{{ path('app_client_dashboard') }}">Back to dashboard</a>
{% else %}
    <a href="{{ path('app_user_index') }}">Back to list</a>
{% endif %}
```

#### `src/Form/ProfileType.php`
**Simplification des validations** :
```php
// SUPPRIMÉ - Contraintes redondantes pour éviter conflits
// Les champs name, email, phoneNumber utilisent maintenant
// uniquement les validations de l'entité User.php

// CONSERVÉ - Validations spécifiques au formulaire
->add('imageFile', FileType::class, [
    'constraints' => [
        new File([
            'maxSize' => '5M',
            'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif']
        ])
    ]
])
```

#### `config/packages/security.yaml`
**Nouvelles règles d'accès** :
```yaml
access_control:
    - { path: ^/client, roles: ROLE_USER }
    - { path: ^/user$, roles: [ROLE_ADMIN, ROLE_SUPER_ADMIN] }
    - { path: ^/user/, roles: [ROLE_ADMIN, ROLE_SUPER_ADMIN] }
    - { path: ^/admin, roles: [ROLE_ADMIN, ROLE_SUPER_ADMIN] }
```

#### `config/packages/framework.yaml`
**Configuration session étendue** :
```yaml
session:
    cookie_lifetime: 3600     # 1 heure
    gc_maxlifetime: 3600      # 1 heure  
    cookie_secure: auto
    cookie_samesite: lax
```

## 🚀 Installation & Configuration

### Prérequis Système
- **PHP 8.1+** avec extensions : `pdo_mysql`, `intl`, `json`, `xml`, `gd`
- **Composer 2.0+** - Gestionnaire de dépendances PHP
- **MySQL 8.0+/MariaDB 10.3+** - Base de données relationnelle
- **Apache/Nginx** (optionnel pour production)
- **Node.js & npm** (optionnel pour assets)

### Services Externes
- **Compte SMTP** (Gmail, Mailtrap, SendGrid, Mailgun)
- **Compte Twilio** (pour notifications SMS)
- **Clés Google reCAPTCHA v2**

### 1. Clonage et Installation
```bash
# Cloner le repository
git clone https://github.com/mohamedrayenbendhia/ratrappage-symfony.git
cd ratrappage-symfony

# Installation des dépendances PHP
composer install

# Vérification des prérequis système
composer check-platform-reqs

# Installation assets (si nécessaire)
npm install
npm run build
```

### 2. Configuration Environnement
Créer `.env.local` avec vos configurations :

```env
# ===== CONFIGURATION BASE =====
APP_ENV=dev
APP_DEBUG=true
APP_SECRET=31313636c4b5b4b6c4b5b4b6c4b5b4b6c4b5b4b6c4b5b4b6

# ===== BASE DE DONNÉES =====
DATABASE_URL="mysql://username:password@127.0.0.1:3306/ratrappage_symfony?charset=utf8mb4"

# ===== MAILER SMTP =====
# Gmail
MAILER_DSN=smtp://username:password@smtp.gmail.com:587

# Mailtrap (développement)
# MAILER_DSN=smtp://username:password@sandbox.smtp.mailtrap.io:2525

# SendGrid (production)
# MAILER_DSN=sendgrid://api_key@default

# ===== TWILIO SMS =====
TWILIO_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_TOKEN=your_auth_token_here
TWILIO_FROM=+1234567890

# ===== GOOGLE RECAPTCHA V2 =====
RECAPTCHA_SITE_KEY=6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI
RECAPTCHA_SECRET_KEY=6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe

# ===== UPLOAD CONFIGURATION =====
UPLOAD_PATH=public/uploads/profile
MAX_FILE_SIZE=5242880  # 5MB en bytes
```

### 3. Base de Données
```bash
# Créer la base de données
php bin/console doctrine:database:create

# Appliquer les migrations (recommandé)
php bin/console doctrine:migrations:migrate --no-interaction

# OU mise à jour forcée du schéma (si problèmes)
php bin/console doctrine:schema:update --force

# Validation de la structure
php bin/console doctrine:schema:validate
```

### 4. Création du Super Admin

**Method 1: SQL Direct (Rapide)**
```sql
INSERT INTO user (email, roles, password, name, phone_number, is_blocked, is_verified, created_at, image) 
VALUES (
    'superadmin@admin.com', 
    '["ROLE_SUPER_ADMIN"]', 
    '$2y$13$J2Q2Z9X8V.x0DFB8C.QW8OfA6ZKfI8QfJ2Q2Z9X8V.x0DFB8C.QW8O', 
    'Super Admin', 
    '12345678', 
    0, 
    1, 
    NOW(),
    NULL
);
```

**Method 2: Commande Console**
```bash
# Créer un utilisateur via commande SQL
php bin/console doctrine:query:sql "INSERT INTO user (email, roles, password, name, phone_number, is_blocked, is_verified, created_at) VALUES ('admin@test.com', '[\"ROLE_SUPER_ADMIN\"]', '\$2y\$13\$xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', 'Admin Test', '12345678', 0, 1, NOW())"

# Vérifier la création
php bin/console doctrine:query:sql "SELECT id, email, roles, name FROM user WHERE email = 'superadmin@admin.com'"
```

### 5. Configuration Permissions et Dossiers
```bash
# Permissions sur dossiers système
chmod -R 775 var/cache var/log

# Création et permissions dossier uploads
mkdir -p public/uploads/profile
chmod -R 775 public/uploads

# Nettoyage du cache
php bin/console cache:clear
```

### 6. Lancement et Tests

**Serveur de Développement**
```bash
# Symfony CLI (recommandé)
symfony serve -d
# Accessible sur https://127.0.0.1:8000

# Serveur PHP intégré
php -S localhost:8000 -t public/

# Vérification du statut
symfony server:status
```

**Tests de Validation**
```bash
# Vérifier les routes
php bin/console debug:router | grep -E "(user|client|admin)"

# Tester les emails (Mailtrap/Gmail)
php bin/console debug:config mailer

# Vérifier la sécurité
php bin/console debug:config security

# Lister les utilisateurs
php bin/console doctrine:query:sql "SELECT id, email, roles, name, is_verified FROM user"

# Tester upload (permissions)
ls -la public/uploads/profile/
```

## 🔐 Système de Rôles et Permissions

### Hiérarchie des Rôles
```
ROLE_SUPER_ADMIN (Administrateur Principal)
    ├── Accès complet à toutes les fonctionnalités
    ├── Gestion des ADMIN et USER
    ├── Création/Modification/Suppression tous utilisateurs
    └── Accès aux statistiques globales
    
ROLE_ADMIN (Administrateur)  
    ├── Gestion limitée aux utilisateurs ROLE_USER
    ├── Création/Modification des clients uniquement
    ├── Accès aux rapports d'activité
    └── Impossibilité de gérer d'autres admins
    
ROLE_USER (Client)
    ├── Accès à l'espace client personnel
    ├── Gestion de son profil avec photo
    ├── Système de rating/avis
    └── Aucun accès aux fonctions admin
```

### Routes et Accès par Rôle

| Route | SUPER_ADMIN | ADMIN | USER |
|-------|-------------|-------|------|
| `/admin` | ✅ | ✅ | ❌ |
| `/user` | ✅ | ✅ | ❌ |
| `/user/new` | ✅ | ✅ | ❌ |
| `/user/{id}/edit` | ✅ | ✅ (USER only) | ❌ |
| `/client/dashboard` | ❌ | ❌ | ✅ |
| `/client/rating` | ❌ | ❌ | ✅ |
| `/client/profile` | ❌ | ❌ | ✅ |
| `/user/profile` | ✅ | ✅ | ❌ |

## 📱 Interface Utilisateur et UX

### Design Système
- **CSS Grid et Flexbox** pour layouts responsives
- **Gradient backgrounds** pour modernité visuelle
- **Cards avec shadows** pour séparation du contenu
- **Animations CSS** pour interactions fluides
- **Font Awesome** pour iconographie cohérente

### Responsive Design
```css
/* Mobile First Approach */
@media (min-width: 768px) { /* Tablet */ }
@media (min-width: 1024px) { /* Desktop */ }
@media (min-width: 1200px) { /* Large Desktop */ }
```

### Fonctionnalités JavaScript
- **Upload preview** en temps réel
- **Validation côté client** avec fallbacks
- **Confirmation dialogs** pour actions critiques
- **AJAX submissions** pour fluidité
- **Dynamic UI updates** sans rechargement

## 🔧 Commandes Utiles

### Développement
```bash
# Nettoyage et optimisation
php bin/console cache:clear
php bin/console cache:warm

# Debug et inspection
php bin/console debug:router
php bin/console debug:config security
php bin/console debug:config doctrine

# Base de données
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force

# Génération de code
php bin/console make:controller
php bin/console make:entity
php bin/console make:form
php bin/console make:migration
```

### Production
```bash
# Optimisation pour production
composer install --no-dev --optimize-autoloader
php bin/console cache:clear --env=prod
php bin/console cache:warm --env=prod

# Assets (si Webpack Encore)
npm run build

# Permissions production
chown -R www-data:www-data var/ public/uploads/
chmod -R 755 var/ public/uploads/
```

### Base de Données
```bash
# Backup base de données
mysqldump -u username -p ratrappage_symfony > backup.sql

# Restauration
mysql -u username -p ratrappage_symfony < backup.sql

# Requêtes utiles
php bin/console doctrine:query:sql "SELECT COUNT(*) as total_users FROM user"
php bin/console doctrine:query:sql "SELECT COUNT(*) as total_ratings FROM rating"
php bin/console doctrine:query:sql "SELECT roles, COUNT(*) as count FROM user GROUP BY roles"
```

## 🐛 Dépannage & Solutions

### Problèmes Courants

#### 1. Erreurs de Migration
```bash
# Solution 1: Forcer la mise à jour
php bin/console doctrine:schema:update --force

# Solution 2: Reset complet
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

#### 2. Problèmes de Sessions/Déconnexions
```bash
# Vider le cache de session
rm -rf var/cache/dev/sessions/*
php bin/console cache:clear

# Vérifier la configuration session
php bin/console debug:config framework session
```

#### 3. Erreurs d'Upload
```bash
# Vérifier permissions
ls -la public/uploads/
chmod -R 775 public/uploads/

# Vérifier configuration PHP
php -m | grep gd  # Extension GD pour images
php -i | grep upload_max_filesize  # Taille max upload
```

#### 4. Problèmes SMTP/Email
```bash
# Test configuration mailer
php bin/console debug:config mailer

# Test envoi (créer commande de test)
php bin/console app:test-email admin@test.com
```

### Messages d'Erreur Fréquents

#### "Access Denied"
- Vérifier les rôles utilisateur dans la base
- Contrôler les règles `access_control` dans `security.yaml`
- Valider les redirections dans `AppAuthenticator`

#### "Unable to guess the mime type"
- Installer l'extension PHP `fileinfo`
- Vérifier que l'extension `gd` est active
- Contrôler les permissions du dossier d'upload

#### "Class not found"
```bash
# Regénérer l'autoloader
composer dump-autoload

# Vider le cache
php bin/console cache:clear
```

## 📊 Monitoring & Analytics

### Logs Importants
```bash
# Logs d'erreurs
tail -f var/log/dev.log

# Logs de sécurité  
grep "authentication" var/log/dev.log

# Logs d'upload
grep "FileException" var/log/dev.log
```

### Métriques à Surveiller
- Nombre d'utilisateurs actifs
- Taux de vérification email  
- Nombre d'avis par utilisateur
- Temps de réponse des pages
- Erreurs 500 et exceptions

## 🚀 Déploiement Production

### Checklist Pré-Déploiement
- [ ] Configuration `.env.prod` avec variables sécurisées
- [ ] Optimisation Composer (`--no-dev --optimize-autoloader`)
- [ ] Compilation des assets CSS/JS
- [ ] Configuration HTTPS et certificats SSL
- [ ] Backup de la base de données
- [ ] Tests de charge et performance
- [ ] Configuration des logs rotatifs
- [ ] Mise en place monitoring (New Relic, Sentry)

### Configuration Apache/Nginx
```apache
# Apache VirtualHost
<VirtualHost *:443>
    ServerName yourdomain.com
    DocumentRoot /var/www/project/public
    
    <Directory /var/www/project/public>
        AllowOverride All
        Require all granted
        FallbackResource /index.php
    </Directory>
    
    # SSL Configuration
    SSLEngine on
    SSLCertificateFile /path/to/certificate.crt
    SSLCertificateKeyFile /path/to/private.key
</VirtualHost>
```

## 📚 Documentation Technique

### APIs et Intégrations
- [Symfony Security](https://symfony.com/doc/current/security.html) - Authentication & Authorization
- [Doctrine ORM](https://www.doctrine-project.org/projects/orm.html) - Database Management
- [Twig Templates](https://twig.symfony.com/doc/3.x/) - Template Engine
- [Twilio API](https://www.twilio.com/docs/sms) - SMS Integration
- [Google reCAPTCHA](https://developers.google.com/recaptcha/docs/display) - Bot Protection

### Standards et Conventions
- **PSR-4** pour l'autoloading des classes
- **PSR-12** pour le style de code PHP
- **Symfony Coding Standards** pour la structure
- **Semantic Versioning** pour les releases
- **Git Flow** pour la gestion des branches

### Tests et Qualité
```bash
# Tests unitaires (si configurés)
php bin/phpunit

# Analyse statique avec PHPStan
./vendor/bin/phpstan analyse src/

# Code style avec PHP-CS-Fixer
./vendor/bin/php-cs-fixer fix src/
```

## 👨‍💻 Contributeurs & Support

**Développé par** : Mohamed Rayen Ben Dhia  
**Email** : rayen@example.com  
**GitHub** : [@mohamedrayenbendhia](https://github.com/mohamedrayenbendhia)

### Contribution
1. Fork le projet
2. Créer une branche feature (`git checkout -b feature/amazing-feature`)
3. Commit les changements (`git commit -m 'Add amazing feature'`)
4. Push vers la branche (`git push origin feature/amazing-feature`)
5. Ouvrir une Pull Request

### Licence
Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

---

## 🎯 Roadmap Future

### Version 2.0 (Planifiée)
- [ ] **API REST** pour applications mobiles
- [ ] **WebSockets** pour notifications temps réel
- [ ] **Multi-tenancy** pour entreprises
- [ ] **Advanced Analytics** avec graphiques
- [ ] **Export PDF** des rapports
- [ ] **Tests automatisés** complets
- [ ] **Docker** containerization
- [ ] **CI/CD** avec GitHub Actions

### Améliorations Techniques
- [ ] **Cache Redis** pour performances
- [ ] **Queue System** pour emails
- [ ] **Image optimization** automatique
- [ ] **Rate limiting** anti-spam
- [ ] **Audit trail** des modifications
- [ ] **Backup automatique** base de données

---

*Ce projet démontre l'implémentation complète d'un système d'authentification, d'autorisation et de rating avec Symfony 6, incluant toutes les bonnes pratiques de sécurité et d'architecture moderne.*
