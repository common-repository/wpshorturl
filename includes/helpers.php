<?php

function wsu_get_click_count($id)
{
    global $wpdb;
    $tblName    =   $wpdb->prefix . 'wsu_items_history';
    $output = $wpdb->get_row($wpdb->prepare("SELECT SUM(url_hit) as hits FROM  $tblName WHERE wsu_item_id=%d", $id));
    return (isset($output->hits)) ? $output->hits : 0;
}

function wsu_get_post_types()
{
    $args = array(
        'public'   => true,
        '_builtin' => true
    );
    $postTypes =  get_post_types($args, 'objects');
    unset($postTypes['attachment']);
    return $postTypes;
}

function wsu_get_shorturl_list()
{
    global $wpdb;
    $tblName    =   $wpdb->prefix . 'wsu_items';

    $page       =   isset($_GET['wsupage']) ? abs((int) $_GET['wsupage']) : 1;
    $offset     =   ($page * 20) - 20;


    return $wpdb->get_results($wpdb->prepare("SELECT *  FROM  $tblName WHERE `status` = %d ORDER BY created_at DESC LIMIT %d, 20", 1, $offset));
}

function wsu_get_post_title($postid = '', $custom_url = '')
{
    $WsupostTitle = '';
    if ($postid > 0) :
        $WsupostTitle = get_the_title((int)$postid);
    elseif ($custom_url != '') :
        $WsupostTitle = wsu_get_title($custom_url);
    endif;
    return $WsupostTitle;
}

function wsu_get_title($url)
{
    $response = wp_remote_get($url);
    $page = wp_remote_retrieve_body($response);
    $title = preg_match('/<title[^>]*>(.*?)<\/title>/ims', $page, $match) ? $match[1] : null;
    return esc_attr($title);
}

function wsu_create_short_url()
{
    $id = rand(100000000, 999999999999);
    $shorturl = base_convert($id, 20, 36);

    if (wsu_is_short_url_exists($shorturl) != 0) :
        return wsu_create_short_url();
    endif;

    return $shorturl;
}

function wsu_is_postid_exists($postid)
{
    global $wpdb;
    $tblName    =   $wpdb->prefix . 'wsu_items';
    return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $tblName WHERE post_id=%d ", $postid));
}

function wsu_is_short_url_exists($shortslug)
{
    global $wpdb;
    $tblName    =   $wpdb->prefix . 'wsu_items';
    return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $tblName WHERE short_slug = '%s'", $shortslug));
}

function wsu_is_custom_url_exists($url)
{
    global $wpdb;
    $tblName    =   $wpdb->prefix . 'wsu_items';
    return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $tblName WHERE custom_url LIKE %s", '%' . $url . '%'));
}

function WsuIsCategoriesExists($title)
{
    global $wpdb;
    $tblName    =   $wpdb->prefix . 'wsu_categories';
    return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $tblName WHERE name LIKE %s", '%' . $title . '%'));
}

function wsu_get_post_url($postid = '', $fullurl = '')
{
    $returnurl = '';
    if ($postid > 0) :
        $returnurl = get_permalink($postid);
    else :
        $returnurl = $fullurl;
    endif;
    return $returnurl;
}

function wsu_get_last_record()
{
    global $wpdb;
    $tblName    =   $wpdb->prefix . 'wsu_items';
    return $wpdb->get_row($wpdb->prepare("SELECT *  FROM  $tblName WHERE `status` = %d ORDER BY created_at DESC", 1));
}


