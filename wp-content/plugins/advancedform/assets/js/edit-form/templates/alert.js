// alert's template
advancedform.alert_html = (index, data_alert = null) => {
    let pseudo = '',
        email_from = '',
        email_to = '',
        subject = '',
        cc = ''

    if (data_alert) {
        pseudo = data_alert.pseudo ? data_alert.pseudo : ''
        email_from = data_alert.email_from ? data_alert.email_from : ''
        email_to = data_alert.email_to ? data_alert.email_to : ''
        subject = data_alert.subject ? data_alert.subject : ''

        if (data_alert.cc) {

            data_alert.cc.forEach(value => {
                cc += `
                    <div>
                        <input id="advancedform-alert-cc-${index}" type="email" class="cc" placeholder="Cc" value="${value}" />
                        <a href="#0" class="delete remove-cc">Supprimer</a>
                    </div>
                `
            })
        }
    }
    
    return `
    <div class="alert alert-${index}" data-alert="${index}">
        <div class="advancedform-admin-field">
            <div class="advancedform-admin-label">
                <label>Alerte <span>${index}</span></label>
                <div class="container-field-actions">
                    <a class="delete" data-alert="${index}" title="Supprimer cette alerte" href="#0">Supprimer</a>
                </div>
            </div>
            <div class="advancedform-admin-input">"{Mon label}" peut être utilisé pour insérer une donnée du formulaire dynamiquement</div>
        </div>
        <div class="advancedform-admin-field">
            <div class="advancedform-admin-label">
                <label for="advancedform-alert-pseudo-${index}">Pseudo</label>
            </div>
            <div class="advancedform-admin-input">
                <input id="advancedform-alert-pseudo-${index}" type="text" class="pseudo" placeholder="Pseudo" value="${pseudo}" required />
            </div>
        </div>
        <div class="advancedform-admin-field">
            <div class="advancedform-admin-label">
                <label for="advancedform-alert-email-from-${index}">Email</label>
                Expéditeur
            </div>
            <div class="advancedform-admin-input">
                <input id="advancedform-alert-email-from-${index}" type="text" class="email-from" placeholder="lorem@mail.com" value="${email_from}" required />
            </div>
        </div>
        <div class="advancedform-admin-field">
            <div class="advancedform-admin-label">
                <label for="advancedform-alert-subject-${index}">Sujet</label>
            </div>
            <div class="advancedform-admin-input">
                <input id="advancedform-alert-subject-${index}" type="text" class="subject" placeholder="Sujet" value="${subject}" required />
            </div>
        </div>
        <div class="advancedform-admin-field">
            <div class="advancedform-admin-label">
                <label for="advancedform-alert-email-to-${index}">Email</label>
                Destinataire
            </div>
            <div class="advancedform-admin-input">
                <input id="advancedform-alert-email-to-${index}" type="text" class="email-to" placeholder="lorem@mail.com" value="${email_to}" required />
            </div>
        </div>
        <div class="advancedform-admin-field">
            <div class="advancedform-admin-label">
                <label for="advancedform-alert-cc-${index}">Cc</label>
            </div>
            <div class="advancedform-admin-input">
                <div class="container-cc">
                    ${cc}
                </div>
                <button type="button" class="button button-add-cc">+ Ajouter</button>
            </div>
        </div>
        <div class="advancedform-admin-field">
            <div class="advancedform-admin-label">
                <label for="editor-alert-${index}">Contenu</label>
            </div>
            <div class="advancedform-admin-input">
                <textarea id="editor-alert-${index}"></textarea>
            </div>
        </div>
    </div>
    `
}