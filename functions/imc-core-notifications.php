<?php


/**
 * 8.01
 * Email Notification when issue created (frontend)
 *
 */


function imcplus_mailnotify_4submit($post_id,$imccategory_id, $final_address){
	
	//GET OPTIONS
	$notifyoptions = get_option( 'notifications_settings' );
	$optionNewUser = $notifyoptions["notify_new_to_user"];
	$optionNewAdmin = $notifyoptions["notify_new_to_admin"];
	
	$headers = array('Content-Type: text/html; charset=UTF-8');

	//Usefull things about the issue
	$issue_title = get_the_title( $post_id );
	$issue_permalink = get_permalink( $post_id );
	$issue_address = $final_address;
	$issue_category_id = $imccategory_id;
	$get_issue_category = get_term_by( 'id', absint( $issue_category_id ), 'imccategory' );
	$issue_category = $get_issue_category->name;
		
	// Recipients ($admin_email & $author_email)
	$admin_email = get_option( 'admin_email' );//site administrator's email
    $catadmin_email = get_option( "tax_imccategory_mail_$issue_category_id");//category mail
	//the author's email
	$post_tmp = get_post($post_id);
	$author_id = $post_tmp ->post_author;//author's id of current #post
	$author_name = get_userdata($author_id)->display_name; //author's name by id
	$author_email = get_userdata($author_id)->user_email; //author's email by id
		
				
	// Email subject for admin
	$subject_to_admin = __('New issue submitted by: ','participace-projekty') .  $author_name;

	// Email subject for author
	$subject_to_author = __('You submitted a new issue to IMC with title: ','participace-projekty') . ' ' . $issue_title;
		
	$message_to_admin =	 __('New issue submitted, classified as: ','participace-projekty') . ' '
							. $issue_category . '.'
							. '<br/>' 
							. __('The issue','participace-projekty') . ' '
							. $issue_title . ' '
							. __('located at: ','participace-projekty') . ' '
							. $issue_address . '.'
							. '<br/>' 
							. __('See the issue at: ','participace-projekty') . ' '
							. $issue_permalink
							;
	
		
	$message_to_author = __('You submitted a new issue, classified as: ','participace-projekty') . ' '
							. $issue_category . '.'
							.'<br/>' 
							. __('You can see your issue at: ','participace-projekty') . ' '
							. $issue_permalink
							;
	
	if($optionNewAdmin != 2){
		//Sending mail - warn admin that we have a new issue						
		if($catadmin_email!=''){
			wp_mail( $catadmin_email, $subject_to_admin, $message_to_admin, $headers);
		}else{
			wp_mail( $admin_email, $subject_to_admin, $message_to_admin, $headers);
		}
	} 
		
	//Sending mail - warn author that he submitted a new issue 
	if($optionNewUser != 2){
		wp_mail( $author_email, $subject_to_author, $message_to_author, $headers);
	}

}


/************************************************************************************************************************/


/**
 * 8.02
 * Email Notification on Issue imcstatus change
 *
 */

