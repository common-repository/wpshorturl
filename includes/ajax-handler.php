<?php
function wsurl_get_posts_callback()
{
    $response = '';

    if (isset($_REQUEST['type']) && $_REQUEST['type'] != '') :
        $args = array(
            'numberposts' => -1,
            'post_type'   => sanitize_text_field($_REQUEST['type'])
        );

        $postData   =   get_posts($args);
        $postid     =   intval(sanitize_text_field($_REQUEST['id']));

        if ($postData) :
            $postValues = wp_list_pluck($postData, 'post_title', 'ID');

            $response .= wp_sprintf('<option value="-1">Select Item</option>');

            foreach ($postValues as $key => $item) :
                if ($key == $postid) :
                    $slct = 'selected="selected"';
                    $response .=  wp_sprintf('<option class="level-0" value="%s" %s >%s</option>', esc_html($key), esc_html($slct), esc_html($item));
                else :
                    $response .=  wp_sprintf('<option class="level-0" value="%s">%s</option>', esc_html($key), esc_html($item));
                endif;
            endforeach;
        endif;
    endif;

    echo wp_kses($response, ["select" => [], "option" => ["class" => [], "value" => [], "selected" => []]]);

    wp_die();
}
add_action("wp_ajax_wsurl_get_posts", "wsurl_get_posts_callback");
add_action("wp_ajax_nopriv_wsurl_get_posts", "wsurl_get_posts_callback");

/**
 * Create Short URL for post types
 *
 * @return void
 */
function wsu_add_item_callback()
{
    global $wpdb;
    $tblName    =   $wpdb->prefix . 'wsu_items';

    wp_parse_str($_REQUEST['data'], $data);

    if (!wp_verify_nonce($data['_wpnonce'], "WpShortUrl-nonce")) :
        exit("No Script Kiddies!!");
    endif;

    $Action         =   intval($data['updateid']);
    $postType       =   sanitize_text_field($data['wsu-post-type']);
    $postId         =   (int)$data['wsu-post-name'];
    $shorturltype   =   sanitize_text_field($data['typeofurl']);
    $shorturltitle  =   sanitize_text_field($data['wsu_custom_title']);
    $customUrl      =   sanitize_text_field($data['post_page_custom_url']);
    $categories     =   intval(sanitize_text_field($data['wsu_categories']));
    $date           =   time();
    $LastInsetID    =   0;

    $PostIDexist    =   wsu_is_postid_exists($postId);
    $data           =   [];

    if ($shorturltype == 'autogenerate_selection') :
        $shorturl   =   wsu_create_short_url();
    else :
        $shorturl   =   $customUrl;
    endif;

    if ($Action > 0) :
        $Updateitem     =   $wpdb->update($tblName, [
            'post_id'       =>  $postId,
            'post_type'     =>  $postType,
            'title'         =>  $shorturltitle,
            'custom_url'    =>  '',
            'short_slug'    =>  $shorturl,
            'catid'         =>  $categories,
            'status'        =>  1,
        ], ['ID' => $Action]);
        $LastInsetID = $Action;

        if (is_wp_error($Updateitem)) :
            $data[] = ['msg' => __('Something went wrong please try again!!', 'wpshorturl'), 'status' => 400];
        else :
            $data[] = ['msg' => __('Updated successfully!', 'wpshorturl'), 'status' => 200, 'html' => ''];
        endif;

    else :

        if ($PostIDexist > 0) :
            $data[] = ['msg' => __('Short URL already exists', 'wpshorturl'), 'status' => 200, 'html' => ''];
        else :
            $insertItem =   $wpdb->insert($tblName, [
                'post_id'       =>  $postId,
                'post_type'     =>  $postType,
                'title'         =>  $shorturltitle,
                'custom_url'    =>  '',
                'short_slug'    =>  $shorturl,
                'catid'         =>  $categories,
                'status'        =>  1,
                'created_at'    =>  $date,
            ]);

            $LastInsetID = $wpdb->insert_id;
            if (is_wp_error($insertItem)) :
                $data[] = ['msg' => __('Something went wrong please try again!!', 'wpshorturl'), 'status' => 400];
            else :
                $data[] = ['msg' => __('Created successfully!', 'wpshorturl'), 'status' => 200, 'html' => ''];
            endif;
        endif;
    endif;

    $lastrow = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tblName WHERE ID=%d ", $LastInsetID));

    $html = '<div class="wsu-short-url-list wsu-list-lavel-' . wp_sprintf(' %s', $LastInsetID) . '" data-row-Id="' . wp_sprintf('%s', $lastrow->ID) . '">
                <div class="wsu-list-first_row">
                <label>' . wp_sprintf(' %s', $lastrow->title) . '</label>
                  <span class="wsu-clicks-count">' . wp_sprintf(' %s &nbsp;<img src="%s">', wsu_get_click_count($lastrow->ID), esc_url(WSURL_URL . '/assets/img/click.png')) . '</span>
                    </div>
                    <span>' . wp_sprintf('%s', preg_replace('#^https?://#', '', site_url()) . '/' . $lastrow->short_slug) . '</span>
                    </div>';
    $data[0]['html'] = $html;
    $data[0]['title'] = $lastrow->title;
    $data[0]['shorturl'] = preg_replace('#^https?://#', '', site_url()) . '/' . $lastrow->short_slug;
    $data[0]['targeturl'] = get_permalink((int)$lastrow->post_id);
    wp_die(wp_send_json($data[0]));
}
add_action("wp_ajax_wsu_add_item", "wsu_add_item_callback");
add_action("wp_ajax_nopriv_wsu_add_item", "wsu_add_item_callback");


