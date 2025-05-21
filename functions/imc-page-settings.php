<?php

/**
 * 11.01
 * Create Admin Page about "IMC Settings"
 *
 */


if( is_admin() )
    $my_settings_page = new ImcSettingsPage();

class ImcSettingsPage {

    /*
     * For easier overriding we declared the keys
     * here as well as our tabs array which is populated
     * when registering settings
     */
    private $gmap_settings_key = 'gmap_settings';
    private $notifications_settings_key = 'notifications_settings';
    private $general_settings_key = 'general_settings';
    private $api_settings_key = 'api_settings';
    private $firebase_settings_key = 'firebase_settings';
    private $pb_form_settings_key = 'pb_form_settings'; // New key for PB Form settings
    private $options_key = 'imc_options';
    private $settings_tabs = array();

    /*
     * Fired during plugins_loaded (very very early),
     * so don't miss-use this, only actions and filters,
     * current ones speak for themselves.
     */
    function __construct() {
        add_action( 'init', array( &$this, 'load_settings' ) );
        add_action( 'admin_init', array( &$this, 'register_gmap_settings' ) );
        add_action( 'admin_init', array( &$this, 'register_notifications_settings' ) );
        add_action( 'admin_init', array( &$this, 'register_general_settings' ) );
        add_action( 'admin_init', array( &$this, 'register_api_settings' ) );
        add_action( 'admin_init', array( &$this, 'register_firebase_settings' ) );
        add_action( 'admin_init', array( &$this, 'register_pb_form_settings' ) ); // Action for new settings
        add_action( 'admin_menu', array( &$this, 'render_setting') );
    }

    /*
     * Loads both the gmap and notifications settings from
     * the database into their respective arrays. Uses
     * array_merge to merge with default values if they're
     * missing.
     */
    function load_settings() {
        $this->gmap_settings = (array) get_option( $this->gmap_settings_key );
        $this->notifications_settings = (array) get_option( $this->notifications_settings_key );
        $this->general_settings = (array) get_option( $this->general_settings_key );
        $this->firebase_settings = (array) get_option( $this->firebase_settings_key );
        $this->pb_form_settings = (array) get_option( $this->pb_form_settings_key ); // Initialize PB form settings

        // Merge with defaults
        $this->gmap_settings = array_merge( array(
            'gmap_api_key' => '',
            'gmap_initial_address' => '',
            'gmap_initial_lat' => '40.1349854',
            'gmap_initial_lng' => '22.0264538',
            'gmap_initial_zoom' => '7',
            'gmap_mlang' => 'en',
            'gmap_mreg' => 'GB',
            'gmap_mscroll' => '2',
            'gmap_cluster' => '2',
            'gmap_search_boundaries' => '',
            'gmap_boundaries' => '',
        ), $this->gmap_settings );

        $this->notifications_settings = array_merge( array(
            'notify_new_to_user' => '1',
            'notify_new_to_admin' => '1',
            'notify_cat_to_user' => '1',
            'notify_cat_to_admin' => '1',
            'notify_stat_to_user' => '1',
            'notify_stat_to_admin' => '1',
            'notify_comment_to_admin' => '1',
        ), $this->notifications_settings );

        $this->general_settings = array_merge( array(
            'moderate_new' => '1',
            'slogin_use' => '1',
            'default_view' => '2',
            'imc_custom_slug' => '',
            'imc_comments' => '1',
            'imc_ratings' => '1',
        ), $this->general_settings );

        $this->api_settings = array();
        $this->api_settings = array_merge( array(
            'api_newTitle' => '',
            'api_newKey' => '',
        ), $this->api_settings );


        $this->firebase_settings = array_merge( array(
            'mobile_notification_use' => '1',
            'api_access_key' => '',
            'notify_cat_to_user_mobile' => '1',
            'notify_stat_to_user_mobile' => '1',
            'notify_comment_to_user_mobile' => '1',
        ), $this->firebase_settings );

        // Defaults for PB Form Settings
        // Field names are taken from the `set_meta_fields` method in `pb-additional-fields.php`
        $pb_form_defaults = array(
            'pb_terms_url' => site_url("podminky-pouziti-a-ochrana-osobnich-udaju/"),
            // 'title' and 'description' are handled by standard post title and content, not part of these custom fields.
            'pb_actions_enabled' => true,
            'pb_actions_mandatory' => true,
            'pb_actions_label' => 'Co by se mělo udělat',
            'pb_goals_enabled' => true,
            'pb_goals_mandatory' => true,
            'pb_goals_label' => 'Proč je projekt důležitý, co je jeho cílem',
            'pb_profits_enabled' => true,
            'pb_profits_mandatory' => true,
            'pb_profits_label' => 'Kdo bude mít z projektu prospěch',
            'pb_parcel_enabled' => true,
            'pb_parcel_mandatory' => true,
            'pb_parcel_label' => 'Parcelní číslo',
            // 'photo' (featured image) is handled by WordPress core.
            'pb_map_enabled' => true,
            'pb_map_mandatory' => true,
            'pb_map_label' => 'Mapa (situační nákres) místa, kde se má návrh realizovat (povinná příloha)',
            'pb_cost_enabled' => true,
            'pb_cost_mandatory' => true,
            'pb_cost_label' => 'Předpokládané náklady (povinná příloha)',
            'pb_budget_total_enabled' => true,
            'pb_budget_total_mandatory' => true,
            'pb_budget_total_label' => 'Celkové náklady',
            'pb_budget_increase_enabled' => true,
            'pb_budget_increase_mandatory' => true, // This was true by default for the checkbox logic
            'pb_budget_increase_label' => 'Náklady byly navýšeny o rezervu 10%',
            'pb_attach1_enabled' => true,
            'pb_attach1_mandatory' => false,
            'pb_attach1_label' => 'Vizualizace, výkresy, fotodokumentace… 1',
            'pb_attach2_enabled' => true,
            'pb_attach2_mandatory' => false,
            'pb_attach2_label' => 'Vizualizace, výkresy, fotodokumentace… 2',
            'pb_attach3_enabled' => true,
            'pb_attach3_mandatory' => false,
            'pb_attach3_label' => 'Vizualizace, výkresy, fotodokumentace… 3',
            'pb_name_enabled' => true,
            'pb_name_mandatory' => true,
            'pb_name_label' => 'Jméno a příjmení navrhovatele',
            'pb_phone_enabled' => true,
            'pb_phone_mandatory' => false,
            'pb_phone_label' => 'Tel. číslo',
            'pb_email_enabled' => true,
            'pb_email_mandatory' => true,
            'pb_email_label' => 'E-mail',
            'pb_address_enabled' => true,
            'pb_address_mandatory' => true,
            'pb_address_label' => 'Adresa (název ulice, číslo popisné, část Prahy 8)',
            'pb_signatures_enabled' => true,
            'pb_signatures_mandatory' => true,
            'pb_signatures_label' => 'Podpisový arch (povinná příloha)',
            'pb_age_conf_enabled' => true,
            'pb_age_conf_mandatory' => true,
            'pb_age_conf_label' => 'Prohlašuji, že jsem starší 15 let',
            'pb_agreement_enabled' => true,
            'pb_agreement_mandatory' => true,
            'pb_agreement_label' => 'Souhlasím s <a href="%s" target="_blank" title="Přejít na stránku s podmínkami">podmínkami použití</a>', // %s will be replaced by pb_terms_url
            'pb_completed_enabled' => true,
            'pb_completed_mandatory' => false,
            'pb_completed_label' => 'Popis projektu je úplný a chci ho poslat k vyhodnocení',
        );
        $this->pb_form_settings = array_merge( $pb_form_defaults, $this->pb_form_settings );
    }

