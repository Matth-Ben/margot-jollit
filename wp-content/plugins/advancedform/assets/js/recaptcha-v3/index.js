document.addEventListener('DOMContentLoaded', () => {

    let forms = document.querySelectorAll('form.advancedform'),
        inputs_token = document.querySelectorAll('form.advancedform input[name="g-token"]')
        

    forms.forEach(form => {

        form.addEventListener('submit', (e) => {
            e.preventDefault();
        
            grecaptcha.ready(() => {

                grecaptcha
                    .execute(public_key, {action: 'submit'})
                    .then((token) => {
                        inputs_token.forEach(input => input.setAttribute('value', token));
                        form.submit();
                    });
            });
        })
    })
})