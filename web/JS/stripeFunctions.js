Stripe.setPublishableKey('pk_test_oW03SdAAO7rbWlUciFFQvBXa');

var $paiementForm = $('#formulairePaiement');
var $boutonValider = $paiementForm.find('button');

$paiementForm.submit(function(e) {

   e.preventDefault();
   Stripe.card.createToken($paiementForm, function(status, response) {

        if(response.error) {

            $('#message-flash').addClass('message-erreur').fadeIn(600).append('<div class = "message"><p>' + response.error.message + '</p> <br /></div>').delay(1300).fadeOut(600);
            $boutonValider.prop('disabled', true);
            setTimeout(function() {

                $('.message').remove();
                $boutonValider.prop('disabled', false);
                $('#message-flash').removeClass('message-erreur');

            }, 2600);

        } else {

            var $token = response.id;
            $paiementForm.append('<input type = "hidden" name = "stripeToken />');
            $('[name = stripeToken]').val($token);
            $paiementForm.get(0).submit();

        }

   });

});