function wsu_get_weekly_graph_data($rowid)
{
    global $wpdb;

    $tblName        =   $wpdb->prefix . 'wsu_items_history';
    $currentDate    =   time();
    $date           =   strtotime("-7 day", $currentDate);
    $weekOfdays     =   array();

    for ($i = 1; $i <= 7; $i++) {
        $weekOfdays[] = date('Y-m-d', strtotime("+$i day", $date));
    }
    $WeekGraph = array();

    foreach ($weekOfdays as $day) {

        $result = $wpdb->get_row($wpdb->prepare("SELECT SUM(url_hit) as hits  FROM  $tblName WHERE hit_date ='%s' AND wsu_item_id =%d ", strtotime($day), $rowid));

        if (!empty($result->hits)) :
            $WeekGraph[] = array('weekday' => date('l', strtotime($day)), 'hiturl' => $result->hits, 'month' => date('F', strtotime($day)));
        else :
            $WeekGraph[] = array('weekday' => date('l', strtotime($day)), 'hiturl' => 0, 'month' => date('F', strtotime($day)));
        endif;
    }
    return $WeekGraph;
}

function wsu_get_monthly_graph_data($rowid)
{
    global $wpdb;
    $tblName        =   $wpdb->prefix . 'wsu_items_history';
    $currentDate    =   time();
    $date           =   strtotime("-30 day", $currentDate);
    $weekOfdays     =   array();

    for ($i = 1; $i <= 30; $i++) {
        $weekOfdays[] = date('Y-m-d', strtotime("+$i day", $date));
    }
    $WeekGraph = array();
    foreach ($weekOfdays as $day) {
        $result = $wpdb->get_row($wpdb->prepare("SELECT SUM(url_hit) as hits  FROM  $tblName WHERE hit_date ='%s' AND wsu_item_id =%d", strtotime($day), $rowid));
        if (!empty($result->hits)) :
            $WeekGraph[] = array('weekday' => date('d', strtotime($day)), 'hiturl' => $result->hits, 'month' => date('F', strtotime($day)));
        else :
            $WeekGraph[] = array('weekday' => date('d', strtotime($day)), 'hiturl' => 0, 'month' => date('F', strtotime($day)));
        endif;
    }
    return $WeekGraph;
}

function wsu_get_item_by_slug($slug)
{
    global $wpdb;
    $tblName    =   $wpdb->prefix . 'wsu_items';
    return $wpdb->get_row($wpdb->prepare("SELECT * FROM $tblName WHERE short_slug='%s' ", $slug));
}

function wsu_get_time_ago($time)
{
    $time_difference = time() - $time;
    if ($time_difference < 1) {
        return 'less than 1 second ago';
    }
    $condition = array(
        12 * 30 * 24 * 60 * 60 =>  'year',
        30 * 24 * 60 * 60       =>  'month',
        24 * 60 * 60            =>  'day',
        60 * 60                 =>  'hour',
        60                      =>  'minute',
        1                       =>  'second'
    );
    foreach ($condition as $secs => $str) {
        $d = $time_difference / $secs;
        if ($d >= 1) {
            $t = round($d);
            return $t . ' ' . $str . ($t > 1 ? 's' : '') . ' ago';
        }
    }
}

/**
 * Add URL Hit Entry
 *
 * @return void
 */
function wsurl_url_hit_entry($id)
{
    global $wpdb;
    $tblHit     =   $wpdb->prefix . 'wsu_items_history';
    $hitDate    =   strtotime("today");

    //Check if exist
    $getCount   =   $wpdb->get_var($wpdb->prepare("SELECT url_hit FROM $tblHit WHERE ID=%d AND hit_date='%s'", $id, $hitDate));

    //wsurl_debug($getCount);

    if ($getCount) :
        return   $wpdb->update($tblHit, ['url_hit' => ($getCount + 1)], ['ID' => $id]);
    else :
        return   $wpdb->insert($tblHit, [
            'wsu_item_id'   =>  $id,
            'url_hit'       =>  1,
            'hit_date'      =>  $hitDate,
        ]);
    endif;
}

/**
 * Get total count
 *
 * @return void
 */
function wsu_get_entry_count()
{
    global $wpdb;
    $tblName    =   $wpdb->prefix . 'wsu_items';
    return $wpdb->get_var("SELECT COUNT(*) FROM $tblName");
}

/**
 * Get categories List
 */

function wsu_get_categories_list()
{
    global $wpdb;
    $tblName    =   $wpdb->prefix . 'wsu_categories';
    return $wpdb->get_results("SELECT *  FROM  $tblName");
}
