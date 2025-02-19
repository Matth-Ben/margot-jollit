<?php
if ( ! defined('ABSPATH') ) {
    exit;
}

class DankaCustomAuth {

    const ROLE_NAME           = 'client';
    const META_KEY_CONFIRMED  = 'custom_auth_confirmed';
    const META_KEY_ACTIVATION = 'custom_auth_activation_code';

    // Propriétés statiques pour stocker les erreurs
    protected static $register_errors;
    protected static $login_errors;

    public static function init() {
        // 1) Créer/MàJ le rôle "Client" lors de l'activation du plugin/thème
        register_activation_hook( __FILE__, array( __CLASS__, 'on_activation' ) );

        // 2) Bloquer l’accès wp-admin pour ceux qui n’ont pas manage_options
        add_action( 'admin_init', array( __CLASS__, 'block_admin_for_non_admins' ) );

        // 3) Déclarer nos shortcodes
        add_shortcode( 'custom_register_form', array( __CLASS__, 'render_register_form' ) );
        add_shortcode( 'custom_login_form',    array( __CLASS__, 'render_login_form' ) );

        // 4) Gérer la soumission (inscription + login)
        add_action( 'init', array( __CLASS__, 'handle_form_submission' ) );

        // 5) Vérifier l’URL de confirmation (double opt-in)
        add_action( 'init', array( __CLASS__, 'check_confirmation_link' ) );

        // 6) Ajouter un lien “Valider” dans la liste des utilisateurs (Comptes)
        add_filter( 'user_row_actions', array( __CLASS__, 'maybe_add_validate_link' ), 10, 2 );

        // 7) Traiter l’action de validation quand on clique sur ce lien
        add_action( 'admin_action_validate_user', array( __CLASS__, 'handle_validate_user_action' ) );

        // 8) Afficher un message de confirmation dans la liste des utilisateurs
        add_action( 'admin_notices', array( __CLASS__, 'maybe_show_validation_notice' ) );

        // Initialiser les WP_Error
        self::$register_errors = new WP_Error();
        self::$login_errors    = new WP_Error();
    }

    /**
     * Crée le rôle "Client" si besoin
     */
    public static function on_activation() {
        add_role(
            self::ROLE_NAME,
            'Client',
            array(
                'read' => true,
            )
        );
    }

    /**
     * Bloque /wp-admin/ aux non-admins (tout rôle sans manage_options)
     */
    public static function block_admin_for_non_admins() {
        if ( is_admin() && ! current_user_can( 'manage_options' ) && ! wp_doing_ajax() ) {
            wp_redirect( home_url() );
            exit;
        }
    }

    /**
     * Shortcode : Formulaire d’inscription
     */
    public static function render_register_form() {
        // Si déjà connecté, ne pas afficher le formulaire
        if ( is_user_logged_in() ) {
            return '<p>Vous êtes déjà connecté.</p>';
        }

        // On capture le HTML
        ob_start();

        // --- 1) Afficher les erreurs éventuelles ---
        if ( ! empty( self::$register_errors ) && is_wp_error( self::$register_errors ) ) {
            foreach ( self::$register_errors->get_error_messages() as $error ) {
                echo '<div class="custom-error" style="color:red;">' . esc_html( $error ) . '</div>';
            }
        }
        ?>

        <!-- 2) Le formulaire d'inscription -->
        <form method="post" class="custom-register-form">
            <p>
                <label for="reg_email">Email *</label><br>
                <input type="email" name="reg_email" id="reg_email" required>
            </p>
            <p>
                <label for="reg_password">Mot de passe *</label><br>
                <input type="password" name="reg_password" id="reg_password" required>
            </p>
            <?php wp_nonce_field( 'custom_register_action', 'custom_register_nonce' ); ?>
            <p>
                <input type="submit" name="custom_register_submit" value="Créer mon compte">
            </p>
        </form>
        <?php

