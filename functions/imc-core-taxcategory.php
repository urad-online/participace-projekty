<?php

/**
 * 2.01
 * Register Taxonomy 'imccategory'
 *
 * Categories for the Issues
 */

function imccreation_taxcategory() {

	$labels = array(
		'name'              => _x( 'Categories', 'taxonomy general name', 'participace-projekty' ),
		'singular_name'     => _x( 'Category', 'taxonomy singular name', 'participace-projekty' ),
		'menu_name'         => _x( 'Issue Categories', 'admin menu', 'participace-projekty' ),
		'search_items'      => __( 'Search Categories', 'participace-projekty' ),
		'all_items'         => __( 'All Categories', 'participace-projekty' ),
		'parent_item'       => __( 'Parent Category', 'participace-projekty' ),
		'parent_item_colon' => __( 'Parent Category:', 'participace-projekty' ),
		'edit_item'         => __( 'Edit Category', 'participace-projekty' ),
		'update_item'       => __( 'Update Category', 'participace-projekty' ),
		'add_new_item'      => __( 'Add new Category', 'participace-projekty' ),
		'new_item_name'     => __( 'New Category Name', 'participace-projekty' )
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'capabilities' => array (
			'manage_terms' => 'manage_imc_issues',
			'edit_terms' => 'manage_imc_issues',
			'delete_terms' => 'manage_imc_issues',
			'assign_terms' => 'edit_imc_issues'
		),
		'rewrite' => array(
			'slug' => 'imccategory',
			'with_front' => false,
			'hierarchical' => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'show_in_menu'		=> true,
			'show_in_nav_menus'	=> true,
			'rewrite'           => array( 'slug' => 'imccategory' ),
			'supports'			=> array( 'thumbnail' ),
		),
		'show_in_rest'       => true,
		'rest_base'          => 'imccategory',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
	);

	register_taxonomy( 'imccategory', 'imc_issues', $args );

}

add_action( 'init', 'imccreation_taxcategory', 0 );


/************************************************************************************************************************/


/**
 * 2.02
 * Add Admin Mail @ 'imccategory'
 *
 * Mail that needs for category's notifications
 */


//Add extra fields @ imccategory create & edit forms - callback function

function imcplus_taxcat_mailfield_oncreate() {
	// Check for existing taxonomy meta for term ID

	//$t_id = $tag->term_id;
	//$term_meta_option = 'tax_imccategory_mail_' . $t_id;
	//$term_meta_mail = get_option($term_meta_option);
	$term_meta_mail = "";
	?>
	<div class="form-field">
		<label for="term_meta_mail"><?php echo __('E-mail for Notifications','participace-projekty'); ?></label>
		<input type="text" name="term_meta_mail" id="term_meta_mail" size="3" style="width:60%;" value="<?php echo $term_meta_mail ? $term_meta_mail : ''; ?>"><br />
		<p><?php echo __('The e-mail address that will receive all notifications. You can use more than one if you like, by separating them with comma.','participace-projekty');?></p>
	</div>

	<?php
}


function imcplus_taxcat_mailfield_onedit($tag) {
	//check for existing taxonomy meta for term ID
	$t_id = $tag->term_id;
	$term_meta_mail = get_option( "tax_imccategory_mail_$t_id");
	?>
	<tr class="form-field">
		<th scope="row"><label for="term_meta_mail"><?php echo __('E-mail for Notifications','participace-projekty'); ?></label></th>
		<td>
			<input type="text" name="term_meta_mail" id="term_meta_mail" size="3" style="width:60%;" value="<?php echo $term_meta_mail ? $term_meta_mail : ''; ?>"><br />
			<p class="description"><?php echo __('The e-mail address that will receive all notifications. You can use more than one if you like, by separating them with comma.','participace-projekty');?></p>
		</td>
	</tr>
	<?php
}


// Save mail fields @ imccategory - callback function

