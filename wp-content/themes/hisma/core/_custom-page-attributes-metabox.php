<?php

add_action( 'admin_head', function() {
    ?>
    <style>
        #pageparentdiv .inside .parent-id-label-wrapper,
        #pageparentdiv .inside #parent_id,
        #pageparentdiv .inside .menu-order-label-wrapper,
        #pageparentdiv .inside #menu_order,
        #pageparentdiv .inside .post-attributes-help-text {
            display: none;
        }
    </style>
    <?php
} );