/**
 * Create Short URL for custom url
 */

function wsu_add_item_custom_callback()
{
    global $wpdb;
    wp_parse_str($_REQUEST['data'], $data);
    if (!wp_verify_nonce($data['_wpnonce'], "WpShortUrl-custom-nonce")) :
        exit("No Script Kiddies!!");
    endif;
    $Action         =   intval(sanitize_text_field($data['updateid']));
    $shorturl       =   sanitize_text_field($data['wsu-custom-url']);
    $custom_url     =   esc_url_raw($data['fullcustomurl']);
    $shorturltitle  =   sanitize_text_field($data['customtitle']);
    $categories     =   intval(sanitize_text_field($data['wsu_categories']));

    $tblName        =   $wpdb->prefix . 'wsu_items';
    $date           =   time();
    $LastInsetID    =   0;
    $data           =   [];

    if ($Action > 0) :
        $Updateitem     =   $wpdb->update($tblName, [
            'post_id'       =>  0,
            'post_type'     =>  'wsu-custom',
            'title'         =>  $shorturltitle,
            'custom_url'    =>  $custom_url,
            'short_slug'    =>  $shorturl,
            'catid'         =>  $categories,
            'status'        =>  1,
            'created_at'    =>  $date,
        ], ['ID' => $Action]);

        $LastInsetID    =   $Action;

        if (is_wp_error($updatestatus)) :
            $data[] = ['msg' => __('Something went wrong please try again!!', 'wpshorturl'), 'status' => 400];
        else :
            $data[] = ['msg' => __('Updated successfully!', 'wpshorturl'), 'status' => 200, 'html' => ''];
        endif;

    else :
        if (wsu_is_custom_url_exists($custom_url) > 0) :
            $data[] = ['msg' => __('Short URL already exists', 'wpshorturl'), 'status' => 200];
        else :
            $insertItem     =   $wpdb->insert($tblName, [
                'post_id'       =>  0,
                'post_type'     =>  'wsu-custom',
                'title'         =>  $shorturltitle,
                'custom_url'    =>  $custom_url,
                'short_slug'    =>  $shorturl,
                'catid'         =>  $categories,
                'status'        =>  1,
                'created_at'    =>  $date,
            ]);
            $LastInsetID = $wpdb->insert_id;
            if (is_wp_error($insertItem)) :
                $data[] = ['msg' => __('Something went wrong please try again!!', 'wpshorturl'), 'status' => 400];
            else :
                $data[] = ['msg' => __('Created successfully!', 'wpshorturl'), 'status' => 200, 'html' => ''];
            endif;
        endif;
    endif;

    $lastrow = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tblName WHERE ID=%d ", $LastInsetID));

    $html = '<div class="wsu-short-url-list wsu-list-lavel-' . wp_sprintf(' %s', $LastInsetID) . '" data-row-Id="' . wp_sprintf('%s', $lastrow->ID) . '">
                <div class="wsu-list-first_row">
                <label>' . wp_sprintf(' %s', $lastrow->title) . '</label>
                  <span class="wsu-clicks-count">' . wp_sprintf(' %s &nbsp;<img src="%s">', wsu_get_click_count($lastrow->ID), esc_url(WSURL_URL . '/assets/img/click.png')) . '</span>
                    </div>
                    <span>' . wp_sprintf('%s', preg_replace('#^https?://#', '', site_url()) . '/' . $lastrow->short_slug) . '</span>
                    </div>';
    $data[0]['html'] = $html;
    $data[0]['title'] = $lastrow->title;
    $data[0]['rowID'] = $lastrow->ID;
    $data[0]['shorturl'] = preg_replace('#^https?://#', '', site_url()) . '/' . $lastrow->short_slug;
    $data[0]['targeturl'] = $lastrow->custom_url;
    wp_die(wp_send_json($data[0]));
}
add_action("wp_ajax_wsu_add_item_custom", "wsu_add_item_custom_callback");
add_action("wp_ajax_nopriv_wsu_add_item_custom", "wsu_add_item_custom_callback");