    /*
     * Registers the general settings via the Settings API,
     * appends the setting to the tabs array of the object.
     */
    function register_gmap_settings() {
        $this->settings_tabs[$this->gmap_settings_key] = __('Google Map','participace-projekty');

        register_setting( $this->gmap_settings_key, $this->gmap_settings_key );
        add_settings_section( 'section_gmap', __('Google Map Settings','participace-projekty'), array( &$this, 'section_gmap_desc' ), $this->gmap_settings_key );

        add_settings_field( 'gmap_api_key', __('Google Maps API KEY','participace-projekty'), array( &$this, 'field_gmap_api_key' ), $this->gmap_settings_key, 'section_gmap' );

        add_settings_field( 'gmap_initial_address', __('Initial Address','participace-projekty'), array( &$this, 'field_gmap_initial_address' ), $this->gmap_settings_key, 'section_gmap' );

        add_settings_field( 'gmap_search_boundaries', __('Search for Boundaries','participace-projekty'), array( &$this, 'field_gmap_search_boundaries' ), $this->gmap_settings_key, 'section_gmap' );

        add_settings_field( 'gmap_boundaries', __('Boundaries','participace-projekty'), array( &$this, 'field_gmap_boundaries' ), $this->gmap_settings_key, 'section_gmap' );

        add_settings_field( 'gmap_initial_lat', __('Initial Latitude','participace-projekty'), array( &$this, 'field_gmap_initial_lat' ), $this->gmap_settings_key, 'section_gmap' );

        add_settings_field( 'gmap_initial_lng', __('Initial Longitude','participace-projekty'), array( &$this, 'field_gmap_initial_lng' ), $this->gmap_settings_key, 'section_gmap' );

        add_settings_field( 'gmap_initial_zoom', __('Initial Map Zoom','participace-projekty'), array( &$this, 'field_gmap_initial_zoom' ), $this->gmap_settings_key, 'section_gmap' );

        add_settings_field( 'gmap_mlang', __('Map Language','participace-projekty'), array( &$this, 'field_gmap_mlang' ), $this->gmap_settings_key, 'section_gmap' );

        add_settings_field( 'gmap_mreg', __('Map Region','participace-projekty'), array( &$this, 'field_gmap_mreg' ), $this->gmap_settings_key, 'section_gmap' );

        add_settings_field( 'gmap_mscroll', __('Allow zooming with mouse scroll wheel','participace-projekty'), array( &$this, 'field_gmap_mscroll' ), $this->gmap_settings_key, 'section_gmap' );

        //add_settings_field( 'gmap_cluster', __('Clustering markers','participace-projekty'), array( &$this, 'field_gmap_cluster' ), $this->gmap_settings_key, 'section_gmap' );



    }

    /*
     * Registers the advanced settings and appends the
     * key to the plugin settings tabs array.
     */
    function register_notifications_settings() {
        $this->settings_tabs[$this->notifications_settings_key] = __('Mail Notifications','participace-projekty');

        register_setting( $this->notifications_settings_key, $this->notifications_settings_key );
        add_settings_section( 'section_notifications', __('Mail Notifications','participace-projekty'), array( &$this, 'section_notifications_desc' ), $this->notifications_settings_key );

        add_settings_field( 'notify_new_to_user', __('On new issue to user','participace-projekty'), array( &$this, 'field_notify_new_to_user' ), $this->notifications_settings_key, 'section_notifications' );

        add_settings_field( 'notify_new_to_admin', __('On new issue to admins','participace-projekty'), array( &$this, 'field_notify_new_to_admin' ), $this->notifications_settings_key, 'section_notifications' );

        add_settings_field( 'notify_cat_to_user', __('On change category to user','participace-projekty'), array( &$this, 'field_notify_cat_to_user' ), $this->notifications_settings_key, 'section_notifications' );

        add_settings_field( 'notify_cat_to_admin', __('On change category to admins','participace-projekty'), array( &$this, 'field_notify_cat_to_admin' ), $this->notifications_settings_key, 'section_notifications' );

        add_settings_field( 'notify_stat_to_user', __('On change status to user','participace-projekty'), array( &$this, 'field_notify_stat_to_user' ), $this->notifications_settings_key, 'section_notifications' );

        add_settings_field( 'notify_stat_to_admin', __('On change status to admins','participace-projekty'), array( &$this, 'field_notify_stat_to_admin' ), $this->notifications_settings_key, 'section_notifications' );

        add_settings_field( 'notify_comment_to_admin', __('On new comment to admins','participace-projekty'), array( &$this, 'field_notify_comment_to_admin' ), $this->notifications_settings_key, 'section_notifications' );

    }


