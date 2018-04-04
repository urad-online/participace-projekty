<?php


/**
 * 9.01
 * Add Box 'Issues Log'
 *
 */


add_action( 'add_meta_boxes', 'imccreation_logsbox_add' );

function imccreation_logsbox_add(){
	add_meta_box( 'imc_logsbox_id',  __('Logs','participace-projekty'), 'imccreation_logsbox_impl', 'imc_issues', 'normal' , 'core');
}


function imccreation_logsbox_impl(){



	// $post is already set, and contains an object: the WordPress post
	global $post;
	$values = get_post_custom( $post->ID );
	$my_post_id = $post->ID;
	$resultsLogs = imc_get_issue_logs($my_post_id);
	$logs = json_encode($resultsLogs,true);

	?>
	<table id="current_logs_table" class="IMCBackendTableStyle">
		<thead id="headings" class="IMCBackendTableHeaderStyle">
		<tr>
			<th id="timeLog"><?php _e('Date','participace-projekty') ?><span class="dashicons dashicons-sort"></span></th>
			<th id="status_transition"><?php _e('Activity','participace-projekty') ?><span class="dashicons dashicons-sort"></span></th>
			<th id="theUser"><?php _e('User','participace-projekty') ?><span class="dashicons dashicons-sort"></span></th>
			<th id="content"><?php _e('Reason','participace-projekty') ?><span class="dashicons dashicons-sort"></span></th>
		</tr>
		</thead>
		<tbody id="results">
		<!-- this will be auto-populated by functions-->

		<?php

		if ( $resultsLogs ) {
			foreach ($resultsLogs as $logs) { ?>
				<tr>
					<td><?php $timeLocal = get_date_from_gmt($logs->created, 'Y-m-d H:i:s');
						echo esc_html($timeLocal);?></td>
					<td><?php echo esc_html($logs->transition_title);?></td>
					<?php $user_info = get_userdata($logs->created_by);
							$user_name = $user_info->user_login; ?>
					<td><?php echo esc_html($user_name);?></td>
					<td><?php echo esc_html($logs->description);?></td>
				</tr>
			<?php }
		} ?>


		</tbody>
	</table>



<?php
}

/************************************************************************************************************************/

/**
 * 9.02
 * Creates first Log when issue created from frontend without moderate
 *
 */

function imcplus_crelog_frontend_nomoder( $post_id , $step_id , $currentUser_id ){
	global $wpdb;


	$current_step_id = $step_id;
	$quantityTermObject = get_term_by('id', absint($current_step_id), 'imcstatus');
	$current_step_name = $quantityTermObject->name;

	$transition = __( 'Issue published as ', 'participace-projekty' ) . $current_step_name;
	$content = __( 'Issue reported and published', 'participace-projekty' );
	$timeline_label = $current_step_name;
	$option_label = 'tax_imcstatus_color_' . $current_step_id;
	$term_data = get_option($option_label);
	$currentStatusColor = $term_data;
	$theUser = $currentUser_id;
	$currentlang = get_bloginfo('language');

	$imc_logs_table_name = $wpdb->prefix . 'imc_logs';

	$wpdb->insert(
		$imc_logs_table_name,
		array(
			'issueid' => $post_id,
			'stepid' => $current_step_id,
			'transition_title' => $transition,
			'timeline_title' => $timeline_label,
			'theColor' => $currentStatusColor,
			'description' => $content,
			'action' => 'step',
			'state' => 1,
			'created' => gmdate("Y-m-d H:i:s",time()),
			'created_by' => $theUser,
			'language' => $currentlang,
		)
	);

	$likes = get_post_meta($post_id, "imc_likes", true);
	if(empty($likes)) {
		add_post_meta($post_id, 'imc_likes', '0', true); // Initial vote value
	}

}

/************************************************************************************************************************/

/**
 * 9.03
 * Creates first Log when issue created from frontend with backend-moderate (pending->publish)
 *
 */

function imcplus_crelog_frontend_moder( $post ) {
	global $wpdb;
	$post_id = $post->ID;

	// A function to perform when a pending post is published.
	$post_type = get_post_type($post);
	if ($post_type == 'imc_issues') {

		$status_reason_list = get_post_meta($post->ID, "status_reason_textarea", false);
		$category_reason_list = get_post_meta($post->ID, "category_reason_textarea", false);

		if(empty($status_reason_list) && empty($category_reason_list)) {

			$imcstatus_currentterm = get_the_terms($post->ID, 'imcstatus');
			if ($imcstatus_currentterm) {
				$current_step_name = $imcstatus_currentterm[0]->name;
				$current_step_id = $imcstatus_currentterm[0]->term_id;
			}

			$transition = __( 'Issue published as ', 'participace-projekty' ) . $current_step_name;
			$content = __( 'Issue reported and published', 'participace-projekty' );
			$timeline_label = $current_step_name;
			$option_label = 'tax_imcstatus_color_' . $current_step_id;
			$term_data = get_option($option_label);
			$currentStatusColor = $term_data;
			$theUser = $post->post_author;
			$currentlang = get_bloginfo('language');

			$imc_logs_table_name = $wpdb->prefix . 'imc_logs';

			$wpdb->insert(
				$imc_logs_table_name,
				array(
					'issueid' => $post_id,
					'stepid' => $current_step_id,
					'transition_title' => $transition,
					'timeline_title' => $timeline_label,
					'theColor' => $currentStatusColor,
					'description' => $content,
					'action' => 'step',
					'state' => 1,
					'created' => gmdate("Y-m-d H:i:s",time()),
					'created_by' => $theUser,
					'language' => $currentlang,
				)
			);

		}

		$likes = get_post_meta($post->ID, "imc_likes", true);
		if(empty($likes)) {
			add_post_meta($post->ID, 'imc_likes', '0', true); // Initial vote value
		}

	}
}

