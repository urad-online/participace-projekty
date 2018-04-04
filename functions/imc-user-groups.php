<?php

class imc_UserGroupsTaxonomy {
    private static $taxonomies	= array();

    /**
     * Register all the hooks and filters we can in advance
     * Some will need to be registered later on, as they require knowledge of the taxonomy name
     */
    public function __construct() {
        // Taxonomies
        add_action('registered_taxonomy',		array($this, 'registered_taxonomy'), 10, 3);

        // Menus
        add_action('admin_menu',				array($this, 'admin_menu'));
        add_filter('parent_file',				array($this, 'parent_menu'));

        // User Profiles
        add_action('show_user_profile',			array($this, 'user_profile'));
        add_action('edit_user_profile',			array($this, 'user_profile'));
        add_action('personal_options_update',	array($this, 'save_profile'));
        add_action('edit_user_profile_update',	array($this, 'save_profile'));
        add_filter('sanitize_user',				array($this, 'restrict_username'));
    }

    /**
     * This is our way into manipulating registered taxonomies
     * It's fired at the end of the register_taxonomy function
     *
     * @param String $taxonomy	- The name of the taxonomy being registered
     * @param String $object	- The object type the taxonomy is for; We only care if this is "user"
     * @param Array $args		- The user supplied + default arguments for registering the taxonomy
     */
    public function registered_taxonomy($taxonomy, $object, $args) {
        global $wp_taxonomies;

        // Only modify user taxonomies, everything else can stay as is
        if($object != 'user') return;

        // We're given an array, but expected to work with an object later on
        $args	= (object) $args;

        // Register any hooks/filters that rely on knowing the taxonomy now
        add_filter("manage_edit-{$taxonomy}_columns",	array($this, 'set_user_column'));
        add_action("manage_{$taxonomy}_custom_column",	array($this, 'set_user_column_values'), 10, 3);

        // Set the callback to update the count if not already set
        if(empty($args->update_count_callback)) {
            $args->update_count_callback	= array($this, 'update_count');
        }

        // We're finished, make sure we save out changes
        $wp_taxonomies[$taxonomy]		= $args;
        self::$taxonomies[$taxonomy]	= $args;
    }

    /**
     * We need to manually update the number of users for a taxonomy term
     *
     * @see	_update_post_term_count()
     * @param Array $terms		- List of Term taxonomy IDs
     * @param Object $taxonomy	- Current taxonomy object of terms
     */
    public function update_count($terms, $taxonomy) {
        global $wpdb;

        foreach((array) $terms as $term) {
            $count	= $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d", $term));

            do_action('edit_term_taxonomy', $term, $taxonomy);
            $wpdb->update($wpdb->term_taxonomy, compact('count'), array('term_taxonomy_id'=>$term));
            do_action('edited_term_taxonomy', $term, $taxonomy);
        }
    }

    /**
     * Add each of the taxonomies to the Users menu
     * They will behave in the same was as post taxonomies under the Posts menu item
     * Taxonomies will appear in alphabetical order
     */
    public function admin_menu() {
        // Put the taxonomies in alphabetical order
        $taxonomies	= self::$taxonomies;
        ksort($taxonomies);

        foreach($taxonomies as $key=>$taxonomy) {
            add_users_page(
                $taxonomy->labels->menu_name,
                $taxonomy->labels->menu_name,
                $taxonomy->cap->manage_terms,
                "edit-tags.php?taxonomy={$key}"
            );
        }
    }

    /**
     * Fix a bug with highlighting the parent menu item
     * By default, when on the edit taxonomy page for a user taxonomy, the Posts tab is highlighted
     * This will correct that bug
     */
    function parent_menu($parent = '') {
        global $pagenow;

        // If we're editing one of the user taxonomies
        // We must be within the users menu, so highlight that
        if(!empty($_GET['taxonomy']) && $pagenow == 'edit-tags.php' && isset(self::$taxonomies[$_GET['taxonomy']])) {
            $parent	= 'users.php';
        }

        return $parent;
    }

    /**
     * Correct the column names for user taxonomies
     * Need to replace "Posts" with "Users"
     */
    public function set_user_column($columns) {
        unset($columns['posts']);
        $columns['users']	= __('Users');
        return $columns;
    }

    /**
     * Set values for custom columns in user taxonomies
     */
    public function set_user_column_values($display, $column, $term_id) {
        if('users' === $column) {
            $term	= get_term($term_id, $_GET['taxonomy']);
            echo $term->count;
        }
    }

