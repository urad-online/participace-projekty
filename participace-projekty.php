<?php
/*
Plugin Name: Participace na projektech
Plugin URI: https://wordpress.org/plugins/participace-projekty/
Description: A platform for managing local issues; from reporting, to administration and analysis
Version: 1.4.1
Author: infalia
Author URI: http://www.improve-my-city.com
Text Domain: participace-projekty
Domain Path: /languages
License: AGPLv3
*/


if ( version_compare( PHP_VERSION, '5.4.0', '<' ) ) {

	/**
	 * We are running under PHP 5.4.0
	 * Display an admin notice and do nothing.
	 */
	is_admin() && add_action( 'admin_notices', create_function( '', "
	echo '
		<div class=\"error\"><p>

		<strong>The Participace na projektech plugin requires PHP 5.4.0 or later. Please deactivate the plugin or update your PHP version</strong>.
		</p></div>
	';"
	) );
}


add_action( 'init', 'load_imc_translations', 0 );
function load_imc_translations() {
	load_plugin_textdomain('participace-projekty', FALSE, dirname(plugin_basename(__FILE__)).'/languages');
}


// 1.01 Register 'imc_issues'
// 1.02 Set "single_template" for our imc_issues.
// 1.03 Set "archive_template" for our imc_issues.
include_once( plugin_dir_path( __FILE__ ) . 'functions/imc-core-issues.php' );

// 2.01 Register Taxonomy 'imccategory'
// 2.02 Add Admin Mail @ 'imccategory'
// 2.03 Add Featured Image @ 'imccategory'
// 2.04 Create 'imccategory' Box @ issue's backend (has API use also)
// 2.05 Add thumbnail image to Issue Category Column
// 2.06 Add category Mail to Issue Category Column
// 2.07 Extra Info about ImcCategory for API (API USE only)
// 2.08 Clear Category Image after Submitted
// 2.09 Go Back Link at 'imccategory' edit screen
include_once( plugin_dir_path( __FILE__ ) . 'functions/imc-core-taxcategory.php' );


// 3.01 Register Taxonomy 'imcstatus'
// 3.02 Create 'imcstatus' Box @ issue's backend (has API use also)
// 3.03 Add Status Color @ 'imccategory'
// 3.04 Add status color to Issue Status Column
// 3.05 Add status id to Issue Status Column
// 3.06 Extra Info about ImcStatus for API
// 3.07 Go Back Link at 'imcstatus' edit screen
// 3.08 Clear Status Color after Submitted
include_once( plugin_dir_path( __FILE__ ) . 'functions/imc-core-taxstatus.php' );


// 4.01 Add Columns @ Issues Admin Panel
// 4.02 Make Columns of the IMC Issues sortable
// 4.03 Enable Filtering @ issues' admin columns
include_once( plugin_dir_path( __FILE__ ) . 'functions/imc-core-issues-col.php' );


// 5.01 Add Box with Lat-Lng-Address-Votes
// 5.02 Data for Box with Lat-Lng-Address-Votes
// 5.03 Save Data @ Box with Lat-Lng-Address-Votes
// 5.04 Hide Custom Field panel from imc_issues
include_once( plugin_dir_path( __FILE__ ) . 'functions/imc-core-issues-info.php' );


// 6.01 Time ago
// 6.02 Add Issue ID & Issue Author right after the Issue Title (@ backend)
// 6.03 Create select box with imccategory options for insert page
// 6.04 Rename Featured Image Metabox and change its position
// 6.05 Upload Image to Gallery from front-end form
// 6.06 Remove Participace na projektech menu and admin bar "New Issue" for subscribers
// 6.07 Custom Pagination links for overview issues
// 6.08 Function that checks if message @ subscriber is necessary
// 6.09 Function that checks if user can edit an issue
// 6.10 Function that enables Sessions on wordpress
// 6.11 Calculates the root path of the plugin
// 6.12 Creates Custom Role "Department Admin"
// 6.13 Capabilities about imccategory
// 6.14 Hides imcstatus from quick edit
// 6.15 Hides status radio choice (private-public-protected)
// 6.16 Unsets View link @ quick edit for imccategory & imcstatus
// 6.17 Social Login Implementation
include_once( plugin_dir_path( __FILE__ ) . 'functions/imc-core-functions.php' );


// 7.01 Get Methods for Status Name, ID and Color
// 7.02 Returns all available imc_status taxonomy choices
// 7.03 Get page by slug function
// 7.04 Returns all imccategory terms
// 7.05 Get Archive/Insert/Edit Pages
// 7.06
// 7.07 Get user's FirebaseID by userID (API use)
include_once( plugin_dir_path( __FILE__ ) . 'functions/imc-core-setget-func.php' );


