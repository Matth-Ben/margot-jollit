var recaptchaOnload = () => {
    let options = {sitekey: parameters.public_key}

    if (parameters.language !== '') options.hl = parameters.language
    
    grecaptcha.render('g-recaptcha', options)
}