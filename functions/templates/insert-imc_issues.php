<?php

/**

 * Template Name: Insert Issue Page

 *

 */



wp_enqueue_script('imc-gmap');

$listpage = getIMCArchivePage();

$postTitleError = '';

if(isset($_POST['submitted']) && isset($_POST['post_nonce_field']) && wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce')) {

	$imccategory_id = esc_attr(strip_tags($_POST['my_custom_taxonomy']));

	// Check options if the status of new issue is pending or publish

	$generaloptions = get_option( 'general_settings' );
	$moderateOption = $generaloptions["moderate_new"];
	$mypost_status 	= 'pending';

	if($moderateOption == 2){$mypost_status = 'publish';}

	//CREATE THE ISSUE TO DB

	$post_information = array(
		'post_title' => esc_attr(strip_tags($_POST['postTitle'])),
		'post_content' => esc_attr(strip_tags($_POST['postContent'])),
		'post_type' => 'imc_issues',
		'post_status' => $mypost_status,
		'post_name'   => sanitize_title( $_POST['postTitle']),
		'tax_input' => array( 'imccategory' => $imccategory_id ),
	);

	$post_information['meta_input'] = pb_new_project_meta_save_prep( $_POST);

	$post_id = wp_insert_post($post_information, true);

	if ( $post_id && ( ! is_wp_error($post_id)) ) {
		pb_new_project_insert_attachments( $post_id, $_FILES);
	}

	// Choose the imcstatus with smaller id
	// zmenit order by imc_term_order

	$pb_edit_completed = (! empty( $_POST['pb_project_edit_completed']) ) ?  $_POST['pb_project_edit_completed'] : 0;
	$all_status_terms = get_terms( 'imcstatus' , array( 'hide_empty' => 0 , 'orderby' => 'id', 'order' => 'ASC') );
	if ( $pb_edit_completed ) {
		$first_status = $all_status_terms[1];
	} else {
		$first_status = $all_status_terms[0];
	}

	wp_set_object_terms($post_id, $first_status->name, 'imcstatus');

	//Create Log if moderate is OFF

	if($moderateOption == 2) {

		imcplus_crelog_frontend_nomoder($post_id, $first_status->term_id, get_current_user_id());

	}

	/************************ ABOUT FEATURED IMAGE  ***********************************/

	$image =  $_FILES['featured_image'];

	$orientation = intval(strip_tags($_POST['imcPhotoOri']), 10);



	if ($orientation !== 0) {

		$attachment_id = imc_upload_img( $image, $post_id, $post_information['post_title'], $orientation);

	} else {

		$attachment_id = imc_upload_img( $image, $post_id, $post_information['post_title'], null);

	}



	set_post_thumbnail( $post_id, $attachment_id );

	/************************ END FEATURED IMAGE  ***********************************/



	imcplus_mailnotify_4submit($post_id,$imccategory_id, $post_information['meta_input']['imc_address']);



	if($post_id){

		wp_redirect(get_permalink($listpage[0]->ID));

		exit;

	}

}


get_header();





/************************** GMAP SETTINGS *************************************/

$map_options = get_option('gmap_settings');

$map_options_initial_lat = $map_options["gmap_initial_lat"];

$map_options_initial_lng = $map_options["gmap_initial_lng"];

$map_options_initial_zoom = $map_options["gmap_initial_zoom"];

$map_options_initial_mscroll = $map_options["gmap_mscroll"];

$map_options_initial_bound = $map_options["gmap_boundaries"];



/*****************************************************************************/



$plugin_path_url = imc_calculate_plugin_base_url();

// checks if the current user has the ability to post anything

$user = wp_get_current_user();



