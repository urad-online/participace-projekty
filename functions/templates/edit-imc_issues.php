<?php
/**
 * Template Name: Edit Issue Page
 *
 */

include_once( PB_PATH . 'functions/pb-project-edit.php' );
$project_single = null;

wp_enqueue_script('imc-gmap');

$listpage = getIMCArchivePage();
$postTitleError = '';
$all_status_terms = get_terms( 'imcstatus' , array( 'hide_empty' => 0 , 'orderby' => 'id', 'order' => 'ASC') );

if(isset($_POST['submitted']) && isset($_POST['post_nonce_field']) && wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce')) {

	$project_save = new pbProjectSaveData;

	$post_id = $project_save->update_project();
	if (is_wp_error($post_id)) {
		$errors = $post_id->get_error_messages();
		foreach ($errors as $error) {
			echo $error;
		}
		exit;
	}

	if($post_id){
		wp_redirect(get_permalink($listpage[0]->ID));
		exit;
	}

}

/************************** GMAP SETTINGS *************************************/
$map_options = get_option('gmap_settings');
$map_options_initial_lat = $map_options["gmap_initial_lat"];
$map_options_initial_lng = $map_options["gmap_initial_lng"];
$map_options_initial_zoom = $map_options["gmap_initial_zoom"];
$map_options_initial_mscroll = $map_options["gmap_mscroll"];
$map_options_initial_bound = $map_options["gmap_boundaries"];
/*****************************************************************************/

/* create instance of class for form rendering */
$project_single = new pbProjectEdit;

get_header();
// checks if the current user has the ability to post anything
$user = wp_get_current_user();

$safe_inserted_id = intval( $_GET['myparam'] );
$safe_inserted_id = sanitize_text_field( $safe_inserted_id );
$given_issue_id   = $safe_inserted_id;

$issue_for_edit = get_post($given_issue_id);
// $issue_title 	= get_the_title($given_issue_id);
// $issue_content 	= $issue_for_edit->post_content;
// $issue_image 	= wp_get_attachment_url( get_post_thumbnail_id($given_issue_id) );

$pb_project_meta = get_post_meta($safe_inserted_id);
$pb_project_meta[ 'issue_image'][0] = wp_get_attachment_url( get_post_thumbnail_id($given_issue_id) );
$pb_project_meta[ 'postTitle'][0] = get_the_title($given_issue_id);
$pb_project_meta[ 'postContent'][0] = $issue_for_edit->post_content;
$imccategory_currentterm = get_the_terms($given_issue_id , 'imccategory' );
if ($imccategory_currentterm) {
	$pb_project_meta[ 'my_custom_taxonomy'][0] = $imccategory_currentterm[0]->term_id;
}


$pb_project_status = wp_get_object_terms($given_issue_id, 'imcstatus');
$pb_project_edit_completed = '0';
foreach ($all_status_terms as $key => $term ) {
	if ( $term->slug == $pb_project_status[0]->slug) {
		$pb_project_edit_completed = $key;
	}
}
if ($pb_project_edit_completed == '0') {
	$pb_project_meta[ 'pb_project_edit_completed'][0] = "0";
} else {
	$pb_project_meta[ 'pb_project_edit_completed'][0] = "1";
}

$issue_address 	= $pb_project_meta[ 'imc_address'][0];
$issue_lat 		= $pb_project_meta[ 'imc_lat'][0];
$issue_lng 		= $pb_project_meta[ 'imc_lng'][0];

$plugin_path_url = imc_calculate_plugin_base_url();