/**
 * Get graph result
 *
 * @return void
 */
function wsu_get_graph_result_callback()
{
    global $wpdb;

    $tblName    =   $wpdb->prefix . 'wsu_items';
    $rowid      =   intval(sanitize_text_field($_POST['rowid']));
    $type       =   sanitize_text_field($_POST['type']);
    if ($type == 'month') {
        $weekdata   =   wsu_get_monthly_graph_data($rowid);
    } else {
        $weekdata   =   wsu_get_weekly_graph_data($rowid);
    }


    $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tblName WHERE status=%d AND ID=%d ", 1, $rowid));

    $url        =   wsu_get_post_url($result->post_id, $result->custom_url);
    $jsondata   =   [
        'shorturl'  =>  preg_replace('#^https?://#', '', site_url()) . '/' . $result->short_slug,
        'title'     =>  $result->title,
        'rowID'     =>  $result->ID,
        'fullurl'   =>  $url,
        'graph'     =>  $weekdata
    ];
    wp_die(wp_send_json($jsondata));
}
add_action("wp_ajax_wsu_get_graph_result", "wsu_get_graph_result_callback");
add_action("wp_ajax_nopriv_wsu_get_graph_result", "wsu_get_graph_result_callback");



function wsu_data_search_callback()
{
    global $wpdb;

    $tblName        =   $wpdb->prefix . 'wsu_items';
    $srchQuery      =   sanitize_text_field($_REQUEST['query']);
    $CatID          =   intval(sanitize_text_field($_REQUEST['cat']));
    $wild           =   '%';

    if ($CatID == '-1'  &&  empty($srchQuery)) :
        $output         =   $wpdb->get_results($wpdb->prepare("SELECT *  FROM  $tblName"));

    elseif ($CatID > 0  &&  empty($srchQuery)) :
        $output         =   $wpdb->get_results($wpdb->prepare("SELECT *  FROM  $tblName WHERE catid =%d ", $CatID));

    elseif ($CatID == '-1' &&  !empty($srchQuery)) :

        $likeStatement  =   $wild . $wpdb->esc_like($srchQuery) . $wild;
        $output         =   $wpdb->get_results($wpdb->prepare("SELECT *  FROM  $tblName WHERE title LIKE '%s' ", $likeStatement));

    elseif ($CatID  > 0  &&  !empty($srchQuery)) :

        $likeStatement  =   $wild . $wpdb->esc_like($srchQuery) . $wild;
        $output         =   $wpdb->get_results($wpdb->prepare("SELECT *  FROM  $tblName WHERE catid =%d AND title LIKE '%s' ", $CatID, $likeStatement));

    endif;

    $outputHtml     =   '';

    if (is_array($output) && count($output) > 0) :
        foreach ($output as $item) :
            $rowId      =   $item->ID;
            $clicks     =   wsu_get_click_count($item->ID);
            $link       =   preg_replace('#^https?://#', '', site_url($item->short_slug));
            $title      =   $item->title;
            $iconUrl    =   esc_url(WSURL_URL . '/assets/img/click.png');
            $outputHtml .=   '<div class="wsu-short-url-list wsu-list-lavel-' . $rowId . '" data-row-Id="' . $rowId . '"><div class="wsu-list-first_row"> <label> ' . $title . '</label> <span class="wsu-clicks-count">' . $clicks . ' &nbsp;<img src="' . $iconUrl . '"></span> </div> <span>' . $link . '</span></div>';
        endforeach;
    else :
        $outputHtml .=  wp_sprintf('<div class="wsu-no-result"><div class="wsu-no-result-found"> <label> %s </label> </div></div>', __('No results. Try again', 'wpshorturl'));
    endif;

    echo wp_kses_post($outputHtml);

    wp_die();
}
add_action("wp_ajax_wsu_data_search", "wsu_data_search_callback");
add_action("wp_ajax_nopriv_wsu_data_search", "wsu_data_search_callback");

