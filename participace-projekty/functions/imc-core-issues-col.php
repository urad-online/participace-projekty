<?php


/**
 * 4.01
 * Add Columns @ Issues Admin Panel
 *
 */


add_filter( 'manage_edit-imc_issues_columns', 'imcplus_admincol_add' ) ;

function imcplus_admincol_add( $columns ) {

	$columns = array(
		'cb' => '<input type="checkbox" />',
		'issue-id' => __( 'Issue ID' , 'participace-projekty' ),
		'title' => __( 'Issue' , 'participace-projekty' ),
		'imccategory' => __( 'Issue Category'  , 'participace-projekty' ),
		'imcstatus' => __( 'Issue Status' , 'participace-projekty'  ),
		'author' => __( 'Author' , 'participace-projekty'  ),
		'comments' => '<span class="vers comment-grey-bubble" title="Comments"><span class="screen-reader-text">'.__('Comments' , 'participace-projekty').'</span></span>',
		'date' => __( 'Date' , 'participace-projekty' )
	);

	return $columns;
}


add_action( 'manage_imc_issues_posts_custom_column', 'imcplus_admincol_manage', 10, 2 );

function imcplus_admincol_manage( $column, $post_id ) {
	global $post;

	switch( $column ) {

		/* If displaying the 'imccategory' column. */
		case 'imccategory' :

			/* Get the terms. */
			$imccategory = get_the_term_list( $post_id, 'imccategory' );

			/* If no imccategory is found, output a default message. */
			if ( empty( $imccategory ) )
				echo __( 'Unknown Category' , 'participace-projekty' );

			/* If there is a imccategory, print it. */
			else
				echo $imccategory;

			break;

		/* If displaying the 'imcstatus' column. */
		case 'imcstatus' :

			/* Get the terms. */
			$imcstatus = get_the_term_list( $post_id, 'imcstatus' );

			//The color of status is in wp_options table so:
			//Returns Array of Term ID's for "tax_imcstatus_color_"
			$term_list = wp_get_post_terms($post_id, 'imcstatus', array("fields" => "ids"));

			if ( $term_list ) {
				foreach ( $term_list as $term_color ) {
					//real label of the saved option
					//tax_imcstatus_color_ + the id of status
					$option_label = 'tax_imcstatus_color_' . $term_color;

					//the desired color code			
					$term_data = get_option($option_label);
					$currentStatusColor = $term_data;
				}
				//if the issue's category admin mail is blank
				//we notify website's admin
			}else{
				$currentStatusColor = 'none';
			}

			/* If no imcstatus is found, output a default message. */
			if ( empty( $imcstatus ) )
				echo __( 'Unknown Status' , 'participace-projekty' );

			/* If there is a imcstatus, print it. */
			else
				echo '<div style="width:20px;height:20px;float:left;margin-right:2px;border-radius:10px;background-color:#' . $currentStatusColor . '"></div>&nbsp;' . $imcstatus ;

			break;

		case 'issue-id' :

			echo '#' . get_the_ID();
			break;

		/* Just break out of the switch statement for everything else. */
		default :
			break;
	}
}

/************************************************************************************************************************/


/**
 * 4.02
 * Make Columns of the IMC Issues sortable
 *
 *
 */

function imcplus_sortablecol_byid( $columns ) {
	$columns['issue-id'] = 'issue-id';
	return $columns;
}

function imcplus_sortablecol_bycategory( $columns ) {
	$columns['imccategory'] = 'imccategory';
	return $columns;
}

function imcplus_sortablecol_bystatus( $columns ) {
	$columns['imcstatus'] = 'imcstatus';
	return $columns;
}

function imcplus_sortablecol_byauthor( $columns ) {
	$columns['author'] = 'author';
	return $columns;
}

add_filter( 'manage_edit-imc_issues_sortable_columns', 'imcplus_sortablecol_byid' );
add_filter( 'manage_edit-imc_issues_sortable_columns', 'imcplus_sortablecol_bycategory' );
add_filter( 'manage_edit-imc_issues_sortable_columns', 'imcplus_sortablecol_bystatus' );
add_filter( 'manage_edit-imc_issues_sortable_columns', 'imcplus_sortablecol_byauthor' );


