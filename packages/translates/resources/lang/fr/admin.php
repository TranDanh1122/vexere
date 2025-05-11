<?php

return [
	// General Module
    'homepage' => 'Accueil',
	// Login Page
    'login'                     => [
        'desc'                  => 'Connexion',
        'user_or_email'         => "Nom d'utilisateur ou e-mail",
        'password'              => 'Mot de passe',
        'remember_me'           => 'Se souvenir de moi',
        'login'                 => 'Connexion',
        'forgot_password'       => 'Mot de passe oublié',
        'your_email'            => 'Votre e-mail ici',
        'comfirm'               => 'Confirmer',
        'close'                 => 'Fermer',
        'email_not_exist'       => "Votre e-mail n'existe pas dans le système !",
        'forgot_success'        => 'Veuillez changer le mot de passe en utilisant le lien que nous avons envoyé à votre e-mail !',

        'required_info'         => "Vous devez fournir suffisamment d'informations !",
        'required_name'         => "Nom d'utilisateur ou e-mail est requis !",
        'required_password'     => 'Mot de passe est requis !',
        'required_forgot_email' => 'E-mail est requis !',

        'login_success'         => 'Connexion réussie',
        'login_error'           => "Nom d'utilisateur ou mot de passe incorrect !",
        'login_status_permision'=> "Votre compte n'est pas actif !",
    ],

    'forgot_password'           => [
        'comfirm'               => 'Confirmer',
        'change_password'       => 'Changer le mot de passe',
        'password_new'          => 'Mot de passe',
        'password_comfirm'      => 'Confirmer le mot de passe',

        'required_info'         => "Vous devez fournir suffisamment d'informations !",
        'required_password'     => 'Mot de passe est requis !',
        'required_password_comfirm' => 'Confirmer le mot de passe est requis !',
        'required_equal'        => 'Confirmer le mot de passe ne correspond pas !',
        'required_strong'       => "Le mot de passe doit comporter des majuscules, des minuscules, des chiffres, des caractères spéciaux et être composé d'au moins 6 caractères !",
    ],

    // Header
    'account_info'              => 'Informations du compte',
    'change_password'           => 'Changer le mot de passe',
    'logout'                    => 'Déconnexion',
    'view_website'              => 'Voir le site web',
    'cache_clear'               => 'Effacer le cache',

    // Footer
    'footer'            => [
        'copyright'             => "Droits d'auteur appartenant à <a href='https://dreamteam.com' target='_blank>DreamTeam</a>, Équipe : <a href='javascript:;''>DreamTeamDevTeam<a>",
        'vesion'                => 'DreamTeamCore v2',
    ],

    // Dashboard Page
    'admin_system'              => 'Système d\'administration',
    'dashboard'         => [
        'dreamteam_intro'            => 'Système d\'administration de DreamTeam',
    ],

    // Phân quyền
    'role'              => [
        'name'                  => 'Rôle',
        'select_all'            => 'Tout cocher',
    ],
    'create_success' => 'Création réussie.',
    'update_success' => 'Mise à jour réussie.',
    // Kết quả Ajax
    // Thành công
    'delete_success' => 'Suppression réussie.',
    'restore_success' => 'Restauration réussie.',
    // Không tìm thấy dữ liệu
    'no_data_delete' => 'Aucune donnée à supprimer.',
    'no_data_restore' => 'Aucune donnée à restaurer.',
    'no_data_edit' => 'Aucune donnée à modifier.',
    'not_found_data' => 'Données non trouvées.',
    'has_found_data' => 'Données trouvées.',
    // Không có quyền hoặc lỗi
    'no_permission' => 'Autorisation refusée.',
    'ajax_error' => "Une erreur s'est produite lors de l'opération.",
    'ajax_fail' => "Une erreur s'est produite. Veuillez réessayer !",
    'ajax_error_edit' => "Vous n'avez pas modifié de données ou une erreur s'est produite lors de l'opération.",
    // Xóa cache
    'cache_clear_success' => 'Nettoyage du cache réussi.',
    'cache_clear_fail' => 'Échec du nettoyage du cache.',

    // Text chung
    'unknown' => 'Inconnu',
    'recore_origin' => "Origine de l'enregistrement",
    'no_select_category' => '-- Aucune catégorie sélectionnée --',
    'can_delete_users' => 'Impossible de supprimer ces utilisateurs !',

    // Hành động system_logs
    'create' => 'Créer',
    'update' => 'Mettre à jour',
    'quick_delete' => 'Suppression rapide',
    'quick_update' => 'Mise à jour rapide',
    'quick_restore' => 'Restauration rapide',

    // Lịch sử hệ thống (logs)
    'logs' => [
    'title' => 'Détail des logs',
    'info_title' => 'Info',
    'detail' => 'Détail',
    'name' => "Nom de l'administrateur",
    'ip' => 'Adresse IP',
    'action' => 'Action',
    'type' => 'Nom du module',
    'type_id' => 'ID du module',
    'time' => 'Heure',
    'field' => 'Champ modifié',
    'old' => 'Ancien',
    'new' => 'Nouveau',
    'data' => 'Données',
    ],

    'validate' => [
    'required_password' => '"Mot de passe" est requis !',
    'required_password_comfirm' => '"Confirmation du mot de passe" est requis !',
    'required_equal' => '"Confirmation du mot de passe" ne correspond pas !',
    ],
];