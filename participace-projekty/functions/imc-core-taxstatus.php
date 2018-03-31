<?php


/**
 * 3.01
 * Register Taxonomy 'imcstatus'
 *
 * Statuses for the Issues
 */

function imccreation_taxstatus() {

    $labels = array(
        'name'              => _x( 'Statuses', 'taxonomy general name', 'participace-projekty' ),
        'singular_name'     => _x( 'Status', 'taxonomy singular name', 'participace-projekty' ),
        'menu_name'         => _x( 'Issue Statuses', 'taxonomy menu name', 'participace-projekty'),
        'search_items'      => __( 'Search Statuses', 'participace-projekty' ),
        'all_items'         => __( 'All Statuses', 'participace-projekty' ),
        'parent_item'       => __( 'Parent Status', 'participace-projekty' ),
        'parent_item_colon' => __( 'Parent Status:', 'participace-projekty' ),
        'edit_item'         => __( 'Edit Status', 'participace-projekty' ),
        'update_item'       => __( 'Update Status', 'participace-projekty' ),
        'add_new_item'      => __( 'Add New Status', 'participace-projekty' ),
        'new_item_name'     => __( 'New Status Name', 'participace-projekty' ),
        'popular_items'     => NULL
    );

    $args = array(
        'hierarchical'      => false,
        'labels'            => $labels,
        'rewrite' => array(
            'slug' => 'imcstatus',
            'with_front' => false,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'show_in_menu'		=> true,
            'show_in_nav_menus'	=> true,
            'rewrite'           => array( 'slug' => 'imcstatus' ),
        ),
        'capabilities' => array (
            'manage_terms' => 'manage_imc_issues',
            'edit_terms' => 'manage_imc_issues',
            'delete_terms' => 'manage_imc_issues',
            'assign_terms' => 'edit_imc_issues'
        ),
        'show_in_rest'       => true,
        'rest_base'          => 'imcstatus',
        'rest_controller_class' => 'WP_REST_Terms_Controller',
    );

    register_taxonomy( 'imcstatus', 'imc_issues', $args );

}

add_action( 'init', 'imccreation_taxstatus', 0 );


/************************************************************************************************************************/


/**
 * 3.02
 * Create 'imcstatus' Box @ issue's backend
 *
 * (dropdown style)
 */

function imccreation_statbox() {

    remove_meta_box( 'imcstatusdiv', 'imc_issues', 'side' ); //Removes the default metabox at side

    add_meta_box( 'tagsdiv-imcstatus', __( 'Issue Status', 'participace-projekty' ), 'imcplus_statbox_content', 'imc_issues', 'side' , 'high'); //Adds the custom metabox with select box
}

add_action('add_meta_boxes', 'imccreation_statbox');



