<?php

/**
 * 13.01
 * Creates extra args for http-variables use
 *
 */

function imcCreateFilterVariablesLong($perma_structure, $issues_per_page, $theorder, $theview, $thesstatus, $thescategory, $thekeyword ) {
    if( $perma_structure){
        $ppage = '/?ppage=';
    }else{
        $ppage = '&ppage=';
    }

    $order = '&sorder=';
    $view = '&view=';
    $sstatus = '&sstatus=';
    $scategory = '&scategory=';
    $keyword = '&keyword=';

    $extra_args = $ppage . $issues_per_page . $order . $theorder . $view . $theview . $sstatus . $thesstatus . $scategory . $thescategory . $keyword . $thekeyword ;
    return $extra_args;
}



/**
 * 13.02
 * Creates extra args for http-variables use (without category/status/keyword)
 *
 */

function imcCreateFilterVariablesShort($perma_structure, $issues_per_page, $theorder, $theview){
    if( $perma_structure){
        $ppage = '/?ppage=';
    }else{
        $ppage = '&ppage=';
    }

    $order = '&sorder=';
    $view = '&view=';

    $extra_args =  $ppage . $issues_per_page . $order . $theorder . $view . $theview;
    return $extra_args;
}



/**
 * 13.03
 * Returns issues with given imccategory
 *
 */

function imcFilterIssuesByCategory($imported_scategory, $include_pending){
    if($include_pending){$post_status = array( 'publish','pending' );}else{$post_status = array('publish');}

    $filtering_category = get_posts(array(
        'post_type' => 'imc_issues',
        'post_status' => $post_status,
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'imccategory',
                'field' => 'id',
                'terms' => $imported_scategory,
            ),
        ),
    ));

    $postids = array();
    foreach ($filtering_category as $item) {
        $postids[] = $item->ID; //create a new query only of the post ids
    }

    $uniqueposts_category = array_unique($postids); //remove duplicate post ids
    return $uniqueposts_category;

}



/**
 * 13.04
 * Returns issues with given imcstatus
 *
 */

function imcFilterIssuesByStatus($imported_sstatus, $include_pending){
    if($include_pending){$post_status = array( 'publish','pending' );}else{$post_status = array('publish');}

    $filtering_category = get_posts(array(
        'post_type' => 'imc_issues',
        'post_status' => $post_status,
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'imcstatus',
                'field' => 'term_taxonomy_id',
                'terms' => $imported_sstatus,
            ),
        ),
    ));

    $postids = array();
    foreach ($filtering_category as $item) {
        $postids[] = $item->ID; //create a new query only of the post ids
    }

    $uniqueposts_status = array_unique($postids); //remove duplicate post ids
    return $uniqueposts_status;

}



/**
 * 13.05
 * Returns issues for non users
 *
 */

function imcLoadIssuesForGuests($paged, $page, $status, $category){

    $imported_sstatus = ( $status ) ? explode(",", $status): $status;
    $imported_scategory = ( $category ) ? explode(",", $category) : $category;

    $custom_query_args = array(
        'post_type' => 'imc_issues',
        'post_status' => array('publish'),
        'paged' => $paged,
        'posts_per_page' => $page,
    );

    //the filtering is for no users so -no pending issues-
    $include_pending = false;

    if($imported_scategory && !$imported_sstatus) {
        $filter = imcFilterIssuesByCategory($imported_scategory,$include_pending);
        $custom_query_args['post__not_in'] = $filter;
    }elseif(!$imported_scategory && $imported_sstatus){
        $filter = imcFilterIssuesByStatus($imported_sstatus,$include_pending);
        $custom_query_args['post__not_in'] = $filter;
    }elseif($imported_scategory && $imported_sstatus){
        $filter1 = imcFilterIssuesByCategory($imported_scategory,$include_pending);
        $filter2 = imcFilterIssuesByStatus($imported_sstatus,$include_pending);
        $mergedfilters = array_merge($filter1, $filter2); //combine queries
        $uniqueposts_filters = array_unique($mergedfilters); //remove duplicate post ids
        $custom_query_args['post__not_in'] = $uniqueposts_filters;
    }

    return $custom_query_args;
}



/**
 * 13.06
 * Returns issues for non admins
 *
 */