    /*
     * Registers the general settings and appends the
     * key to the plugin settings tabs array.
     */
    function register_general_settings() {
        $this->settings_tabs[$this->general_settings_key] = __('General','participace-projekty');

        register_setting( $this->general_settings_key, $this->general_settings_key );
        add_settings_section( 'section_general', __('General Settings','participace-projekty'), array( &$this, 'section_general_desc' ), $this->general_settings_key );

        add_settings_field( 'moderate_new', __('Moderate new issues','participace-projekty'), array( &$this, 'field_moderate_new' ), $this->general_settings_key, 'section_general' );

        add_settings_field( 'slogin_use', __('Enable Social Login','participace-projekty'), array( &$this, 'field_slogin_use' ), $this->general_settings_key, 'section_general' );

        add_settings_field( 'default_view', __('Default Issues View','participace-projekty'), array( &$this, 'field_default_view' ), $this->general_settings_key, 'section_general' );

        add_settings_field( 'imc_custom_slug', __('Add a custom slug for the issues page','participace-projekty'), array( &$this, 'field_imc_custom_slug' ), $this->general_settings_key, 'section_general' );

        add_settings_field( 'imc_comments', __('Povolit komentáře návrhů projektů','participace-projekty'), array( &$this, 'field_comment_view' ), $this->general_settings_key, 'section_general' );

        add_settings_field( 'imc_ratings', __('Povolit hodnocení návrhů projektů','participace-projekty'), array( &$this, 'field_ratings_view' ), $this->general_settings_key, 'section_general' );

    }


    function register_api_settings() {
        $this->settings_tabs[$this->api_settings_key] = __('API','participace-projekty');

        register_setting( $this->api_settings_key, $this->api_settings_key );
        add_settings_section( 'section_api', __('API Settings','participace-projekty'), array( &$this, 'section_api_desc' ), $this->api_settings_key );
        add_settings_field( 'allApiKeys', __('API keys','participace-projekty'), array( &$this, 'populate_key_table' ), $this->api_settings_key, 'section_api' );


        add_settings_field( 'api_newTitle', __('Add new API key','participace-projekty'), array( &$this, 'add_new_key' ), $this->api_settings_key, 'section_api' );
        //add_settings_field( 'api_newKey', __('','participace-projekty'), array( &$this, 'field_api_newKey' ), $this->api_settings_key, 'section_api' );

        //add_settings_field( 'moderate_new', __('Moderate new issues','participace-projekty'), array( &$this, 'field_moderate_new' ), $this->general_settings_key, 'section_general' );
    }

    function register_firebase_settings() {
        $this->settings_tabs[$this->firebase_settings_key] = __('Mobile Notifications','participace-projekty');

        register_setting( $this->firebase_settings_key, $this->firebase_settings_key );
        add_settings_section( 'section_firebase', __('Mobile Notifications - Google Firebase Settings','participace-projekty'), array( &$this, 'section_firebase_desc' ), $this->firebase_settings_key );

        add_settings_field( 'mobile_notification_use', __('Enable mobile notifications','participace-projekty'), array( &$this, 'field_mobile_notification_use' ), $this->firebase_settings_key, 'section_firebase' );

        add_settings_field( 'api_access_key', __('Google Firebase API access key','participace-projekty'), array( &$this, 'field_api_access_key' ), $this->firebase_settings_key, 'section_firebase' );

        add_settings_field( 'notify_cat_to_user_mobile', __('When the category of a user\'s issue has changed','participace-projekty'), array( &$this, 'field_notify_cat_to_user_mobile' ), $this->firebase_settings_key, 'section_firebase' );

        add_settings_field( 'notify_stat_to_user_mobile', __('When the status of a user\'s issue has changed','participace-projekty'), array( &$this, 'field_notify_stat_to_user_mobile' ), $this->firebase_settings_key, 'section_firebase' );

        add_settings_field( 'notify_comment_to_user_mobile', __('When a new comment is added to a user\'s issue','participace-projekty'), array( &$this, 'field_notify_comment_to_user_mobile' ), $this->firebase_settings_key, 'section_firebase' );

    }

    /*
     * The following methods provide descriptions
     * for their respective sections, used as callbacks
     * with add_settings_section
     */
    function section_gmap_desc() { echo __('Settings regarding the map and its initial settings.', 'participace-projekty' ); }
    function section_notifications_desc() { echo __('Settings about mail notifications sent to users and administrators, when a new issue or comment is available.','participace-projekty'); }
    function section_general_desc() { echo __('Settings concerning the functionality of the application','participace-projekty'); }
    function section_api_desc() { echo __('Settings concerning the functionality of API','participace-projekty'); }
    function section_firebase_desc() { echo __('Settings concerning the functionality of mobile notifications through Google Firebase','participace-projekty'); }
    function section_pb_form_fields_desc() { echo __('Configure the fields for the project submission form. Uncheck "Enabled" to hide a field. Check "Mandatory" to make it required. You can also customize the labels.', 'participace-projekty'); }
    /*
     * field_gmap_api_key_option field callback, renders a
     * text input, note the name and value.
     */

    /*************************** GMAP TAB FIELDS: ****************************************/

    function field_gmap_api_key() { ?>

        <input placeholder="<?php echo __('Google Maps API Key','participace-projekty'); ?>" type="text" name="<?php echo $this->gmap_settings_key; ?>[gmap_api_key]" value="<?php echo esc_attr( $this->gmap_settings['gmap_api_key'] ); ?>" class="IMCBackendInputMediumStyle" />

    <?php }

