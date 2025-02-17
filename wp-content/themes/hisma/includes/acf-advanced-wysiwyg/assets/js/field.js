const uniqid = ( prefix = "", random = false ) => {
	const sec = Date.now() * 1000 + Math.random() * 1000
	const id = sec.toString(16).replace(/\./g, "").padEnd(14, "0")
	return `${prefix}${id}${random ? `.${Math.trunc(Math.random() * 100000000)}`:""}`
}

/**
 * Included when FIELD_NAME fields are rendered for editing by publishers.
 */
( function( $ ) {

	function initialize_field( $field ) {
		const textarea = $field[0].querySelector( 'textarea' )
		const id = uniqid()
		const settings = JSON.parse( textarea.getAttribute( 'data-settings' ) )

		if ( settings ) {
			textarea.setAttribute( 'id', id )
			wp.editor.initialize( id, settings )
		}
	}

	if( typeof acf.add_action !== 'undefined' ) {
		/**
		 * Run initialize_field when existing fields of this type load,
		 * or when new fields are appended via repeaters or similar.
		 */
		acf.add_action( 'ready_field/type=advanced_wysiwyg', initialize_field );
		acf.add_action('append_field/type=advanced_wysiwyg', field => {
			// Sélectionne le champ textarea correctement via ACF
			const textarea = field[0].querySelector('textarea');
			const name = textarea.getAttribute('name');
			const data_settings = textarea.getAttribute('data-settings');
			const content = textarea.value;
		
			// Génère un ID unique pour ce champ
			const uniqueID = `wysiwyg-${Date.now()}`;
		
			// Remplace le contenu du champ WYSIWYG personnalisé
			field[0].querySelector('.acf-input').innerHTML = `
				<textarea id="${uniqueID}" name="${name}" data-settings='${data_settings}'>${content}</textarea>
			`;
		
			// Vérifie s'il y a déjà une instance existante et la supprime si nécessaire
			if (tinymce.get(uniqueID)) {
				tinymce.remove(`#${uniqueID}`);
			}
		
			// Initialiser Field
			initialize_field( field )
		});
		
	}
} )( jQuery );
