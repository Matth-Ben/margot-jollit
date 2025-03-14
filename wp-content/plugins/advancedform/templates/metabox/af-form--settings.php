<?php
    $post_types = get_post_types(['public' => true, '_builtin' => false,], 'objects', 'and');
    $data = get_post_meta($post->ID, 'af_form_data', true);

    $recaptcha_v2 = $af_form->get_recaptcha(2);
    $recaptcha_v3 = $af_form->get_recaptcha(3);
?>

<div class="advancedform-admin-field post-type">
    <div class="advancedform-admin-label">
        <label for="advancedform-post-type">Action du formulaire</label>
    </div>
    <div class="advancedform-admin-input">
        <select id="advancedform-post-type">
            <option value="">Créer une entrée (Défaut)</option>
            <option value="post">Créer un post (Article)</option>
            <?php foreach ($post_types as $p => $item) echo ($p !== 'af_form' && $p !== 'af_entry') ? "<option value='{$p}'>Créer un post ({$item->labels->name})</option>" : ''; ?>
        </select>
        <div id="advancedform-see-entries"><br><a href="/wp-admin/edit.php?post_type=af_entry&the_form=af-<?php echo $post->ID ?>">Voir les entrées</a></div>
    </div>
</div>

<div class="advancedform-admin-field post-status">
    <div class="advancedform-admin-label">
        <label for="advancedform-post-status">Status</label>
    </div>
    <div class="advancedform-admin-input">
        <select id="advancedform-post-status">
            <option value="publish">Publié</option>
            <option value="pending">En attente de relecture</option>
            <option value="draft">Brouillon</option>
        </select>
    </div>
</div>

<?php if (AF_ACF_IS_ACTIVE) { ?>
    <div class="advancedform-admin-field post-status container-button">
        <div class="advancedform-admin-label"></div>
        <div class="advancedform-admin-input">
            <button id="import-acf-fields-post" class="import-acf-fields-post button button-secondary button-large" type="button" data-confirm="Cette action va écraser les champs existants.\nVoulez-vous continuer ?">Importer les champs ACF</button>
        </div>
    </div>
<?php } ?>

<div class="advancedform-admin-field entry-title">
    <div class="advancedform-admin-label">
        <label for="advancedform-entry-title">Titre basé sur le champ</label>
    </div>
    <div class="advancedform-admin-input">
        <select id="advancedform-entry-title"></select>
    </div>
</div>

<div class="advancedform-admin-field success-message">
    <div class="advancedform-admin-label">
        <label for="advancedform-success-message">Message de confirmation</label>
    </div>
    <div class="advancedform-admin-input">
        <input id="advancedform-success-message" type="text" />
    </div>
</div>

<div class="advancedform-admin-field error-message">
    <div class="advancedform-admin-label">
        <label for="advancedform-error-message">Message d'erreur</label>
    </div>
    <div class="advancedform-admin-input">
        <input id="advancedform-error-message" type="text" />
    </div>
</div>

<div class="advancedform-admin-field information-message">
    <div class="advancedform-admin-label">
        <label for="advancedform-information-message">Information supplémentaire</label>
    </div>
    <div class="advancedform-admin-input">
        <input id="advancedform-information-message" type="text" />
    </div>
</div>

<?php if ($recaptcha_v2 || $recaptcha_v3) { ?>
    <div class="advancedform-admin-field recaptcha-version">
        <div class="advancedform-admin-label">
            <label for="advancedform-recaptcha">Utilisation du reCAPTCHA</label>
        </div>
        <div class="advancedform-admin-input">
            <select id="advancedform-recaptcha-version">
                <option value="">Ne pas utiliser</option>
                <?php echo $recaptcha_v2 ? '<option value="2">reCAPTCHA v2</option>' : '' ?>
                <?php echo $recaptcha_v3 ? '<option value="3">reCAPTCHA v3</option>' : '' ?>
            </select>
        </div>
    </div>
    <div class="advancedform-admin-field recaptcha-language">
        <div class="advancedform-admin-label">
            <label for="advancedform-recaptcha">Langue (reCAPTCHA)</label>
            <a href="https://developers.google.com/recaptcha/docs/language">Voir la documentation</a>
        </div>
        <div class="advancedform-admin-input">
            <select id="advancedform-recaptcha-language">
                <option value="">Automatique</option>
                <option value="ar">Arabic</option>
                <option value="af">Afrikaans</option>
                <option value="am">Amharic</option>
                <option value="hy">Armenian</option>
                <option value="az">Azerbaijani</option>
                <option value="eu">Basque</option>
                <option value="bn">Bengali</option>
                <option value="bg">Bulgarian</option>
                <option value="ca">Catalan</option>
                <option value="zh-HK">Chinese (Hong Kong)</option>
                <option value="zh-CN">Chinese (Simplified)</option>
                <option value="zh-TW">Chinese (Traditional)</option>
                <option value="hr">Croatian</option>
                <option value="cs">Czech</option>
                <option value="da">Danish</option>
                <option value="nl">Dutch *</option>
                <option value="en-GB">English (UK)</option>
                <option value="en">English (US) *</option>
                <option value="et">Estonian</option>
                <option value="fil">Filipino</option>
                <option value="fi">Finnish</option>
                <option value="fr">French *</option>
                <option value="fr-CA">French (Canadian)</option>
                <option value="gl">Galician</option>
                <option value="ka">Georgian</option>
                <option value="de">German *</option>
                <option value="de-AT">German (Austria)</option>
                <option value="de-CH">German (Switzerland)</option>
                <option value="el">Greek</option>
                <option value="gu">Gujarati</option>
                <option value="iw">Hebrew</option>
                <option value="hi">Hindi</option>
                <option value="hu">Hungarain</option>
                <option value="is">Icelandic</option>
                <option value="id">Indonesian</option>
                <option value="it">Italian *</option>
                <option value="ja">Japanese</option>
                <option value="kn">Kannada</option>
                <option value="ko">Korean</option>
                <option value="lo">Laothian</option>
                <option value="lv">Latvian</option>
                <option value="lt">Lithuanian</option>
                <option value="ms">Malay</option>
                <option value="ml">Malayalam</option>
                <option value="mr">Marathi</option>
                <option value="mn">Mongolian</option>
                <option value="no">Norwegian</option>
                <option value="fa">Persian</option>
                <option value="pl">Polish</option>
                <option value="pt">Portuguese *</option>
                <option value="pt-BR">Portuguese (Brazil)</option>
                <option value="pt-PT">Portuguese (Portugal)</option>
                <option value="ro">Romanian</option>
                <option value="ru">Russian</option>
                <option value="sr">Serbian</option>
                <option value="si">Sinhalese</option>
                <option value="sk">Slovak</option>
                <option value="sl">Slovenian</option>
                <option value="es">Spanish *</option>
                <option value="es-419">Spanish (Latin America)</option>
                <option value="sw">Swahili</option>
                <option value="sv">Swedish</option>
                <option value="ta">Tamil</option>
                <option value="te">Telugu</option>
                <option value="th">Thai</option>
                <option value="tr">Turkish</option>
                <option value="uk">Ukrainian</option>
                <option value="ur">Urdu</option>
                <option value="vi">Vietnamese</option>
                <option value="zu">Zulu</option>
            </select>
        </div>
    </div>
<?php } ?>

<input type="text" id="af-form-data" name="af_form_data" value="<?php echo esc_attr($data) ?>" />