function imcplus_taxcat_mailfield_onsave( $term_id ) {

	//Sanitize: Cleaning User Input
	$safe_mail_meta = sanitize_text_field($_POST['term_meta_mail']);
	//Validating: User Input Data
	$ArrayWithMails = explode(',', $safe_mail_meta);
	foreach ( $ArrayWithMails as $checkMail ) {
		if(!(filter_var($checkMail, FILTER_VALIDATE_EMAIL))) {
			$ArrayWithMails = array_diff($ArrayWithMails, array($checkMail));
		}
	}
	$safe_mail_meta = implode(',',$ArrayWithMails);

	if ( $safe_mail_meta != '' ) {
		//save the option array
		update_option( "tax_imccategory_mail_$term_id", $safe_mail_meta );
	}
}

//Add Create & Save to "Create Category"
add_action( 'imccategory_add_form_fields', 'imcplus_taxcat_mailfield_oncreate', 10, 2);
add_action( 'create_imccategory', 'imcplus_taxcat_mailfield_onsave', 10, 2 );

//Add Create & Save to "Edit Category" (Category single page)
add_action( 'imccategory_edit_form_fields', 'imcplus_taxcat_mailfield_onedit', 10, 2);
add_action( 'edited_imccategory', 'imcplus_taxcat_mailfield_onsave', 10, 2);



/************************************************************************************************************************/


/**
 * 2.03
 * Add Featured Image @ 'imccategory'
 *
 */


