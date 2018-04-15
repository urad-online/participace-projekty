<?php

/**
 * 20.02
 * Add a check box with the terms of the login and registration form
 *
 */

// souhlas s podmínkami při registraci uživatele nebo při jeho přihlášení


// As part of WP authentication process, call our function
add_filter('wp_authenticate_user', 'wp_authenticate_user_acc', 99999, 2);

function wp_authenticate_user_acc($user, $password) {
    // See if the checkbox #login_accept was checked
    if ( isset( $_REQUEST['login_accept'] ) && $_REQUEST['login_accept'] == 'on' ) {
        // Checkbox on, allow login
        return $user;
    } else {
        // Did NOT check the box, do not allow login
        $error = new WP_Error();
        $error->add('did_not_accept', 'Musíte akceptovat naše podmínky užití a podmínky ochrany osobních údajů' );
        return $error;
    }
}

// As part of WP login form construction, call our function
add_filter ( 'login_form', 'login_form_acc');

function login_form_acc(){
    // Add an element to the login form, which must be checked
    ?>
    <script type="text/javascript">
        has_social_form = false;
        socialLogins = null;        
    </script>
    <?php

    echo '<span title="asdf" class="fa fa-info"></span><label><input type="checkbox" name="login_accept" id="login_accept" /> Souhlasím s <a href="podminky-pouziti-a-ochrana-osobnich-udaju/" target="_blank" rel="noopener">podmínkami použití</a></label>';
}

// As part of WP authentication process, call our function
add_filter('registration_errors', 'user_register_acc', 99999, 2);

function user_register_acc($user, $password) {
    // See if the checkbox #login_accept was checked
    if ( isset( $_REQUEST['login_accept'] ) && $_REQUEST['login_accept'] == 'on' ) {
        // Checkbox on, allow login
        return $user;
    } else {
        // Did NOT check the box, do not allow login
        $error = new WP_Error();
        $error->add('did_not_accept', 'Musíte akceptovat naše podmínky užití a podmínky ochrany osobních údajů' );
        return $error;
    }
}

// As part of WP login form construction, call our function
add_filter ( 'register_form', 'user_register_form_acc');

function user_register_form_acc(){
    // Add an element to the login form, which must be checked
    echo '<label><input type="checkbox" name="login_accept" id="login_accept" /> Souhlasím s <a href="podminky-pouziti-a-ochrana-osobnich-udaju/" target="_blank" rel="noopener">podmínkami použití</a></label>';
}
