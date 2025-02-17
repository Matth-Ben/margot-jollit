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
		console.log(id)
		// debugger
		const settings = JSON.parse( textarea.getAttribute( 'data-settings' ) )

		if ( settings ) {
			textarea.setAttribute( 'id', id )
			wp.editor.initialize( id, settings )

			////
			// debugger
			// wp.editor.windowManager.oldOpen = wp.editor.windowManager.open;  // save for later
			// wp.editor.windowManager.open = function (t, r) {    // replace with our own function
			// 	var modal = this.oldOpen.apply(this, [t, r]);  // call original

			// 	if (t.title === "Insert/Edit Link") {
			// 		$('.tox-dialog__footer-end').append(
			// 			'<button title="Custom button" type="button" data-alloy-tabstop="true" tabindex="-1" class="tox-button" id="custom_button">Custom button</button>'
			// 		);

			// 		$('#custom_button').on('click', function () {
			// 			//Replace this with your custom function
			// 			console.log('Running custom function')
			// 		});
			// 	}

			// 	return modal; // Template plugin is dependent on this return value
			// }
			////
		}

		/**
		 * $field is a jQuery object wrapping field elements in the editor.
		 */
		// console.log( 'FIELD_NAME field initialized', $field );
	}

	if( typeof acf.add_action !== 'undefined' ) {
		/**
		 * Run initialize_field when existing fields of this type load,
		 * or when new fields are appended via repeaters or similar.
		 */
		acf.add_action( 'ready_field/type=advanced_wysiwyg', field => {
			initialize_field( field )
		} );
		// acf.add_action( 'append_field/type=advanced_wysiwyg', field => {
		// 	const textarea = field[0].querySelector( 'textarea' )
		// 	const name = textarea.getAttribute( 'name' )
		// 	const data_settings = textarea.getAttribute( 'data-settings' )
		// 	const content = textarea.value

		// 	field[0].querySelector('.acf-input').innerHTML = `
		// 		<textarea name="${name}" data-settings='${data_settings}'>${content}</textarea>
		// 	`
			
		// 	initialize_field( field )
		// } );

		acf.add_action('append_field/type=advanced_wysiwyg', field => {
			console.log('append')
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
