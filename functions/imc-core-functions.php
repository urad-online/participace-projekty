<?php

/**
 * 6.01
 * Time ago
 *
 * Function that displays "time ago" by calling the_time() in a post
 *
 * @param int $time
 * @return string
 */

function imc_calculate_relative_date($post_time) {

	$duration = 180;
	$human_time = '';
	$time_now = date('U');

	// use human time if less that $duration days ago (180 days by default)
	// 60 seconds * 60 minutes * 24 hours * $duration days
	if ( $post_time > $time_now - ( 60 * 60 * 24 * $duration ) ) {
		$human_time = sprintf( _x( '%s ago', 'relative date', 'participace-projekty'), human_time_diff( $post_time, current_time( 'timestamp' ) ) );
	} else {
		$human_time = date_i18n( get_option( 'date_format' ), $post_time);

	}

	return $human_time;

}


/**
 * 6.02
 *
 * Add Issue ID & Issue Author right after the Issue Title (@ backend)
 *
 */

function imc_add_issue_id_after_title() {

	$post_type = get_post_type( );
	$post_id = get_the_ID();

	$author_id = get_post_field('post_author',$post_id);
	$author_name = get_userdata($author_id)->display_name;
	$likes = get_post_meta($post_id,'imc_likes', true);
	$address = get_post_meta($post_id,'imc_address', true);

	if($post_type=='imc_issues'){
		echo '<h4 id="issue-id-above-title">' . '#' . intval(get_the_ID(),10)  . '</h4>';

		echo '<h4 class="dashicons-before dashicons-admin-users" id="issue-author-above-title">' . esc_html($author_name)  . '</h4>';

		echo '<h4 class="dashicons-before dashicons-thumbs-up" id="issue-likes-above-title">' . intval($likes,10)  . '</h4>';

		echo '<h4 class="dashicons-before dashicons-location" id="issue-address-above-title">' . esc_html($address)  . '</h4>';
	}
}

add_action( 'edit_form_after_title', 'imc_add_issue_id_after_title');


/**
 * 6.03
 * Create select box with imccategory options
 * for insert page
 */

function imc_insert_cat_dropdown( $taxonomy = 'my_custom_taxonomy', $selected_term_id = 0) {

	function create_select_with_grandchildren( $fieldName, $selected_term_id  ) {
		$args = array('hide_empty' => false, 'hierarchical' => true, 'parent' => 0);
		$terms = get_terms('imccategory', $args);

		$html = '';
		$html .= '<select name="' . $fieldName . '" id="'.$fieldName.'"class="' . $fieldName . ' "' . '>';

		$html .= '<option value="" class="imc-CustomOptionDisabledStyle" disabled selected>'.__('Select a category','participace-projekty').'</option>';

		foreach ( $terms as $term ) {
			$selected = ((!empty( $selected_term_id)) && ( $selected_term_id == $term->term_id )) ? "selected" : "";
			$html .= '<option class="imc-CustomOptionParentStyle" '.$selected.' value="' . $term->term_id . '" >'.$term->name.'</option>';

			$args = array(
				'hide_empty'    => false,
				'hierarchical'  => true,
				'parent'        => $term->term_id
			);
			$childterms = get_terms('imccategory', $args);

			foreach ( $childterms as $childterm ) {
				$html .= '<option class="imc-CustomOptionChildStyle" value="' . $childterm->term_id . '">&nbsp; ' . $childterm->name . '</option>';

				$args = array('hide_empty' => false, 'hierarchical'  => true, 'parent' => $childterm->term_id);
				$granchildterms = get_terms('imccategory', $args);

				foreach ( $granchildterms as $granchild ) {
					$html .= '<option class="imc-CustomOptionGrandchildStyle" value="' . $granchild->term_id . '">&nbsp;&nbsp; ' . $granchild->name . '</option>';
				}
			}
		}
		$html .=  "</select>";

		return $html;
	}

	$selector = create_select_with_grandchildren( $taxonomy, $selected_term_id);
	return $selector;

}


/**
 * 6.04
 * Rename Featured Image Metabox and change its position
 *
 */

add_action( 'do_meta_boxes', 'imc_rename_featured_img_metabox');