if(pb_user_can_edit($given_issue_id, $user->ID)) { ?>
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

                        <div class="imc-row-no-margin">

                            <h2 class="imc-PageTitleTextStyle imc-TextColorPrimary u-pull-left"><?php echo __('Edit issue','participace-projekty'); ?></h2>
                            <div class="u-pull-right">

                                <span class="imc-Text-MD imc-TextColorSecondary imc-TextBold imc-FontRoboto">#</span>
                                <span class="imc-Text-MD imc-TextColorSecondary imc-TextMedium imc-FontRoboto"><?php echo esc_html($given_issue_id); ?></span>
                            </div>
                        </div>


                        <div class="imc-Separator"></div>

                        <!-- <div class="imc-row">

                            <div class="imc-grid-6 imc-columns">

                                <h3 class="imc-SectionTitleTextStyle"><?php //echo '1. ' . __('Title','participace-projekty'); ?></h3>
								<input autocomplete="off" placeholder="<?php //echo __('Add a short title for the issue','participace-projekty'); ?>"
									type="text" name="postTitle" id="postTitle" class="imc-InputStyle" value="<?php //echo esc_attr($issue_title); ?>"/>
                                <label id="postTitleLabel" class="imc-ReportFormErrorLabelStyle imc-TextColorPrimary"></label>
                            </div>

                            <div class="imc-grid-6 imc-columns">
                                <h3 class="imc-SectionTitleTextStyle"><?php //echo '2. ' . __('Category', 'participace-projekty'); ?></h3>

								<?php //$imccategory_currentterm = get_the_terms($given_issue_id , 'imccategory' );
								//if ($imccategory_currentterm) {
									// $current_category_name = $imccategory_currentterm[0]->name;
									//$current_category_id = $imccategory_currentterm[0]->term_id;
									// $term_thumb = get_term_by('id', $current_category_id, 'imccategory');
									// $cat_thumb_arr = wp_get_attachment_image_src( $term_thumb->term_image);
								//}?>

								<label class="imc-CustomSelectStyle u-full-width">

									<?php //echo imc_insert_cat_dropdown( 'my_custom_taxonomy', $current_category_id ); ?>

                                </label>

								<label id="my_custom_taxonomyLabel" class="imc-ReportFormErrorLabelStyle imc-TextColorPrimary"></label>
                            </div>
                        </div> -->

                        <!-- <div class="imc-row">
                            <h3 class="u-pull-left imc-SectionTitleTextStyle"><?php //echo '3. ' . __('Description','participace-projekty'); ?>&nbsp; <?php// echo $project_single->render_mandatory(false)?></h3>
                            <textarea placeholder="<?php// echo __('Add a thorough description of the issue','participace-projekty'); ?>" rows="2"
								class="imc-InputStyle" title="Description" name="postContent" id="postContent"><?php //echo esc_html($issue_content); ?>
								  <?php  //if(isset($_POST['postContent'])) { if(function_exists('stripslashes')) { echo esc_html(stripslashes($_POST['postContent'])); } else { echo esc_html($_POST['postContent']); } } ?></textarea>
							<label id="postContentLabel" class="imc-ReportFormErrorLabelStyle imc-TextColorPrimary"></label>
                        </div> -->

						<?php echo $project_single->template_project_edit(
										array(
											'lat' => $pb_project_meta[ 'imc_lat'][0],
											'lon' => $pb_project_meta[ 'imc_lng'][0],
										),
										$pb_project_meta
										) ;?>

                        <div class="imc-row">
                            <span class="u-pull-left imc-ReportFormSubmitErrorsStyle" id="imcReportFormSubmitErrors"></span>
                        </div>

                    </div>

                    <div class="imc-row">
						<?php wp_nonce_field('post_nonce', 'post_nonce_field'); ?>
                        <input type="hidden" name="submitted" id="submitted" value="true" />
                        <input id="imcEditIssueSubmitBtn" class="imc-button imc-button-primary imc-button-block pb-project-submit-btn"
							type="submit" value="Odeslat" />
                    </div>

                    <!-- Hidden inputs to pass to php -->
                    <input title="imgScenario" type="hidden" id="imcImgScenario" name="imcImgScenario" value="0"/>


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

                    <h3 class="imc-FontRoboto imc-Text-LG imc-TextColorSecondary imc-TextMedium imc-CenterContents"><?php echo __('You are not authorised to edit an issue','participace-projekty'); ?></h3>

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

    (function() {
        /*Google Maps API*/
        google.maps.event.addDomListener(window, 'load', imcInitMap);

        jQuery( document ).ready(function() {

            var validator = new FormValidator('report_an_issue_form',<?PHP
				echo pb_new_project_mandatory_fields_js_validation();
				?>, function(errors, events) {
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
								jQuery('#'+errors[i].id+'Label').html(errors[i].messages[j]);
								jQuery("#imcReportFormSubmitErrors").append("<p>"+errors[i].message+"</p>");
							}
                        }
                    }
                } else {
                    jQuery('#imcEditIssueSubmitBtn').attr('disabled', 'disabled');
					jQuery('label.imc-ReportFormErrorLabelStyle').html();
                }
            });
			validator.registerConditional( 'pb_project_js_validate_required', function(field){
				/* povinna pole se validuji pouze pokud narhovatel zaskrtne odeslat k vyhodnoceni
				 plati pro pole s pravidlem "depends" */
				// console.log('validuju povinna pole');
				return jQuery('#pb_project_edit_completed').prop('checked');
			});
        });
    })();

    function imcInitMap() {
        "use strict";

        var mapId = "imcReportIssueMapCanvas";

        // Checking the current latlng of the issue
        var lat = parseFloat('<?php echo floatval($issue_lat); ?>');
        var lng = parseFloat('<?php echo floatval($issue_lng); ?>');

        var allowScroll;
        "<?php echo intval($map_options_initial_mscroll, 10); ?>" === '1' ? allowScroll = true : allowScroll = false;

        var boundaries = <?php echo json_encode($map_options_initial_bound);?> ?
			<?php echo json_encode($map_options_initial_bound);?>: null;

        imcInitializeMap(lat, lng, mapId, 'imcAddress', true, 15, allowScroll, JSON.parse(boundaries));

        imcFindAddress('imcAddress', false, lat, lng);

    }

    document.getElementById('imcReportAddImgInput').onchange = function (e) {

        if (document.getElementById('imcPreviousImg')) {
            jQuery('#imcPreviousImg').remove();
        }

        var file = jQuery("#imcReportAddImgInput")[0].files[0];

        // Delete image if "Cancel"
        if (document.getElementById("imcReportAttachedImageThumb")) {
            imcDeleteAttachedImage("imcReportAttachedImageThumb");
        }

        // If image is too big
        if(file && file.size < 2097152) { // 2 MB (this size is in bytes)

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

                            document.getElementById('imcImgScenario').value = "2";

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

            e.preventDefault();
            jQuery("#imcNoPhotoAttachedLabel").hide();
            jQuery("#imcPhotoAttachedLabel").hide();
            jQuery("#imcLargePhotoAttachedLabel").show();

        }

    };

</script>

<?php get_footer(); ?>
