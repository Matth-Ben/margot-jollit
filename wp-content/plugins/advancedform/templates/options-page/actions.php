<?php
    $advancedforms = [];
    $query = new WP_Query([
        'post_type'     => 'af_form',
        'post_per_page' => -1
    ]);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $advancedforms[get_the_ID()] = get_the_title();
        }
    }

    wp_reset_postdata();
?>

<form method="post">
    <h3>Exporter les données</h3>

    <div id="export-data">
        <div class="advancedform-admin-field">
            <div class="advancedform-admin-label">
                <label for="export-data-advancedform">Formulaire</label>
            </div>
            <div class="advancedform-admin-input">
                <select name="advancedform" id="export-data-advancedform">
                    <?php
                        foreach ($advancedforms as $id => $title) {
                            echo "<option value='{$id}'>{$title}</option>";
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class="advancedform-admin-field">
            <div class="advancedform-admin-label">
                <label for="export-data-format">Format</label>
            </div>
            <div class="advancedform-admin-input">
                <select name="format" id="export-data-format">
                    <option value="json">json</option>
                    <option value="xlsx">xlsx (Excel)</option>
                </select>
            </div>
        </div>
        <div class="advancedform-admin-field container-button">
            <div class="advancedform-admin-label"></div>
            <div class="advancedform-admin-input">
                <button type="submit" class="button button-primary button-large">Télécharger</button>
            </div>
        </div>
    </div>
    <input type="hidden" name="action" value="download_data" />
</form>

<form method="post">
    <h3>Exporter un formulaire</h3>

    <div id="export-form">
        <div class="advancedform-admin-field">
            <div class="advancedform-admin-label">
                <label for="import-form-advancedform">Formulaire</label>
            </div>
            <div class="advancedform-admin-input">
                <select name="advancedform" id="import-form-advancedform">
                    <?php
                        foreach ($advancedforms as $id => $title) {
                            echo "<option value='{$id}'>{$title}</option>";
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class="advancedform-admin-field container-button">
            <div class="advancedform-admin-label"></div>
            <div class="advancedform-admin-input">
                <button type="submit" class="button button-primary button-large">Télécharger</button>
            </div>
        </div>
    </div>
    <input type="hidden" name="action" value="download_form" />
</form>

<form method="post">
    <h3>Importer un formulaire</h3>

    <div id="import-form">
        <div class="advancedform-admin-field">
            <div class="advancedform-admin-label">
                <label for="import-form-advancedform">Fichier json</label>
            </div>
            <div class="advancedform-admin-input">
                <input id="import-form-advancedform" name="file" type="file" accept=".json" />
            </div>
        </div>
        <div class="advancedform-admin-field container-button">
            <div class="advancedform-admin-label"></div>
            <div class="advancedform-admin-input">
                <button type="submit" class="button button-primary button-large">Envoyer</button>
            </div>
        </div>
    </div>
    <input type="hidden" name="action" value="import_form" />
</form>

<script>
    document.querySelectorAll( 'form' ).forEach( item => {
        item.addEventListener( 'submit', async event => {
            const select = item.querySelector( 'select[name="advancedform"]' )
            const input_action = item.querySelector( 'input[name="action"]' )
            const advancedform = select ? select.value : null
            const action = input_action ? input_action.value : null
            const input_file = item.querySelector( 'input[name="file"]' )

            if ( (advancedform || input_file) && action ) {
                event.preventDefault()

                if ( action === "download_form" ) {
                    const query = await fetch( ajaxurl, {
                        method: 'post',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'Cache-Control': 'no-cache',
                        },
                        body: new URLSearchParams( {
                            action: 'export_form',
                            advancedform,
                        } )
                    } )
                    const response = await query.json()
    
                    if ( response.error ) {
                        console.error( response.error )
                    } else {
                        // download json file
                        const blob = new Blob( [ JSON.stringify( response ) ], { type: 'application/json' } )
                        const url = URL.createObjectURL( blob )
                        const a = document.createElement( 'a' )
                        a.href = url
                        a.download = `${response.title}.json`
                        a.click()
                        URL.revokeObjectURL( url )
                    }
                }
                
                if ( action === "import_form" ) {

                    if ( !input_file || !input_file.files[0] ) {
                        return
                    }

                    // read json file
                    const file = input_file.files[0]
                    const reader = new FileReader()

                    reader.addEventListener( 'load', async () => {
                        const query = await fetch( ajaxurl, {
                            method: 'post',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                                'Cache-Control': 'no-cache',
                            },
                            body: new URLSearchParams( {
                                action,
                                file_data: reader.result,
                            } )
                        } )
                        const response = await query.json()

                        console.log( response )
                        
                        if ( response.post ) {
                            alert( 'Formulaire importé' )
                        }
                        else if ( response.error ) {
                            console.error( response.error )
                        }
                    } )

                    reader.readAsText( file )
                }
            }
        } )
    } )
</script>