// 8.01 Email Notification when issue created (frontend)
// 8.02 Email Notification on Issue imcstatus change
// 8.03 Email Notification on imccategory change
// 8.03 Email Notification on submitted comments about issues
// 8.05 'From:' name @ all emails
include_once( plugin_dir_path( __FILE__ ) . 'functions/imc-core-notifications.php' );


// 9.01 Add Box 'Issues Log'
// 9.02 Creates first Log when issue created from frontend without moderate
// 9.03 Creates first Log when issue created from frontend with backend-moderate (pending->publish)
// 9.04 Creates first Log when issue created from backend (draft->publish)
// 9.05 Changes state of Logs when issues moved on trash
// 9.06 Delete Logs and Votes when issue permanently deleted
// 9.07 Changes state of Logs when issues restored from trash
include_once( plugin_dir_path( __FILE__ ) . 'functions/imc-core-logs.php' );


// 10.01 Create Admin Page with "All Logs"
include_once( plugin_dir_path( __FILE__ ) . 'functions/imc-page-logs.php' );


// 11.01 Create Admin Page about "IMC Settings"
include_once( plugin_dir_path( __FILE__ ) . 'functions/imc-page-settings.php' );


// 12.01 Add insert, edit & archive templates to every theme
// 12.02 Creates page "IMC - Report Issue page" on plugin activation
// 12.03 Creates page "IMC - Edit Issue page" on plugin activation
// 12.04 Creates page "IMC - Participace na projektech Main page" on plugin activation
include_once( plugin_dir_path( __FILE__ ) . 'functions/imc-core-templates.php' );
register_activation_hook(__FILE__,'imc_create_reporting_page');
register_activation_hook(__FILE__,'imc_create_edit_page');
register_activation_hook(__FILE__,'imc_create_main_page');


// 13.01 Creates extra args for http-variables use
// 13.02 Creates extra args for http-variables use (without category/status/keyword)
// 13.03 Returns issues with given imccategory
// 13.04 Returns issues with given imcstatus
// 13.05 Returns issues for non users
// 13.06 Returns issues for non admins
// 13.07 Returns issues for user
include_once( plugin_dir_path( __FILE__ ) . 'functions/imc-core-filter-func.php' );


// 14.01 IMC-Archive part for grid-option
include_once( plugin_dir_path( __FILE__ ) . 'functions/templates/imc-part-archive-grid.php' );


// 15.01 IMC-Archive part for list-option
include_once( plugin_dir_path( __FILE__ ) . 'functions/templates/imc-part-archive-list.php' );


// 16.01 Creates first postIndex after issue post-status transition
// 16.02 Adds modality as post_meta, when the issue created from backend
// 16.03 Adds modality as commentmeta, when comment posted backend/frontend
include_once( plugin_dir_path( __FILE__ ) . 'functions/imc-core-posts-index.php' );

// 17.01 Create Logs Table imc_logs
// 17.02 Create Tokens Table imc_tokens
// 17.03 Create Keys Table imc_keys
// 17.04 Create Votes Table imc_votes
// 17.05 Create Posts Index Table imc_posts_index
// 17.06 Create Users slogin Table imc_users_slogin
// 17.07 Create Firebase Users Table imc_users_firebase
include_once( plugin_dir_path( __FILE__ ) . 'functions/imc-core-tables.php' );
register_activation_hook( __FILE__, 'imc_create_logs_table' );
register_activation_hook( __FILE__, 'imc_create_tokens_table' );
register_activation_hook( __FILE__, 'imc_create_keys_table' );
register_activation_hook( __FILE__, 'imc_create_votes_table' );
register_activation_hook( __FILE__, 'imc_create_posts_index_table' );
register_activation_hook( __FILE__, 'imc_create_users_slogin_table' );
register_activation_hook( __FILE__, 'imc_create_users_firebase_table' );


// 18.01
include_once( plugin_dir_path( __FILE__ ) . 'functions/imc-core-taxstatus-ordering.php' );

// 19.01
include_once( plugin_dir_path( __FILE__ ) . 'functions/imc-user-groups.php' );

// 20.01 Add additional fields to imc_issues
// 20.02 Add a check box with the terms of the login and registration form
include_once( plugin_dir_path( __FILE__ ) . 'functions/pb-additional-fields.php' );
include_once( plugin_dir_path( __FILE__ ) . 'functions/pb-add-check-box-terms.php' );




/*******************************            SETTINGS LINK ON PLUGINS PAGE           **********************************/

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'add_action_links' );