function imc_rename_featured_img_metabox() {
	remove_meta_box( 'postimagediv', 'imc_issues', 'side' );
	add_meta_box( 'postimagediv', __( 'Issue Image', 'participace-projekty' ), 'post_thumbnail_meta_box', 'imc_issues', 'side', 'high' );
}


/**
 * 6.05
 * Upload Image to Gallery from front-end form
 *
 */



function imc_filename_rename_to_hash( $filename ) {
	$info = pathinfo( $filename );
	$ext  = empty( $info['extension'] ) ? '' : '.' . $info['extension'];
	$name = basename( $filename, $ext );
	return md5( $name ) . $ext;
}

function imc_upload_img($file = array(), $parent_post_id, $issue_title, $orientation = null) {

	require_once( ABSPATH . 'wp-admin/includes/admin.php' );

	add_filter( 'sanitize_file_name', 'imc_filename_rename_to_hash', 10 );
	$file_return = wp_handle_upload( $file, array(
		'test_form' => false,
		'unique_filename_callback' => 'imc_rename_attachment' // Use this to rename photo
	) );
	remove_filter( 'sanitize_file_name', 'imc_filename_rename_to_hash', 10 );

	if( isset( $file_return['error'] ) || isset( $file_return['upload_error_handler'] ) ) {
		return false;
	} else {

		$filename = $file_return['file'];

		if ($orientation) {
			imc_fix_img_orientation( $filename, $file_return['type'], $orientation );
		}

		$attachment = array(
			'post_mime_type' => $file_return['type'],
			'post_title' => mb_convert_encoding(preg_replace( '/\.[^.]+$/', '', basename( $filename ) ), "UTF-8"),
			'post_content' => '',
			'post_status' => 'inherit',
			'guid' => $file_return['url']
		);

		$attachment_id = wp_insert_attachment( $attachment, $file_return['url'], $parent_post_id );
		require_once(ABSPATH . 'wp-admin/includes/image.php');

		$attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );

		wp_update_attachment_metadata( $attachment_id, $attachment_data );

		if( 0 < intval( $attachment_id, 10 ) ) {
			return $attachment_id;
		}

	}
	return false;
}


function imc_fix_img_orientation($file, $type, $ori ) {

	include_once( ABSPATH . 'wp-admin/includes/image-edit.php' );

	switch ( $ori ) {
		case 3:
			$orientation = -180;
			break;
		case 6:
			$orientation = -90;
			break;
		case 8:
		case 9:
			$orientation = -270;
			break;
		default:
			$orientation = 0;
			break;
	}

	switch ( $type ) {
		case 'image/jpeg':
		case 'image/jpg':
			$image = imagecreatefromjpeg( $file );
			break;
		case 'image/png':
			$image = imagecreatefrompng( $file );
			break;
		case 'image/gif':
			$image = imagecreatefromgif( $file );
			break;
		default:
			$image = false;
			break;
	}

	if ($image) {
		$rotated = imagerotate( $image, $orientation, 0 );
		if ( is_resource( $rotated ) ) {
			imagedestroy( $image );
			$image = $rotated;
		}
		switch ( $type ) {
			case 'image/jpeg':
				imagejpeg( $image, $file, apply_filters( 'jpeg_quality', 90, 'edit_image' ) );
				break;
			case 'image/png':
				imagepng($image, $file );
				break;
			case 'image/gif':
				imagegif($image, $file );
				break;
		}
	}
}


/**
 * 6.06
 * Remove Participace na projektech menu and admin bar "New Issue" for subscribers
 *
 */

/* Remove 'New issue' submenu from the admin bar (only for non admins) */
function imc_remove_new_issue_link_from_admin_bar(){
	global $wp_admin_bar;

	if(!current_user_can('administrator')  ) {

		$wp_admin_bar->remove_node( 'new-imc_issues' );
		$wp_admin_bar->remove_node( 'edit' );

	}
}

/* Remove 'Participace na projektech' menu from dashboard (only for non admins) */
function imc_remove_site_link_from_admin_bar() {

	if(! (  current_user_can('administrator') || current_user_can('dep_admin') ) ) {
		remove_menu_page( 'edit.php?post_type=imc_issues' );
	}
}

