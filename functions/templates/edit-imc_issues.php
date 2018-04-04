<?php
/**
 * Template Name: Edit Issue Page
 *
 */

wp_enqueue_script('imc-gmap');

$listpage = getIMCArchivePage();
$postTitleError = '';

if(isset($_POST['submitted']) && isset($_POST['post_nonce_field']) && wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce')) {

	$safe_inserted_id = intval( $_GET['myparam'] );
	$safe_inserted_id = sanitize_text_field( $safe_inserted_id );
	$issue_id = $safe_inserted_id;

	$lat = esc_attr(strip_tags($_POST['imcLatValue']));
	$lng = esc_attr(strip_tags($_POST['imcLngValue']));

	$address = esc_attr(strip_tags($_POST['postAddress']));
	$title = esc_attr(strip_tags($_POST['postTitle']));
	$content = esc_attr(strip_tags($_POST['postContent']));

	//UPDATE THE ISSUE TO DB
	$post_information = array(
		'ID' => $issue_id,
		'post_title' => $title,
		'post_content' => $content,
	);

	$post_id = wp_update_post( $post_information, true );
	if (is_wp_error($post_id)) {
		$errors = $post_id->get_error_messages();
		foreach ($errors as $error) {
			echo $error;
		}
	}

	//we now use $post id to help add out post meta data
	update_post_meta($post_id, 'imc_lat', $lat);
	update_post_meta($post_id, 'imc_lng', $lng);
	update_post_meta($post_id, 'imc_address', $address);

	/************************ ABOUT FEATURED IMAGE  ***********************************/

	$imageScenario = intval(strip_tags($_POST['imcImgScenario']), 10);

	if (intval($imageScenario, 10) === 1) {
		delete_post_thumbnail( $post_id );

	} else if (intval($imageScenario, 10) === 2) {
		$image =  $_FILES['featured_image'];
		$orientation = intval(strip_tags($_POST['imcPhotoOri']), 10);

		if ($orientation !== 0) {
			$attachment_id = imc_upload_img( $image, $post_id, $post_information['post_title'], $orientation);
		} else {
			$attachment_id = imc_upload_img( $image, $post_id, $post_information['post_title'], null);
		}


		set_post_thumbnail( $post_id, $attachment_id );
	}



	/************************ END FEATURED IMAGE  ***********************************/

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

get_header();
// checks if the current user has the ability to post anything
$user = wp_get_current_user();

$safe_inserted_id = intval( $_GET['myparam'] );
$safe_inserted_id = sanitize_text_field( $safe_inserted_id );
$given_issue_id = $safe_inserted_id;

$issue_for_edit = get_post($given_issue_id);
$issue_title = get_the_title($given_issue_id);
$issue_content = $issue_for_edit->post_content;
$issue_address = get_post_meta($given_issue_id, "imc_address", true);

$issue_image = wp_get_attachment_url( get_post_thumbnail_id($given_issue_id) );

$issue_lat = get_post_meta($given_issue_id, "imc_lat", true);
$issue_lng = get_post_meta($given_issue_id,"imc_lng",true);

$plugin_path_url = imc_calculate_plugin_base_url();

if(imc_user_can_edit($given_issue_id, $user->ID)) { ?>
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

                        <div class="imc-row">

                            <!-- Issue's Title -->
                            <div class="imc-grid-6 imc-columns">

                                <h3 class="imc-SectionTitleTextStyle"><?php echo __('Title','participace-projekty'); ?></h3>
                                <input required autocomplete="off" placeholder="<?php echo __('Add a short title for the issue','participace-projekty'); ?>" type="text" name="postTitle" id="postTitle" class="imc-InputStyle" value="<?php echo esc_attr($issue_title); ?>"/>
                                <label id="postTitleLabel" class="imc-ReportFormErrorLabelStyle imc-TextColorPrimary"></label>
                            </div>

                            <!-- Issue's Category -->
                            <div class="imc-grid-6 imc-columns">
                                <h3 class="imc-SectionTitleTextStyle"><?php echo __('Category', 'participace-projekty'); ?></h3>

								<?php $imccategory_currentterm = get_the_terms($given_issue_id , 'imccategory' );
								if ($imccategory_currentterm) {
									$current_category_name = $imccategory_currentterm[0]->name;
									$current_category_id = $imccategory_currentterm[0]->term_id;
									$term_thumb = get_term_by('id', $current_category_id, 'imccategory');
									$cat_thumb_arr = wp_get_attachment_image_src( $term_thumb->term_image);
								}?>

                                <div class="imc-EditCatBlockStyle">
									<?php if ( $cat_thumb_arr ) { ?>

                                        <img src="<?php echo esc_url($cat_thumb_arr[0]); ?>" class="imc-OverviewTileCategoryIcon u-pull-left">

									<?php }	else { ?>

                                        <img src="<?php echo esc_url($plugin_path_url);?>/img/ic_default_cat.png" class="imc-OverviewTileCategoryIcon u-pull-left">

									<?php } ?>

                                    <span class="imc-EditCatNameStyle"><?php echo esc_html($current_category_name); ?></span>
                                </div>

                            </div>
                        </div>

                        <!-- Issue's Description -->
                        <div class="imc-row">
                            <h3 class="u-pull-left imc-SectionTitleTextStyle"><?php echo __('Description','participace-projekty'); ?>&nbsp; </h3> <span class="imc-OptionalTextLabelStyle"> <?php echo __(' (optional)','participace-projekty'); ?></span>
                            <textarea placeholder="<?php echo __('Add a thorough description of the issue','participace-projekty'); ?>" rows="2" class="imc-InputStyle" title="Description" name="postContent" id="postContent"><?php echo esc_html($issue_content); ?><?php if(isset($_POST['postContent'])) { if(function_exists('stripslashes')) { echo esc_html(stripslashes($_POST['postContent'])); } else { echo esc_html($_POST['postContent']); } } ?></textarea>
                        </div>

                        <!-- Issue's Address -->
                        <div class="imc-row-no-margin">
                            <h3 class="imc-SectionTitleTextStyle"><?php echo __('Address','participace-projekty'); ?></h3>

                            <button class="imc-button u-pull-right" type="button" onclick="imcFindAddress('imcAddress', true);">
                                <i class="material-icons md-24 imc-AlignIconToButton">search</i> <?php echo __('Locate', 'participace-projekty') ?>
                            </button>

                            <div style="padding-right: .5em;" class="imc-OverflowHidden">
                                <input required name="postAddress" placeholder="<?php echo __('Add an address','participace-projekty'); ?>" value="<?php echo esc_attr($issue_address);?>" id="imcAddress" class="u-pull-left imc-InputStyle"/>
                            </div>

                        </div>

                        <!-- Issue's Map -->
                        <div class="imc-row">

                            <div id="imcReportIssueMapCanvas" class="u-full-width imc-ReportIssueMapCanvasStyle"></div>
                        </div>

                        <!-- Issue's Image -->
                        <div class="imc-row" id="imcImageSection">

                            <h3 class="u-pull-left imc-SectionTitleTextStyle"><?php echo __('Photo','participace-projekty');  ?>&nbsp; </h3><span class="imc-OptionalTextLabelStyle"> <?php echo __(' (optional)','participace-projekty'); ?></span>

							<?php if ($issue_image) { ?>

                                <div class="u-cf">
                                    <input autocomplete="off" class="imc-ReportAddImgInputStyle" id="imcReportAddImgInput" type="file" name="featured_image" />
                                    <label for="imcReportAddImgInput">
                                        <i class="material-icons md-24 imc-AlignIconToButton">photo</i>
										<?php echo __('Add photo','participace-projekty'); ?>
                                    </label>

                                    <button type="button" class="imc-button" onclick="imcDeleteAttachedImage('imcReportAddImgInput'); "><i class="material-icons md-24 imc-AlignIconToButton">delete</i><?php echo __('Delete Photo', 'participace-projekty');?></button>
                                </div>

                                <span style="display: none;" id="imcNoPhotoAttachedLabel" class="imc-ReportGenericLabelStyle imc-TextColorSecondary"><?php echo __('No photo attached','participace-projekty'); ?></span>
                                <span style="display: none;" id="imcLargePhotoAttachedLabel" class="imc-ReportGenericLabelStyle imc-TextColorSecondary"><?php echo __('Photo size must be less than 2MB, please resize it or select a smaller one!','participace-projekty'); ?></span>
                                <span id="imcPhotoAttachedLabel" class="imc-ReportGenericLabelStyle imc-TextColorSecondary"><?php echo __('A photo has been selected:','participace-projekty'); ?></span>
                                <span class="imc-ReportGenericLabelStyle imc-TextColorPrimary" id="imcPhotoAttachedFilename"></span>

                                <br>
                                <br>

                                <img id="imcPreviousImg" class="u-cf" style="max-height: 200px;" src="<?php echo esc_url($issue_image); ?>">

							<?php } else {  ?>

                                <div class="u-cf">
                                    <input autocomplete="off" class="imc-ReportAddImgInputStyle" id="imcReportAddImgInput" type="file" name="featured_image" />
                                    <label for="imcReportAddImgInput">
                                        <i class="material-icons md-24 imc-AlignIconToButton">photo</i>
										<?php echo __('Add photo','participace-projekty'); ?>
                                    </label>

                                    <button type="button" class="imc-button" onclick="imcDeleteAttachedImage('imcReportAddImgInput');"><i class="material-icons md-24 imc-AlignIconToButton">delete</i><?php echo __('Delete Photo', 'participace-projekty');?></button>
                                </div>

                                <span id="imcNoPhotoAttachedLabel" class="imc-ReportGenericLabelStyle imc-TextColorSecondary"><?php echo __('No photo attached','participace-projekty'); ?></span>
                                <span style="display: none;" id="imcPhotoAttachedLabel" class="imc-ReportGenericLabelStyle imc-TextColorSecondary"><?php echo __('A photo has been selected:','participace-projekty'); ?></span>
                                <span style="display: none;" id="imcLargePhotoAttachedLabel" class="imc-ReportGenericLabelStyle imc-TextColorSecondary"><?php echo __('Photo size must be less than 2MB, please resize it or select a smaller one!','participace-projekty'); ?></span>
                                <span class="imc-ReportGenericLabelStyle imc-TextColorPrimary" id="imcPhotoAttachedFilename"></span>

							<?php } ?>

                        </div>

                        <div class="imc-row">
                            <span class="u-pull-left imc-ReportFormSubmitErrorsStyle" id="imcReportFormSubmitErrors"></span>
                        </div>

                    </div>

                    <div class="imc-row">
						<?php wp_nonce_field('post_nonce', 'post_nonce_field'); ?>
                        <input type="hidden" name="submitted" id="submitted" value="true" />
                        <input id="imcEditIssueSubmitBtn" class="imc-button imc-button-primary imc-button-block" type="submit" value="<?php echo __('Save changes','participace-projekty'); ?>" />
                    </div>

                    <!-- Hidden inputs to pass to php -->
                    <input title="lat" type="hidden" id="imcLatValue" name="imcLatValue"/>
                    <input title="lng" type="hidden" id="imcLngValue" name="imcLngValue"/>
                    <input title="orientation" type="hidden" id="imcPhotoOri" name="imcPhotoOri"/>
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
                <img src="<?php echo esc_url($plugin_path_url);?>/img/img_banner.png" class="u-full-width">

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

            var validator = new FormValidator('report_an_issue_form', [{
                name: 'postTitle',
                display: 'Title',
                rules: 'required|min_length[3]|max_length[255]'
            }, {
                name: 'postAddress',
                display: 'Address',
                rules: 'required'
            }, {
                name: 'featured_image',
                display: 'Photo',
                rules: 'is_file_type[gif,GIF,png,PNG,jpg,JPG,jpeg,JPEG]'
            }], function(errors) {
                if (errors.length > 0) {
                    var i, j;
                    var errorLength;
                    jQuery("#imcReportFormSubmitErrors").html("");
                    jQuery('#postTitleLabel').html();

                    for (i = 0, errorLength = errors.length; i < errorLength; i++) {
                        if (errors[i].name === "postTitle") {
                            for(j=1; j < errors[i].messages.length; j++) {
                                jQuery('#'+errors[i].id+'Label').html(errors[i].messages[j]);
                            }
                        }
                        else if (errors[i].name === "featured_image") {
                            imcDeleteAttachedImage('imcReportAddImgInput');
                            jQuery("#imcReportFormSubmitErrors").html(errors[i].message);
                        }
                    }
                } else {
                    jQuery('#imcEditIssueSubmitBtn').attr('disabled', 'disabled');
                }
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
