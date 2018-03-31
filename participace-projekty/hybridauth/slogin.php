<?php

if( isset( $_REQUEST["provider"] ) )
{
	include_once( '../../../../wp-load.php' );

	// The selected provider
	$provider_name = $_REQUEST["provider"];

	$user_profile = '';
	try
	{
		// Include HybridAuth library
		// Change the following paths if necessary
		$config   = dirname(__FILE__) . '/config.php';
		require_once( 'Hybrid/Auth.php' );

		// initialize Hybrid_Auth class with the config file
		$hybridauth = new Hybrid_Auth( $config );

		// try to authenticate with the selected provider
		$adapter = $hybridauth->authenticate( $provider_name );

		// then grab the user profile
		$user_profile = $adapter->getUserProfile();

		// Check if the current user already have authenticated using this provider before
		$slogin_user_exists = imc_slogin_get_user( $provider_name, $user_profile->identifier );

		// If it does log him in
		if( $slogin_user_exists ) {

			$wp_user = get_user_by('email', $user_profile->email);
			$id = $wp_user->ID;

		}

		// If it doesn't check for a WP account and create user
		else {

			// Check if email is already registered
			if (email_exists( $user_profile->email )) {
				$id = imc_modify_wp_slogin_user( $provider_name, $user_profile );
			}
			// If the user didn't authenticate using the selected provider before
			// and is not a WP user, Create a new entry on database for him
			else {
				$id = imc_create_new_slogin_user( $provider_name, $user_profile );
			}
		}

		wp_set_auth_cookie( $id );
		$_SESSION["user_connected"] = true;
		wp_redirect ( get_home_url() );

	}

		// something went wrong?
	catch( Exception $e )
	{
		print_r($e);
	}
}