<div class="imc-row">
     <div class="imc-grid-6 imc-columns">
         <h3 class="imc-SectionTitleTextStyle"><?php echo __("Proposer's full name",'participace-projekty'); ?></h3>

         <input required autocomplete="off" placeholder="<?php echo __('Fill a name and surname','participace-projekty'); ?>" type="text" name="pb_project_navrhovatel_jmeno" id="pb_project_navrhovatel_jmeno" class="imc-InputStyle" />

         <label id="pb_project_navrhovatel_jmenoLabel" class="imc-ReportFormErrorLabelStyle imc-TextColorPrimary"></label>

     </div>

     <div class="imc-grid-3 imc-columns">
         <h3 class="imc-SectionTitleTextStyle"><?php echo __("Phone",'participace-projekty'); ?></h3>

         <input required autocomplete="off" placeholder="<?php echo __("Enter proposer's phone number",'participace-projekty'); ?>" type="text" name="pb_project_navrhovatel_telefon" id="pb_project_navrhovatel_telefon" class="imc-InputStyle" />

         <label id="pb_project_navrhovatel_telefonLabel" class="imc-ReportFormErrorLabelStyle imc-TextColorPrimary"></label>
     </div>

     <div class="imc-grid-3 imc-columns">
         <h3 class="imc-SectionTitleTextStyle"><?php echo __("E-mail",'participace-projekty'); ?></h3>

         <input required autocomplete="off" placeholder="<?php echo __("Enter proposer's e-mail",'participace-projekty'); ?>" type="text" name="pb_project_navrhovatel_email" id="pb_project_navrhovatel_email" class="imc-InputStyle" />

         <label id="pb_project_navrhovatel_emailLabel" class="imc-ReportFormErrorLabelStyle imc-TextColorPrimary"></label>
     </div>

 </div>
<?php
 // $filename should be the path to a file in the upload directory.
 $filename = '/path/to/uploads/2013/03/filename.jpg';

 // The ID of the post this attachment is for.
 $parent_post_id = 37;

 // Check the type of file. We'll use this as the 'post_mime_type'.
 $filetype = wp_check_filetype( basename( $filename ), null );

 // Get the path to the upload directory.
 $wp_upload_dir = wp_upload_dir();

 // Prepare an array of post data for the attachment.
 $attachment = array(
     'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
     'post_mime_type' => $filetype['type'],
     'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
     'post_content'   => '',
     'post_status'    => 'inherit'
 );

 // Insert the attachment.
 $attach_id = wp_insert_attachment( $attachment, $filename, $parent_post_id );

 // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
 require_once( ABSPATH . 'wp-admin/includes/image.php' );

 // Generate the metadata for the attachment, and update the database record.
 $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
 wp_update_attachment_metadata( $attach_id, $attach_data );

 set_post_thumbnail( $parent_post_id, $attach_id );

// dalsi ukazka
 $file = '/path/to/file.png';
 $filename = basename($file);
 $upload_file = wp_upload_bits($filename, null, file_get_contents($file));
 if (!$upload_file['error']) {
 	$wp_filetype = wp_check_filetype($filename, null );
 	$attachment = array(
 		'post_mime_type' => $wp_filetype['type'],
 		'post_parent' => $parent_post_id,
 		'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
 		'post_content' => '',
 		'post_status' => 'inherit'
 	);
 	$attachment_id = wp_insert_attachment( $attachment, $upload_file['file'], $parent_post_id );
 	if (!is_wp_error($attachment_id)) {
 		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
 		$attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file['file'] );
 		wp_update_attachment_metadata( $attachment_id,  $attachment_data );
 	}
 }

 ?>

<!-- zobrazeni mapy -->
 <div class="imc-row-no-margin">

     <h3 class="imc-SectionTitleTextStyle"><?php echo '4. ' . __('Address','participace-projekty'); ?></h3>



     <button class="imc-button u-pull-right" type="button" onclick="imcFindAddress('imcAddress', true);">

         <i class="material-icons md-24 imc-AlignIconToButton">search</i> <?php echo __('Locate', 'participace-projekty') ?>

     </button>



     <div style="padding-right: .5em;" class="imc-OverflowHidden">

         <input required name="postAddress" placeholder="<?php echo __('Add an address','participace-projekty'); ?>" id="imcAddress" class="u-pull-left imc-InputStyle"/>

     </div>

     <input title="lat" type="hidden" id="imcLatValue" name="imcLatValue"/>

     <input title="lng" type="hidden" id="imcLngValue" name="imcLngValue"/>

 </div>



 <!-- Issue's Map -->

 <div class="imc-row">

     <div id="imcReportIssueMapCanvas" class="u-full-width imc-ReportIssueMapCanvasStyle"></div>

 </div>

 <!-- zobrazeni fotky -->
 <div class="imc-row" id="imcImageSection">



     <h3 class="u-pull-left imc-SectionTitleTextStyle"><?php echo __('Photo','participace-projekty');  ?>&nbsp; </h3><span class="imc-OptionalTextLabelStyle"> <?php echo __(' (optional)','participace-projekty'); ?></span>



     <div class="u-cf">

         <input autocomplete="off" class="imc-ReportAddImgInputStyle" id="imcReportAddImgInput" type="file" name="featured_image" />

         <label for="imcReportAddImgInput">

             <i class="material-icons md-24 imc-AlignIconToButton">photo</i>

            <?php echo __('Add photo','participace-projekty'); ?>

         </label>



         <button type="button" class="imc-button" onclick="imcDeleteAttachedImage('imcReportAddImgInput');"><i class="material-icons md-24 imc-AlignIconToButton">delete</i><?php echo __('Delete Photo', 'participace-projekty');?></button>

     </div>



     <span id="imcNoPhotoAttachedLabel" class="imc-ReportGenericLabelStyle imc-TextColorSecondary"><?php echo __('No photo attached','participace-projekty'); ?></span>

     <span style="display: none;" id="imcLargePhotoAttachedLabel" class="imc-ReportGenericLabelStyle imc-TextColorSecondary"><?php echo __('Photo size must be smaller in size, please resize it or select a smaller one!','participace-projekty'); ?></span>

     <span style="display: none;" id="imcPhotoAttachedLabel" class="imc-ReportGenericLabelStyle imc-TextColorSecondary"><?php echo __('A photo has been selected:','participace-projekty'); ?></span>

     <span class="imc-ReportGenericLabelStyle imc-TextColorPrimary" id="imcPhotoAttachedFilename"></span>



 </div>
