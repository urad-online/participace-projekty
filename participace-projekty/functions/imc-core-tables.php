<?php

/**
 * 17.01
 * Create Logs Table imc_logs
 *
 */

function imc_create_logs_table() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $imc_logs_table_name = $wpdb->prefix . "imc_logs";

    $sql_create_table = "CREATE TABLE IF NOT EXISTS $imc_logs_table_name (
        id INT(11) unsigned NOT NULL auto_increment,
        issueid INT(11) NOT NULL,
        stepid TEXT NOT NULL,
        transition_title TEXT NOT NULL,
        timeline_title TEXT NOT NULL,
        theColor VARCHAR(6),
        description TEXT NOT NULL,
        action VARCHAR(512) NOT NULL,
        created DATETIME NOT NULL,
        updated DATETIME NOT NULL,
        ordering INT(11) NOT NULL,
        state TINYINT(1) NOT NULL,
        checked_out INT(11) NOT NULL,
        checked_out_time DATETIME NOT NULL default '0000-00-00 00:00:00',
        created_by INT(11) NOT NULL,
        language VARCHAR(255) NOT NULL,
        updated_by INT(11) NOT NULL,
        PRIMARY KEY  (id)
		) $charset_collate; ";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_create_table);
}

/************************************************************************************************************************/

/**
 * 17.02
 * Create Tokens Table imc_tokens
 *
 */

function imc_create_tokens_table() {

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . "imc_tokens";

    $wpdb->query("DROP TABLE IF EXISTS $table_name");

    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name
            (
                id INT(11) NOT NULL AUTO_INCREMENT,
                key_id INT(11) NOT NULL,
                user_id INT(11) NOT NULL,
                json_size INT(11) NOT NULL,
                method VARCHAR(7) NOT NULL,
                token VARCHAR(512) NOT NULL,
                unixtime VARCHAR(12) NOT NULL,
                created DATETIME NOT NULL,
                PRIMARY KEY  (id),
                UNIQUE KEY id (id)
            ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
}

/************************************************************************************************************************/

/**
 * 17.03
 * Create Keys Table imc_keys
 *
 */

function imc_create_keys_table() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . "imc_keys";

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT(11) unsigned NOT NULL auto_increment,
        title VARCHAR(255) NOT NULL,
        skey VARCHAR(255) NOT NULL,
        ordering INT(11) NOT NULL,
        created_by INT(11) NOT NULL,
        created DATETIME NOT NULL,
        updated DATETIME NOT NULL,
        updated_by INT(11) NOT NULL,
        quota INT(11) NOT NULL,
        PRIMARY KEY  (id)
		) $charset_collate; ";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

/************************************************************************************************************************/

/**
 * 17.04
 * Create Votes Table imc_votes
 *
 */

function imc_create_votes_table() {

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . "imc_votes";

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT(11) unsigned NOT NULL auto_increment,
        issueid INT(11) NOT NULL,
        created DATETIME NOT NULL,
        created_by INT(11) NOT NULL,
        modality INT(11) DEFAULT 0 NOT NULL,
        PRIMARY KEY  (id)
		) $charset_collate; ";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

/************************************************************************************************************************/

/**
 * 17.05
 * Create Posts Index Table imc_posts_index
 *
 */

function imc_create_posts_index_table() {

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . "imc_posts_index";

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT(11) unsigned NOT NULL auto_increment,
        issueid INT(11) NOT NULL,
        modified DATETIME NOT NULL,
        modified_by INT(11) NOT NULL,
        state INT(11) NOT NULL,
        PRIMARY KEY  (id)
		) $charset_collate; ";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

/************************************************************************************************************************/

/**
 * 17.06
 * Create Users slogin Table imc_users_slogin
 *
 */

function imc_create_users_slogin_table() {

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . "imc_users_slogin";

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT(11) unsigned NOT NULL auto_increment,
        userid INT(11) unsigned NOT NULL,
        provider_name VARCHAR (255) NOT NULL,
        provider_uid VARCHAR (255) NOT NULL,
        PRIMARY KEY  (id)
		) $charset_collate; ";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

/************************************************************************************************************************/

/**
 * 17.07
 * Create Firebase Users Table imc_users_firebase
 *
 */

function imc_create_users_firebase_table() {

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . "imc_users_firebase";

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT(11) unsigned NOT NULL auto_increment,
        userid INT(11) unsigned NOT NULL,
        firebaseid VARCHAR (255) NOT NULL,
        topicid INT(11) NOT NULL,
        PRIMARY KEY  (id)
		) $charset_collate; ";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