add_action( 'admin_bar_menu', 'imc_remove_new_issue_link_from_admin_bar', 999 );
add_action( 'admin_menu', 'imc_remove_site_link_from_admin_bar');


/**
 * 6.07
 * Custom Pagination links for overview issues
 *
 */


if ( ! function_exists('imc_paginate') ) {
	function imc_paginate($cb_qry = NULL, $my_paged , $page, $order, $view, $status, $category, $keyword ) {

		$cb_paged2 = $my_paged;

		if ( $cb_qry == NULL ) {
			global $wp_query;
			$cb_total = $GLOBALS['wp_query']->max_num_pages;
			$cb_paged = get_query_var('paged');
		} else {
			if ( is_page() ) {
				$cb_total = $cb_qry->max_num_pages;
				$cb_pagination_type = 'n';
				$cb_paged = get_query_var('page');
			} else {
				global $wp_query;
				$cb_paged = get_query_var('paged');
				$cb_total = $GLOBALS['wp_query']->max_num_pages;
			}
		}

		$cb_pagination = paginate_links(array(
			'base' => preg_replace('/\?.*/', '/', get_pagenum_link(1)) . '%_%',
			'current' => max(1, $cb_paged2),
			'total' => $cb_total,
			'mid_size' => 2,
			'type' => 'list',
			'prev_text' => '<i class="material-icons md-24">chevron_left</i>',
			'next_text' => '<i class="material-icons md-24">chevron_right</i>',
			'add_args' => array(
				'ppage' => $page,
				'sorder' => $order,
				'view' => $view,
				'sstatus' => $status,
				'scategory' => $category,
				'keyword' => $keyword,
			)
		));

		echo '<nav class="imc-PaginationStyle">' . $cb_pagination  .'</nav>';
	}
}


/**
 * 6.08
 * Function that checks if message @ subscriber is necessary
 *
 */

function imc_show_issue_message($post_id, $current_user){

	$editMessage = __('You cannot longer edit your issue because its status has changed', 'participace-projekty');
	$moderationMessage = __('This issue is under moderation and is not yet published', 'participace-projekty');

	$my_issue = get_post($post_id);
	$author_id = intval($my_issue->post_author, 10);

	if ($author_id !== $current_user) {
		return false;
	} else {

		if (get_post_status($post_id) == 'pending') {
			return $moderationMessage;
		} else if (!pb_user_can_edit($post_id, $current_user)) {
			return $editMessage;
		}

		return false;
	}
}


/**
 * 6.09
 * Function that checks if user can edit an issue
 * basically checks if current user is issue's author
 * and if status changed
 */

function imc_user_can_edit($post_id, $current_user) {

	$status_terms = get_terms( 'imcstatus' , array( 'hide_empty' => 0 , 'orderby' => 'id', 'order' => 'ASC') );
	$first_status = $status_terms[0]->term_id;

	// Issue is not current user's
	$my_issue = get_post($post_id);
	$author_id = intval($my_issue ->post_author, 10); // Author's id of current #post

	if($author_id == $current_user) {
		return getCurrentImcStatusID($post_id) == $first_status ? true : false;
	}

	return false;
}


/**
 * 6.10
 * Function that enables Sessions on wordpress
 *
 */


add_action('init', 'imc_start_session', 1);
add_action('wp_logout', 'imc_destroy_session');
add_action('wp_login', 'imc_destroy_session');

function imc_start_session() {
	if(!session_id()) {
		session_start();
	}
}

function imc_destroy_session() {
	session_destroy ();
}


/**
 * 6.11
 * Calculates the root path of the plugin
 *
 */

function imc_calculate_plugin_base_url() {
	$url_full_path = plugin_basename( __FILE__ );
	$url_pieces = explode("/", $url_full_path);
	return plugin_dir_url( '' ). $url_pieces[0];
}


/**
 * 6.12
 * Creates Custom Role "Department Admin"
 *
 */

function imc_add_dep_admin_role() {
	add_role( 'dep_admin', 'Department Admin',
		array(
			'read' => true,
			'level_0' => true,
		)
	);
}

