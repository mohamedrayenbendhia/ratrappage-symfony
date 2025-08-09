-- Script pour créer le SUPER_ADMIN
-- Supprimer l'utilisateur s'il existe déjà
DELETE FROM user WHERE email = 'superadmin@admin.com';

-- Insérer le nouveau SUPER_ADMIN
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
