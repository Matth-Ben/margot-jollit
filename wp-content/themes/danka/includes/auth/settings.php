<?php
if ( ! defined('ABSPATH') ) {
    exit;
}

/**
 * Classe pour gérer la page de réglages "Auth"
 */
class DankaAuthSettings {

    public static function init() {
        // 1) Ajout du sous-menu (enfant de DANKA_SETTINGS_SLUG)
        add_action( 'admin_menu', array( __CLASS__, 'add_subpage' ) );

        // 2) Enregistre nos champs (email subjects/body) dans la page "danka-auth-settings"
        add_action( 'admin_init', array( __CLASS__, 'register_fields' ) );
    }

    /**
     * 1) Ajouter un sous-menu "Auth"
     */
    public static function add_subpage() {
        add_submenu_page(
            DANKA_SETTINGS_SLUG,             // parent slug
            'Auth Settings',                 // page title
            'Auth',                          // menu title
            'manage_options',                // capability
            'danka-auth-settings',           // menu slug
            array( __CLASS__, 'render_page' )// callback
        );
    }

    /**
     * 2) Déclarer les champs (subject/body) pour l'email de confirmation & de bienvenue
     */
    public static function register_fields() {

        // Section unique
        add_settings_section(
            'danka_auth_section',
            'Paramètres d’authentification',
            '',
            'danka-auth-settings'
        );

        // On enregistre 4 options :
        register_setting( 'danka-auth-settings', 'danka_auth_mail_confirm_subject' );
        register_setting( 'danka-auth-settings', 'danka_auth_mail_confirm_body' );
        register_setting( 'danka-auth-settings', 'danka_auth_mail_welcome_subject' );
        register_setting( 'danka-auth-settings', 'danka_auth_mail_welcome_body' );

        // Email Confirmation - Subject
        add_settings_field(
            'danka_auth_mail_confirm_subject',
            'Sujet de l’email de confirmation',
            array( __CLASS__, 'render_text_field' ),
            'danka-auth-settings',
            'danka_auth_section',
            array(
                'option_name' => 'danka_auth_mail_confirm_subject',
                'placeholder' => 'Par ex : Confirmez votre compte',
            )
        );

        // Email Confirmation - Body
        add_settings_field(
            'danka_auth_mail_confirm_body',
            'Contenu de l’email de confirmation',
            array( __CLASS__, 'render_textarea' ),
            'danka-auth-settings',
            'danka_auth_section',
            array(
                'option_name' => 'danka_auth_mail_confirm_body',
                'placeholder' => "Bonjour {username},\nMerci pour votre inscription. Pour valider votre compte, cliquez ici : {confirm_link}"
            )
        );

        // Email Welcome - Subject
        add_settings_field(
            'danka_auth_mail_welcome_subject',
            'Sujet de l’email de bienvenue',
            array( __CLASS__, 'render_text_field' ),
            'danka-auth-settings',
            'danka_auth_section',
            array(
                'option_name' => 'danka_auth_mail_welcome_subject',
                'placeholder' => 'Par ex : Bienvenue sur notre site !',
            )
        );

        // Email Welcome - Body
        add_settings_field(
            'danka_auth_mail_welcome_body',
            'Contenu de l’email de bienvenue',
            array( __CLASS__, 'render_textarea' ),
            'danka-auth-settings',
            'danka_auth_section',
            array(
                'option_name' => 'danka_auth_mail_welcome_body',
                'placeholder' => "Bonjour {username},\nVotre compte est désormais actif !"
            )
        );
    }

    /**
     * Champ texte (subject)
     */
    public static function render_text_field( $args ) {
        $option_name = $args['option_name'];
        $value = get_option( $option_name, '' );
        $placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : '';
        printf(
            '<input type="text" name="%1$s" value="%2$s" placeholder="%3$s" style="width: 100%%;" />',
            esc_attr( $option_name ),
            esc_attr( $value ),
            esc_attr( $placeholder )
        );
    }

    /**
     * Champ textarea (body)
     */
    public static function render_textarea( $args ) {
        $option_name = $args['option_name'];
        $value = get_option( $option_name, '' );
        $placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : '';
        printf(
            '<textarea name="%1$s" rows="6" style="width: 100%%;" placeholder="%2$s">%3$s</textarea>',
            esc_attr( $option_name ),
            esc_attr( $placeholder ),
            esc_textarea( $value )
        );
    }

    /**
     * Affichage de la page de sous-menu
     */
    public static function render_page() {
        ?>
        <div class="wrap">
            <h1>Auth - Paramètres</h1>
            <p>
                Utilisez les shortcodes suivants sur vos pages :
            </p>
            <ul style="list-style:disc; margin-left: 20px;">
                <li><strong>[custom_register_form]</strong> : Formulaire d’inscription</li>
                <li><strong>[custom_login_form]</strong> : Formulaire de connexion</li>
            </ul>
            <hr>
            <form method="post" action="options.php">
                <?php
                    settings_fields( 'danka-auth-settings' );
                    do_settings_sections( 'danka-auth-settings' );
                    submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}

// Initialise
DankaAuthSettings::init();