/* Prints the box content */
function imcplus_statbox_content($post) {

    $tax_name = 'imcstatus';
    
    // Get issue last modified date, to use it as left limit on the datepicker.
    $issue_modified_date = get_the_modified_date('Y-m-d H:i:s');
    ?>

    <div class="tagsdiv" id="<?php echo $tax_name; ?>">
        <p class="howto"><?php echo __( 'Select status for current Issue', 'participace-projekty' ) ?></p>
        <?php
        // Use nonce for verification
        wp_nonce_field( plugin_basename( __FILE__ ), 'imcstatus_noncename' );
        $type_IDs = wp_get_object_terms( $post->ID, 'imcstatus', array('fields' => 'ids') );
        if(!empty($type_IDs[0])){$selectedCatItemID=$type_IDs[0];}else{$selectedCatItemID=0;};

        $args = array(
            'show_option_none'   => __( 'Select Status', 'participace-projekty' ),
            'orderby'            => 'id',
            'hide_empty'         => 0,
            'selected'           => $selectedCatItemID,
            'name'               => 'imcstatus',
            'taxonomy'           => 'imcstatus',
            'echo'               => 0,
            'option_none_value'  => '-1',
            'id' => 'imc-select-status-dropdown'
        );

        $select = wp_dropdown_categories($args);

        $replace = "<select$1 required>";
        $select  = preg_replace( '#<select([^>]*)>#', $replace, $select );


        // Tasos addition
        // String replace first option with our new one
        $old_option = "<option value='-1'>";
        $new_option = "<option disabled selected value='-1'>".__( 'Select status', 'participace-projekty' )."</option>";
        $select = str_replace($old_option, $new_option, $select);

        echo $select;
        ?>
    </div>

    <div id="status_reason_box" style="display: none;">

        <p class="howto"><?php echo __( 'Add a reason', 'participace-projekty' ) ?></p>
        <textarea title="<?php echo __( 'Add a reason', 'participace-projekty' ) ?>" rows="3" name="imcstatus_reason_textarea" id="imc-status-reason-textarea"></textarea>

        <p class="howto"><?php echo __( 'Selected date', 'participace-projekty' ) ?></p>

        <input title="status datetime input" type="text" name="status_datetime_input" id="status_datetime_input" value="">

        <script type="text/javascript">
            jQuery(function(){

                console.log(<?php $issue_modified_date; ?>);

                jQuery('*[name=status_datetime_input]').appendDtpicker({
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

//When the post is saved, also saves imcstatus
function imcplus_statbox_content_save( $post_id ) {

    global $wpdb;

    // verify if this is an auto save routine. 
    // If it is our form has not been submitted, so we dont want to do anything
    if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) )
        return;

    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if ( !isset( $_POST['imcstatus_noncename'] ) || !wp_verify_nonce( $_POST['imcstatus_noncename'], plugin_basename( __FILE__ ) ) )
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
    $type_ID = intval($_POST['imcstatus'], 10);
    $type = ( $type_ID > 0 ) ? get_term( $type_ID, 'imcstatus' )->slug : NULL;

    wp_delete_object_term_relationships( $post_id, 'imcstatus' );//Unlink all previous imcstatus terms of the issue
    wp_set_object_terms(  $post_id , $type, 'imcstatus' );//Set the new imcstatus term

    //Sanitize: Cleaning User Input
    $safe_reason_textarea = sanitize_text_field($_POST['imcstatus_reason_textarea']);
    //Validating: User Input Data (if length is more than 100 chars)
    if ( strlen( $safe_reason_textarea ) > 100 ) {$safe_reason_textarea = substr( $safe_reason_textarea, 0, 100 );}

    // Make sure your data is set before trying to save it
    if( isset( $safe_reason_textarea ) ){
        if ( ! $safe_reason_textarea == '') {

            //time from calendar
            //Sanitize: Cleaning User Input
            $safe_date = sanitize_text_field($_POST['status_datetime_input']);


            //Validating: User Input Data (if length is more than 10 chars) ->  "dateFormat": "DD/MM/YYYY"
            if ( strlen( $safe_date ) > 10 ) {$safe_date = substr( $safe_date, 0, 10 );}
            //Validating: User Input Data (if format is Date) ->  "dateFormat": "DD/MM/YYYY"
            if (DateTime::createFromFormat('d/m/Y', $safe_date) !== FALSE){}else{$safe_date='';}


            if($safe_date!=''){
                list($day, $month, $year, $hour, $minute) = preg_split('/[ :\\/]+/', $safe_date);
                //The variables should be arranged according to your date format and so the separators

                $hour = date('G');
                $minute = date('i');
                $second = date('s');

                $timestamp = mktime($hour, $minute, $second, $month, $day, $year);
            }


            $current_step_id = intval($_POST['imcstatus'], 10);
            $quantityTermObject = get_term_by('id', absint($current_step_id), 'imcstatus');
            $current_step_name = $quantityTermObject->name;
            $transition = __( 'Status changed: ', 'participace-projekty' ) .  $current_step_name;
            $tagid = intval($quantityTermObject->term_id, 10);
            $theColor = 'tax_imcstatus_color_' . $tagid;
            $term_data = get_option($theColor);
            $currentStatusColor = $term_data;
            $timeline_label = $current_step_name;
            $theUser =  get_current_user_id();
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
                    'description' => $safe_reason_textarea,
                    'action' => 'step',
                    'state' => 1,
                    'created' => gmdate("Y-m-d H:i:s",time()),
                    'created_by' => $theUser,
                    'language' => $currentlang,
                )
            );

            //fires mail notification
            imcplus_mailnotify_4imcstatuschange($transition, $post_id, $theUser);

            //API USE
            //fires mobile notification - Settings checked into function
            //$post_tmp = get_post($post_id);
            //$author_id = $post_tmp->post_author;//author's id of current #issue
            //imcplus_statuschange_mobile_notification($post_id,$author_id);
        }

    }

}

/* Do something with the data entered */
add_action( 'save_post', 'imcplus_statbox_content_save' );


/************************************************************************************************************************/


/**
 * 3.03
 * Add Status Color @ 'imcstatus'
 *
 * Color that needs for status' identifier
 */


//add fields to imcstatus edit form - callback function
function imcplus_taxstat_colorfield_oncreate() {
    //check for existing taxonomy meta for term ID
    //$t_id = $tag->term_id;
    //$term_meta_option = 'tax_imcstatus_color_' . $t_id;
    //$term_meta = get_option($term_meta_option);
    $term_meta = '';
    ?>
    <div class="form-field">
        <label for="term_meta"><?php echo __('Status Color','participace-projekty'); ?></label>
        <input type="hidden" name="term_meta" id="term_meta" size="3" style="width:60%;" value="<?php echo $term_meta ? $term_meta : ''; ?>">

        <input title="Color Picker" type='text' class="color-picker-box"/>

        <script type="text/javascript">
            jQuery(function(){
                jQuery(".color-picker-box").spectrum({
                    color:'',
                    clickoutFiresChange: true,
                    hideAfterPaletteSelect:true,
                    showInput: true,
                    allowEmpty:true,
                    showPalette: true,
                    palette: [
                        ['#F44336', '#E91E63', '#9C27B0', '#673AB7'],
                        ['#3F51B5', '#2196F3', '#03A9F4', '#00BCD4'],
                        ['#009688', '#4CAF50', '#8BC34A', '#CDDC39'],
                        ['#FFEB3B', '#FFC107', '#FF9800', '#FF5722'],
                        ['#795548', '#607D8B', '#363F45', '#9E9E9E'],
                        ['#FFFFFF', '#000000']

                    ],
                    change: function(color) {
                        //jQuery("#basic-log").text("change called: " + color.toHexString());
                        var tempColor = color.toHexString();
                        var tempElement = document.getElementById("term_meta");
                        tempElement.value = tempColor;
                    }
                });
            });
        </script>
        <br />
        <p><?php echo __("Pick a color that identifies the Issue's Status.",'participace-projekty');?></p>
    </div>

    <?php
}


function imcplus_taxstat_colorfield_onedit($tag) {
    //check for existing taxonomy meta for term ID
    $t_id = $tag->term_id;
    $term_meta = get_option( "tax_imcstatus_color_$t_id");
    ?>
    <tr class="form-field">
        <th scope="row"><label for="term_meta"><?php echo __('Status Color','participace-projekty'); ?></label></th>
        <td>
            <input type="hidden" name="term_meta" id="term_meta" size="3" style="width:60%;" value="<?php echo $term_meta ? $term_meta : ''; ?>">

            <input title="Color Picker" type='text' class="color-picker-box"/>

            <script type="text/javascript">
                var statusColorEdit = document.getElementById("term_meta").value;
                if(statusColorEdit == ""){
                    statusColorEdit = '';//the default color if term_meta is empty
                }
                jQuery(function(){
                    jQuery(".color-picker-box").spectrum({
                        color: statusColorEdit,
                        clickoutFiresChange: true,
                        hideAfterPaletteSelect:true,
                        showInput: true,
                        allowEmpty:true,
                        showPalette: true,
                        palette: [
                            ['#F44336', '#E91E63', '#9C27B0', '#673AB7'],
                            ['#3F51B5', '#2196F3', '#03A9F4', '#00BCD4'],
                            ['#009688', '#4CAF50', '#8BC34A', '#CDDC39'],
                            ['#FFEB3B', '#FFC107', '#FF9800', '#FF5722'],
                            ['#795548', '#607D8B', '#363F45', '#9E9E9E'],
                            ['#FFFFFF', '#000000']

                        ],
                        change: function(color) {
                            //jQuery("#basic-log").text("change called: " + color.toHexString());
                            var tempColor = color.toHexString();
                            var tempElement = document.getElementById("term_meta");
                            tempElement.value = tempColor;
                        }
                    });
                });
            </script>
            <br />
            <p class="description"><?php echo __("Pick a color that identifies the Issue's Status.", 'participace-projekty');?></p>
        </td>
    </tr>
    <?php
}


// save extra fields - callback function
function imcplus_taxstat_colorfield_onsave( $term_id ) {

    //Sanitize: Cleaning User Input
    $safe_color_meta = sanitize_text_field($_POST['term_meta']);
    //Validating: User Input Data (if length is more than 7 chars)
    if ( strlen( $safe_color_meta ) > 7 ) {$safe_color_meta = '';}

    //Validating: User Input Data (if length is hexidecimal color code)
    $ckeck_if_is_color = substr($safe_color_meta, 1);
    if ( ! ( ctype_xdigit($ckeck_if_is_color) && (strlen($ckeck_if_is_color) == 6 || strlen($ckeck_if_is_color) == 3))){$safe_color_meta = '';}

    if ( $safe_color_meta!='' ) {
        //save the option
        update_option( "tax_imcstatus_color_$term_id", substr($safe_color_meta, 1) );
    }
}

//Add Create & Save to "Create Status"
add_action( 'imcstatus_add_form_fields', 'imcplus_taxstat_colorfield_oncreate', 10, 2);
add_action( 'create_imcstatus', 'imcplus_taxstat_colorfield_onsave', 10, 2 );

//Add Create & Save to "Edit Status" (Status single page)
add_action( 'imcstatus_edit_form_fields', 'imcplus_taxstat_colorfield_onedit', 10, 2);
add_action( 'edited_imcstatus', 'imcplus_taxstat_colorfield_onsave', 10, 2);


/************************************************************************************************************************/


/**
 * 3.04
 * Add status color to Issue Status Column
 *
 *
 */


function imcplus_taxstat_color_addthumbcol($columns){
    // add 'Status Color Column'
    $columns['color_status_column'] = __('Color','participace-projekty');

    return $columns;
}
add_filter('manage_edit-imcstatus_columns','imcplus_taxstat_color_addthumbcol');


function imcplus_taxstat_color_managethumbcol($deprecated,$column_name,$term_id){
    if ($column_name == 'color_status_column') {
        //get current term
        //The color of status is in wp_options table so:
        //real label of the saved option
        //tax_imcstatus_color_ + the id of status
        $option_label = 'tax_imcstatus_color_' . $term_id;

        //the desired color code
        $term_data = get_option($option_label);
        $currentStatusColor = $term_data;

        printf( '<div style="width:20px;height:20px;float:left;-webkit-border-radius:10px;-moz-border-radius:10px;border-radius:10px;background-color:#' . $currentStatusColor . '"></div>' );
    }

    if($column_name == 'id_status_column'){
        $order_term_meta = get_term_meta($term_id,'imc_term_order');
        printf($order_term_meta[0]);
    }


}
add_filter ('manage_imcstatus_custom_column', 'imcplus_taxstat_color_managethumbcol', 10,3);


/************************************************************************************************************************/

/**
 * 3.05
 * Add status id to Issue Status Column
 *
 *
 */



function imcplus_taxstat_id_addthumbcol($columns){
    // add 'Status Color Column'
    $columns['id_status_column'] = __('Order','participace-projekty');

    return $columns;
}
add_filter('manage_edit-imcstatus_columns','imcplus_taxstat_id_addthumbcol');

/************************************************************************************************************************/

/**
 * 3.06
 *
 * Extra Info about ImcStatus for API
 * Created, Created_by, Modified, Modified_by as term metas
 */


// Save mail fields @ imccategory - callback function

function imcplus_taxstat_extraInfo_oncreateSave( $term_id ) {
    $theUser =  get_current_user_id();
    $currentTime = gmdate("Y-m-d H:i:s",time());

    add_term_meta($term_id,'created',$currentTime,true);
    add_term_meta($term_id,'created_by',$theUser,true);
    add_term_meta($term_id,'modified',$currentTime,false);
    add_term_meta($term_id,'modified_by',$theUser,false);
    add_term_meta($term_id,'ordering',0,false);
}

function imcplus_taxstat_extraInfo_oneditSave( $term_id ) {
    $theUser =  get_current_user_id();
    $currentTime = gmdate("Y-m-d H:i:s",time());

    update_term_meta($term_id,'modified',$currentTime,false);
    update_term_meta($term_id,'modified_by',$theUser,false);
}


add_action( 'create_imcstatus', 'imcplus_taxstat_extraInfo_oncreateSave', 10, 2 );
add_action( 'edited_imcstatus', 'imcplus_taxstat_extraInfo_oneditSave', 10, 2);

/************************************************************************************************************************/

/**
 * 3.07
 *
 * Go Back Link at 'imcstatus' edit screen
 *
 */

function imcplus_taxstat_gobacklink_onedit( $tag ){
    $url = admin_url( 'edit-tags.php' );
    $url = add_query_arg( 'post_type', 'imc_issues', $url );
    $url = add_query_arg( 'taxonomy', 'imcstatus', $url );
    printf( '<a href="%s">%s</a>', $url, __( 'Go back to Issue Statuses', 'participace-projekty' ) );
}

add_action( 'imcstatus_edit_form_fields', 'imcplus_taxstat_gobacklink_onedit' , 10 , 2);

/************************************************************************************************************************/

/**
 * 3.08
 *
 * Clear Status Color after Submitted
 *
 */

function imcplus_taxstat_color_clearColor(){
    ?>
    <script>
        jQuery(function($) {
            $('#submit').click(function() {
                // Run Your Jquery Here
                document.getElementsById("term_meta").value="";
            });
        });
    </script>
    <?php
}

add_action( 'imcstatus_add_form_fields', 'imcplus_taxstat_color_clearColor' );


?>