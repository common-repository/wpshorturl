<?php

function wsu_manage_uri_request()
{
    global $wp_query;

    if (isset($wp_query->is_404) && $wp_query->is_404 == 1 && isset($wp_query->query_vars['name'])  && $wp_query->query_vars['name'] != '') :
        $slug = $wp_query->query_vars['name'];

        $shortData = wsu_get_item_by_slug($slug);

        if (is_object($shortData)) :

            $id             =   intval($shortData->ID);
            $post_id        =   intval($shortData->post_id);
            $custom_url     =   $shortData->custom_url;

            /**
             * Add Url Hit && Redirect
             */

            wsurl_url_hit_entry($id);

            if ($post_id != 0) :
                wp_safe_redirect(get_permalink($post_id), 301);
                exit();
            else :
                wp_redirect($custom_url, 301);
                exit();
            endif;

        endif;

    endif;
}
add_action('template_redirect', 'wsu_manage_uri_request');
