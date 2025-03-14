// field's template
advancedform.field_html = (field = null) => {
    let attr_class = field === null ? 'new' : '',
        id = field ? field.id : '',
        type = field ? field.type : ''
    
    return `
        <div class="field ${attr_class}" field-id="${id}" data-type="${type}">
            <div class="field-head">
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <span class="advancedform-field-index"></span>
                    </div>
                    <div class="advancedform-admin-input">
                        <div>
                            <a href="#0" class="advancedform-field-name">(choisir un label)</a>
                            <div class="container-field-actions">
                                <a class="duplicate" title="Dupliquer ce champ" href="#0">Dupliquer</a>
                                <a class="delete" title="Supprimer ce champ" href="#0">Supprimer</a>
                            </div>
                        </div>` +

                        // If the field is required or not
                        `<input id="advancedform-required-${id}" type="checkbox" data-parameter="required" />
                    </div>
                </div>
            </div>
            <div class="field-body">` +

                /********************
                 * Parameters
                ********************/

                // Label and type for all fields
                `<div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-label-${id}">Label</label>
                    </div>
                    <div class="advancedform-admin-input">
                        <input id="advancedform-label-${id}" type="text" data-parameter="label" />
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-label-${id}">Afficher le label ?</label>
                    </div>
                    <div class="advancedform-admin-input">
                        <input id="advancedform-label-${id}" type="checkbox" checked data-parameter="display_label" />
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-type-${id}">Type</label>
                    </div>
                    <div class="advancedform-admin-input">
                        <select id="advancedform-type-${id}" data-parameter="type">
                            <optgroup label="Post">
                                <option value="title">Titre</option>
                                <option value="content">Contenu</option>
                                <option value="thumbnail">Image mise en avant</option>
                            </optgroup>
                            <optgroup label="Autre">
                                <option value="text" selected="selected">Texte</option>
                                <option value="textarea">Zone de texte</option>
                                <option value="number">Nombre</option>
                                <option value="email">E-mail</option>
                                <option value="files">Fichier(s)</option>
                                <option value="choice">Choix</option>
                                <option value="checkbox">Case à cocher</option>
                                <option value="date">Date</option>
                                <option value="datetime">Date & Heure</option>
                                <option value="time">Heure</option>
                                <option value="color">Couleur</option>
                                <option value="password">Mot de passe</option>
                                <option value="hidden">Champ caché</option>
                                <option value="recaptcha_v2">reCAPTCHA v2</option>
                            </optgroup>
                        </select>
                    </div>
                </div>` +

                // Placeholder
                `<div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-placeholder-${id}">Placeholder</label>
                        Texte explicatif
                    </div>
                    <div class="advancedform-admin-input">
                        <textarea id="advancedform-placeholder-${id}" data-parameter="placeholder" rows="2"></textarea>
                    </div>
                </div>` +

                // Restrictions
                `<div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-files-extension-allowed-${id}">Extensions autorisées</label>
                        Indiquez une valeur par ligne
                    </div>
                    <div class="advancedform-admin-input">
                        <textarea id="advancedform-files-extension-allowed-${id}" data-parameter="file_extensions_allowed" rows="2"></textarea>
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-files-extension-disallowed-${id}">Extensions interdites</label>
                        Indiquez une valeur par ligne
                    </div>
                    <div class="advancedform-admin-input">
                        <textarea id="advancedform-files-extension-disallowed-${id}" data-parameter="file_extensions_disallowed" rows="2">svg</textarea>
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-files-many-${id}">Plusieurs fichiers ?</label>
                        Autoriser l'upload de plusieurs fichiers
                    </div>
                    <div class="advancedform-admin-input">
                        <input id="advancedform-files-many-${id}" type="checkbox" data-parameter="files_multiple" />
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-length-min-${id}">Longueur minimale</label>
                    </div>
                    <div class="advancedform-admin-input">
                        <input id="advancedform-length-min-${id}" type="number" min="1" data-parameter="length_min" />
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-length-max-${id}">Longueur maximale</label>
                    </div>
                    <div class="advancedform-admin-input">
                        <input id="advancedform-length-max-${id}" type="number" data-parameter="length_max" />
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-number-min-${id}">Valeur minimale</label>
                    </div>
                    <div class="advancedform-admin-input">
                        <input id="advancedform-number-min-${id}" type="number" data-parameter="number_min" />
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-number-max-${id}">Valeur maximale</label>
                    </div>
                    <div class="advancedform-admin-input">
                        <input id="advancedform-number-max-${id}" type="number" data-parameter="number_max" />
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-size-min-${id}">Taille minimale</label>
                        en Mo
                    </div>
                    <div class="advancedform-admin-input">
                        <input id="advancedform-size-min-${id}" type="number" step="any" data-parameter="size_min" />
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-size-max-${id}">Taille maximale</label>
                        en Mo
                    </div>
                    <div class="advancedform-admin-input">
                        <input id="advancedform-size-max-${id}" type="number" step="any" data-parameter="size_max" />
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-date-min-${id}">Date minimale</label>
                    </div>
                    <div class="advancedform-admin-input">
                        <input id="advancedform-date-min-${id}" type="date" data-parameter="date_min" />
                        <button class="button button-secondary button-large refresh-empty-parameter" type="button">Réinitialiser</button>
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-date-max-${id}">Date maximale</label>
                    </div>
                    <div class="advancedform-admin-input">
                        <input id="advancedform-date-max-${id}" type="date" data-parameter="date_max" />
                        <button class="button button-secondary button-large refresh-empty-parameter" type="button">Réinitialiser</button>
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-datetime-min-${id}">Date & Heure minimale</label>
                    </div>
                    <div class="advancedform-admin-input">
                        <input id="advancedform-datetime-min-${id}" type="datetime-local" data-parameter="datetime_min" />
                        <button class="button button-secondary button-large refresh-empty-parameter" type="button">Réinitialiser</button>
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-datetime-max-${id}">Date & Heure maximale</label>
                    </div>
                    <div class="advancedform-admin-input">
                        <input id="advancedform-datetime-max-${id}" type="datetime-local" data-parameter="datetime_max" />
                        <button class="button button-secondary button-large refresh-empty-parameter" type="button">Réinitialiser</button>
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-time-min-${id}">Heure minimale</label>
                    </div>
                    <div class="advancedform-admin-input">
                        <input id="advancedform-time-min-${id}" type="time" data-parameter="time_min" />
                        <button class="button button-secondary button-large refresh-empty-parameter" type="button">Réinitialiser</button>
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-time-max-${id}">Heure maximale</label>
                    </div>
                    <div class="advancedform-admin-input">
                        <input id="advancedform-time-max-${id}" type="time" data-parameter="time_max" />
                        <button class="button button-secondary button-large refresh-empty-parameter" type="button">Réinitialiser</button>
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-regex-${id}">Regex</label>
                    </div>
                    <div class="advancedform-admin-input">
                        <input id="advancedform-regex-${id}" type="text" data-parameter="regex" />
                    </div>
                </div>` +

                // Choice field
                `<div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-choice-logic-${id}">Logique de choix</label>
                    </div>
                    <div class="advancedform-admin-input">
                        <select id="advancedform-choice-logic-${id}" data-parameter="choice_logic">
                            <option value="select">Liste déroulante</option>
                            <option value="checkbox">Case à cocher</option>
                            <option value="radio">Bouton radio</option>
                        </select>
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-choices-${id}">Choix</label>
                        Indiquez une valeur par ligne
                    </div>
                    <div class="advancedform-admin-input">
                        <textarea id="advancedform-choices-${id}" data-parameter="choices" rows="2"></textarea>
                        <p>Possibilité de spécifier la valeur (valeur|label)</p>
                    </div>
                </div>` +
                
                // Checkbox field
                `<div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-checkbox-message-${id}">Message</label>
                    </div>
                    <div class="advancedform-admin-input">
                        <textarea id="advancedform-checkbox-message-${id}" data-parameter="checkbox_message"></textarea>
                    </div>
                </div>` +

                // Show default data
                `<div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-show-default-title-${id}">Valeur à l'affichage</label>
                        Par défaut au chargement du formulaire
                    </div>
                    <div class="advancedform-admin-input">
                        <textarea id="advancedform-show-default-title-${id}" data-parameter="show_default_title" rows="2"></textarea>
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-show-default-content-${id}">Valeur à l'affichage</label>
                        Par défaut au chargement du formulaire
                    </div>
                    <div class="advancedform-admin-input">
                        <textarea id="advancedform-show-default-content-${id}" data-parameter="show_default_content" rows="2"></textarea>
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-show-default-text-${id}">Valeur à l'affichage</label>
                        Par défaut au chargement du formulaire
                    </div>
                    <div class="advancedform-admin-input">
                        <textarea id="advancedform-show-default-text-${id}" data-parameter="show_default_text" rows="2"></textarea>
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-show-default-textarea-${id}">Valeur à l'affichage</label>
                        Par défaut au chargement du formulaire
                    </div>
                    <div class="advancedform-admin-input">
                        <textarea id="advancedform-show-default-textarea-${id}" data-parameter="show_default_textarea" rows="2"></textarea>
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-show-default-number-${id}">Valeur à l'affichage</label>
                        Par défaut au chargement du formulaire
                    </div>
                    <div class="advancedform-admin-input">
                        <input id="advancedform-show-default-number-${id}" type="number" data-parameter="show_default_number" />
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-show-default-email-${id}">Valeur à l'affichage</label>
                        Par défaut au chargement du formulaire
                    </div>
                    <div class="advancedform-admin-input">
                        <input id="advancedform-show-default-email-${id}" type="email" data-parameter="show_default_email" />
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-show-default-choice-${id}">Valeur à l'affichage</label>
                        Par défaut au chargement du formulaire
                    </div>
                    <div class="advancedform-admin-input">
                        <textarea id="advancedform-show-default-choice-${id}" data-parameter="show_default_choice" rows="2"></textarea>
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-show-default-date-${id}">Valeur à l'affichage</label>
                        Par défaut au chargement du formulaire
                    </div>
                    <div class="advancedform-admin-input">
                        <input id="advancedform-show-default-date-${id}" type="date" data-parameter="show_default_date" />
                        <button class="button button-secondary button-large refresh-empty-parameter" type="button">Réinitialiser</button>
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-show-default-datetime-${id}">Valeur à l'affichage</label>
                        Par défaut au chargement du formulaire
                    </div>
                    <div class="advancedform-admin-input">
                        <input id="advancedform-show-default-datetime-${id}" type="datetime-local" data-parameter="show_default_datetime" />
                        <button class="button button-secondary button-large refresh-empty-parameter" type="button">Réinitialiser</button>
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-show-default-time-${id}">Valeur à l'affichage</label>
                        Par défaut au chargement du formulaire
                    </div>
                    <div class="advancedform-admin-input">
                        <input id="advancedform-show-default-time-${id}" type="time" data-parameter="show_default_time" />
                        <button class="button button-secondary button-large refresh-empty-parameter" type="button">Réinitialiser</button>
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-show-default-color-${id}">Valeur à l'affichage</label>
                        Par défaut au chargement du formulaire
                    </div>
                    <div class="advancedform-admin-input">
                        <input id="advancedform-show-default-color-${id}" type="color" data-parameter="show_default_color" />
                    </div>
                </div>` +

                // Submit default data
                `<div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-submit-default-title-${id}">Valeur à la soumission</label>
                        Par défaut à la soumission du formulaire si aucune donnée n'est renseignée
                    </div>
                    <div class="advancedform-admin-input">
                        <textarea id="advancedform-submit-default-title-${id}" data-parameter="submit_default_title" rows="2"></textarea>
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-submit-default-content-${id}">Valeur à la soumission</label>
                        Par défaut à la soumission du formulaire si aucune donnée n'est renseignée
                    </div>
                    <div class="advancedform-admin-input">
                        <textarea id="advancedform-submit-default-content-${id}" data-parameter="submit_default_content" rows="2"></textarea>
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-submit-default-thumbnail-${id}">Image en avant par défaut</label>
                    </div>
                    <div class="advancedform-admin-input">
                        <div id="preview-thumbnail-${id}" class="container-preview-files"></div>
                        <button type="button" class="wp-media-add button" target-input="#advancedform-submit-default-thumbnail-${id}" target-preview="#preview-thumbnail-${id}">Choisir</button>
                        <input id="advancedform-submit-default-thumbnail-${id}" type="hidden" data-parameter="submit_default_thumbnail" />
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-submit-default-text-${id}">Valeur à la soumission</label>
                        Par défaut à la soumission du formulaire si aucune donnée n'est renseignée
                    </div>
                    <div class="advancedform-admin-input">
                        <textarea id="advancedform-submit-default-text-${id}" data-parameter="submit_default_text" rows="2"></textarea>
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-submit-default-textarea-${id}">Valeur à la soumission</label>
                        Par défaut à la soumission du formulaire si aucune donnée n'est renseignée
                    </div>
                    <div class="advancedform-admin-input">
                        <textarea id="advancedform-submit-default-textarea-${id}" data-parameter="submit_default_textarea" rows="2"></textarea>
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-submit-default-number-${id}">Valeur à la soumission</label>
                        Par défaut à la soumission du formulaire si aucune donnée n'est renseignée
                    </div>
                    <div class="advancedform-admin-input">
                        <input id="advancedform-submit-default-number-${id}" type="number" data-parameter="submit_default_number" />
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-submit-default-email-${id}">Valeur à la soumission</label>
                        Par défaut à la soumission du formulaire si aucune donnée n'est renseignée
                    </div>
                    <div class="advancedform-admin-input">
                        <input id="advancedform-submit-default-email-${id}" type="email" data-parameter="submit_default_email" />
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-submit-default-files-${id}">Fichiers par défaut</label>
                    </div>
                    <div class="advancedform-admin-input">
                        <div id="preview-files-${id}" class="container-preview-files"></div>
                        <button type="button" class="wp-media-add multiple button" target-input="#advancedform-submit-default-files-${id}" target-preview="#preview-files-${id}">Choisir</button>
                        <input id="advancedform-submit-default-files-${id}" type="hidden" data-parameter="submit_default_files" />
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-submit-default-date-${id}">Valeur à la soumission</label>
                        Par défaut à la soumission du formulaire si aucune donnée n'est renseignée
                    </div>
                    <div class="advancedform-admin-input">
                        <input id="advancedform-submit-default-date-${id}" type="date" data-parameter="submit_default_date" />
                        <button class="button button-secondary button-large refresh-empty-parameter" type="button">Réinitialiser</button>
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-submit-default-datetime-${id}">Valeur à la soumission</label>
                        Par défaut à la soumission du formulaire si aucune donnée n'est renseignée
                    </div>
                    <div class="advancedform-admin-input">
                        <input id="advancedform-submit-default-datetime-${id}" type="datetime-local" data-parameter="submit_default_datetime" />
                        <button class="button button-secondary button-large refresh-empty-parameter" type="button">Réinitialiser</button>
                    </div>
                </div>
                <div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-submit-default-time-${id}">Valeur à la soumission</label>
                        Par défaut à la soumission du formulaire si aucune donnée n'est renseignée
                    </div>
                    <div class="advancedform-admin-input">
                        <input id="advancedform-submit-default-time-${id}" type="time" data-parameter="submit_default_time" />
                        <button class="button button-secondary button-large refresh-empty-parameter" type="button">Réinitialiser</button>
                    </div>
                </div>` +

                // The class et meta_key/field name for all fields
                `<div class="advancedform-admin-field">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-class-${id}">class</label>
                        Séparez les classes par un espace
                    </div>
                    <div class="advancedform-admin-input">
                        <input id="advancedform-class-${id}" type="text" data-parameter="class" />
                    </div>
                </div>
                <div class="advancedform-admin-field postmeta-key">
                    <div class="advancedform-admin-label">
                        <label for="advancedform-postmetakey-${id}">postmeta-key</label>
                        Utile pour rattacher la valeur du champ du formulaire à un champ personnalisé du post
                    </div>
                    <div class="advancedform-admin-input">
                        <input id="advancedform-postmetakey-${id}" type="text" data-parameter="postmetakey" />
                    </div>
                </div>
            </div>
        </div>
    `
}