if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists('imcplus_taxcat_image') ) :

	class imcplus_taxcat_image {

		// object version used for enqueuing scripts
		private $version = '1.5.1';
		// url for the directory where our js is located
		private $js_dir_url;
		// the slug for the taxonomy we are targeting
		// api: use filter 'imcplus-taxcat-image-definefunc' to override
		private $taxonomy = 'category';
		// defined during __construct() for i18n reasons
		// api: use filter 'imcplus-taxcat-image-labelsfunc' to override
		private $labels = array();
		// where we will store our term_data
		// will dynamically be set to $this->taxonomy . '_term_images' by default
		// api: use filter 'imcplus-taxcat-image-optnamefunc' to override
		private $option_name = '';
		// array of key value pairs:  term_id => image_id
		public $term_images = array();


		/**
		 * Simple singleton to enforce once instance
		 *
		 * @return imcplus_taxcat_image object
		 */
		static function instance() {
			static $object = null;
			if ( is_null( $object ) ) {
				$object = new imcplus_taxcat_image();
			}
			return $object;
		}

		/**
		 * Init the plugin and hook into WordPress
		 */
		private function __construct() {
			// default labels
			/*
			$this->labels = array(
				'fieldTitle'       => __( 'Category Image', 'participace-projekty' ),
				'fieldDescription' => __( 'Select which image should represent this category. Images should have at least 100 x 100 size.', 'participace-projekty' ),
				'imageButton'      => __( 'Select Image', 'participace-projekty' ),
				'removeButton'     => __( 'Remove', 'participace-projekty' ),
				'modalTitle'       => __( 'Select or upload an image for this category', 'participace-projekty' ),
				'modalButton'      => __( 'Attach', 'participace-projekty' ),
			);
			*/
			// default option name keyed to the taxonomy
			$this->option_name = $this->taxonomy . '_term_images';
			// allow overriding of the target taxonomy
			$this->taxonomy = apply_filters( 'imcplus-taxcat-image-definefunc', $this->taxonomy );
			// allow overriding of the html text
			$this->labels = apply_filters( 'imcplus-taxcat-image-labelsfunc', $this->labels );
			// allow overriding of option_name
			$this->option_name = apply_filters( 'imcplus-taxcat-image-optnamefunc', $this->option_name );
			// get our js location for enqueing scripts
			$this->js_dir_url = apply_filters( 'imcplus-taxcat-image-jsdirfunc', plugin_dir_url( __FILE__ ) . '/js' );
			// gather data
			$this->term_images = get_option( $this->option_name, $this->term_images );
			// hook into WordPress
			$this->hook_up();
		}
		// prevent cloning
		private function __clone(){}
		// prevent unserialization
		private function __wakeup(){}


		/**
		 * Initialize the object
		 * - hook into WordPress admin
		 */
		private function hook_up(){
			// we only need to add most hooks on the admin side
			if ( is_admin() ) {
				add_action( 'admin_enqueue_scripts', array( $this, 'imcplus_taxcat_image_enqscripts' ) );
				// add our image field to the taxonomy term forms
				add_action( $this->taxonomy . '_add_form_fields', array( $this, 'imcplus_taxcat_image_addform' ) );
				add_action( $this->taxonomy . '_edit_form_fields', array( $this, 'imcplus_taxcat_image_editform' ) );
				// hook into term administration actions
				add_action( 'created_term', array( $this, 'imcplus_taxcat_image_onsave' ), 10, 3 );
				add_action( 'edited_term', array( $this, 'imcplus_taxcat_image_onsave' ), 10, 3 );
				add_action( 'delete_term', array( $this, 'delete_term' ), 10, 4 );
			}
			// add our data when term is retrieved
			add_action( 'get_term', array( $this, 'get_term' ), 10, 2 );
			add_action( 'get_terms', array( $this, 'get_terms' ), 10, 3 );
		}


		/**
		 * WordPress action "admin_enqueue_scripts"
		 */
		function imcplus_taxcat_image_enqscripts(){
			// get the screen object to decide if we want to inject our scripts
			$screen = get_current_screen();
			// we're looking for "edit-category"
			if ( $screen->id == 'edit-' . $this->taxonomy ){
				// WP core stuff we need
				wp_enqueue_media();
				wp_enqueue_style( 'thickbox' );
				$dependencies = array( 'jquery', 'thickbox', 'media-upload' );
				// register our custom script
				wp_register_script( 'taxonomy-term-image-js', $this->js_dir_url . '/imc-scripts-taxterm-image.js', $dependencies, $this->version, true );
				// Localize the modal window text so that we can translate it
				wp_localize_script( 'taxonomy-term-image-js', 'TaxonomyTermImageText', $this->labels );
				// enqueue the registered and localized script
				wp_enqueue_script( 'taxonomy-term-image-js' );
			}
		}


		/**
		 * The HTML form for our taxonomy image field
		 *
		 * @param  int    $image_ID  the image ID
		 * @param  array  $image_src
		 * @return string the html output for the image form
		 */
		function imcplus_taxcat_image_thefield( $image_ID = null, $image_src = array() ) {
			wp_nonce_field('taxonomy-term-image-form-save', 'taxonomy-term-image-save-form-nonce');
			?>
			<input type="button" class="taxonomy-term-image-attach imc-button" value="<?php echo esc_attr( __( 'Select Image', 'participace-projekty' ) ); ?>" />
			<input type="button" class="taxonomy-term-image-remove imc-button" value="<?php echo esc_attr( __( 'Remove', 'participace-projekty' ) ); ?>" />
			<input type="hidden" id="taxonomy-term-image-id" name="taxonomy_term_image" value="<?php echo esc_attr( $image_ID ); ?>" />
			<p class="description"><?php echo __( 'Select which image should represent this category. Images should have at least 100 x 100 size.', 'participace-projekty' ); ?></p>

			<p id="taxonomy-term-image-container">
				<?php if ( isset( $image_src[0] ) ) : ?>
					<img class="taxonomy-term-image-attach" src="<?php print esc_attr( $image_src[0] ); ?>" />
				<?php endif; ?>
			</p>
			<?php
		}



		/**
		 * Add new field @ imccategory form (add)
		 */
		function imcplus_taxcat_image_addform(){
			?>
			<div class="form-field term-image-wrap">
				<label><?php echo __( 'Category Image', 'participace-projekty' ); ?></label>
				<?php $this->imcplus_taxcat_image_thefield(); ?>
			</div>
			<?php
		}


		/**
		 *  Add new field @ imccategory form (edit)
		 *
		 * @param $tag | object | the term object
		 */
		function imcplus_taxcat_image_editform( $tag ){
			// default values
			$image_ID = '';
			$image_src = array();
			// look for existing data for this term
			if ( isset( $this->term_images[ $tag->term_id ] ) ) {
				$image_ID  = $this->term_images[ $tag->term_id ];
				$image_src = wp_get_attachment_image_src( $image_ID, 'thumbnail' );
			}
			?>
			<tr class="form-field">
				<th scope="row" valign="top"><label><?php echo __( 'Category Image', 'participace-projekty' ); ?></label></th>
				<td class="taxonomy-term-image-row">
					<?php $this->imcplus_taxcat_image_thefield( $image_ID, $image_src ); ?>
				</td>
			</tr>
			<?php
		}



		/**
		 * Handle saving our custom taxonomy data
		 *
		 * @param $term_id
		 * @param $tt_id
		 * @param $taxonomy
		 */
		function imcplus_taxcat_image_onsave( $term_id, $tt_id, $taxonomy ) {
			// our requirements for saving:
			if (
				// nonce was submitted and is verified
				isset( $_POST['taxonomy-term-image-save-form-nonce'] ) &&
				wp_verify_nonce( $_POST['taxonomy-term-image-save-form-nonce'], 'taxonomy-term-image-form-save' ) &&
				// taxonomy data and taxonomy_term_image data was submitted
				isset( $_POST['taxonomy'] ) &&
				isset( $_POST['taxonomy_term_image'] ) &&
				// the taxonomy submitted is the taxonomy we are dealing with
				$_POST['taxonomy'] == $this->taxonomy
			)
			{
				// see if image data was submitted:
				// sanitize the data and save it in the term_images array
				if ( ! empty( $_POST['taxonomy_term_image'] ) ) {
					$this->term_images[ $term_id ] = absint( $_POST['taxonomy_term_image'] );
				}
				// term was submitted with no image value:
				// if the term previous had image data, remove it
				else if ( isset( $this->term_images[ $term_id ] ) ) {
					unset( $this->term_images[ $term_id ] );
				}
				// save the term image data
				update_option( $this->option_name, $this->term_images );

			}
		}


		/**
		 * Delete a term's image data when the term is deleted
		 *
		 * @param $term_id
		 * @param $tt_id
		 * @param $taxonomy
		 * @param $deleted_term
		 */
		function delete_term( $term_id, $tt_id, $taxonomy, $deleted_term ) {
			if ( $taxonomy == $this->taxonomy && isset( $this->term_images[ $term_id ] ) ) {
				unset( $this->term_images[ $term_id ]  );
				// save the data
				update_option( $this->option_name, $this->term_images );
			}
		}


		/**
		 * Add the image data to any relevant get_term call
		 *
		 * @param $_term
		 * @param $taxonomy
		 *
		 * @return mixed
		 */
		function get_term( $_term, $taxonomy ) {
			// only modify term when dealing with this taxonomy

			if ( $taxonomy == $this->taxonomy ) {
				// default to null if not found
				$_term->term_image = isset( $this->term_images[ $_term->term_id ] ) ? $this->term_images[ $_term->term_id ] : null;
			}
			return $_term;
		}


		/**
		 * Add term_image data to objects when get_terms() is called
		 *
		 * @param $terms
		 * @param $taxonomies
		 * @param $args
		 */
		function get_terms( $terms, $taxonomies, $args ){
			foreach( $terms as $i => $term ) {
			    if (is_object($term)) {
                    $terms[$i] = $this->get_term( $term, $term->taxonomy );
                }
			}
			return $terms;
		}


	}