if( is_user_logged_in() ) {

	?>



    <div class="imc-BGColorGray">



        <div class="imc-SingleHeaderStyle imc-BGColorWhite">

            <a href="<?php echo esc_url(get_permalink($listpage[0]->ID)); ?>" class="u-pull-left imc-SingleHeaderLinkStyle ">

                <i class="material-icons md-36 imc-SingleHeaderIconStyle">keyboard_arrow_left</i>

                <span><?php echo __('Return to overview','participace-projekty'); ?></span>

            </a>

        </div>



        <div class="imc-Separator"></div>



        <div class="imc-container">



            <!-- INSERT FORM BEGIN -->

            <div id="insert_form_wrapper">

                <form name="report_an_issue_form" action="" id="primaryPostForm" method="POST" enctype="multipart/form-data">



                    <div class="imc-CardLayoutStyle">



                        <h2 class="imc-PageTitleTextStyle imc-TextColorPrimary"><?php echo __('Report a new issue','participace-projekty'); ?></h2>

                        <div class="imc-Separator"></div>


                        <div class="imc-row">



                            <!-- Issue's Title -->

                            <div class="imc-grid-6 imc-columns">



                                <h3 class="imc-SectionTitleTextStyle"><?php echo '1. ' . __('Title','participace-projekty'); ?></h3>

                                <input autocomplete="off" placeholder="<?php echo __('Add a short title for the issue','participace-projekty'); ?>" type="text" name="postTitle" id="postTitle" class="imc-InputStyle" />

                                <label id="postTitleLabel" class="imc-ReportFormErrorLabelStyle imc-TextColorPrimary"></label>

                            </div>



                            <!-- Issue's Category -->

                            <div class="imc-grid-6 imc-columns">

                                <h3 class="imc-SectionTitleTextStyle"><?php echo '2. ' . __('Category','participace-projekty'); ?></h3>



                                <!-- Function that creates the select box -->

                                <label class="imc-CustomSelectStyle u-full-width">

									<?php esc_html(imc_insert_cat_dropdown( 'my_custom_taxonomy' )); ?>

                                </label>

								<label id="my_custom_taxonomyLabel" class="imc-ReportFormErrorLabelStyle imc-TextColorPrimary"></label>

                            </div>

                        </div>



                        <!-- Issue's Description -->

                        <div class="imc-row">

                            <h3 class="u-pull-left imc-SectionTitleTextStyle"><?php echo '3. ' . __('Description','participace-projekty'); ?>&nbsp; <?php echo pb_render_mandatory(false)?>

                            <textarea  placeholder="<?php echo __('Add a thorough description of the issue','participace-projekty'); ?>" rows="2" class="imc-InputStyle" title="Description" name="postContent" id="postContent"><?php if(isset($_POST['postContent'])) { if(function_exists('stripslashes')) { echo esc_html(stripslashes($_POST['postContent'])); } else { echo esc_html($_POST['postContent']); } } ?></textarea>
							<label id="postContentLabel" class="imc-ReportFormErrorLabelStyle imc-TextColorPrimary"></label>
                        </div>


						<?php echo pb_template_part_new_project( array(
								'lat' => $map_options_initial_lat,
								'lon' => $map_options_initial_lng,
							)) ;?>

                        <!-- Issue's Image -->



						<div class="imc-row">

							<span class="u-pull-left imc-ReportFormSubmitErrorsStyle" id="imcReportFormSubmitErrors"></span>

						</div>

                    </div>



                    <div class="imc-row">



						<?php wp_nonce_field('post_nonce', 'post_nonce_field'); ?>

                        <input type="hidden" name="submitted" id="submitted" value="true" />

                        <input id="imcInsertIssueSubmitBtn" class="imc-button imc-button-primary imc-button-block pb-project-submit-btn" type="submit" value="<?php echo pb_project_submit_btn_label(false); ?>" />

                    </div>

                </form>

            </div> <!-- Form end -->

        </div>

    </div>







<?php } else { ?>



    <div class="imc-BGColorGray">



        <div class="imc-SingleHeaderStyle imc-BGColorWhite">

            <a href="<?php echo esc_url(get_permalink($listpage[0]->ID)); ?>" class="u-pull-left imc-SingleHeaderLinkStyle ">

                <i class="material-icons md-36 imc-SingleHeaderIconStyle">keyboard_arrow_left</i>

                <span><?php echo __('Return to overview','participace-projekty'); ?></span>

            </a>

        </div>



        <div class="imc-Separator"></div>



        <div class="imc-container">



            <div class="imc-CardLayoutStyle imc-ContainerEmptyStyle">

                <img src="<?php echo esc_url($plugin_path_url);?>/img/img_banner.jpg" class="u-full-width">



                <div class="imc-Separator"></div>



                <div class="imc-row imc-CenterContents imc-GiveWhitespaceStyle">



                    <i class="imc-EmptyStateIconStyle material-icons md-huge">vpn_lock</i>



                    <div class="imc-Separator"></div>



                    <h3 class="imc-FontRoboto imc-Text-LG imc-TextColorSecondary imc-TextMedium imc-CenterContents"><?php echo __('You are not authorised to report an issue','participace-projekty'); ?></h3>



                    <div class="imc-Separator"></div>



                    <a href="<?php echo esc_url(wp_login_url()); ?>" class="imc-Text-XL imc-TextMedium imc-LinkStyle"><?php echo __('Please login!','participace-projekty'); ?></a>



                    <div class="imc-Separator"></div>

                </div>

            </div>

        </div>

    </div>



<?php } ?>



    <!-- Form validation rules -->

    <script>

        "use strict";
        (function(){

            /*Google Maps API*/
            google.maps.event.addDomListener(window, 'load', imcInitMap);

            jQuery( document ).ready(function() {
                var validator = new FormValidator('report_an_issue_form',
					<?PHP echo pb_new_project_mandatory_fields_js_validation(); ?>,
				function(errors, events) {
					jQuery('label.imc-ReportFormErrorLabelStyle').html("");
                    if (errors.length > 0) {
                        var i, j;
                        var errorLength;
                        jQuery("#imcReportFormSubmitErrors").html("");
                        jQuery('#postTitleLabel').html();

                        for (i = 0, errorLength = errors.length; i < errorLength; i++) {
                            if (errors[i].name === "featured_image") {
								imcDeleteAttachedImage('imcReportAddImgInput');
								jQuery("#imcReportFormSubmitErrors").html(errors[i].message);
                            } else {
								for(j=0; j < Math.min(1, errors[i].messages.length); j++) {
									/* zobrazuje se jen prvni chyba, validator vraci stejnou chybu pokud je vice praidel */
									jQuery('#'+errors[i].id+'Label').html(errors[i].messages[j]);
									jQuery("#imcReportFormSubmitErrors").append("<p>"+errors[i].message+"</p>");
								}
                            }
                        }
                    } else {
                        jQuery('#imcInsertIssueSubmitBtn').attr('disabled', 'disabled');
                        jQuery('label.imc-ReportFormErrorLabelStyle').html();
                    }
                });
				validator.registerConditional( 'pb_project_js_validate_required', function(field){
					/* povinna pole se validuji pouze pokud narhovatel zaskrtne odeslat k vyhodnoceni
					 plati pro pole s pravidlem "depends" */
					return jQuery('#pb_project_edit_completed').prop('checked');
				});
            });
        })();

        function imcInitMap() {
            "use strict";
            var mapId = "imcReportIssueMapCanvas";
            // Checking the saved latlng on settings
            var lat = parseFloat('<?php echo floatval($map_options_initial_lat); ?>');
            var lng = parseFloat('<?php echo floatval($map_options_initial_lng); ?>');
            if (lat === '' || lng === '' ) { lat = 40.1349854; lng = 22.0264538; }

            // Options casting if empty
            var zoom = parseInt('<?php echo intval($map_options_initial_zoom, 10); ?>', 10);

            if(!zoom){ zoom = 7; }
            var allowScroll;
            '<?php echo intval($map_options_initial_mscroll, 10); ?>' === '1' ? allowScroll = true : allowScroll = false;
            var boundaries = <?php echo json_encode($map_options_initial_bound);?> ?
				<?php echo json_encode($map_options_initial_bound);?>: null;

            imcInitializeMap(lat, lng, mapId, 'imcAddress', true, zoom, allowScroll, JSON.parse(boundaries));
            imcFindAddress('imcAddress', false, lat, lng);
        }

        document.getElementById('imcReportAddImgInput').onchange = function (e) {
            var file = jQuery("#imcReportAddImgInput")[0].files[0];
            // Delete image if "Cancel"
            if (document.getElementById("imcReportAttachedImageThumb")) {
                imcDeleteAttachedImage("imcReportAttachedImageThumb");
            }
            // If image is too big
            // Get filesize
            var maxFileSize = '<?php echo imc_file_upload_max_size(); ?>';
            if(file && file.size < maxFileSize) {
                loadImage.parseMetaData(file, function(data) { //read image metadata to get orientation info
                    var orientation = 0;
                    if (data.exif) {
                        orientation = data.exif.get('Orientation');
                        console.log(orientation);
                    }
                    document.getElementById('imcPhotoOri').value = parseInt(orientation, 10);
                    var loadingImage =	loadImage (
                        file,
                        function (img) {
                            if(img.type === "error") {
                                console.log("Error loading image ");
                                jQuery("#imcReportFormSubmitErrors").html("The Photo field must contain only gif, png, jpg files.").show();
                                if (document.getElementById("imcReportAttachedImageThumb")) {
                                    imcDeleteAttachedImage("imcReportAttachedImageThumb");
                                }
                            } else {
                                if (document.getElementById("imcReportAttachedImageThumb")) {
                                    imcDeleteAttachedImage("imcReportAttachedImageThumb");
                                }
                                img.setAttribute("id", "imcReportAttachedImageThumb");
                                img.setAttribute("alt", "Attached photo");
                                img.setAttribute("class", "imc-ReportAttachedImgStyle u-cf");
                                document.getElementById('imcImageSection').appendChild(img);
                                jQuery("#imcReportFormSubmitErrors").html("");
                                jQuery("#imcNoPhotoAttachedLabel").hide();
                                jQuery("#imcLargePhotoAttachedLabel").hide();
                                jQuery("#imcPhotoAttachedFilename").html(" " + file.name);
                                jQuery("#imcPhotoAttachedLabel").show();
                            }
                        },
                        {
                            maxHeight: 200,
                            orientation: orientation,
                            canvas: true
                        }
                    );
                });
            } else {
                imcDeleteAttachedImage('imcReportAddImgInput');
                e.preventDefault();
                jQuery("#imcNoPhotoAttachedLabel").hide();
                jQuery("#imcPhotoAttachedLabel").hide();
                jQuery("#imcLargePhotoAttachedLabel").show();
            }
        };
    </script>

<?php get_footer(); ?>
