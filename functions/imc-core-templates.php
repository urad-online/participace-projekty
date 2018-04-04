<?php
/**
 * 12.01
 * Add insert, edit & archive templates to every theme
 * 
 */

class ImcTemplate {

    //A reference to an instance of this class.
    private static $instance;

    //The array of templates that IMC plugin tracks.
    protected $templates;

    //Returns an instance of this class.
    public static function get_instance() {
        if ( null == self::$instance ) { self::$instance = new ImcTemplate();}
        return self::$instance;
    }


    //Initializes the ImcTemplate by setting filters and administration functions.
    private function __construct() {

        $this->templates = array();

        // Add a filter to the attributes metabox to inject template into the cache.
        if ( version_compare( floatval( get_bloginfo( 'version' ) ), '4.7', '<' ) ) {
            // 4.6 and older
            add_filter(
                'page_attributes_dropdown_pages_args',
                array( $this, 'register_project_templates' )
            );
        } else {
            // Add a filter to the wp 4.7 version attributes metabox
            add_filter(
                'theme_page_templates', array( $this, 'add_new_template' )
            );
        }

        // Add a filter to the save post to inject out template into the page cache
        add_filter(
            'wp_insert_post_data',
            array( $this, 'register_project_templates' )
        );

        // Add a filter to the template include to determine if the page has our
        // template assigned and return it's path
        add_filter(
            'template_include',
            array( $this, 'view_project_template')
        );

        // Add your templates to this array.
        $this->templates = array(
            '/templates/insert-imc_issues.php'     => 'Insert Issue Page',
            '/templates/edit-imc_issues.php'     => 'Edit Issue Page',
            '/templates/archive-imc_issues.php'     => 'Archive Issue Page',
        );

    }

    //Adds our templates to the page dropdown for v4.7+
    public function add_new_template( $posts_templates ) {
        $posts_templates = array_merge( $posts_templates, $this->templates );
        return $posts_templates;
    }


    //Adds our templates to the pages cache in order to trick WordPress into thinking the template file exists where it doens't really exist.
    public function register_project_templates( $atts ) {

        // Create the key used for the themes cache
        $cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

        // Retrieve the cache list.
        // If it doesn't exist, or it's empty prepare an array
        $templates = wp_get_theme()->get_page_templates();
        if ( empty( $templates ) ) {
            $templates = array();
        }

        // New cache, therefore remove the old one
        wp_cache_delete( $cache_key , 'themes');

        // Now add our template to the list of templates by merging our templates
        // with the existing templates array from the cache.
        $templates = array_merge( $templates, $this->templates );

        // Add the modified cache to allow WordPress to pick it up for listing
        // available templates
        wp_cache_add( $cache_key, $templates, 'themes', 1800 );

        return $atts;

    }

    //Checks if the templates is assigned to the page
    public function view_project_template( $template ) {

        // Get global post
        global $post;

        // Return template if post is empty
        if ( ! $post ) {
            return $template;
        }

        // Return default template if we don't have a custom one defined
        if ( ! isset( $this->templates[get_post_meta(
                $post->ID, '_wp_page_template', true
            )] ) ) {
            return $template;
        }

        $file = plugin_dir_path( __FILE__ ). get_post_meta(
                $post->ID, '_wp_page_template', true
            );

        // Just to be safe, we check if the file exist first
        if ( file_exists( $file ) ) {
            return $file;
        } else {
            echo $file;
        }

        // Return template
        return $template;

    }

}

add_action( 'plugins_loaded', array( 'ImcTemplate', 'get_instance' ) );

/************************************************************************************************************************/

/**
 * 12.02
 * Creates page "IMC - Report Issue page" on plugin activation
 * check improve-my-city.php file to!
 */

function imc_create_reporting_page() {

    if (! imcplus_get_page_by_slug('imc-report-issue')) {
        $new_page_id = wp_insert_post(array(
            'post_title' => 'IMC - Report Issue page',
            'post_type' => 'page',
            'post_name' => 'imc-report-issue',
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'post_content' => '',
            'post_status' => 'publish',
            'post_author' => get_user_by('id', 1)->user_id,
            'menu_order' => 0,
        ));
        if ($new_page_id && !is_wp_error($new_page_id)) {
            update_post_meta($new_page_id, '_wp_page_template', '/templates/insert-imc_issues.php');
        }

        update_option('hclpage', $new_page_id);
    }
}

/************************************************************************************************************************/

/**
* 12.03
* Creates page "IMC - Edit Issue page" on plugin activation
* check improve-my-city.php file to!
 */

function imc_create_edit_page() {

    if (! imcplus_get_page_by_slug('imc-edit-issue')) {
        $new_page_id = wp_insert_post(array(
            'post_title' => 'IMC - Edit Issue page',
            'post_type' => 'page',
            'post_name' => 'imc-edit-issue',
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'post_content' => '',
            'post_status' => 'publish',
            'post_author' => get_user_by('id', 1)->user_id,
            'menu_order' => 0,
        ));
        if ($new_page_id && !is_wp_error($new_page_id)) {
            update_post_meta($new_page_id, '_wp_page_template', '/templates/edit-imc_issues.php');
        }

        update_option('hclpage', $new_page_id);
    }
}

/************************************************************************************************************************/

/**
 * 12.04
 * Creates page "IMC - Participace na projektech Main page" on plugin activation
 * check improve-my-city.php file to!
 */

function imc_create_main_page() {

    if (! imcplus_get_page_by_slug('participace-projekty')) {
        $new_page_id = wp_insert_post(array(
            'post_title' => 'IMC - Participace na projektech Main page',
            'post_type' => 'page',
            'post_name' => 'participace-projekty',
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'post_content' => '',
            'post_status' => 'publish',
            'post_author' => get_user_by('id', 1)->user_id,
            'menu_order' => 0,
        ));
        if ($new_page_id && !is_wp_error($new_page_id)) {
            update_post_meta($new_page_id, '_wp_page_template', '/templates/archive-imc_issues.php');
        }

        update_option('hclpage', $new_page_id);
    }
}