    function field_gmap_initial_address() { ?>

        <input placeholder="<?php echo __('Initial address','participace-projekty'); ?>" type="text" name="<?php echo $this->gmap_settings_key; ?>[gmap_initial_address]" id="map_input_id" value="<?php echo esc_attr( $this->gmap_settings['gmap_initial_address'] ); ?>" class="IMCBackendInputMediumStyle" />

        <button type="button" onclick="imcFindAddress('map_input_id', true);" class="IMCBackendButtonStyle">
            <span class="dashicons dashicons-admin-site"></span>
            <?php echo _e('Locate address','participace-projekty'); ?>

        </button>


        <div id="settings_map_canvas" class="imcSettingsMapCanvasStyle"></div>

        <script type="text/javascript">

            /*Google Maps API*/
            google.maps.event.addDomListener(window, 'load', loadDefaultMapValues);

            function loadDefaultMapValues() {
                "use strict";

                <?php $map_options = get_option('gmap_settings'); ?>
                var mapId = 'settings_map_canvas';
                var inputId = 'map_input_id';

                // Checking the saved latlng on settings
                var lat = parseFloat('<?php echo floatval($map_options["gmap_initial_lat"]); ?>');
                var lng = parseFloat('<?php echo floatval($map_options["gmap_initial_lng"]); ?>');
                if (lat === '' || lng === '' ) { lat = 40.13498549; lng = 22.0264538; }

                // Options casting if empty
                var zoom = parseInt('<?php echo $map_options["gmap_initial_zoom"]; ?>', 10);
                if(!zoom){ zoom = 7; }

                var allowScroll = false;

                var boundaries = <?php echo json_encode($map_options["gmap_boundaries"]);?> ?
                <?php echo json_encode($map_options["gmap_boundaries"]);?>: null;

                imcInitializeMap(lat, lng, mapId, inputId, true, zoom, allowScroll, JSON.parse(boundaries));

                jQuery("#imc-boundaries-btn").click(function() {

                    if (jQuery("#imc-boundaries-input").val()) {

                        jQuery('#imc-boundaries-ul').empty();
                        imcBoundariesList = [];

                        var query = jQuery("#imc-boundaries-input").val();

                        try
                        {
                            jQuery.ajax ({
                                //url: "http://api.openstreetmap.org/api/0.6/relation/43992/full",
                                /*url: "http://overpass-api.de/api/interpreter?data=[out:json]; ( rel(43992); <; ); out geom;",*/
                                url: "https://nominatim.openstreetmap.org/",
                                dataType: 'json',
                                type: 'get',
                                data: {
                                    q: query,
                                    polygon_geojson: '1',
                                    format: 'json'
                                },
                                success: function (json) {

                                    var i;

                                    if (json.length < 1) {
                                        alert("<?php echo __("Couldn't find city","improve-my-city"); ?>");
                                        return;
                                    }


                                    for (i=0; i< json.length; i++) {
                                        if (json[i].geojson.type === 'MultiPolygon' || json[i].geojson.type === 'Polygon') {
                                            imcBoundariesList.push(json[i]);
                                        }
                                    }

                                    if (imcBoundariesList.length < 1) {
                                        alert("<?php echo __("Couldn't find city","improve-my-city"); ?>");
                                        return;
                                    }


                                    for (i=0; i<imcBoundariesList.length; i++) {

                                        jQuery("#imc-boundaries-ul").append(
                                            '<li><a onclick="imcSelectBoundaries('+i+')" href="#0">' + imcBoundariesList[i].display_name +'</a></li>'
                                        );

                                    }
                                    jQuery("#imc-boundaries-ul").show();

                                    //boundaries = imcSelectBoundaries(found);

                                },
                                error: function () {
                                    alert("<?php echo __("The service is not available, try again later","improve-my-city"); ?>");
                                    jQuery('#imc-boundaries-ul').empty();
                                    imcBoundariesList = [];
                                }

                            });

                        }
                        catch(e)
                        {
                            alert("<?php echo __("Could not connect to geocoding service","improve-my-city"); ?>");
                        }
                    }
                });
            }
        </script>
        <?php
    }

    function field_gmap_initial_lat() {
        ?>
        <input placeholder="<?php echo __('Latitude','participace-projekty'); ?>" type="text" id ="imcLatValue" name="<?php echo $this->gmap_settings_key; ?>[gmap_initial_lat]" value="<?php echo esc_attr( $this->gmap_settings['gmap_initial_lat'] ); ?>" />
        <?php
    }

    function field_gmap_initial_lng() {
        ?>
        <input placeholder="<?php echo __('Longitude','participace-projekty'); ?>" type="text" id="imcLngValue" name="<?php echo $this->gmap_settings_key; ?>[gmap_initial_lng]" value="<?php echo esc_attr( $this->gmap_settings['gmap_initial_lng'] ); ?>" />
        <?php
    }

    function field_gmap_initial_zoom() {
        ?>
        <input placeholder="<?php echo __('Insert zoom value','participace-projekty'); ?>" type="text" name="<?php echo $this->gmap_settings_key; ?>[gmap_initial_zoom]" value="<?php echo esc_attr( $this->gmap_settings['gmap_initial_zoom'] ); ?>" />
        <?php
    }

    function field_gmap_mlang() {
        ?>
        <input placeholder="<?php echo __("Insert language 'e.g.: en'", 'participace-projekty'); ?>" type="text" name="<?php echo $this->gmap_settings_key; ?>[gmap_mlang]" value="<?php echo esc_attr( $this->gmap_settings['gmap_mlang'] ); ?>" />
        <?php
    }

    function field_gmap_mreg() {
        ?>
        <input placeholder="<?php echo __("Insert region 'e.g.: GB'", 'participace-projekty'); ?>" type="text" name="<?php echo $this->gmap_settings_key; ?>[gmap_mreg]" value="<?php echo esc_attr( $this->gmap_settings['gmap_mreg'] ); ?>" />
        <?php
    }

    function field_gmap_mscroll() {
        ?>
        <input type="radio" name="<?php echo $this->gmap_settings_key; ?>[gmap_mscroll]" value="1" <?php checked(1, $this->gmap_settings['gmap_mscroll'], true); ?>><?php _e('YES','participace-projekty'); ?>

        <input type="radio" name="<?php echo $this->gmap_settings_key; ?>[gmap_mscroll]" value="2" <?php checked(2, $this->gmap_settings['gmap_mscroll'], true); ?>><?php _e('ΝΟ','participace-projekty'); ?>
        <?php
    }

    function field_gmap_cluster() {
        ?>
        <input type="radio" name="<?php echo $this->gmap_settings_key; ?>[gmap_cluster]" value="1" <?php checked(1, $this->gmap_settings['gmap_cluster'], true); ?>><?php _e('YES','participace-projekty'); ?>

        <input type="radio" name="<?php echo $this->gmap_settings_key; ?>[gmap_cluster]" value="2" <?php checked(2, $this->gmap_settings['gmap_cluster'], true); ?>><?php _e('ΝΟ','participace-projekty'); ?>
        <?php
    }

    function field_gmap_search_boundaries() {	?>
        <form id="imc-city-search-form">
            <input class="IMCBackendInputMediumStyle" placeholder="<?php echo __('Add city','participace-projekty');?>"  id="imc-boundaries-input" name="<?php echo $this->gmap_settings_key; ?>[gmap_search_boundaries]" value="<?php echo esc_attr( $this->gmap_settings['gmap_search_boundaries'] ); ?>" />
            <button type="button" class="IMCBackendButtonStyle" id="imc-boundaries-btn">
                <span class="dashicons dashicons-admin-site"></span>
                <?php echo _e('Search','participace-projekty'); ?>

            </button>
        </form>

        <ul id="imc-boundaries-ul" class="IMCBackendBoundariesULStyle">

        </ul>

        <script>
            jQuery(document).ready(function() {
                jQuery(window).keydown(function(event){
                    if(event.key == 'Enter') {
                        event.preventDefault();
                        return false;
                    }
                });
            });

        </script>

        <?php
    }

    function field_gmap_boundaries() {	?>
        <textarea title="City or municipality boundaries" id="imc-boundaries-textarea" class="IMCBackendInputLargeStyle" rows="6" name="<?php echo $this->gmap_settings_key; ?>[gmap_boundaries]" value="<?php echo esc_attr( $this->gmap_settings['gmap_boundaries'] ); ?>" ><?php echo esc_attr( $this->gmap_settings['gmap_boundaries'] ); ?></textarea>
        <?php
    }


    /*************************** NOTIFICATIONS TAB FIELDS: ****************************************/

    function field_notify_new_to_user() {
        ?>
        <input type="radio" name="<?php echo $this->notifications_settings_key; ?>[notify_new_to_user]" value="1" <?php checked(1, $this->notifications_settings['notify_new_to_user'], true); ?>><?php _e('YES','participace-projekty'); ?>

        <input type="radio" name="<?php echo $this->notifications_settings_key; ?>[notify_new_to_user]" value="2" <?php checked(2, $this->notifications_settings['notify_new_to_user'], true); ?>><?php _e('ΝΟ','participace-projekty'); ?>
        <?php
    }

    function field_notify_new_to_admin() {
        ?>
        <input type="radio" name="<?php echo $this->notifications_settings_key; ?>[notify_new_to_admin]" value="1" <?php checked(1, $this->notifications_settings['notify_new_to_admin'], true); ?>><?php _e('YES','participace-projekty'); ?>

        <input type="radio" name="<?php echo $this->notifications_settings_key; ?>[notify_new_to_admin]" value="2" <?php checked(2, $this->notifications_settings['notify_new_to_admin'], true); ?>><?php _e('ΝΟ','participace-projekty'); ?>
        <?php
    }

    function field_notify_cat_to_user() {
        ?>
        <input type="radio" name="<?php echo $this->notifications_settings_key; ?>[notify_cat_to_user]" value="1" <?php checked(1, $this->notifications_settings['notify_cat_to_user'], true); ?>><?php _e('YES','participace-projekty'); ?>

        <input type="radio" name="<?php echo $this->notifications_settings_key; ?>[notify_cat_to_user]" value="2" <?php checked(2, $this->notifications_settings['notify_cat_to_user'], true); ?>><?php _e('ΝΟ','participace-projekty'); ?>
        <?php
    }

    function field_notify_cat_to_admin() {
        ?>
        <input type="radio" name="<?php echo $this->notifications_settings_key; ?>[notify_cat_to_admin]" value="1" <?php checked(1, $this->notifications_settings['notify_cat_to_admin'], true); ?>><?php _e('YES','participace-projekty'); ?>

        <input type="radio" name="<?php echo $this->notifications_settings_key; ?>[notify_cat_to_admin]" value="2" <?php checked(2, $this->notifications_settings['notify_cat_to_admin'], true); ?>><?php _e('ΝΟ','participace-projekty'); ?>
        <?php
    }

    function field_notify_stat_to_user() {
        ?>
        <input type="radio" name="<?php echo $this->notifications_settings_key; ?>[notify_stat_to_user]" value="1" <?php checked(1, $this->notifications_settings['notify_stat_to_user'], true); ?>><?php _e('YES','participace-projekty'); ?>

        <input type="radio" name="<?php echo $this->notifications_settings_key; ?>[notify_stat_to_user]" value="2" <?php checked(2, $this->notifications_settings['notify_stat_to_user'], true); ?>><?php _e('ΝΟ','participace-projekty'); ?>
        <?php
    }

    function field_notify_stat_to_admin() {
        ?>
        <input type="radio" name="<?php echo $this->notifications_settings_key; ?>[notify_stat_to_admin]" value="1" <?php checked(1, $this->notifications_settings['notify_stat_to_admin'], true); ?>><?php _e('YES','participace-projekty'); ?>

        <input type="radio" name="<?php echo $this->notifications_settings_key; ?>[notify_stat_to_admin]" value="2" <?php checked(2, $this->notifications_settings['notify_stat_to_admin'], true); ?>><?php _e('ΝΟ','participace-projekty'); ?>
        <?php
    }

    function field_notify_comment_to_admin() {
        ?>
        <input type="radio" name="<?php echo $this->notifications_settings_key; ?>[notify_comment_to_admin]" value="1" <?php checked(1, $this->notifications_settings['notify_comment_to_admin'], true); ?>><?php _e('YES','participace-projekty'); ?>

        <input type="radio" name="<?php echo $this->notifications_settings_key; ?>[notify_comment_to_admin]" value="2" <?php checked(2, $this->notifications_settings['notify_comment_to_admin'], true); ?>><?php _e('ΝΟ','participace-projekty'); ?>
        <?php
    }



    /*************************** GENERAL TAB FIELDS: ****************************************/

    function field_moderate_new() {
        ?>
        <input type="radio" name="<?php echo $this->general_settings_key; ?>[moderate_new]" value="1" <?php checked(1, $this->general_settings['moderate_new'], true); ?>><?php _e('YES','participace-projekty'); ?>

        <input type="radio" name="<?php echo $this->general_settings_key; ?>[moderate_new]" value="2" <?php checked(2, $this->general_settings['moderate_new'], true); ?>><?php _e('ΝΟ','participace-projekty'); ?>
        <?php
    }

    function field_slogin_use() {
        ?>
        <input type="radio" name="<?php echo $this->general_settings_key; ?>[slogin_use]" value="1" <?php checked(1, $this->general_settings['slogin_use'], true); ?>><?php _e('YES','participace-projekty'); ?>

        <input type="radio" name="<?php echo $this->general_settings_key; ?>[slogin_use]" value="2" <?php checked(2, $this->general_settings['slogin_use'], true); ?>><?php _e('ΝΟ','participace-projekty'); ?>
        <?php
    }

    function field_default_view() {
        ?>
        <input type="radio" name="<?php echo $this->general_settings_key; ?>[default_view]" value="1" <?php checked(1, $this->general_settings['default_view'], true); ?>><?php _e('List','participace-projekty'); ?>

        <input type="radio" name="<?php echo $this->general_settings_key; ?>[default_view]" value="2" <?php checked(2, $this->general_settings['default_view'], true); ?>><?php _e('Grid','participace-projekty'); ?>
        <?php
    }

    function field_imc_custom_slug(){
        ?>
        <input style="width: 260px;" placeholder="<?php echo __('Custom slug, eg: issues','participace-projekty'); ?>" type="text" maxlength="15" name="<?php echo $this->general_settings_key; ?>[imc_custom_slug]" value="<?php echo esc_attr( $this->general_settings['imc_custom_slug'] ); ?>" />
        &nbsp;
        <em><?php echo __('Use only one word - no spaces.','participace-projekty'); ?></em>
        <br>
        <p><?php echo __('Save changes and go to Settings -> Permalinks and click on "Save Permalinks" to enable the custom slug.','participace-projekty'); ?></p>
        <b><?php echo __('Default: imc_issues','participace-projekty'); ?></b>

        <?php
    }

    function field_comment_view() {
        ?>
        <input type="radio" name="<?php echo $this->general_settings_key; ?>[imc_comments]" value="1" <?php checked(1, $this->general_settings['imc_comments'], true); ?>><?php _e('YES','participace-projekty'); ?>

        <input type="radio" name="<?php echo $this->general_settings_key; ?>[imc_comments]" value="2" <?php checked(2, $this->general_settings['imc_comments'], true); ?>><?php _e('ΝΟ','participace-projekty'); ?>
        <?php
    }
    function field_ratings_view() {
        ?>
        <input type="radio" name="<?php echo $this->general_settings_key; ?>[imc_ratings]" value="1" <?php checked(1, $this->general_settings['imc_ratings'], true); ?>><?php _e('YES','participace-projekty'); ?>

        <input type="radio" name="<?php echo $this->general_settings_key; ?>[imc_ratings]" value="2" <?php checked(2, $this->general_settings['imc_ratings'], true); ?>><?php _e('ΝΟ','participace-projekty'); ?>
        <?php
    }



    /*************************** FIREBASE TAB FIELDS: ****************************************/


    function field_mobile_notification_use() {
        ?>
        <input type="radio" name="<?php echo $this->firebase_settings_key; ?>[mobile_notification_use]" value="1" <?php checked(1, $this->firebase_settings['mobile_notification_use'], true); ?>><?php _e('YES','participace-projekty'); ?>

        <input type="radio" name="<?php echo $this->firebase_settings_key; ?>[mobile_notification_use]" value="2" <?php checked(2, $this->firebase_settings['mobile_notification_use'], true); ?>><?php _e('ΝΟ','participace-projekty'); ?>

        <?php
    }

    function field_api_access_key() {
        ?>
        <input placeholder="<?php echo __("Insert API_ACCESS_KEY", 'participace-projekty'); ?>" type="text" name="<?php echo $this->firebase_settings_key; ?>[api_access_key]" value="<?php echo esc_attr( $this->firebase_settings['api_access_key'] ); ?>"  class="IMCBackendInputMediumStyle" />
        <?php
    }

    function field_notify_cat_to_user_mobile() {
        ?>
        <input type="radio" name="<?php echo $this->firebase_settings_key; ?>[notify_cat_to_user_mobile]" value="1" <?php checked(1, $this->firebase_settings['notify_cat_to_user_mobile'], true); ?>><?php _e('YES','participace-projekty'); ?>

        <input type="radio" name="<?php echo $this->firebase_settings_key; ?>[notify_cat_to_user_mobile]" value="2" <?php checked(2, $this->firebase_settings['notify_cat_to_user_mobile'], true); ?>><?php _e('ΝΟ','participace-projekty'); ?>
        <?php
    }

    function field_notify_stat_to_user_mobile() {
        ?>
        <input type="radio" name="<?php echo $this->firebase_settings_key; ?>[notify_stat_to_user_mobile]" value="1" <?php checked(1, $this->firebase_settings['notify_stat_to_user_mobile'], true); ?>><?php _e('YES','participace-projekty'); ?>

        <input type="radio" name="<?php echo $this->firebase_settings_key; ?>[notify_stat_to_user_mobile]" value="2" <?php checked(2, $this->firebase_settings['notify_stat_to_user_mobile'], true); ?>><?php _e('ΝΟ','participace-projekty'); ?>
        <?php
    }

    function field_notify_comment_to_user_mobile() {
        ?>
        <input type="radio" name="<?php echo $this->firebase_settings_key; ?>[notify_comment_to_user_mobile]" value="1" <?php checked(1, $this->firebase_settings['notify_comment_to_user_mobile'], true); ?>><?php _e('YES','participace-projekty'); ?>

        <input type="radio" name="<?php echo $this->firebase_settings_key; ?>[notify_comment_to_user_mobile]" value="2" <?php checked(2, $this->firebase_settings['notify_comment_to_user_mobile'], true); ?>><?php _e('ΝΟ','participace-projekty'); ?>
        <?php
    }



    /*************************** API TAB FIELDS: ****************************************/

    function populate_key_table() {
        $allKeys = imc_get_api_keys();
        ?>

        <table id="IMCBackendTableStyle" class="IMCBackendTableStyle">
            <thead id="headings" class="IMCBackendTableHeaderStyle">
            <tr>
                <th id="KeyTitle"><?php _e('API key title','participace-projekty') ?></th>
                <th id="sKeyTitle"><?php _e('Key','participace-projekty') ?></th>
                <th id="CreatedKeyUser"><?php _e('Created by','participace-projekty') ?></th>
                <th id="ApiKeyID"><?php _e('ID','participace-projekty') ?></th>
                <th id="DeleteApiKey"></th>
            </tr>
            </thead>
            <tbody id="results">

            <?php

            if ( $allKeys ) {
                foreach ($allKeys as $key) { ?>
                    <tr>
                        <td><?php echo esc_html($key->title);?></td>
                        <td><?php echo esc_html($key->skey);?></td>
                        <?php $user_info = get_userdata($key->created_by);
                        $user_name = $user_info->user_login; ?>
                        <td><?php echo esc_html($user_name);?></td>
                        <td><?php echo esc_html($key->id);?></td>
                        <td>
                            <form method="post" action="">
                                <input name="id2Delete" type="hidden" value="<?php echo esc_html($key->id);?>" />
                                <input type="submit" value="Delete Key" />
                            </form>
                        </td>
                    </tr>
                <?php }
            } ?>

            </tbody>
        </table>
        <?php

        if ($_POST) {
            $current_key = (int)$_POST['id2Delete'];
            imc_delete_api_key($current_key);
            echo "<meta http-equiv='refresh' content='0'>";
        }

    }

    function add_new_key() { ?>
        <form method="post" action="">

            <input placeholder="<?php echo __('API key title','participace-projekty'); ?>" type="text" name="<?php echo $this->api_settings_key; ?>[api_title]" value="" class="IMCBackendInputMediumStyle" />

            <div style="clear:both"></div>

            <input placeholder="<?php echo __('API key','participace-projekty'); ?>" type="text" id="api_skey_id" name="<?php echo $this->api_settings_key; ?>[api_skey]" value="" class="IMCBackendInputMediumStyle" readonly />
            <button type="button" onclick="imcGenerateKey();">
                <span class="dashicons dashicons-admin-network"></span>
                <?php echo _e('Generate key','participace-projekty'); ?>
            </button>

            <div style="clear:both;margin-bottom:10px;"></div>

            <input class="button button-primary" type="submit" value="Add Key">
        </form>

        <script type="text/javascript">
            function imc_randomString(length, chars) {
                var result = '';
                for (var i = length; i > 0; --i) result += chars[Math.floor(Math.random() * chars.length)];
                return result;
            }


            function imcGenerateKey(){
                var rString = imc_randomString(16, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
                document.getElementById('api_skey_id').value  = rString;
            }

        </script>
        <?php

        if ($_POST) {
            $api_input = $_POST['api_settings'];
            $safe_title = sanitize_text_field($api_input['api_title']);
            $safe_skey = sanitize_text_field($api_input['api_skey']);
            imc_add_api_option_to_table($safe_title,$safe_skey);
            echo "<meta http-equiv='refresh' content='0'>";
        }


    }


    /*
     * Called during admin_menu, adds an options
     * page under Settings called IMC Settings, rendered
     * using the plugin_options_page method.
     */
    function render_setting() {
        add_options_page( __('IMC Plugin Settings','participace-projekty'), __('IMC Settings','participace-projekty'), 'manage_options', $this->options_key, array( &$this, 'render_options' ) );
    }

    /*
     * Plugin Options page rendering goes here, checks
     * for active tab and replaces key with the related
     * settings key. Uses the render_tabs method
     * to render the tabs.
     */
    function render_options() {
        $tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->gmap_settings_key;

        if($tab != 'api_settings') { ?>
            <div class="wrap">
                <?php $this->render_tabs(); ?>
                <form method="post" action="options.php">
                    <?php
                    wp_nonce_field( 'update-options' );
                    settings_fields( $tab );
                    do_settings_sections( $tab );
                    submit_button();
                    ?>
                </form>
            </div>
        <?php }

        elseif ($tab == 'api_settings') { ?>
            <div class="wrap">
                <?php
                $this->render_tabs();
                do_settings_sections( $tab );
                ?>
            </div>
        <?php }
    }


    /*
     * Renders our tabs in the plugin options page,
     * walks through the object's tabs array and prints
     * them one by one. Provides the heading for the
     * render_options method.
     */
    function render_tabs() {
        $current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->gmap_settings_key;

        //This function has been deprecated.
        //screen_icon();
        echo '<h2 class="nav-tab-wrapper">';
        foreach ($this->settings_tabs as $tab_key => $tab_caption ) {
            $active = $current_tab == $tab_key ? 'nav-tab-active' : '';
            echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->options_key . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
        }
        echo '</h2>';
    }
};


function imc_add_api_option_to_table( $safe_title1, $safe_skey1 ) {
    global $wpdb;

    // verify if this is an auto save routine.
    // If it is our form has not been submitted, so we dont want to do anything
    if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) )
        return;

    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if ( !wp_nonce_field( 'update-options' ) )
        return;


    // Check permissions
    if ( ! ( current_user_can( 'manage_options' )  ) )
        return;

    //Add sanitized input data
    $safe_title = $safe_title1;
    $safe_skey = $safe_skey1;

    //Validating: User Input Data (if length is more than 100 chars)
    if ( strlen( $safe_skey ) > 16 ) {$safe_skey = substr( $safe_skey, 0, 16 );}

    // Make sure your data is set before trying to save it
    if( isset( $safe_title ) && isset( $safe_skey ) ){
        if ( ! (($safe_title == '') || ($safe_skey == '')) ) {

            $theUser =  get_current_user_id();

            $imc_keys_table_name = $wpdb->prefix . 'imc_keys';

            $wpdb->insert(
                $imc_keys_table_name,
                array(
                    'title' => $safe_title,
                    'skey' => $safe_skey,
                    'created' => gmdate("Y-m-d H:i:s",time()),
                    'created_by' => $theUser,
                )
            );
        }

    }
}

function imc_delete_api_key($apikey_id){
    $the_apikey_id = $apikey_id;
    global $wpdb;

    // verify if this is an auto save routine.
    // If it is our form has not been submitted, so we dont want to do anything
    if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) )
        return;

    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if ( !wp_nonce_field( 'update-options' ) )
        return;


    // Check permissions
    if ( ! ( current_user_can( 'manage_options' )  ) )
        return;

    $imc_keys_table_name = $wpdb->prefix . 'imc_keys';
    $wpdb->delete( $imc_keys_table_name, array( 'id' => $the_apikey_id ) );
}