endif;


//Change the taxonomy targeted by the plugin.
function imcplus_taxcat_image_definetax( $taxonomy ) {
	// use for tags instead of categories
	return 'imccategory';
}
add_filter( 'imcplus-taxcat-image-definefunc', 'imcplus_taxcat_image_definetax' );


imcplus_taxcat_image::instance();


/************************************************************************************************************************/

/**
 * 2.04
 * Create 'imccategory' Box @ issue's backend
 *
 * (dropdown style)
 */

function imccreation_catbox() {

	remove_meta_box( 'imccategorydiv', 'imc_issues', 'side' ); //Removes the default metabox at side

	add_meta_box( 'tagsdiv-imccategory', __( 'Issue Category', 'participace-projekty' ), 'imcplus_catbox_content', 'imc_issues', 'side' , 'high'); //Adds the custom metabox with select box
}

add_action('add_meta_boxes', 'imccreation_catbox');


/* Prints the box content */
function imcplus_catbox_content($post) {

	$tax_name = 'imccategory';

	// Get issue last modified date, to use it as left limit on the datepicker.
	$issue_modified_date = get_the_modified_date('Y-m-d H:i:s');

	?>

	<div class="tagsdiv" id="<?php echo $tax_name; ?>">

		<p class="howto"><?php echo __( 'Select category for current Issue', 'participace-projekty' ) ?></p>

		<?php
		// Use nonce for verification
		wp_nonce_field( plugin_basename( __FILE__ ), 'imccategory_noncename' );
		$type_IDs = wp_get_object_terms( $post->ID, 'imccategory', array('fields' => 'ids') );
		if(!empty($type_IDs[0])){$selectedCatItemID=$type_IDs[0];}else{$selectedCatItemID=0;};

		$args = array(
			'show_option_none'   => __( 'Select Category', 'participace-projekty' ),
			'orderby'            => 'name',
			'hide_empty'         => 0,
			'selected'           => $selectedCatItemID,
			'name'               => 'imccategory',
			'taxonomy'           => 'imccategory',
			'echo'               => 0,
			'option_none_value'  => '-1',
			'id' => 'imc-select-category-dropdown'
		);

		$select = wp_dropdown_categories($args);

		$replace = "<select$1 required>";
		$select  = preg_replace( '#<select([^>]*)>#', $replace, $select );


		// Tasos addition
		// String replace first option with our new one
		$old_option = "<option value='-1'>";
		$new_option = "<option disabled selected value='-1'>".__( 'Select category', 'participace-projekty' )."</option>";
		$select = str_replace($old_option, $new_option, $select);

		echo $select;
		?>

	</div>


	<input type="hidden" name="category_change_text"  id="category_change_text" value="">
	<input type="hidden" name="category_change_new"  id="category_change_new" value="">

	<div id="cat_reason_box" style="display: none;">
		<p class="howto"><?php echo __( 'Add a reason', 'participace-projekty' ) ?></p>
		<textarea title="<?php echo __( 'Add a reason', 'participace-projekty' ) ?>" rows="3" name="imccategory_reason_textarea" id="imc-category-reason-textarea"></textarea>

		<p class="howto"><?php echo __( 'Selected date', 'participace-projekty' ) ?></p>

		<input title="category datetime input" type="text" name="category_datetime_input" id="category_datetime_input" value="">

		<script type="text/javascript">

            jQuery( document ).ready(function() {
				jQuery('*[name=category_datetime_input]').appendDtpicker({
					"firstDayOfWeek": 1,
					"closeOnSelected": true,
					"dateFormat": "DD/MM/YYYY",
					"dateOnly": true,
					"todayButton": true,
					"autodateOnStart": true,
					"minDate": '<?php echo $issue_modified_date; ?>',
					"maxDate": new Date()
				});
			});
		</script>

	</div>

	<?php
}


