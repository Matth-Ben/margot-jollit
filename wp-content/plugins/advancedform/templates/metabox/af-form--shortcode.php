<div class="advancedform-admin-field shortcode">
    <div class="advancedform-admin-label">
        <label><?php echo __( 'Shortcode', 'advancedform_test_abc' ); ?></label>
    </div>
    <div class="advancedform-admin-input">
        <span>
            <?php
                echo get_the_title() ? '<b>[advancedform name="' . get_the_title() . '"]</b> ou ' : '';
                echo '<b>[advancedform id="' . get_the_ID() . '"]</b>';
            ?>
        </span>
    </div>
</div>