//Managed Categories
function wsu_add_categories_callback()
{
    global $wpdb;
    $tblName        =   $wpdb->prefix . 'wsu_categories';

    wp_parse_str($_REQUEST['data'], $data);

    if (!wp_verify_nonce($data['_wpnonce'], "WpShortUrl-categories-nonce")) :
        exit("No Script Kiddies!!");
    endif;
    $massage = '';
    $title      =   sanitize_text_field($data['catname']);
    $action     =   sanitize_text_field($data['wsucataction']);
    $catID      =   intval($data['wsu_categories_action']);

    if ($action == 'delete') :
        $wpdb->delete($tblName, array('id' => $catID));
        $massage = wp_send_json(['msg' => __('Delete successfully!', 'wpshorturl'), 'status' => 200]);
    endif;

    if ($action == 'update') :
        $insertItem     =   $wpdb->update($tblName, [
            'name'       =>  $title,
        ], ['ID' => $catID]);

        $result     =   $wpdb->get_row($wpdb->prepare("SELECT *  FROM  $tblName WHERE ID =%d", $catID));

        $html = '<option class="categories-lavel-' . $result->ID . '" value="' . $result->ID . '">' . $result->name . '</option>';
        $massage = wp_send_json(['msg' => __('Updated successfully!', 'wpshorturl'), 'html' => $html, 'status' => 200]);

    endif;

    if ($action == 'create') :
        if (WsuIsCategoriesExists($title) > 0) :
            $massage = wp_send_json(['msg' => __('Category already exists', 'wpshorturl'), 'status' => 200]);
        else :
            $insertItem     =   $wpdb->insert($tblName, [
                'name'       =>  $title,
            ]);
            $LastInsetID = $wpdb->insert_id;

            $result     =   $wpdb->get_row($wpdb->prepare("SELECT *  FROM  $tblName WHERE ID =%d ", $LastInsetID));

            $html = '<option class="categories-lavel-' . $result->ID . '" value="' . $result->ID . '">' . $result->name . '</option>';
            if (is_wp_error($insertItem)) :
                $massage = wp_send_json(['msg' => __('Something went wrong. Please try again.', 'wpshorturl'), 'status' => 400]);
            else :
                $massage = wp_send_json(['msg' => __('Category created successfully.', 'wpshorturl'), 'html' => $html, 'status' => 200]);
            endif;
        endif;
    endif;
    wp_die($massage);
}
add_action("wp_ajax_wsu_add_categories", "wsu_add_categories_callback");
add_action("wp_ajax_nopriv_wsu_add_categories", "wsu_add_categories_callback");