add_action( 'init', 'imc_add_dep_admin_role');


/**
 * 6.13
 * Capabilities about imccategory
 *
 */

function imc_add_capabilities_to_admin() {
	$role = get_role( 'administrator' );

	$role->add_cap( 'manage_imc_issues' );
	$role->add_cap( 'edit_imc_issues' );

	$role->add_cap( 'publish_issues' );
	$role->add_cap( 'edit_issues' );
	$role->add_cap( 'edit_others_issues' );
	$role->add_cap( 'delete_issues' );
	$role->add_cap( 'delete_others_issues' );
	$role->add_cap( 'read_private_issues' );
	$role->add_cap( 'edit_issue' );
	$role->add_cap( 'delete_issue' );
	$role->add_cap( 'read_issue' );
	unset( $role );

}

function imc_add_capabilities_to_dep_admin() {
	$role = get_role( 'dep_admin' );

	$role->add_cap( 'manage_imc_issues' );
	$role->add_cap( 'edit_imc_issues' );

	$role->add_cap( 'publish_issues' );
	$role->add_cap( 'edit_issues' );
	$role->add_cap( 'edit_others_issues' );
	$role->add_cap( 'delete_issues' );
	$role->add_cap( 'delete_others_issues' );
	$role->add_cap( 'read_private_issues' );
	$role->add_cap( 'edit_issue' );
	$role->add_cap( 'delete_issue' );
	$role->add_cap( 'read_issue' );

	$role->add_cap( 'upload_files' );

	unset( $role );

}



function imc_add_capabilities_to_subscriber() {
	$role = get_role( 'subscriber' );

	$role->add_cap( 'edit_imc_issues' );
	$role->add_cap( 'read_private_issues' );

	unset( $role );

}

function imc_add_capabilities_to_editor() {
	$role = get_role( 'editor' );

	$role->add_cap( 'edit_imc_issues' );

	$role->add_cap( 'publish_issues' );
	$role->add_cap( 'edit_issues' );
	$role->add_cap( 'read_private_issues' );
	$role->add_cap( 'edit_issue' );
	$role->add_cap( 'read_issue' );
	unset( $role );

}

function imc_add_capabilities_to_author() {
	$role = get_role( 'author' );

	$role->add_cap( 'edit_imc_issues' );

	$role->add_cap( 'publish_issues' );
	$role->add_cap( 'edit_issues' );
	$role->add_cap( 'read_private_issues' );
	$role->add_cap( 'edit_issue' );
	$role->add_cap( 'read_issue' );

	unset( $role );

}

function imc_add_capabilities_to_contributor() {
	$role = get_role( 'contributor' );

	$role->add_cap( 'edit_imc_issues' );
	$role->add_cap( 'read_private_issues' );

	unset( $role );

}

add_action( 'init', 'imc_add_capabilities_to_admin');
add_action( 'init', 'imc_add_capabilities_to_dep_admin');
add_action( 'init', 'imc_add_capabilities_to_subscriber');

add_action( 'init', 'imc_add_capabilities_to_editor');
add_action( 'init', 'imc_add_capabilities_to_author');
add_action( 'init', 'imc_add_capabilities_to_contributor');


/**
 * 6.14
 * Hides imcstatus from quick edit
 *
 */

function imc_hide_quick_edit_status(){
	global $post_type;
	if (get_post_type() === 'imc_issues') {
		?>
        <style type="text/css">
            .inline-edit-tags, .inline-edit-group {
                display: none !important;
            }
        </style>
		<?php
	}
}
add_action( 'admin_head-edit.php', 'imc_hide_quick_edit_status');




/**
 * 6.15
 * Hides status radio choice (private-public-protected)
 *
 */

function imc_remove_quick_edit_status(){
	if (get_post_type() === 'imc_issues') {
		?>

        <script>
            jQuery(document).ready(function ($) {

                jQuery('.edit-visibility').remove();
                jQuery('#post-visibility-select').remove();
            });
        </script>
		<?php
	}
}

add_action( 'post_submitbox_misc_actions', 'imc_remove_quick_edit_status');


/**
 * 6.16
 * Unsets View link @ quick edit for imccategory & imcstatus
 *
 */