//add_action( 'update_option_general_settings', 'wpse_check_settings', 10, 2 );

function wpse_check_settings( $old_value, $new_value ){
    global $wp_rewrite;
    $old_permalink_structure = $wp_rewrite->permalink_structure;


    $wp_rewrite->set_permalink_structure( $old_permalink_structure );
    $wp_rewrite->flush_rules();

}

    /*
     * Registers the PB Form settings and appends the
     * key to the plugin settings tabs array.
     */
    function register_pb_form_settings() {
        $this->settings_tabs[$this->pb_form_settings_key] = __('Participatory Budget Form','participace-projekty');

        register_setting( $this->pb_form_settings_key, $this->pb_form_settings_key );
        add_settings_section( 'section_pb_form_fields', __('Form Field Configuration', 'participace-projekty'), array( &$this, 'section_pb_form_fields_desc' ), $this->pb_form_settings_key );

        $configurable_fields = array(
            'actions' => 'Actions Field',
            'goals' => 'Goals Field',
            'profits' => 'Profits Field',
            'parcel' => 'Parcel Number Field',
            'map' => 'Map Attachment Field',
            'cost' => 'Cost Attachment Field',
            'budget_total' => 'Budget Total Field',
            'budget_increase' => 'Budget Increase Checkbox',
            'attach1' => 'Attachment 1 Field',
            'attach2' => 'Attachment 2 Field',
            'attach3' => 'Attachment 3 Field',
            'name' => 'Proposer Name Field',
            'phone' => 'Proposer Phone Field',
            'email' => 'Proposer Email Field',
            'address' => 'Proposer Address Field',
            'signatures' => 'Signatures Attachment Field',
            'age_conf' => 'Age Confirmation Checkbox',
            'agreement' => 'Agreement Checkbox',
            'completed' => 'Completed Checkbox'
        );

        foreach ($configurable_fields as $field_id => $default_label_part) {
            add_settings_field( "pb_{$field_id}_label", __($default_label_part . ' Label', 'participace-projekty'), array( &$this, "field_pb_field_label_callback"), $this->pb_form_settings_key, 'section_pb_form_fields', array( 'id' => $field_id ) );
            add_settings_field( "pb_{$field_id}_enabled", __($default_label_part . ' Enabled', 'participace-projekty'), array( &$this, "field_pb_field_enabled_callback"), $this->pb_form_settings_key, 'section_pb_form_fields', array( 'id' => $field_id ) );
            // For 'agreement', mandatory is always true and not configurable via UI, but its label (which includes the terms URL) is.
            if ($field_id !== 'agreement') {
                 add_settings_field( "pb_{$field_id}_mandatory", __($default_label_part . ' Mandatory', 'participace-projekty'), array( &$this, "field_pb_field_mandatory_callback"), $this->pb_form_settings_key, 'section_pb_form_fields', array( 'id' => $field_id ) );
            }
        }
        
        add_settings_field( 'pb_terms_url', __('Terms & Conditions URL', 'participace-projekty'), array( &$this, 'field_pb_terms_url_callback' ), $this->pb_form_settings_key, 'section_pb_form_fields' );
    }

    // Generic callback for text input (labels, URLs)
    function field_pb_field_label_callback( $args ) {
        $field_id = $args['id'];
        $option_name = "pb_{$field_id}_label";
        $value = isset($this->pb_form_settings[$option_name]) ? esc_attr($this->pb_form_settings[$option_name]) : '';
        // Special handling for agreement label to show the URL placeholder
        if ($field_id === 'agreement') {
            printf('<input type="text" name="%s[%s]" value="%s" class="regular-text" /><p class="description">%s</p>',
                $this->pb_form_settings_key,
                $option_name,
                $value,
                __('Use %s where the Terms & Conditions URL should appear.', 'participace-projekty')
            );
        } else {
            printf('<input type="text" name="%s[%s]" value="%s" class="regular-text" />', $this->pb_form_settings_key, $option_name, $value);
        }
    }

    // Generic callback for checkbox (enabled/mandatory)
    function field_pb_field_enabled_callback( $args ) {
        $field_id = $args['id'];
        $option_name = "pb_{$field_id}_enabled";
        $checked = isset($this->pb_form_settings[$option_name]) && $this->pb_form_settings[$option_name] ? 'checked="checked"' : '';
        printf('<input type="checkbox" name="%s[%s]" value="1" %s />', $this->pb_form_settings_key, $option_name, $checked);
    }

    function field_pb_field_mandatory_callback( $args ) {
        $field_id = $args['id'];
        $option_name = "pb_{$field_id}_mandatory";
        $checked = isset($this->pb_form_settings[$option_name]) && $this->pb_form_settings[$option_name] ? 'checked="checked"' : '';
        printf('<input type="checkbox" name="%s[%s]" value="1" %s />', $this->pb_form_settings_key, $option_name, $checked);
    }
    
    function field_pb_terms_url_callback() {
        $value = isset($this->pb_form_settings['pb_terms_url']) ? esc_url($this->pb_form_settings['pb_terms_url']) : '';
        printf('<input type="url" name="%s[pb_terms_url]" value="%s" class="regular-text" placeholder="https://example.com/terms" />', $this->pb_form_settings_key, $value);
    }

} // End class ImcSettingsPage
?>