function add_action_links ( $links ) {
	$mylinks = array(
		'<a href="' . admin_url( 'options-general.php?page=imc_options' ) . '">Settings</a>',
	);
	return array_merge( $links, $mylinks );
}

/*************************************              RULES            *************************************************/

register_activation_hook( __FILE__, 'imc_add_rewrite_rules_flush_flag' );

// Add a flag that will allow to flush the rewrite rules when needed.
function imc_add_rewrite_rules_flush_flag() {
	if ( ! get_option( 'imcplugin_flush_rewrite_rules_flag' ) ) {
		add_option( 'imcplugin_flush_rewrite_rules_flag', true );
	}
}

add_action( 'init', 'imc_flush_rewrite_rules', 20 );

// Flush rewrite rules if the previously added flag exists,and then remove the flag.
function imc_flush_rewrite_rules() {
	if ( get_option( 'imcplugin_flush_rewrite_rules_flag' ) ) {
		flush_rewrite_rules();
		delete_option( 'imcplugin_flush_rewrite_rules_flag' );
	}
}


/*************************************              SCRIPTS            *************************************************/

function imc_register_scripts() {

	// Styling
	wp_enqueue_style( 'skeleton', plugin_dir_url( __FILE__ ) . 'css/imc-styles-skeleton.css' );
	wp_enqueue_style( 'imc-plugin-styles', plugin_dir_url( __FILE__ ) . 'css/imc-styles-main.css' );
	wp_enqueue_style( 'material-icons', plugin_dir_url( __FILE__ ) . 'css/imc-styles-material-icons.css' );
	wp_enqueue_style( 'roboto-font', 'https://fonts.googleapis.com/css?family=Roboto:300italic,400,500,400italic,500italic,700|Roboto+Slab:700' );

	// GOOGLE MAPS with GET OPTIONS
	$gmapOptions = get_option( 'gmap_settings' );
	$gmapAPIkey = $gmapOptions["gmap_api_key"];
	$gmapLanguage = $gmapOptions["gmap_mlang"]; if($gmapLanguage==''){$gmapLanguage='en';}
	$gmapRegion = $gmapOptions["gmap_mreg"]; if($gmapRegion==''){$gmapRegion='GB';}
	$gmapCall = "https://maps.googleapis.com/maps/api/js?v=3&key=" . $gmapAPIkey . "&language=" . $gmapLanguage . "&region=" . $gmapRegion . "&libraries=places,geometry";
	wp_register_script('imc-gmap', $gmapCall);

	// Official plugin to Google Maps API -> Extends infoWindow object to do some magic
	wp_register_script( 'mapsV3_infobubble', plugin_dir_url( __FILE__ ) . 'js/gmaps_v3_infobubble.js', array( 'imc-gmap' ));

	// Official plugin to Google Maps API -> Extends marker object to do some magic
	wp_register_script( 'mapsV3_richmarker', plugin_dir_url( __FILE__ ) . 'js/richmarker-compiled.js', array( 'mapsV3_infobubble' ));

	// Form validation (Report an Issue) before sending it to backend.
	wp_register_script( 'imc-insert-form-validation', plugin_dir_url( __FILE__ ) . 'js/validate.min.js', array( 'imc-gmap' ));
	wp_enqueue_script('imc-insert-form-validation');

	wp_register_script(
		'imc-scripts',
		plugin_dir_url( __FILE__ ) . 'js/imc-scripts.js',
		array( 'mapsV3_richmarker' )
	);
	wp_localize_script('imc-scripts', 'imcScriptsVars', array(
			'boundsAlert' => __( 'Please provide a location inside municipality limits', 'participace-projekty' ),
			'addressAlert' => __( 'Couldn\'t find an address', 'participace-projekty' ),
			'noResults' => __( 'No results found', 'participace-projekty' ),
			'geoCoderFail' => __( 'Geocoder failed', 'participace-projekty' )
		)
	);
	wp_enqueue_script( 'imc-scripts' );



// JQuery Date-Time Picker (js)
	wp_register_script( 'add__datetime_picker_js', plugin_dir_url( __FILE__ ) . 'js/jquery.simple-dtpicker.js', array( 'jquery' ), '1.0', TRUE );
	wp_enqueue_script( 'add__datetime_picker_js' );


// Script to orient the Preview image correctly on front-end (based on EXIF data)
	wp_register_script( 'imc-load-image', plugin_dir_url( __FILE__ ) . 'js/load-image.all.min.js');
	wp_enqueue_script('imc-load-image');

}
add_action( 'wp_enqueue_scripts', 'imc_register_scripts');



