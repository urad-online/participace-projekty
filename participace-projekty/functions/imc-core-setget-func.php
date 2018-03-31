<?php

/**
 * 7.01
 * Get Methods for Status Name, ID and Color
 *
 */

function getCurrentImcStatusName($mypostID){

    $imcstatus_currentterm = get_the_terms($mypostID , 'imcstatus' );

    if ($imcstatus_currentterm) {
        $current_step = $imcstatus_currentterm[0]->name;
    }

    return 	$current_step;

}

function getCurrentImcStatusID($mypostID){

    $imcstatus_currentterm = get_the_terms($mypostID , 'imcstatus' );

    if ($imcstatus_currentterm) {
        $current_step = $imcstatus_currentterm[0]->term_id;
    }

    return 	$current_step;

}

function getCurrentImcStatusColor($mypostID){

    $current_step_id = getCurrentImcStatusID($mypostID);

    $term_color_data = get_option('tax_imcstatus_color_' . $current_step_id);

    return 	$term_color_data;

}

/************************************************************************************************************************/

/**
 * 7.02
 * Returns all available imc_status taxonomy choices
 *
 */

function get_all_imcstatus(){

    // no default values. using these as examples
    $taxonomies = array(
        'imcstatus',
    );

    $args = array(
        'orderby'                => 'id',
        'order'                  => 'ASC',
        'hide_empty'             => false,
        'fields'                 => 'all',
    );

    $terms = get_terms($taxonomies, $args);

    return $terms;
}


/************************************************************************************************************************/

/**
 * 7.03
 * Get page by slug function
 *
 */


function imcplus_get_page_by_slug($slug) {
    if ($pages = get_pages())
        foreach ($pages as $page)
            if ($slug === $page->post_name) return $page;
    return false;
}

/************************************************************************************************************************/

/**
 * 7.04
 * Returns all imccategory terms
 *
 */

function get_all_imccategory( ) {

    // no default values. using these as examples
    $taxonomies = array(
        'imccategory',
    );

    $args = array(
        'orderby'                => 'id',
        'order'                  => 'ASC',
        'hide_empty'             => false,
        'fields'                 => 'all',
        'hierarchical' => true,
        'parent' => 0
    );

    $terms = get_terms($taxonomies, $args);

    return $terms;
}

/************************************************************************************************************************/

/**
 * 7.05
 * Get Archive/Insert/Edit Pages
 *
 */

function getIMCArchivePage(){
    $archive_pages = get_pages(array(
        'hierarchical' => 0,
        'parent' => -1,
        'meta_key' => '_wp_page_template',
        'meta_value' => '/templates/archive-imc_issues.php'
    ));
    return $archive_pages;
}

function getIMCInsertPage(){
    $insert_pages = get_pages(array(
        'hierarchical' => 0,
        'parent' => -1,
        'meta_key' => '_wp_page_template',
        'meta_value' => '/templates/insert-imc_issues.php'
    ));
    return $insert_pages;
}

function getIMCEditPage(){
    $edit_pages = get_pages(array(
        'hierarchical' => 0,
        'parent' => -1,
        'meta_key' => '_wp_page_template',
        'meta_value' => '/templates/edit-imc_issues.php'
    ));
    return $edit_pages;
}

/************************************************************************************************************************/

/**
 * 7.06
 *
 *
 */


function imc_get_issue_logs($issue_id){
    $order_by = 'created';
    global $wpdb;
    $logs_table = $wpdb->prefix . 'imc_logs';
    $select_sql = "SELECT* FROM {$logs_table} WHERE issueid={$issue_id} ORDER BY {$order_by} DESC";

    $logs = $wpdb->get_results($select_sql);
    return $logs;
}

function imc_get_issue_timeline($id) {
    global $wpdb;

    $logsTable = $wpdb->prefix . 'imc_logs';
    $usersTable = $wpdb->prefix . 'users';

    $query = " SELECT a.timeline_title AS title, a.description AS description,
	a.theColor AS color, a.created AS dateUTC,  b.display_name AS name
	FROM $logsTable AS a
	INNER JOIN $usersTable AS b ON a.created_by = b.ID 
	WHERE a.issueid = $id 
	ORDER BY a.created DESC ";

    $sql = $wpdb->get_results( $query );

    foreach ($sql as $entry) {
        $entry->localDate = get_date_from_gmt($entry->dateUTC, 'Y-m-d H:i:s');
        $entry->dateTimestamp = strtotime($entry->localDate);
    }

    return $sql;
}

function imc_get_logs(){
    $order_by = 'created';
    global $wpdb;
    $logs_table = $wpdb->prefix . 'imc_logs';
    $select_sql = "SELECT* FROM {$logs_table} ORDER BY {$order_by} DESC";

    $logs = $wpdb->get_results($select_sql);
    return $logs;
}

function imc_get_api_keys(){
    $order_by = 'created';
    global $wpdb;
    $keys_table = $wpdb->prefix . 'imc_keys';
    $select_sql = "SELECT* FROM {$keys_table} ORDER BY {$order_by} DESC";

    $keys = $wpdb->get_results($select_sql);
    return $keys;
}


/************************************************************************************************************************/

/**
 * 7.07
 * Get user's FirebaseID by userID
 * API use
 */


function imc_getUserFirebaseID($userID){
    global $wpdb;
    $imc_users_firebase_table_name = $wpdb->prefix . 'imc_users_firebase';
    $select_sql = "SELECT* FROM {$imc_users_firebase_table_name} WHERE userid={$userID}";
    $firebaseUser = $wpdb->get_results($select_sql);
    return $firebaseUser;
}