/**
 * When the post is saved, also saves imccategory
 * @param $post_id
 */
function imcplus_catbox_content_save( $post_id ) {

	global $wpdb;

	// verify if this is an auto save routine.
	// If it is our form has not been submitted, so we dont want to do anything
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) )
		return;

	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times
	if ( !isset( $_POST['imccategory_noncename'] ) || !wp_verify_nonce( $_POST['imccategory_noncename'], plugin_basename( __FILE__ ) ) )
		return;

	// Check permissions
	if ( 'imc_issues' == $_POST['post_type'] )
	{
		if ( ! ( current_user_can( 'edit_page', $post_id ) ||  current_user_can( 'edit_issue', $post_id ) ) )
			return;
	}
	else
	{
		if ( ! ( current_user_can( 'edit_post', $post_id ) ||  current_user_can( 'edit_issue', $post_id ) ) )
			return;
	}



	// OK, we're authenticated: we need to find and save the data

	$type_ID = intval($_POST['imccategory'], 10);

	$type = ( $type_ID > 0 ) ? get_term( $type_ID, 'imccategory' )->slug : NULL;

	wp_delete_object_term_relationships( $post_id, 'imccategory' );//Unlink all previous imccategory terms of the issue
	wp_set_object_terms(  $post_id , $type, 'imccategory' );//Set the new imccategory term

	//Sanitize: Cleaning User Input
	$safe_reason_textarea_cat = sanitize_text_field($_POST['imccategory_reason_textarea']);
	//Validating: User Input Data (if length is more than 100 chars)
	if ( strlen( $safe_reason_textarea_cat ) > 100 ) {$safe_reason_textarea_cat = substr( $safe_reason_textarea_cat, 0, 100 );}

	// Make sure your data is set before trying to save it
	if( isset( $safe_reason_textarea_cat ) ){
		if ( ! $safe_reason_textarea_cat == '') {

			//time from calendar
			//Sanitize: Cleaning User Input
			$safe_date_cat = sanitize_text_field($_POST['category_datetime_input']);
			//Validating: User Input Data (if length is more than 10 chars) ->  "dateFormat": "DD/MM/YYYY"
			if ( strlen( $safe_date_cat ) > 10 ) {$safe_date_cat = substr( $safe_date_cat, 0, 10 );}
			//Validating: User Input Data (if format is Date) ->  "dateFormat": "DD/MM/YYYY"
			if (DateTime::createFromFormat('d/m/Y', $safe_date_cat) !== FALSE){}else{$safe_date_cat='';}

			if($safe_date_cat==''){
				//current time
				$safe_date_cat = new DateTime();
				$timestamp = $safe_date_cat->getTimestamp();
			}else{

				list($day, $month, $year, $hour, $minute) =  preg_split('/[ :\\/]+/', $safe_date_cat);

				$hour = date('G');
				$minute = date('i');
				$second = date('s');

				//The variables should be arranged according to your date format and so the separators
				$timestamp = mktime($hour, $minute, $second, $month, $day, $year);

			}


			$current_step_id =  get_the_terms($post_id, 'imcstatus');
			$current_step_id = $current_step_id[0]->term_id;
			$quantityTermObject = get_term_by('id', absint($current_step_id), 'imcstatus');
			$tagid = intval($quantityTermObject->term_id, 10);
			$theColor = 'tax_imcstatus_color_' . $tagid;
			$term_data = get_option($theColor);
			$currentStatusColor = $term_data;

			$current_cat_id = intval($_POST['imccategory'], 10);
			$quantityTermObject = get_term_by('id', absint($current_cat_id), 'imccategory');
			$current_cat_name = $quantityTermObject->name;
			$transition = __( 'Category changed: ', 'participace-projekty' ) .  $current_cat_name;

			$timeline_label = $current_cat_name;
			$theUser =  get_current_user_id();
			$currentlang = get_bloginfo('language');

			$imc_logs_table_name = $wpdb->prefix . 'imc_logs';

			$wpdb->insert(
					$imc_logs_table_name,
					array(
						'issueid' => $post_id,
						'stepid' => $current_cat_id,
						'transition_title' => $transition,
						'timeline_title' => $timeline_label,
						'theColor' => $currentStatusColor,
						'description' => $safe_reason_textarea_cat,
						'action' => 'category',
						'state' => 1,
						'created' => gmdate("Y-m-d H:i:s",time()),
						'created_by' => $theUser,
						'language' => $currentlang,
					)
			);

			//fires mail notification
			imcplus_mailnotify_4imccategorychange($transition, $post_id, $theUser);

			//API use
			//fires mobile notification - Settings checked into function
			//$post_tmp = get_post($post_id);
			//$author_id = $post_tmp->post_author;//author's id of current #issue
			//imcplus_categorychange_mobile_notification($post_id,$author_id);

		}

	}

}

