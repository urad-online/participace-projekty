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

<div class="imc-row">

    <h3 class="u-pull-left imc-SectionTitleTextStyle"><?php echo __('Address','participace-projekty'); ?>&nbsp; </h3> <span class="imc-OptionalTextLabelStyle"> <?php echo __(' (optional)','participace-projekty'); ?></span>

    <textarea  placeholder="<?php echo __('Fill street, number, City district','participace-projekty'); ?>" rows="2"
        class="imc-InputStyle" title="Address" name="pb_project_navrhovatel_adresa"
        id="pb_project_navrhovatel_adresa"><?php if(isset($_POST['postContent'])) {
            if(function_exists('stripslashes')) { echo esc_html(stripslashes($_POST['postContent'])); }
            else { echo esc_html($_POST['postContent']); } } ?>
    </textarea>

</div>
