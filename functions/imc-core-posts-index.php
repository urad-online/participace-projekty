<?php

/**
 * 16.01
 * Creates different postIndex after issue post-status transition
 *
 */


function imcplus_create_index_state( $new_status, $old_status, $post ){
    global $wpdb;
    $theUser = get_current_user_id();
    $post_id = $post->ID;
    $post_type = get_post_type($post);

    if ($post_type == 'imc_issues') {

        $imc_posts_index_table_name = $wpdb->prefix . 'imc_posts_index';

        if ( ($new_status == 'publish')  ) {
            $count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $imc_posts_index_table_name WHERE issueid = %d", $post_id ) );

            if($count==0){
                $wpdb->insert(
                    $imc_posts_index_table_name,
                    array(
                        'issueid' => $post_id,
                        'modified' => gmdate("Y-m-d H:i:s",time()),
                        'modified_by' => $theUser,
                        'state' => 1,
                    )
                );
            }else{
                $change_state = array('state' => 1);
                $where = array('issueid' => $post_id);

                $wpdb->update($imc_posts_index_table_name, $change_state, $where);
            }

        }elseif ( ($new_status == 'pending')  ) {

            $count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $imc_posts_index_table_name WHERE issueid = %d", $post_id ) );

            if($count==0){
                $wpdb->insert(
                    $imc_posts_index_table_name,
                    array(
                        'issueid' => $post_id,
                        'modified' => gmdate("Y-m-d H:i:s",time()),
                        'modified_by' => $theUser,
                        'state' => 0,
                    )
                );
            }else{
                $change_state = array('state' => 0);
                $where = array('issueid' => $post_id);

                $wpdb->update($imc_posts_index_table_name, $change_state, $where);
            }

        }elseif ( ($new_status == 'trash')  ) {

            $count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(issueid) FROM $imc_posts_index_table_name WHERE issueid = %d", $post_id ) );

            if($count==0){
                $wpdb->insert(
                    $imc_posts_index_table_name,
                    array(
                        'issueid' => $post_id,
                        'modified' => gmdate("Y-m-d H:i:s",time()),
                        'modified_by' => $theUser,
                        'state' => -2,
                    )
                );
            }else{
                $change_state = array('state' => -2);
                $where = array('issueid' => $post_id);

                $wpdb->update($imc_posts_index_table_name, $change_state, $where);
            }

        }

    }
}

add_action(  'transition_post_status',  'imcplus_create_index_state', 10, 3 );

/************************************************************************************************************************/

/**
 * 16.02
 * Adds modality as post_meta, when the issue created from backend
 *
 */

function imcplus_create_backend_modality( $new_status, $old_status, $post ){
    $post_id = $post->ID;
    // perform actions any time any post changes status
    // and the new status is draft (usually when an issue published from front-end)
    $post_type = get_post_type($post);
    if ($post_type == 'imc_issues') {
        if ( ($old_status == 'draft') || ($old_status == 'auto-draft') ) {
            if (($new_status == 'publish') && is_admin()) {
                $old_modality = get_post_meta($post->ID, "modality", true);
                if(empty($old_modality)) {
                    add_post_meta($post->ID, 'modality', '0', true);
                }
            }
        }
    }


}

add_action(  'transition_post_status',  'imcplus_create_backend_modality', 10, 3 );

/************************************************************************************************************************/

/**
 * 16.03
 * Adds modality as commentmeta, when comment posted backend/frontend
 *
 */

function imcplus_create_comment_modality( $comment_ID, $comment_approved ) {
    $theComment = get_comment( $comment_ID, ARRAY_A );
    $post_id = $theComment['comment_post_ID'];
    $thePost = get_post($post_id);
    $post_type = get_post_type($thePost);



    if ($post_type == 'imc_issues') {
        $old_modality = get_comment_meta($comment_ID, "modality", true);
        if(empty($old_modality)) {
            add_comment_meta($comment_ID,'modality', '0', true);
        }

        $user_info = get_userdata($theComment['user_id']);
        $created_by_admin = 0;
        if($user_info->roles[0] === 'administrator' || $user_info->roles[0] === 'dep_admin') {
            $created_by_admin = 1;
        }
        update_comment_meta( $comment_ID, 'isAdmin', $created_by_admin);
        
    }
}

add_action( 'comment_post', 'imcplus_create_comment_modality', 10, 2 );

?>