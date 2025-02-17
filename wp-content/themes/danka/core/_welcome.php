<?php

add_action( 'init', function() {
	if ( ! session_id() ) {
		@session_start();
	}

	if ( !isset( $_SESSION['welcome'] ) ) {
		$_SESSION['welcome'] = true;
	} else {
        $_SESSION['welcome'] = false;
    }
}, 1 );
