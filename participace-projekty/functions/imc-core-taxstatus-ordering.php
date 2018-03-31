<?php

$imcstatus_ordering = imcstatus_ordering::instance();

/**
 * Class imcstatus_ordering
 */
class imcstatus_ordering {
    private static $instance;

    private static $taxonomies = array( 'imcstatus' );

    private static $plugin_url;
    private static $plugin_path;

    private function __construct() {
        self::$plugin_url  = plugins_url( '', __FILE__ );
        self::$plugin_path = dirname( __FILE__ );


        add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ), 5 );
        add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ), 5 );

        add_action( 'admin_init', array( $this, 'admin_init' ) );

        add_filter( 'terms_clauses', array( $this, 'imcstatus_terms_clauses' ), 10, 3 );

        add_action( 'created_term', array( $this, 'imcstatus_created_term' ), 10, 3 );

        add_action( 'delete_term', array( $this, 'imcstatus_delete_term' ), 10, 3 );
    }

    /**
     * Singleton pattern
     * @return imcstatus_ordering
     */
    public static function instance() {
        if ( ! isset( self::$instance ) ) {
            $class_name     = __CLASS__;
            self::$instance = new $class_name;
        }

        return self::$instance;
    }

    /**
     * Add custom ordering support to one or more taxonomies
     *
     * @param string|array $taxonomy
     */
    public static function imcstatus_add_taxonomy_support( $taxonomy ) {
        $taxonomies       = (array) $taxonomy;
        self::$taxonomies = array_merge( self::$taxonomies, $taxonomies );
    }

    /**
     * Add custom ordering support to one or more taxonomies
     *
     * @param string|array $taxonomy
     */
    public static function imcstatus_remove_taxonomy_support( $taxonomy ) {
        $key = array_search( $taxonomy, self::$taxonomies );
        if ( false !== $key ) {
            unset( self::$taxonomies[ $key ] );
        }
    }

    /**
     * Hooks and filters
     */
    public function plugins_loaded() {
        self::$taxonomies = apply_filters( 'term-ordering-default-taxonomies', self::$taxonomies );
    }

    public function after_setup_theme() {
        self::$taxonomies = apply_filters( 'term-ordering-taxonomies', self::$taxonomies );
    }

    public function admin_init() {
        // Load needed scripts to order terms
        add_action( 'admin_footer-edit-tags.php', array( $this, 'imcstatus_admin_enqueue_scripts' ), 10 );

        add_action( 'admin_print_styles-edit-tags.php', array( $this, 'imcstatus_admin_css' ), 1 );

        // Httpr hadler for drag and drop ordering
        add_action( 'wp_ajax_terms-ordering', array( $this, 'imcstatus_term_ordering_httpr' ) );
    }

    /**
     * Load needed scripts to order categories in admin
     */
    public function imcstatus_admin_enqueue_scripts() {
        if ( ! isset( $_GET['taxonomy'] ) || ! self::imcstatus_has_support( $_GET['taxonomy'] ) ) {
            return;
        }

        wp_register_script( 'imcstatus-term-ordering', plugin_dir_url( __FILE__ ) . '/js/imcstatus-ordering.js', array( 'jquery-ui-sortable' ) );

        wp_enqueue_script( 'imcstatus-term-ordering' );

        wp_localize_script( 'imcstatus-term-ordering', 'terms_order', array( 'taxonomy' => $_GET['taxonomy'] ) );

        wp_print_scripts( 'imcstatus-term-ordering' );
    }

    public static function imcstatus_has_support( $taxonomy ) {
        if ( in_array( $taxonomy, self::$taxonomies ) ) {
            return true;
        }

        return false;
    }

    public function imcstatus_admin_css() {
        if ( ! isset( $_GET['taxonomy'] ) || ! self::imcstatus_has_support( $_GET['taxonomy'] ) ) {
            return;
        }

        ?>
        <style type="text/css">
            .widefat .product-cat-placeholder {
                outline: 1px dotted #21759B;
                height: 60px;
            }
        </style>
        <?php
    }

    /**
     * Httpr handler for categories ordering
     */
    public function imcstatus_term_ordering_httpr() {
        global $wpdb;

        $id       = (int) $_POST['id'];
        $next_id  = isset( $_POST['nextid'] ) && (int) $_POST['nextid'] ? (int) $_POST['nextid'] : null;
        $taxonomy = isset( $_POST['taxonomy'] ) && $_POST['taxonomy'] ? $_POST['taxonomy'] : null;

        if ( ! $id || ! $term = get_term_by( 'id', $id, $taxonomy ) ) {
            die( 0 );
        }

        $this->imcstatus_place_term( $term, $taxonomy, $next_id );

        $children = get_terms( $taxonomy, "child_of=$id&menu_order=ASC&hide_empty=0" );

        if ( $term && sizeof( $children ) ) {
            'children';
            die;
        }
    }

    /**
     * Move a term before a given element of its hierachy level
     *
     * @param object $the_term
     * @param int $next_id the id of the next slibling element in save hierachy level
     * @param int $index
     * @param int $terms
     */
    private function imcstatus_place_term( $the_term, $taxonomy, $next_id, $index = 0, $terms = null ) {

        if ( ! $terms ) {
            $terms = get_terms( $taxonomy, 'menu_order=ASC&hide_empty=0&parent=0' );
        }
        if ( empty( $terms ) ) {
            return $index;
        }

        $id = $the_term->term_id;

        $term_in_level = false; // flag: is our term to order in this level of terms

        foreach ( $terms as $term ) {
            if ( $term->term_id == $id ) { // our term to order, we skip
                $term_in_level = true;
                continue; // our term to order, we skip
            }
            // the nextid of our term to order, lets move our term here
            if ( null !== $next_id && $term->term_id == $next_id ) {
                $index = $this->imcstatus_set_term_order( $id, $taxonomy, $index + 1, true );
            }

            // set order
            $index = $this->imcstatus_set_term_order( $term->term_id, $taxonomy, $index + 1 );

            // if that term has children we walk thru them
            $children = get_terms( $taxonomy, "parent={$term->term_id}&menu_order=ASC&hide_empty=0" );
            if ( ! empty( $children ) ) {
                $index = $this->imcstatus_place_term( $the_term, $taxonomy, $next_id, $index, $children );
            }
        }

        // no nextid meaning our term is in last position
        if ( $term_in_level && null === $next_id ) {
            $index = $this->imcstatus_set_term_order( $id, $taxonomy, $index + 1, true );
        }

        return $index;
    }

    /**
     * Set the sort order of a term
     *
     * @param int $term_id
     * @param int $index
     * @param bool $recursive
     */
    private function imcstatus_set_term_order( $term_id, $taxonomy, $index, $recursive = false ) {
        global $wpdb;

        $term_id = (int) $term_id;
        $index   = (int) $index;

        update_metadata( 'term', $term_id, 'imc_term_order', $index );

        if ( ! $recursive ) {
            return $index;
        }

        $children = get_terms( $taxonomy, "parent=$term_id&menu_order=ASC&hide_empty=0" );

        foreach ( $children as $term ) {
            $index ++;
            $index = $this->imcstatus_set_term_order( $term->term_id, $taxonomy, $index, true );
        }

        return $index;
    }

    /**
     * Add term ordering suport to get_terms, set it as default
     *
     * It enables the support a 'menu_order' parameter to get_terms for the configured taxonomy.
     * By default it is 'ASC'. It accepts 'DESC' too
     *
     * To disable it, set it ot false (or 0)
     */
    public function imcstatus_terms_clauses( $clauses, $taxonomies, $args ) {
        global $wpdb;

        $taxonomies = (array) $taxonomies;

        if ( count($taxonomies) === 1  ) {
            $taxonomy = array_shift( $taxonomies );
        } else {
            return $clauses;
        }

//        if ( sizeof( $taxonomies === 1 ) ) {
//            $taxonomy = array_shift( $taxonomies );
//        } else {
//            return $clauses;
//        }

        if ( ! $this->imcstatus_has_support( $taxonomy ) ) {
            return $clauses;
        }

        // fields
        if ( strpos( 'COUNT(*)', $clauses['fields'] ) === false ) {
            $clauses['fields'] .= ', tm.meta_key, tm.meta_value ';
        }

        // join
        $clauses['join'] .= " LEFT JOIN {$wpdb->termmeta} AS tm ON (t.term_id = tm.term_id AND tm.meta_key = 'imc_term_order') ";

        // order
        if ( isset( $args['menu_order'] ) && ! $args['menu_order'] ) {
            return $clauses;
        } // menu_order is false whe do not add order clause

        // default to ASC
        if ( ! isset( $args['menu_order'] ) || ! in_array( strtoupper( $args['menu_order'] ), array(
                'ASC',
                'DESC'
            ) )
        ) {
            $args['menu_order'] = 'ASC';
        }

        $order = "ORDER BY CAST(tm.meta_value AS SIGNED) " . $args['menu_order'];

        if ( $clauses['orderby'] ) {
            $clauses['orderby'] = str_replace( 'ORDER BY', $order . ',', $clauses['orderby'] );
        } else {
            $clauses['orderby'] = $order;
        }

        return $clauses;
    }

    /**
     * Add term last on insertion
     *
     * @param int $term_id
     */
    public function imcstatus_created_term( $term_id, $tt_id, $taxonomy ) {
        if ( ! $this->imcstatus_has_support( $taxonomy ) ) {
            return;
        }

        $terms = get_terms( 'imcstatus', array('hide_empty' => false,) );
        $lastTerm = call_user_func('end', array_values($terms));
        $lastTermID = $lastTerm->term_id;
        $lastTermORDER = get_term_meta ( $lastTermID, 'imc_term_order');

        update_metadata( 'term', $term_id, 'imc_term_order', $lastTermORDER[0]+1 );
    }

    /**
     * Delete terms metas on deletion
     *
     * @param int $term_id
     */
    public function imcstatus_delete_term( $term_id, $tt_id, $taxonomy ) {
        if ( ! $this->imcstatus_has_support( $taxonomy ) ) {
            return;
        }

        if ( ! (int) $term_id ) {
            return;
        }

        delete_metadata( 'term', $term_id, 'imc_term_order' );

        // reorder
        $this->imcstatus_place_term( $term, $taxonomy, $next_id );
    }
}

if ( ! function_exists( 'imcstatus_add_term_ordering_support' ) ) {
    function imcstatus_add_term_ordering_support( $taxonomy ) {
        imcstatus_ordering::imcstatus_add_taxonomy_support( $taxonomy );
    }
}

if ( ! function_exists( 'imcstatus_remove_term_ordering_support' ) ) {
    function imcstatus_remove_term_ordering_support( $taxonomy ) {
        imcstatus_ordering::imcstatus_remove_taxonomy_support( $taxonomy );
    }
}

if ( ! function_exists( 'imcstatus_has_term_ordering_support' ) ) {
    function imcstatus_has_term_ordering_support( $taxonomy ) {
        return imcstatus_ordering::imcstatus_has_support( $taxonomy );
    }
}

?>