function wsu_get_edit_record_callback()
{
    global $wpdb;
    $tblName        =   $wpdb->prefix . 'wsu_items';
    $massage        =   '';

    if (!empty($_REQUEST['rowid'])) :

        $rowid  =   intval($_REQUEST['rowid']);
        $row    =   $wpdb->get_row($wpdb->prepare("SELECT * FROM $tblName WHERE ID=%d", $rowid));
        $pagepostlist = '';
        if ($row->post_id > 0) {
        }
        $massage = wp_send_json(['msg' => __('Result successfully Found !', 'wpshorturl'), 'data' => $row, 'status' => 200]);
    else :
        $massage = wp_send_json(['msg' => __('Something went wrong please try again!!', 'wpshorturl'), 'status' => 400]);
    endif;
    wp_die($massage);
}
add_action("wp_ajax_wsu_get_edit_record", "wsu_get_edit_record_callback");
add_action("wp_ajax_nopriv_wsu_get_edit_record", "wsu_get_edit_record_callback");



/**
 * Delete Short URL
 *
 * @return void
 */
function wsu_action_delete_callback()
{
    global $wpdb;

    $tblItems   =   $wpdb->prefix . 'wsu_items';
    $tblHistoy  =   $wpdb->prefix . 'wsu_items_history';
    $tblCat     =   $wpdb->prefix . 'wsu_categories';
    $rowid      =   intval(sanitize_text_field($_POST['rowid']));
    $rowType    =   sanitize_text_field($_POST['rowtype']);

    if ($rowType == 'cat') :
        $wpdb->delete($tblCat, array('ID' => $rowid), array('%d'));
    else :
        $wpdb->delete($tblItems, array('ID' => $rowid), array('%d'));
        $wpdb->delete($tblHistoy, array('wsu_item_id' => $rowid), array('%d'));
    endif;
}
add_action("wp_ajax_wsu_action_delete", "wsu_action_delete_callback");
add_action("wp_ajax_nopriv_wsu_action_delete", "wsu_action_delete_callback");




/**
 * Add New / Update Category
 *
 * @return void
 */
function wsu_categoy_action_callback()
{
    global $wpdb;
    $tblCat         =   $wpdb->prefix . 'wsu_categories';
    $catName        =   trim(sanitize_text_field($_POST['catname']));
    $dataAction     =   trim(sanitize_text_field($_POST['dataaction']));
    $rowID          =   intval(sanitize_text_field($_POST['rowid']));

    if ($dataAction == 'add' && WsuIsCategoriesExists($catName)) :
        $massage = wp_send_json(['msg' => __('Category already exists.', 'wpshorturl'), 'status' => 'error']);
    elseif ($dataAction == 'add' && !WsuIsCategoriesExists($catName)) :

        $wpdb->insert($tblCat, ['name' =>  $catName]);

        $LastInsetID = $wpdb->insert_id;
        $massage = ($LastInsetID) ? wp_send_json(['msg' => __('Category successfully created.', 'wpshorturl'), 'status' => 'success']) : wp_send_json(['msg' => __('Something went wrong. Please try again.', 'wpshorturl'), 'status' => 'error']);

    elseif ($dataAction == 'update' && $rowID != 0 && !WsuIsCategoriesExists($catName) ) :

        $wpdb->update($tblCat, ['name' =>  $catName], ['ID' => $rowID]);

        $massage = wp_send_json(['msg' => __('Category successfully updated.', 'wpshorturl'), 'status' => 'success']);

    endif;

    wp_die($massage);
}
add_action("wp_ajax_wsu_categoy_action", "wsu_categoy_action_callback");
add_action("wp_ajax_nopriv_wsu_categoy_action", "wsu_categoy_action_callback");