function imc_remove_quick_edit_taxonomy_view_links($actions ){
	unset($actions['view']);
	return $actions;
}

add_filter( 'imccategory_row_actions', 'imc_remove_quick_edit_taxonomy_view_links', 10, 1 );
add_filter( 'imcstatus_row_actions', 'imc_remove_quick_edit_taxonomy_view_links', 10, 1 );


/**
 * 6.17
 *
 * Social Login Implementation
 */


function imc_check_slogin_use(){
	$generaloptions = get_option( 'general_settings' );
	$sloginOption = $generaloptions["slogin_use"];
	if($sloginOption == 2){ remove_action( 'login_footer', 'imc_add_slogin_buttons' );}
	else{add_action( 'login_footer', 'imc_add_slogin_buttons' );}
}

add_action( 'login_form', 'imc_check_slogin_use' );



function imc_add_slogin_buttons() { ?>

    <style>
        .imc-slogin-btn-style {
            height: 40px;
            line-height: 40px;
            font-family: 'Roboto', sans-serif;
            font-weight: 500;
            display: inline-block;
            padding-right: 16px;
            text-align: center;
            font-size: 14px;
            text-decoration: none;
            white-space: nowrap;
            border-radius: 2px;
            box-sizing: border-box;
            text-transform: none;
            margin: 12px 6px 12px 6px;
            box-shadow:0 2px 2px rgba(0,0,0,0.23), inset 0 -2px 2px rgba(0,0,0,0.12);
        }

        .imc-slogin-fb-btn-style {
            background: #4862A3;
            color: #dddddd;
            color: rgba(255,255,255,0.6);
        }
        .imc-slogin-fb-btn-style:hover {
            color: #ffffff;
            color: rgba(255,255,255,1);
        }

        .imc-slogin-google-btn-style {
            background: #ffffff;
            color: #333333;
            color: rgba(0,0,0,0.54);
        }
        .imc-slogin-google-btn-style:hover {
            color: #111111;
            color: rgba(0,0,0,0.87);
        }

        .imc_slogin_glyph_style {
            width: 18px;
            height: 18px;
            vertical-align: middle;
            padding-right: 8px;
            padding-left: 12px;
        }

    </style>

    <p style="text-align: center;">
        <a class="imc-slogin-btn-style imc-slogin-fb-btn-style" href="<?php echo imc_calculate_plugin_base_url() . '/hybridauth/slogin.php?provider=Facebook'?>">
            <img class="imc_slogin_glyph_style" src="<?php echo imc_calculate_plugin_base_url() . '/img/ic_fb.png'?>">
            SIGN IN WITH FACEBOOK</a>
        <a class="imc-slogin-btn-style imc-slogin-google-btn-style" href="<?php echo imc_calculate_plugin_base_url() . '/hybridauth/slogin.php?provider=Google'?>">
            <img class="imc_slogin_glyph_style" src="<?php echo imc_calculate_plugin_base_url() . '/img/ic_google.png'?>">
            SIGN IN WITH GOOGLE</a>
    </p>

<?php }





function imc_check_unique_name($username)
{
	$uname = $username;
	$i = 0;
	$name = '';
	while($uname)
	{
		$name = ($i == 0) ? $username : $username.'-'.$i;

		global $wpdb;
		$usersTable = $wpdb->prefix . 'users';
		$query = " SELECT users.user_login
	        FROM $usersTable AS users
	        WHERE users.user_login = $name
	        ";

		$uname = $wpdb->get_results( $query );
		$i++;

	}
	return $name;
}

function imc_create_dummy_mail(){

	// array of dummy top-level domains
	$tlds = array("cccom", "nnnet", "gggov", "ooorg", "eeedu", "bbbiz", "iiinfo");

	// string of possible characters
	$char = "0123456789abcdefghijklmnopqrstuvwxyz";

	// choose random lengths for the username ($ulen) and the domain ($dlen)
	$ulen = mt_rand(5, 10);
	$dlen = mt_rand(7, 17);

	// get $ulen random entries from the list of possible characters
	// these make up the username (to the left of the @)
	$a = "";
	for ($i = 1; $i <= $ulen; $i++) {
		$a .= substr($char, mt_rand(0, strlen($char)), 1);
	}

	$a .= "@";

	// now get $dlen entries from the list of possible characters
	// this is the domain name (to the right of the @, excluding the tld)
	for ($i = 1; $i <= $dlen; $i++) {
		$a .= substr($char, mt_rand(0, strlen($char)), 1);
	}

	$a .= ".";

	// Finally, pick a random top-level domain and stick it on the end
	$a .= $tlds[mt_rand(0, (sizeof($tlds)-1))];

	return $a;
}