function imcLoadIssuesForAdmins($paged, $page, $status, $category){

    $imported_sstatus = ( $status ) ? explode(",", $status): $status;
    $imported_scategory = ( $category ) ? explode(",", $category) : $category;

    $custom_query_args = array(
        'post_type' => 'imc_issues',
        'post_status' => array( 'publish','pending' ),
        'paged' => $paged,
        'posts_per_page' => $page,
    );

    //the filtering is for admins only so -plus pending issues-
    $include_pending = true;

    if($imported_scategory && !$imported_sstatus) {
        $filter = imcFilterIssuesByCategory($imported_scategory,$include_pending);
        $custom_query_args['post__not_in'] = $filter;
    }elseif(!$imported_scategory && $imported_sstatus){
        $filter = imcFilterIssuesByStatus($imported_sstatus,$include_pending);
        $custom_query_args['post__not_in'] = $filter;
    }elseif($imported_scategory && $imported_sstatus){
        $filter1 = imcFilterIssuesByCategory($imported_scategory,$include_pending);
        $filter2 = imcFilterIssuesByStatus($imported_sstatus,$include_pending);
        $mergedfilters = array_merge($filter1, $filter2); //combine queries
        $uniqueposts_filters = array_unique($mergedfilters); //remove duplicate post ids
        $custom_query_args['post__not_in'] = $uniqueposts_filters;
    }

    return $custom_query_args;
}



/**
 * 13.07
 * Returns issues for user
 *
 */

function imcLoadIssuesForUsers($paged, $page, $user_id, $status, $category){

    $imported_sstatus = ( $status ) ? explode(",", $status): $status;
    $imported_scategory = ( $category ) ? explode(",", $category) : $category;

    //the filtering is for users so -plus user's pending issues-
    $include_pending = true;

    if($imported_scategory && !$imported_sstatus) {
        $filter = imcFilterIssuesByCategory($imported_scategory,$include_pending);
        $pendingOfUser = get_posts(array(
            'post_status' => 'pending',
            'post_type' => 'imc_issues',
            'author' => $user_id,
            'posts_per_page' => -1,
            'post__not_in' => $filter,
        ));

        $allPublish = get_posts(array(
            'post_status' => 'publish',
            'post_type' => 'imc_issues',
            'posts_per_page' => -1,
            'post__not_in' => $filter,
        ));
    }elseif(!$imported_scategory && $imported_sstatus){
        $filter = imcFilterIssuesByStatus($imported_sstatus,$include_pending);
        $pendingOfUser = get_posts(array(
            'post_status' => 'pending',
            'post_type' => 'imc_issues',
            'author' => $user_id,
            'posts_per_page' => -1,
            'post__not_in' => $filter,
        ));
        $allPublish = get_posts(array(
            'post_status' => 'publish',
            'post_type' => 'imc_issues',
            'posts_per_page' => -1,
            'post__not_in' => $filter,
        ));
    }elseif($imported_scategory && $imported_sstatus){
        $filter1 = imcFilterIssuesByCategory($imported_scategory,$include_pending);
        $filter2 = imcFilterIssuesByStatus($imported_sstatus,$include_pending);
        $mergedfilters = array_merge($filter1, $filter2); //combine queries
        $uniqueposts_filters = array_unique($mergedfilters); //remove duplicate post ids
        $pendingOfUser = get_posts(array(
            'post_status' => 'pending',
            'post_type' => 'imc_issues',
            'author' => $user_id,
            'posts_per_page' => -1,
            'post__not_in' => $uniqueposts_filters,
        ));
        $allPublish = get_posts(array(
            'post_status' => 'publish',
            'post_type' => 'imc_issues',
            'posts_per_page' => -1,
            'post__not_in' => $uniqueposts_filters,
        ));
    }else{
        //first query
        $pendingOfUser = get_posts(array(
            'post_status' => 'pending',
            'post_type' => 'imc_issues',
            'author' => $user_id,
            'posts_per_page' => -1,
        ));
        //second query
        $allPublish = get_posts(array(
            'post_status' => 'publish',
            'post_type' => 'imc_issues',
            'posts_per_page' => -1,
        ));
    }

    $mergedposts = array_merge($pendingOfUser, $allPublish); //combine queries

    $postids = array();
    foreach ($mergedposts as $item) {
        $postids[] = $item->ID; //create a new query only of the post ids
    }

    $uniqueposts = array_unique($postids); //remove duplicate post ids

    if(!empty($uniqueposts)) {
        $custom_query_args = array(
            'post_type' => 'imc_issues',
            'post_status' => array( 'publish','pending' ),
            'post__in' => $uniqueposts,
            'paged' => $paged,
            'posts_per_page' => $page,
        );
    }else{
        $custom_query_args = array(
            'post_type' => 'imc_issues',
            'post_status' => array( 'publish' ),
            'paged' => $paged,
            'posts_per_page' => $page,
        );
    }

    return $custom_query_args;
}
?>