/* Do something with the data entered */
add_action( 'save_post', 'imcplus_catbox_content_save' );


/************************************************************************************************************************/



/**
 * 2.05
 * Add thumbnail image to Issue Category Column
 *
 *
 */


function imcplus_taxcat_image_addthumbcol($columns){
	// add 'Thumbnail Image Column'
	$columns['thumb_category_column'] = __('Image','participace-projekty');

	return $columns;
}
add_filter('manage_edit-imccategory_columns','imcplus_taxcat_image_addthumbcol');


function imcplus_taxcat_image_managethumbcol($deprecated, $column_name,$term_id){
	if ($column_name == 'thumb_category_column') {
		//get current term
		$term = get_term_by('id', $term_id, 'imccategory');

		if ( $term->term_image ){
			echo wp_get_attachment_image( $term->term_image, array(32, 32) );
		}
	}

	if ($column_name == 'mail_category_column') {
		//get current term
		//The color of status is in wp_options table so:
		//real label of the saved option
		//tax_imcstatus_color_ + the id of status
		$option_label = 'tax_imccategory_mail_' . $term_id;

		//the desired color code
		$term_data_mail = get_option($option_label);

		$ArrayWithMails = explode(',', $term_data_mail);
		foreach ( $ArrayWithMails as $Mail ) {
			echo $Mail . "\n";
		}

	}

}
add_filter ('manage_imccategory_custom_column', 'imcplus_taxcat_image_managethumbcol', 10,3);