//Email Notification when Issue Status change
function imcplus_mailnotify_4imcstatuschange($transition,$post_id,$changer_id){
	
	//GET OPTIONS
	$notifyoptions = get_option( 'notifications_settings' );
	$optionStatUser = $notifyoptions["notify_stat_to_user"];
	$optionStatAdmin = $notifyoptions["notify_stat_to_admin"]; 
	
	$headers = array('Content-Type: text/html; charset=UTF-8');
	
	//Usefull things about the issue
	$issue_title = get_the_title( $post_id );
	$issue_permalink = get_permalink( $post_id );
	$changer_name = get_userdata($changer_id)->display_name; //the author who made the change (name by id)
	$new_imcstatus = get_the_term_list( $post_id, 'imcstatus' );
	
	$issue_category = 'Category Name'; $issue_category_id = 'Category ID';
	$imccategory_terms = get_the_terms( $post_id, 'imccategory');
						
	if ( $imccategory_terms ){ 
		$imccategory_links = array();
		$imccategory_links2 = array();

		foreach ( $imccategory_terms as $term ) {
			$imccategory_links[] = $term->name;
			$imccategory_links2[] = $term->term_id;
		}
											
		$issue_category = join( $imccategory_links );
		$issue_category_id = join( $imccategory_links2 );
	}

	// Recipients ($admin_email & $author_email)
	$admin_email = get_option( 'admin_email' );//site administrator's email
    $catadmin_email = get_option( "tax_imccategory_mail_$issue_category_id");//category mail
	//the author's email
	$post_tmp = get_post($post_id);
	$author_id = $post_tmp->post_author;//author's id of current #post
	$author_name = get_userdata($author_id)->display_name; //author's name by id
	$author_email = get_userdata($author_id)->user_email; //author's email by id
		
		
	// Email subject for admin
	$subject_to_admin = __('Issue status has been modified to: ','participace-projekty') .  $issue_title;

	// Email subject for author
	$subject_to_author = __('Your submitted issue status has been modified to: ','participace-projekty') .  $issue_title;
	
	$message_to_admin =	'"' . $issue_title . '"'
						. ' ' . __('Issue status has changed to ','participace-projekty')
						. ' ' . $new_imcstatus . '.'
						. '<br/>' . __('Status was modified by ','participace-projekty')
						. ' ' . $changer_name . '.'
						. '<br/>' . __('You can see the issue at: ','participace-projekty')
						. ' ' . $issue_permalink
						;
							
	$message_to_author = __('Your issue','participace-projekty')
						. ' ' . $issue_title
						. ' ' . __('has changed status to ','participace-projekty')
						. ' ' . $new_imcstatus . '.'
						. '<br/>' . __('You can see your issue at: ','participace-projekty')
						. ' ' . $issue_permalink
						;
							
	
	//Sending mail - warn category admin that issue's status changed
	//if there's no category admin, the mail is sent to website admin
	if($optionStatAdmin != 2){
		if($catadmin_email!=''){
			wp_mail( $catadmin_email, $subject_to_admin, $message_to_admin, $headers);
		}else {
			wp_mail($admin_email, $subject_to_admin, $message_to_admin, $headers);
		}
	}
	
	if($optionStatUser != 2){
		//Sending mail - warn author that his submitted issue's status has been changed 
		wp_mail( $author_email, $subject_to_author, $message_to_author, $headers);
	}

 }

/************************************************************************************************************************/
 
/**
 * 8.03
 * Email Notification on imccategory change
 *
 */
 
 
 function imcplus_mailnotify_4imccategorychange($transition,$post_id,$changer_id){
	 
	//GET OPTIONS
	$notifyoptions = get_option( 'notifications_settings' );
	$optionCatUser = $notifyoptions["notify_cat_to_user"];
	$optionCatAdmin = $notifyoptions["notify_cat_to_admin"]; 
		
	$headers = array('Content-Type: text/html; charset=UTF-8');
	
	//Usefull things about the issue
	$issue_title = get_the_title( $post_id );
	$issue_permalink = get_permalink( $post_id );
	$changer_name = get_userdata($changer_id)->display_name; //the author who made the change (name by id)
	//$new_imcstatus = get_the_term_list( $post_id, 'imcstatus' );
	
	$issue_category = 'Category Name'; $issue_category_id = 'Category ID';
	$imccategory_terms = get_the_terms( $post_id, 'imccategory');
						
	if ( $imccategory_terms ){ 
		$imccategory_links = array();
		$imccategory_links2 = array();

		foreach ( $imccategory_terms as $term ) {
			$imccategory_links[] = $term->name;
			$imccategory_links2[] = $term->term_id;
		}
											
		$issue_category = join( $imccategory_links );
		$issue_category_id = join( $imccategory_links2 );
	}
		
	
	// Recipients ($admin_email & $author_email)

	$admin_email = get_option( 'admin_email' );//site administrator's email
    $catadmin_email = get_option( "tax_imccategory_mail_$issue_category_id");//category mail
	//the author's email
	$post_tmp = get_post($post_id);
	$author_id = $post_tmp->post_author;//author's id of current #post
	$author_name = get_userdata($author_id)->display_name; //author's name by id
	$author_email = get_userdata($author_id)->user_email; //author's email by id
		
		
	// Email subject for admin
	$subject_to_admin = __('Issue category has been modified to: ','participace-projekty') .  $issue_title;

	// Email subject for author
	$subject_to_author = __('Your submitted issue category has been changed to: ','participace-projekty') .  $issue_title;
	
	$message_to_admin =	'"' . $issue_title . '"'
						. ' ' . __('Issue updated with new category: ','participace-projekty')
						. ' ' . $issue_category . '.'
						. '<br/>' . __('Category is modified by ','participace-projekty')
						. ' ' . $changer_name . '.'
						. '<br/>' . __('You can see the issue at: ','participace-projekty')
						. ' ' . $issue_permalink
						;
							
	$message_to_author = __('Your issue','participace-projekty')
						. ' ' . $issue_title
						. ' ' . __('has changed category to ','participace-projekty')
						. ' ' . $issue_category . '.'
						. '<br/>' . __('You can see your issue at: ','participace-projekty')
						. ' ' . $issue_permalink
						;

	
	//Sending mail - warn category admin that issue's status changed
	//if there's no category admin, the mail is sent to website admin
	if($optionCatAdmin != 2){
		if($catadmin_email!=''){
			wp_mail( $catadmin_email, $subject_to_admin, $message_to_admin, $headers);
		}else{
			wp_mail( $admin_email, $subject_to_admin, $message_to_admin, $headers);
		}
	}
	
	if($optionCatUser != 2){
		//Sending mail - warn author that his submitted issue's status has been changed 
		wp_mail( $author_email, $subject_to_author, $message_to_author, $headers);
	}
 }