        return ob_get_clean();
    }

    /**
     * Shortcode : Formulaire de connexion
     */
    public static function render_login_form() {
        // Si déjà connecté, ne pas afficher le formulaire
        if ( is_user_logged_in() ) {
            return '<p>Vous êtes déjà connecté.</p>';
        }

        ob_start();

        // --- 1) Afficher les erreurs éventuelles ---
        if ( ! empty( self::$login_errors ) && is_wp_error( self::$login_errors ) ) {
            foreach ( self::$login_errors->get_error_messages() as $error ) {
                echo '<div class="custom-error" style="color:red;">' . esc_html( $error ) . '</div>';
            }
        }
        ?>

        <!-- 2) Le formulaire de connexion -->
        <form method="post" class="custom-login-form">
            <p>
                <label for="log_username">Nom d’utilisateur ou Email *</label><br>
                <input type="text" name="log_username" id="log_username" required>
            </p>
            <p>
                <label for="log_password">Mot de passe *</label><br>
                <input type="password" name="log_password" id="log_password" required>
            </p>
            <?php wp_nonce_field( 'custom_login_action', 'custom_login_nonce' ); ?>
            <p>
                <input type="submit" name="custom_login_submit" value="Se connecter">
            </p>
        </form>
        <?php

        return ob_get_clean();
    }

    /**
     * Gérer la soumission de l’un ou l’autre formulaire
     */
    public static function handle_form_submission() {
        // Inscription
        if ( isset( $_POST['custom_register_submit'] ) ) {
            self::process_registration();
        }
        // Connexion
        if ( isset( $_POST['custom_login_submit'] ) ) {
            self::process_login();
        }
    }

    /**
     * Traiter la création de compte
     */
    protected static function process_registration() {
        // Vérifier nonce
        if ( ! isset($_POST['custom_register_nonce']) || 
             ! wp_verify_nonce( $_POST['custom_register_nonce'], 'custom_register_action' ) ) {
            // Par sécurité, on peut soit stocker l'erreur dans self::$register_errors, soit wp_die
            self::$register_errors->add('invalid_nonce', 'Jeton de sécurité invalide. Veuillez réessayer.');
            return;
        }

        // Récupérer & nettoyer
        $email    = ( ! empty($_POST['reg_email']) )    ? sanitize_email( $_POST['reg_email'] )    : '';
        $password = ( ! empty($_POST['reg_password']) ) ? sanitize_text_field( $_POST['reg_password'] ) : '';

        // Vérifications basiques
        if ( empty($email) || empty($password) ) {
            self::$register_errors->add('missing_fields', 'Veuillez remplir tous les champs.');
            return;
        }
        if ( ! is_email($email) ) {
            self::$register_errors->add('invalid_email', 'Adresse email invalide.');
            return;
        }
        if ( email_exists($email) ) {
            self::$register_errors->add('email_exists', 'Un compte existe déjà avec cet email.');
            return;
        }

        // --- Supprimer la notion de "username" côté front ---
        // On va auto-générer un username depuis l'email
        // Exemple : partie avant le '@'
        $username = sanitize_user( current( explode('@', $email ) ), true );

        // Si ce "username" existe déjà, on ajoute un suffixe aléatoire
        if ( username_exists($username) ) {
            $username .= '_' . wp_generate_password(4, false);
        }

        // Créer l'utilisateur (rôle Client)
        $user_id = wp_create_user( $username, $password, $email );
        if ( is_wp_error($user_id) ) {
            self::$register_errors->add('create_user_error', 'Erreur lors de la création du compte : ' . $user_id->get_error_message());
            return;
        }

        // Assigner le rôle
        $user = new WP_User($user_id);
        $user->set_role( self::ROLE_NAME );

        // Marquer le compte comme non confirmé
        update_user_meta( $user_id, self::META_KEY_CONFIRMED, 0 );

        // Envoyer l’email de confirmation (avec lien)
        self::send_confirmation_email( $user_id );

        // On stocke un message de succès (ou on pourrait rediriger)
        self::$register_errors->add('register_success', 'Votre compte a été créé ! Un email de confirmation vous a été envoyé pour valider votre compte.');
    }

    /**
     * Envoyer l’email de confirmation (avec lien)
     */
    protected static function send_confirmation_email( $user_id ) {
        $user = get_userdata( $user_id );
        $code = md5( $user->user_login . time() );
        update_user_meta( $user_id, self::META_KEY_ACTIVATION, $code );

        // Construire l’URL de confirmation
        $confirm_url = add_query_arg( array(
            'action' => 'confirm_user',
            'uid'    => $user_id,
            'code'   => $code,
        ), home_url('/') );

        // On récupère le sujet & contenu configurés en admin (ou valeurs par défaut)
        $subject_template = get_option( 'danka_auth_mail_confirm_subject', 'Confirmez votre compte' );
        $body_template    = get_option( 'danka_auth_mail_confirm_body', "Bonjour {username},\nMerci pour votre inscription. Cliquez ici : {confirm_link}" );

        // Remplacer les placeholders
        $subject = str_replace( array('{username}', '{email}'), array($user->user_login, $user->user_email), $subject_template );
        $body    = str_replace(
            array('{username}', '{email}', '{confirm_link}'),
            array($user->user_login, $user->user_email, $confirm_url),
            $body_template
        );

        // Envoyer
        wp_mail( $user->user_email, $subject, $body );
    }

    /**
     * Traiter la connexion
     */
    protected static function process_login() {
        // Vérifier nonce
        if ( ! isset($_POST['custom_login_nonce']) ||
             ! wp_verify_nonce( $_POST['custom_login_nonce'], 'custom_login_action' ) ) {
            self::$login_errors->add('invalid_nonce', 'Jeton de sécurité invalide. Veuillez réessayer.');
            return;
        }

        $username_or_email = sanitize_text_field( $_POST['log_username'] );
        $password          = $_POST['log_password'];

        if ( empty($username_or_email) || empty($password) ) {
            self::$login_errors->add('empty_login_fields', 'Veuillez saisir votre identifiant et votre mot de passe.');
            return;
        }

        // Trouver l'utilisateur (par username ou email)
        $user = get_user_by( 'login', $username_or_email );
        if ( ! $user ) {
            $user = get_user_by( 'email', $username_or_email );
        }
        if ( ! $user ) {
            self::$login_errors->add('no_user_found', 'Identifiants invalides.');
            return;
        }

        // Vérifier si le compte est confirmé
        $confirmed = get_user_meta( $user->ID, self::META_KEY_CONFIRMED, true );
        if ( '1' !== (string)$confirmed ) {
            self::$login_errors->add('account_not_confirmed', 'Votre compte n’est pas encore activé. Vérifiez vos emails.');
            return;
        }

        // Tenter la connexion
        $creds = array(
            'user_login'    => $user->user_login,
            'user_password' => $password,
            'remember'      => true,
        );
        $auth = wp_signon( $creds, false );
        if ( is_wp_error($auth) ) {
            self::$login_errors->add('login_failed', 'Erreur de connexion : ' . $auth->get_error_message());
            return;
        }

        // Succès, on peut rediriger ou afficher un message
        // Ici on redirige vers la home (ou /mon-compte, etc.)
        wp_redirect( home_url('/') );
        exit;
    }

    /**
     * Vérifier si on a ?action=confirm_user&uid=X&code=Y
     */
    public static function check_confirmation_link() {
        if ( isset($_GET['action']) && $_GET['action'] === 'confirm_user' ) {
            $uid  = isset($_GET['uid'])  ? absint($_GET['uid']) : 0;
            $code = isset($_GET['code']) ? sanitize_text_field($_GET['code']) : '';

            if ( $uid && $code ) {
                $saved_code = get_user_meta( $uid, self::META_KEY_ACTIVATION, true );
                if ( $saved_code && $saved_code === $code ) {
                    // Confirmer le compte
                    update_user_meta( $uid, self::META_KEY_CONFIRMED, 1 );
                    delete_user_meta( $uid, self::META_KEY_ACTIVATION );

                    // Envoyer l’email de bienvenue
                    self::send_welcome_email( $uid );

                    wp_die( 'Votre compte est maintenant activé ! Vous pouvez vous connecter.', 'Confirmation' );
                } else {
                    wp_die( 'Lien de confirmation invalide ou expiré.' );
                }
            } else {
                wp_die( 'Paramètres manquants pour confirmer votre compte.' );
            }
        }
    }

    /**
     * Envoyer un email de bienvenue après activation
     */
    protected static function send_welcome_email( $user_id ) {
        $user = get_userdata( $user_id );

        $subject_template = get_option( 'danka_auth_mail_welcome_subject', 'Bienvenue !' );
        $body_template    = get_option( 'danka_auth_mail_welcome_body', "Bonjour {username},\nVotre compte est actif !" );

        $subject = str_replace( array('{username}', '{email}'), array($user->user_login, $user->user_email), $subject_template );
        $body    = str_replace(
            array('{username}', '{email}'),
            array($user->user_login, $user->user_email),
            $body_template
        );

        wp_mail( $user->user_email, $subject, $body );
    }


    /* ==================================================
     * AJOUT : Valider un compte depuis "Comptes" (admin)
     * ================================================== */

    /**
     * Ajoute un lien "Valider" dans user_row_actions
     * si user_meta custom_auth_confirmed != 1
     */
    public static function maybe_add_validate_link( $actions, $user ) {
        // Seuls les admins (manage_options) peuvent valider
        if ( ! current_user_can( 'manage_options' ) ) {
            return $actions;
        }
        // Vérifier si déjà confirmé
        $confirmed = get_user_meta( $user->ID, self::META_KEY_CONFIRMED, true );
        if ( '1' === (string) $confirmed ) {
            return $actions; // déjà validé
        }

        // Créer un nonce
        $nonce = wp_create_nonce( 'validate_user_' . $user->ID );
        // Lien vers admin.php?action=validate_user&user_id=XXX&_wpnonce=YYY
        $url = add_query_arg(
            array(
                'action'   => 'validate_user',
                'user_id'  => $user->ID,
                '_wpnonce' => $nonce,
            ),
            admin_url( 'admin.php' )
        );

        $actions['validate_account'] = sprintf(
            '<a href="%s" style="color:green; font-weight:bold;">Valider</a>',
            esc_url( $url )
        );

        return $actions;
    }

    /**
     * Traiter l’action "validate_user" => ?action=validate_user&user_id=...
     */
    public static function handle_validate_user_action() {
        // Vérifier qu'on est admin
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Vous n’avez pas les permissions requises.' );
        }

        $user_id = isset( $_GET['user_id'] ) ? absint( $_GET['user_id'] ) : 0;
        $nonce   = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : '';

        if ( ! $user_id || ! $nonce ) {
            wp_die( 'Informations manquantes.' );
        }

        // Vérifier le nonce
        if ( ! wp_verify_nonce( $nonce, 'validate_user_' . $user_id ) ) {
            wp_die( 'Nonce invalide.' );
        }

        // Mettre à jour la meta => validé
        update_user_meta( $user_id, self::META_KEY_CONFIRMED, 1 );

        // (Optionnel) Envoyer un mail de bienvenue
        // self::send_welcome_email( $user_id );

        // Rediriger vers la liste des comptes, avec un param
        wp_redirect( admin_url( 'users.php?validated=1' ) );
        exit;
    }

    /**
     * Afficher un message "Compte validé" en haut de la liste (si ?validated=1)
     */
    public static function maybe_show_validation_notice() {
        global $pagenow;
        if ( $pagenow === 'users.php' && isset( $_GET['validated'] ) ) {
            echo '<div class="notice notice-success is-dismissible"><p>Le compte a été validé avec succès.</p></div>';
        }
    }
}

// Initialiser la classe
DankaCustomAuth::init();