/************************************************************************************************************************/

/**
 * 2.06
 * Add category Mail to Issue Category Column
 *
 *
 */


function imcplus_taxcat_mail_addcol($columns){
	// add 'Category Mail Column'
	$columns['mail_category_column'] = __('Email','participace-projekty');

	return $columns;
}
add_filter('manage_edit-imccategory_columns','imcplus_taxcat_mail_addcol');

/************************************************************************************************************************/

/**
 * 2.07
 *
 * Extra Info about ImcCategory for API
 * Created, Created_by, Modified, Modified_by as term metas
 */


// Save mail fields @ imccategory - callback function

function imcplus_taxcat_extraInfo_oncreateSave( $term_id ) {
	$theUser =  get_current_user_id();
	$currentTime = gmdate("Y-m-d H:i:s",time());

	add_term_meta($term_id,'created',$currentTime,true);
	add_term_meta($term_id,'created_by',$theUser,true);
	add_term_meta($term_id,'modified',$currentTime,false);
	add_term_meta($term_id,'modified_by',$theUser,false);
}

function imcplus_taxcat_extraInfo_oneditSave( $term_id ) {
	$theUser =  get_current_user_id();
	$currentTime = gmdate("Y-m-d H:i:s",time());

	update_term_meta($term_id,'modified',$currentTime,false);
	update_term_meta($term_id,'modified_by',$theUser,false);
}


add_action( 'create_imccategory', 'imcplus_taxcat_extraInfo_oncreateSave', 10, 2 );
add_action( 'edited_imccategory', 'imcplus_taxcat_extraInfo_oneditSave', 10, 2);

/************************************************************************************************************************/

/**
 * 2.08
 *
 * Clear Category Image after Submitted
 *
 */

function imcplus_taxcat_image_clearImage(){
	?>
	<script>
		jQuery(function($) {
			$('#submit').click(function() {
				// Run Your Jquery Here
				document.getElementsById("taxonomy-term-image-container").html="";
				document.getElementsByClassName("taxonomy-term-image-attach")[1].src="";
			});
		});
	</script>
	<?php
}

add_action( 'imccategory_add_form_fields', 'imcplus_taxcat_image_clearImage');

/************************************************************************************************************************/

/**
 * 2.09
 *
 * Go Back Link at 'imccategory' edit screen
 *
 */

function imcplus_taxcat_gobacklink_onedit( $tag ){
	$url = admin_url( 'edit-tags.php' );
	$url = add_query_arg( 'post_type', 'imc_issues', $url );
	$url = add_query_arg( 'taxonomy', 'imccategory', $url );
	printf( '<a href="%s">%s</a>', $url, __( 'Go back to Issue Categories', 'participace-projekty' ) );
}

add_action( 'imccategory_edit_form_fields', 'imcplus_taxcat_gobacklink_onedit' , 10 , 2);


?>