function imc_enqueue_admin_scripts($hook) {

	wp_enqueue_style( 'backend-styles', plugin_dir_url( __FILE__ ) . 'css/imc-styles-backend.css' );
	wp_enqueue_style( 'jquery.simple-dtpicker', plugin_dir_url( __FILE__ ) . 'css/imc-styles-dtpicker.css' );
	wp_enqueue_style( 'spectrum-jquery-color-picker', plugin_dir_url( __FILE__ ) . 'css/imc-styles-colorpicker.css' );

	// GOOGLE MAPS with GET OPTIONS
	$gmapOptions = get_option( 'gmap_settings' );
	$gmapAPIkey = $gmapOptions["gmap_api_key"];
	$gmapLanguage = $gmapOptions["gmap_mlang"]; if($gmapLanguage==''){$gmapLanguage='en';}
	$gmapRegion = $gmapOptions["gmap_mreg"]; if($gmapRegion==''){$gmapRegion='GB';}
	$gmapCall = "https://maps.googleapis.com/maps/api/js?v=3&key=" . $gmapAPIkey . "&language=" . $gmapLanguage . "&region=" . $gmapRegion . "&libraries=places,geometry";
	wp_register_script('imc-gmap', $gmapCall);

	wp_register_script(
		'imc-scripts',
		plugin_dir_url( __FILE__ ) . 'js/imc-scripts.js',
		array( 'jquery' )
	);
	wp_localize_script('imc-scripts', 'imcScriptsVars', array(
			'boundsAlert' => __( 'Please provide a location inside municipality limits', 'participace-projekty' ),
			'addressAlert' => __( 'Couldn\'t find an address', 'participace-projekty' ),
			'noResults' => __( 'No results found', 'participace-projekty' ),
			'geoCoderFail' => __( 'Geocoder failed', 'participace-projekty' )
		)
	);
	wp_enqueue_script( 'imc-scripts' );

	wp_register_script( 'add__datetime_picker_js', plugin_dir_url( __FILE__ ) . 'js/jquery.simple-dtpicker.js', array( 'jquery' ), '1.0', TRUE );
	wp_enqueue_script( 'add__datetime_picker_js' );

	// Load libs in new custom post page
	if ( $hook == 'post-new.php' ) {
		if ((isset($_GET['post_type']) && $_GET['post_type'] == 'imc_issues')){
			wp_enqueue_script('imc-gmap');
		}
	}
	// Load libs in edit custom post page
	else if ( $hook == 'post.php' ) {
		if (get_post_type() == 'imc_issues') {
			wp_enqueue_script('imc-gmap');
		}
	}
	// Load libs in status & categories pages
	else if ($hook == 'edit-tags.php' || $hook == 'term.php') {

		if ((isset($_GET['post_type']) && $_GET['post_type'] == 'imc_issues')) {

			// JQuery Color Picker (js)
			wp_register_script( 'spectrum-jquery-color-picker', plugin_dir_url( __FILE__ ) . 'js/spectrum-jquery-color-picker.js', array( 'jquery' ), '1.0', TRUE );
			// Localize script labels
			wp_localize_script('spectrum-jquery-color-picker', 'colorPickerVars', array(
					'chooseLabel' => _x( 'Choose', 'For color picker', 'participace-projekty' ),
					'cancelLabel' => _x( 'Cancel', 'For color picker', 'participace-projekty' )
				)
			);

			wp_localize_script('spectrum-jquery-color-picker', 'catPickerVars', array(
					'titleLabel' => _x( 'Select or upload an image for this category', 'For category image', 'participace-projekty' ),
					'attachLabel' => _x( 'Attach', 'For category image', 'participace-projekty' )
				)
			);

			wp_enqueue_script( 'spectrum-jquery-color-picker' );

		}
	}

	// Load libs in IMC Settings
	else if ($hook == 'settings_page_imc_options') {
		wp_enqueue_script('imc-gmap');
	}

	wp_register_script( 'require-reason', plugin_dir_url( __FILE__ ) . 'js/require-reason.js');
	// Localize script labels
	wp_localize_script('require-reason', 'requireReasonVars', array(
			'categoryAlert' => __( 'Please make sure that you have selected a Category.', 'participace-projekty' ),
			'statusAlert' => __( 'Please make sure that you have selected a Status.', 'participace-projekty' ),
			'reasonAlert' => __( 'Please make sure that you have entered a reason!', 'participace-projekty' )
		)
	);
	wp_enqueue_script('require-reason');
}

add_action( 'admin_enqueue_scripts', 'imc_enqueue_admin_scripts');