/************************************************************************************************************************/

/**
 * 8.04
 * Email Notification on submitted comments about issues
 *
 */


add_action( 'comment_post', 'imc_mail_user_for_comments', 10, 2 );

function imc_mail_user_for_comments( $comment_ID, $comment_approved ) {

	//GET OPTIONS
	$notifyoptions = get_option( 'notifications_settings' );
	//$optionStatUser = $notifyoptions["notify_stat_to_user"];
	$optionCommentAdmin = $notifyoptions["notify_comment_to_admin"];

	$headers = array('Content-Type: text/html; charset=UTF-8');

	//Usefull things about the issue
	$post_id = $comment_ID->comment_post_ID;
	$issue_title = get_the_title( $post_id );
	//$issue_permalink = get_permalink( $post_id );
	$comment_link = get_comment_link( $comment_ID );
	$comment_author = get_comment_author( $comment_ID );
	$post_posttype = get_post_type($post_id);

	$issue_category_id = '';
	$imccategory_terms = get_the_terms( $post_id, 'imccategory');
	if ( $imccategory_terms ){$issue_category_id = $imccategory_terms[0]->term_id;}


	// Recipients ($admin_email & $author_email)
	$admin_email = get_option( 'admin_email' );//site administrator's email
	$catadmin_email = get_option( "tax_imccategory_mail_$issue_category_id");//category mail

	// Email subject for admin
	$subject_to_admin = __('New Comment on Issue: ','participace-projekty') .  $issue_title;

	// Email subject for author
	//$subject_to_author = __('Your submitted issue status is modified:','participace-projekty') .  $issue_title;

	$message_to_admin =	__('New comment on issue ','participace-projekty')
						. ' ' . $issue_title
						. ' ' . __('from user: ','participace-projekty') . $comment_author . '.'
						. '<br/>' . __('See comment here: ','participace-projekty')
						. ' ' . $comment_link
						;
	if($post_posttype == 'imc_issues' ) {
		//Sending mail - warn category admin that issue's status changed
		//if there's no category admin, the mail is sent to website admin
		if ($optionCommentAdmin != 2) {
			if ($catadmin_email != '') {
				wp_mail($catadmin_email, $subject_to_admin, $message_to_admin, $headers);
			} else {
				wp_mail($admin_email, $subject_to_admin, $message_to_admin, $headers);
			}
		}
	}
}

/************************************************************************************************************************/

/**
 * 8.05
 * 'From:' name @ all emails
 *
 */

//add_filter( 'wp_mail_from_name', 'imcplus_fromname_atmails' );

function imcplus_fromname_atmails( $original_email_from ) {
	return __('Participace na projektech','participace-projekty');
}

/************************************************************************************************************************/


?>