/************************************************************************************************************************/


/**
 * 4.03
 * Enable Filtering @ issues' admin columns
 *
 */

/*********************************************** Filter by imccategory **********************************************/

//Display filtering by imccategory
add_action('restrict_manage_posts', 'imcplus_filtercol_bycategory_add');

function imcplus_filtercol_bycategory_add() {
	global $typenow;
	$post_type = 'imc_issues';
	$taxonomy  = 'imccategory';
	if ($typenow == $post_type) {
		$selected      = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
		$info_taxonomy = get_taxonomy($taxonomy);
		wp_dropdown_categories(array(
			'show_option_all' => __('Show All Categories' , 'participace-projekty'),
			'taxonomy'        => $taxonomy,
			'name'            => $taxonomy,
			'orderby'         => 'name',
			'selected'        => $selected,
			'show_count'      => true,
			'hide_empty'      => false,
		));
	};
}

//Filter posts by imccategory implementation
add_filter('parse_query', 'imcplus_filtercol_bycategory_impl');

function imcplus_filtercol_bycategory_impl($query) {
	global $pagenow;
	$post_type = 'imc_issues';
	$taxonomy  = 'imccategory';
	$q_vars    = &$query->query_vars;
	if ( $pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0 ) {
		$term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
		$q_vars[$taxonomy] = $term->slug;
	}
}


/*********************************************** Filter by imcstatus **********************************************/

//Display filtering by imcstatus
add_action('restrict_manage_posts', 'imcplus_filtercol_bystatus_add');

function imcplus_filtercol_bystatus_add() {
	global $typenow;
	$post_type = 'imc_issues';
	$taxonomy  = 'imcstatus';
	if ($typenow == $post_type) {
		$selected      = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
		$info_taxonomy = get_taxonomy($taxonomy);
		wp_dropdown_categories(array(
			'show_option_all' => __('Show All Statuses' , 'participace-projekty'),
			'taxonomy'        => $taxonomy,
			'name'            => $taxonomy,
			'orderby'         => 'name',
			'selected'        => $selected,
			'show_count'      => true,
			'hide_empty'      => false,
		));
	};
}

//Filter posts by imcstatus implementation
add_filter('parse_query', 'imcplus_filtercol_bystatus_impl');

function imcplus_filtercol_bystatus_impl($query) {
	global $pagenow;
	$post_type = 'imc_issues';
	$taxonomy  = 'imcstatus';
	$q_vars    = &$query->query_vars;
	if ( $pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0 ) {
		$term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
		$q_vars[$taxonomy] = $term->slug;
	}
}


/*********************************************** Filter by author **********************************************/

//Display filtering by imcstatus
add_action('restrict_manage_posts','imcplus_filtercol_byauthor_add');

function imcplus_filtercol_byauthor_add(){

	//execute only on imc_issues
	global $post_type;
	if($post_type == 'imc_issues'){
		//get a listing of all users that are 'author' or above
		$user_args = array(
			'show_option_all'   => __('Show All Authors' , 'participace-projekty'),
			'orderby'           => 'display_name',
			'order'             => 'ASC',
			'name'              => 'aurthor_admin_filter',
			'who'               => 'authors',
			'include_selected'  => true
		);
		//determine if we have selected a user to be filtered by already
		if(isset($_GET['aurthor_admin_filter'])){
			//set the selected value to the value of the author
			$user_args['selected'] = intval(sanitize_text_field($_GET['aurthor_admin_filter']), 10);
		}

		//display the users as a drop down
		wp_dropdown_users($user_args);
	}

}

//Filter posts by author implementation
add_action('pre_get_posts','imcplus_filtercol_byauthor_impl');

function imcplus_filtercol_byauthor_impl($query){

	global $post_type, $pagenow;

	//if we are currently on the edit screen of the post type listings
	if($pagenow == 'edit.php' && $post_type == 'imc_issues'){

		if(isset($_GET['aurthor_admin_filter'])){

			//set the query variable for 'author' to the desired value
			$author_id = sanitize_text_field($_GET['aurthor_admin_filter']);

			//if the author is not 0 (meaning all)
			if($author_id != 0){
				$query->query_vars['author'] = $author_id;
			}

		}
	}
}



?>