add_action(  'pending_to_publish',  'imcplus_crelog_frontend_moder', 10, 1 );


/************************************************************************************************************************/

/**
 * 9.04
 * Creates first Log when issue created from backend (draft->publish)
 *
 */


function imcplus_crelog_backend( $new_status, $old_status, $post ){
	global $wpdb;
	$post_id = $post->ID;
	// perform actions any time any post changes status
	// and the new status is draft (usually when an issue published from front-end)
	$post_type = get_post_type($post);
	if ($post_type == 'imc_issues') {

		if ( ($old_status == 'draft') || ($old_status == 'auto-draft') ) {

			if ( ($new_status == 'publish') && is_admin() ) {

				$status_reason_list = get_post_meta($post->ID, "status_reason_textarea", false);
				$category_reason_list = get_post_meta($post->ID, "category_reason_textarea", false);

				if(empty($status_reason_list) && empty($category_reason_list)) {

					$current_step_id = esc_attr(strip_tags($_POST['imcstatus']));
					$quantityTermObject = get_term_by('id', absint($current_step_id), 'imcstatus');
					$current_step_name = $quantityTermObject->name;

					$transition = __( 'Issue published as ', 'participace-projekty' ) . $current_step_name;
					$content = __( 'Issue reported and published', 'participace-projekty' );
					$timeline_label = $current_step_name;
					$option_label = 'tax_imcstatus_color_' . $current_step_id;
					$term_data = get_option($option_label);
					$currentStatusColor = $term_data;
					$theUser = get_current_user_id();
					$currentlang = get_bloginfo('language');

					$imc_logs_table_name = $wpdb->prefix . 'imc_logs';

					$wpdb->insert(
						$imc_logs_table_name,
						array(
							'issueid' => $post_id,
							'stepid' => $current_step_id,
							'transition_title' => $transition,
							'timeline_title' => $timeline_label,
							'theColor' => $currentStatusColor,
							'description' => $content,
							'action' => 'step',
							'state' => 1,
							'created' => gmdate("Y-m-d H:i:s",time()),
							'created_by' => $theUser,
							'language' => $currentlang,
						)
					);

				}

				$likes = get_post_meta($post->ID, "imc_likes", true);
				if(empty($likes)) {
					add_post_meta($post->ID, 'imc_likes', '0', true); // Initial vote value
				}
			}

		}

	}
}

add_action(  'transition_post_status',  'imcplus_crelog_backend', 10, 3 );

/************************************************************************************************************************/


/**
 * 9.05
 * Changes state of Logs when issues moved on trash
 *
 */

function imcplus_changeStateLog_trash( $new_status, $old_status, $post ){
	global $wpdb;
	$post_id = $post->ID;
	// perform actions any time any post changes status
	// and the new status is trash
	$post_type = get_post_type($post);
	if ($post_type == 'imc_issues') {
			if ( ($new_status == 'trash') ) {

				$imc_logs_table_name = $wpdb->prefix . 'imc_logs';
				$change_state = array('state' => 0);
				$where = array('issueid' => $post_id);

				$wpdb->update($imc_logs_table_name, $change_state, $where);

			}
		}
}

add_action(  'transition_post_status',  'imcplus_changeStateLog_trash', 10, 3 );

/************************************************************************************************************************/


/**
 * 9.06
 * Delete Logs and Votes when issue permanently deleted
 *
 */

function imcplus_deleteLogs_afterDeleteIssue( $postid ){
	global $wpdb;
	global $post_type;

	if ( $post_type == 'imc_issues' ){

		$imc_logs_table_name = $wpdb->prefix . 'imc_logs';
		$where = array('issueid' => $postid);

		$wpdb->delete($imc_logs_table_name,$where);

	}

}

function imcplus_deleteVotes_afterDeleteIssue( $postid ){
	global $wpdb;
	global $post_type;

	if ( $post_type == 'imc_issues' ){

		$imc_votes_table_name = $wpdb->prefix . 'imc_votes';
		$where = array('issueid' => $postid);

		$wpdb->delete($imc_votes_table_name,$where);

	}

}

add_action( 'before_delete_post', 'imcplus_deleteLogs_afterDeleteIssue' );
add_action( 'before_delete_post', 'imcplus_deleteVotes_afterDeleteIssue' );

/************************************************************************************************************************/

/**
 * 9.07
 * Changes state of Logs when issues restored from trash
 *
 */

function imcplus_changeStateLog_restore( $new_status, $old_status, $post ){

	global $wpdb;
	$post_id = $post->ID;
	$post_type = get_post_type($post);

	if ($post_type == 'imc_issues') {
		if ( ($old_status == 'trash') ) {

				$imc_logs_table_name = $wpdb->prefix . 'imc_logs';
				$change_state = array('state' => 1);
				$where = array('issueid' => $post_id);

				$wpdb->update($imc_logs_table_name, $change_state, $where);

			}
	}
}

add_action(  'transition_post_status',  'imcplus_changeStateLog_restore', 10, 3 );


/************************************************************************************************************************/

?>