function imc_is_string_english($str)
{
	if (strlen($str) != strlen(utf8_decode($str))) {
		return false;
	} else {
		return true;
	}
}


// Returns a file size limit in bytes based on the PHP upload_max_filesize
// and post_max_size
function imc_file_upload_max_size() {
	static $max_size = -1;

	if ($max_size < 0) {
		// Start with post_max_size.
		$max_size = imc_parse_size(ini_get('post_max_size'));

		// If upload_max_size is less, then reduce. Except if upload_max_size is
		// zero, which indicates no limit.
		$upload_max = imc_parse_size(ini_get('upload_max_filesize'));
		if ($upload_max > 0 && $upload_max < $max_size) {
			$max_size = $upload_max;
		}
	}
	return $max_size;
}

function imc_parse_size($size) {
	$unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
	$size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
	if ($unit) {
		// Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
		return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
	}
	else {
		return round($size);
	}
}

// Social Login functions START
// Get the user data from database by provider name and provider user id
function imc_slogin_get_user($provider_name, $provider_user_id )
{
	global $wpdb;
	$usersTable = $wpdb->prefix . 'users';
	$userSloginTable = $wpdb->prefix . 'imc_users_slogin';

	// Select the required fields from the table.
	$query = "SELECT *

	FROM $usersTable AS users
	INNER JOIN $userSloginTable AS userSlogin ON users.ID = userSlogin.userid

	WHERE userSlogin.provider_name = '$provider_name' AND userSlogin.provider_uid = '$provider_user_id'";

	$sql = $wpdb->get_results( $query );

	return $sql[0];
}


// Create slogin user
function imc_create_new_slogin_user( $provider_name, $user )
{

	$args = array();
	// Generate a random password for the user
	$args['user_pass'] = md5( str_shuffle( "0123456789abcdefghijklmnoABCDEFGHIJ" ) );
	$args['role'] = 'subscriber';
	$args['user_registered'] = '';

	$usernameExists = imc_is_string_english($user->displayName);

	$args['user_email'] = ($user->email ? $user->email : imc_create_dummy_mail());

	if ($usernameExists)
	{
		$args['display_name'] = $user->displayName;
	}
	else
	{
		$email_handle = explode("@", $args['user_email']);
		$args['display_name'] = $email_handle[0];
	}

	$args['user_login'] = str_replace(" ", "-", $args['display_name']).'-'.$provider_name;
	$args['user_login'] = imc_check_unique_name($args['user_login']);

	$result = wp_insert_user( $args );

	if (!$result) { return false; }

	if ($user->phone) {add_user_meta( $result, 'imc-phone', $user->phone );}
	if ($user->address) {add_user_meta( $result, 'imc-address', $user->address );}

	global $wpdb;
	$userSloginTable = $wpdb->prefix . 'imc_users_slogin';
	$wpdb->insert(
		$userSloginTable,
		array(
			'userid' => $result,
			'provider_name' => $provider_name,
			'provider_uid' => $user->identifier
		)
	);

	return $result;
}

// Modify wp user with slogin info
function imc_modify_wp_slogin_user( $provider_name, $user )
{

	$wp_user = get_user_by('email', $user->email);

	if (!$wp_user) { return false; }

	$id = $wp_user->ID;

	global $wpdb;
	$userSloginTable = $wpdb->prefix . 'imc_users_slogin';
	$wpdb->insert(
		$userSloginTable,
		array(
			'userid' => $id,
			'provider_name' => $provider_name,
			'provider_uid' => $user->identifier
		)
	);

	return $id;
}

// Social Login functions END

?>