    /**
     * Add the taxonomies to the user view/edit screen
     *
     * @param Object $user	- The user of the view/edit screen
     */
    public function user_profile($user) {
        // Using output buffering as we need to make sure we have something before outputting the header
        // But we can't rely on the number of taxonomies, as capabilities may vary
        ob_start();

        foreach(self::$taxonomies as $key=>$taxonomy):
            // Check the current user can assign terms for this taxonomy
            if(!current_user_can($taxonomy->cap->assign_terms)) continue;

            //get the terms that the user is assigned to
            $assigned_terms = wp_get_object_terms( $user->ID, 'imc_usergroup' );
            $assigned_term_ids = array();
            foreach( $assigned_terms as $term ) {
                $assigned_term_ids[] = $term->term_id;
            }

            //get all the terms we have
            $user_cats = get_terms( 'imc_usergroup', array('hide_empty'=>false) );

            echo "<h3>User Group</h3>";

            //list the terms as checkbox, make sure the assigned terms are checked
            foreach( $user_cats as $cat ) { ?>
                <input type="checkbox" id="imc-usergroup-<?php echo $cat->term_id ?>" <?php if(in_array( $cat->term_id, $assigned_term_ids )) echo 'checked=checked';?> name="imc_usergroup[]"  value="<?php echo $cat->term_id;?>"/>
                <?php
                echo '<label for="imc-usergroup-'.$cat->term_id.'">'.$cat->name.'</label>';
                echo '<br />';
            }
            ?>

            <?php
        endforeach; // Taxonomies

        // Output the above if we have anything, with a heading
        $output	= ob_get_clean();
        if(!empty($output)) {
            echo $output;
        }
    }

    /**
     * Save the custom user taxonomies when saving a users profile
     *
     * @param Integer $user_id	- The ID of the user to update
     */
    public function save_profile($user_id) {
        foreach(self::$taxonomies as $key=>$taxonomy) {
            // Check the current user can edit this user and assign terms for this taxonomy
            if(!current_user_can('edit_user', $user_id) && current_user_can($taxonomy->cap->assign_terms)) return false;

            // Save the data
            $term	= esc_attr($_POST[$key]);
            wp_set_object_terms($user_id, array($term), $key, false);
            clean_object_term_cache($user_id, $key);
        }
    }

    /**
     * Usernames can't match any of our user taxonomies
     * As otherwise it will cause a URL conflict
     * This method prevents that happening
     */
    public function restrict_username($username) {
        if(isset(self::$taxonomies[$username])) return '';

        return $username;
    }

}

new imc_UserGroupsTaxonomy;

/************************************************************************************************************************/

/**
 * 19.01
 * Register Taxonomy 'imc_usergroup'
 *
 * User Group for the Users
 */

function imc_usergroup_tax() {

    $labels = array(
        'name'                       => _x( 'User Groups', 'Taxonomy General Name', 'participace-projekty' ),
        'singular_name'              => _x( 'User Group', 'Taxonomy Singular Name', 'participace-projekty' ),
        'menu_name'                  => __( 'User Groups', 'participace-projekty' ),
        'all_items'                  => __( 'All User Groups', 'participace-projekty' ),
        'parent_item'                => __( 'Parent User Group', 'participace-projekty' ),
        'parent_item_colon'          => __( 'Parent User Group:', 'participace-projekty' ),
        'new_item_name'              => __( 'New User Group', 'participace-projekty' ),
        'add_new_item'               => __( 'Add New User Group', 'participace-projekty' ),
        'edit_item'                  => __( 'Edit User Group', 'participace-projekty' ),
        'update_item'                => __( 'Update User Group', 'participace-projekty' ),
        'view_item'                  => __( 'View User Group', 'participace-projekty' ),
        'separate_items_with_commas' => __( 'Separate User Groups with commas', 'participace-projekty' ),
        'add_or_remove_items'        => __( 'Add or remove User Groups', 'participace-projekty' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'participace-projekty' ),
        'popular_items'              => NULL,
        'search_items'               => __( 'Search User Groups', 'participace-projekty' ),
        'not_found'                  => __( 'Not Found', 'participace-projekty' ),
        'no_terms'                   => __( 'No items', 'participace-projekty' ),
        'items_list'                 => __( 'Items list', 'participace-projekty' ),
        'items_list_navigation'      => __( 'Items list navigation', 'participace-projekty' ),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => false,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => false,
    );
    register_taxonomy( 'imc_usergroup', 'user', $args );


}

add_action( 'init', 'imc_usergroup_tax', 0 );










/**
 * 19.02
 * Add imc_usergroup taxonomy Editing Sub-Page to menu
 *
 */

function add_imc_usergroup_menu() {
    add_submenu_page( 'users.php' , 'User Groups', 'User Groups' , 'add_users',  'edit-tags.php?taxonomy=imc_usergroup' );
}

//add_action(  'admin_menu', 'add_imc_usergroup_menu' );

/************************************************************************************************************************/

/**
 * 19.03
 * Add imc_usergroup taxonomy to user profile pages.
 *
 */

function show_imc_usergroup( $user ) {


}

//add_action( 'show_user_profile', 'show_imc_usergroup' );
//add_action( 'edit_user_profile', 'show_imc_usergroup' );

/************************************************************************************************************************/

/**
 * 19.04
 * Save imc_usergroup taxonomy at user profile pages.
 *
 */

function save_imc_usergroup( $user_id ) {

    $user_terms = $_POST['imc_usergroup'];
    $terms = array_unique( array_map( 'intval', $user_terms ) );
    wp_set_object_terms( $user_id, $terms, 'imc_usergroup', false );

    //make sure you clear the term cache
    clean_object_term_cache($user_id, 'imc_usergroup');
}

//add_action( 'personal_options_update', 'save_imc_usergroup' );
//add_action( 'edit_user_profile_update', 'save_imc_usergroup' );



?>