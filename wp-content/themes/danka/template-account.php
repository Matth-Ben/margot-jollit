<?php

/**
 * Template Name: Mon compte
 */

global $wp_query;

$options = get_fields('options');
$key = $options['stripe_secret_key'];
$context = \Timber\Timber::context();
$templates = array( 'template-account.twig' );
$timber_post = \Timber\Timber::get_post( false, get_custom_timber_post() );
$context['post'] = $timber_post;

////
if ( isset( $_GET['payment'] ) && $key ) {
    $stripe = new \Stripe\StripeClient( $key );
    $checkout_session = $stripe->checkout->sessions->create([
      'line_items' => [[
        'price_data' => [
          'currency' => 'eur',
          'product_data' => [
            'name' => 'Paywall',
          ],
          'unit_amount' => 800,
          'recurring' => [
            'interval' => 'month',
          ],
        ],
        'quantity' => 1,
      ]],
      'mode' => 'subscription',
    //   'mode' => 'payment',
      'success_url' => get_home_url() . "/mon-compte?session_id={CHECKOUT_SESSION_ID}",
      'cancel_url' => get_home_url() . "/mon-compte?cancel",
    ]);
    
    header("HTTP/1.1 303 See Other");
    header("Location: " . $checkout_session->url);
}

else if ( isset( $_GET['cancel'] ) && $key ) {
    $stripe = new \Stripe\StripeClient( $key );
    $stripe->subscriptions->update(
        get_user_meta( get_current_user_id(), 'paywall', true ),
        ['cancel_at_period_end' => true]
    );
    $subscription = $stripe->subscriptions->retrieve( get_user_meta( get_current_user_id(), 'paywall', true ) );
    
    // Vérifier si l'abonnement a bien été annulé
    if ( $subscription->cancel_at_period_end === true ) {
      update_user_meta( get_current_user_id(), 'paywall', '' );
      update_user_meta( get_current_user_id(), 'paywall_end', $subscription->current_period_end );
      $context['message'] = "Votre abonnement s'arrêtera à la fin du mois 😢";
    }
}

else if ( isset( $_GET['session_id'] ) && $key ) {
    $stripe = new \Stripe\StripeClient( $key );

    try {
        $session = $stripe->checkout->sessions->retrieve( $_GET['session_id'] );
        $subscription_id = $session->subscription;

        if ( $subscription_id ) {
            $customer = $stripe->customers->retrieve( $session->customer );
            update_user_meta( get_current_user_id(), 'paywall', $subscription_id );
            $context['message'] = "Merci, votre achat a bien été effectué $customer->name ! 🎉";
        }
        // http_response_code(200);
    } catch (Error $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

if ( is_user_logged_in() ) {
    $end = get_user_meta( get_current_user_id(), 'paywall_end', true );
    $end = $end ? date('d/m/Y', $end) : '';
    $is_active = get_user_meta( get_current_user_id(), 'paywall', true ) ? true : false;
    $is_active = !$is_active && $end > date('d/m/Y') ? 'end' : $is_active;
    $context['is_active'] = $is_active;
    $context['end'] = $end;
    $context['user'] = new Timber\User();
} else {
    header("Location: " . get_home_url() . '/connexion');
}

// end date: 1745505740
// $stripe = new \Stripe\StripeClient( $key );
// $subscription = $stripe->subscriptions->retrieve( 'sub_1R6CCmEyQCn4d39i20S2iCNN' );
// dump($subscription);
// dump(date('d/m/Y', $subscription->current_period_end));
// dump(date('d/m/Y', 1745505740));
// dump(date('d/m/Y', 1742827950));
// die;
 
\Timber\Timber::render( $templates, $context );