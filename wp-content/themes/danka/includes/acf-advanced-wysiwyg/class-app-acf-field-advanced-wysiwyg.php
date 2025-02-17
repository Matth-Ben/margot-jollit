<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class app_acf_field_advanced_wysiwyg extends \acf_field {

	/**
	 * Controls field type visibilty in REST requests.
	 *
	 * @var bool
	 */
	public $show_in_rest = true;

	/**
	 * Environment values relating to the theme or plugin.
	 *
	 * @var array $env Plugin or theme context such as 'url' and 'version'.
	 */
	private $env;

	/**
	 * Constructor.
	 */
	public function __construct() {
		/**
		 * Field type reference used in PHP and JS code.
		 *
		 * No spaces. Underscores allowed.
		 */
		$this->name = 'advanced_wysiwyg';

		/**
		 * Field type label.
		 *
		 * For public-facing UI. May contain spaces.
		 */
		$this->label = 'Éditeur WYSIWYG (avancé)';

		/**
		 * The category the field appears within in the field type picker.
		 */
		$this->category = 'content'; // basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME

		/**
		 * Defaults for your custom user-facing settings for this field type.
		 */
		$this->defaults = array(
			'has_h1' => 1,
			'has_h2' => 1,
			'has_h3' => 1,
			'has_h4' => 1,
			'has_h5' => 1,
			'has_h6' => 1,
			'has_p' => 1,
			'has_button' => 1,
			'has_bold' => 1,
			'has_italic' => 1,
			'has_underline' => 1,
			'has_bullist' => 1,
			'has_numlist' => 1,
			'has_link' => 1,
			'has_forecolor' => 0,
		);

		$this->env = array(
			'url'     => site_url( str_replace( ABSPATH, '', __DIR__ ) ), // URL to the acf-advanced-wysiwyg directory.
			'version' => '1.0', // Replace this with your theme or plugin version constant.
		);

		$this->formats = array(
			'h1' => 'Titre 1',
			'h2' => 'Titre 2',
			'h3' => 'Titre 3',
			'h4' => 'Titre 4',
			'h5' => 'Titre 5',
			'h6' => 'Titre 6',
			'p' => 'Paragraphe',
			'button' => 'Bouton',
		);

		$this->buttons = array(
			'bold' => 'Gras',
			'italic' => 'Italique',
			'underline' => 'Souligné',
			'bullist' => 'Liste à puces',
			'numlist' => 'Liste numérotée',
			'link' => 'Lien',
			'forecolor' => 'Couleurs de texte',
		);

		$this->colors = array();

		$data_file_path = get_template_directory() . '/data.json';

		if ( file_exists( $data_file_path ) ) {
			$data = json_decode( file_get_contents( $data_file_path ), true );
		}

		parent::__construct();
	}

	/**
	 * Settings to display when users configure a field of this type.
	 *
	 * These settings appear on the ACF “Edit Field Group” admin page when
	 * setting up the field.
	 *
	 * @param array $field
	 * @return void
	 */
	public function render_field_settings( $field ) {

		foreach ( $this->formats as $key => $name ) {

			acf_render_field_setting(
				$field,
				array(
					'label' => $name,
					'type' => 'true_false',
					'name' => 'has_' . $key,
					'ui' => 1,
				)
			);
		}

		echo '<br>';

		foreach( $this->buttons as $key => $name ) {

			acf_render_field_setting(
				$field,
				array(
					'label' => $name,
					'type' => 'true_false',
					'name' => 'has_' . $key,
					'ui' => 1,
				)
			);
		}

		echo '<br>';

		acf_render_field_setting(
			$field,
			array(
				'label' => 'Couleurs',
				'type' => 'textarea',
				'name' => 'style_colors',
				'instructions' => __( 'Enter each choice on a new line.', 'acf' ) . '<br />' . __( 'For more control, you may specify both a value and label like this:', 'acf' ) . '<br /><span class="acf-field-setting-example">' . __( 'red : Red', 'acf' ) . '</span>',
				'conditions' => array(
					'field' => 'has_forecolor',
					'operator' => '==',
					'value' => 1,
				),
			)
		);

		// To render field settings on other tabs in ACF 6.0+:
		// https://www.advancedcustomfields.com/resources/adding-custom-settings-fields/#moving-field-setting
	}

	/**
	 * Renders the field settings used in the "Presentation" tab.
	 *
	 * @since 6.0
	 *
	 * @param array $field The field settings array.
	 * @return void
	 */
	function render_field_presentation_settings( $field ) {

		foreach ( $this->formats as $format ) {
			//
		}
	}

	/**
	 * HTML content to show when a publisher edits the field on the edit screen.
	 *
	 * @param array $field The field settings and values.
	 * @return void
	 */
	public function render_field( $field ) {
		$default_plugins = array( "charmap", "colorpicker", "hr", "lists", "media", "paste", "tabfocus", "textcolor", "fullscreen", "wordpress", "wpautoresize", "wpeditimage", "wpemoji", "wpgallery", "wplink", "wpdialogs", "wptextpattern", "wpview" );
		array_splice($default_plugins, 1, 1);
		
		$toolbar = "undo,redo";
		$quicktags = "";
		$tinymce = array(
			'plugins' => $default_plugins,
			'toolbar2' => '',
			'toolbar3' => '',
			'custom_color' => '',
		);

		// style formats
		if ( $this->formats ) {
			$formats = array();
	
			foreach ( $this->formats as $key => $format ) {
	
				if ( isset( $field['has_' . $key] ) && $field['has_' . $key] ) {
					$formats[] = array(
						// 'name' => '',
						'title' => $format,
						'block' => $key,
						'classes' => false,
						'wrapper' => false,
					);
				}
			}
	
			if ( $formats ) {
				$tinymce['style_formats'] = $formats;
				$toolbar .= ',' . 'styleselect';
			}
		}

		// buttons
		if ( $this->buttons ) {
	
			foreach ( $this->buttons as $key => $button ) {
	
				if ( isset( $field['has_' . $key] ) && $field['has_' . $key] ) {
					
					if ( $key !== 'forecolor' ) {
						$toolbar .= ',' . $key;
						
						if ( $key == 'link' ) {
							$toolbar .= ',unlink';
						}
					}

					switch ( $key ) {
						case 'bold' :
							$quicktags .= ',strong';
							break;
						case 'italic' :
							$quicktags .= ',em';
							break;
						case 'link' :
							$quicktags .= ',link';
							break;
						case 'bullist' :
							$quicktags .= ',ul';
							break;
						case 'numlist' :
							$quicktags .= ',ol';
							break;
					}
				}
			}

			if ( str_contains( $quicktags, 'ul' ) || str_contains( $quicktags, 'ol' ) ) {
				$quicktags .= ',li';
			}
		}

		// colors
		if ( isset( $field['style_colors'] ) && $field['style_colors'] ) {
			$colors = str_replace( "\r", "", $field['style_colors'] );
			$colors = explode( "\n", $colors );

			if ( $colors ) {
				$textcolor_map = array();

				foreach ( $colors as $color ) {
					$color = str_replace( "#", "", $color );
					$textcolor_map[] = $color; // value
					$textcolor_map[] = $color; // name
				}

				if ( $textcolor_map ) {
					$toolbar .= ',forecolor';
					$tinymce['textcolor_rows'] = 1;
					$tinymce['textcolor_map'] = $textcolor_map;
				}
			}
		}

		$tinymce['toolbar1'] = $toolbar . ',removeformat';
		$tinymce['height'] = 300;
		$settings =   array(
			'wpautop' => true, // enable auto paragraph?
			'media_buttons' => false, // show media buttons?
			'textarea_name' => $field['name'], // id of the target textarea
			'textarea_rows' => get_option('default_post_edit_rows', 10), // This is equivalent to rows="" in HTML
			'tabindex' => '',
			'editor_css' => '', //  additional styles for Visual and Text editor,
			'editor_class' => '', // sdditional classes to be added to the editor
			'teeny' => false, // show minimal editor
			'dfw' => false, // replace the default fullscreen with DFW
			'tinymce' => $tinymce,
			'quicktags' => array(
				'buttons' => $quicktags
			)
		);

		echo "<textarea name='" . $field['name'] . "' data-settings='" . esc_attr( json_encode( $settings ) ) . "'>" . $field['value'] . "</textarea>";
	}

	/**
	 * Enqueues CSS and JavaScript needed by HTML in the render_field() method.
	 *
	 * Callback for admin_enqueue_script.
	 *
	 * @return void
	 */
	public function input_admin_enqueue_scripts() {
		$url = trailingslashit( $this->env['url'] );
		$version = $this->env['version'];

		wp_register_script(
			'advanced_wysiwyg',
			"{$url}assets/js/field.js",
			array( 'acf-input' ),
			$version
		);

		wp_enqueue_script( 'advanced_wysiwyg' );
	}
}
