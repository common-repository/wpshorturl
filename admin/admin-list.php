<div class="wrap">
    <h1 class="wp-heading-inline">Short URL</h1>
</div>
<div id="col-container" class="wp-clearfix">


    <div id="col-left">
        <div class="col-wrap">
            <div class="searchurl">
                <?php $listCat = wsu_get_categories_list(); ?>
                <div class="wsu-select-categories">
                    <select name="wsu_categories" id="wsu_categories">
                        <option value="-1">All</option>
                        <?php
                        foreach ($listCat as $label) :
                            echo wp_sprintf('<option class="categories-lavel-%s" value="%s">%s</option>', esc_html($label->ID), esc_html($label->ID), esc_html($label->name));
                        endforeach;
                        ?>
                    </select>
                </div>
                <div class="wsu-input-search-auto">
                    <input type="text" name="searchshorturl" id="searchshorturl" value="" placeholder="Search...">
                </div>
            </div>
        </div>
    </div>


    <div class="col-wrap">
        <div id="col-right">
            <div class="searchurl">
              <a href="#" class="add-new-button" id="url-create-button" class="button" data-modal="modalOne">Create new</a>
            </div>
        </div>
    </div>


    <div id="col-left">
        <div class="col-wrap" data-row="wsu-list-wrapper"></div>
        <div class="col-wrap wsu-append-list" data-row="wsu-query-list-wrapper">
            <?php $shorturlLIst = wsu_get_shorturl_list();
            if (!empty($shorturlLIst)) :
                foreach ($shorturlLIst as $key => $list) : ?>
                    <div class="wsu-short-url-list wsu-list-lavel-<?php _e($list->ID, 'wpshorturl'); ?>" data-row-Id="<?php _e($list->ID, 'wpshorturl'); ?>">
                        <div class="wsu-list-first_row">
                            <label><?php echo wp_sprintf(' %s', $list->title); ?></label>
                            <span class="wsu-clicks-count"><?php echo wp_sprintf(' %s &nbsp;<img src="%s">', wsu_get_click_count($list->ID), esc_url(WSURL_URL . '/assets/img/click.png')); ?> </span>
                        </div>
                        <span><?php _e(preg_replace('#^https?://#', '', site_url()) . '/' . $list->short_slug, 'wpshorturl'); ?></span>
                    </div>
            <?php
                endforeach;
            else :
                _e('<div class="wsu-no-link">There are no links yet! <a href="#" id="first_link">Create now</a></div>', 'wpshorturl');
            endif;
            ?>
        </div>
        <div class="col-wrap wsu-list-pagination">
            <?php
            echo paginate_links(array(
                'base' => add_query_arg('wsupage', '%#%'),
                'format' => '',
                'prev_text' => __('&laquo;', 'wpshorturl'),
                'next_text' => __('&raquo;', 'wpshorturl'),
                'total' => ceil(wsu_get_entry_count() / 20),
                'current' => isset($_GET['wsupage']) ? abs((int) $_GET['wsupage']) : 1
            ));
            ?>
        </div>
    </div>


    <div id="col-right">
        <div class="col-wrap">
            <div class="wsu-graph-view" style="display: none;">
                <div class="wsu-graphview-head">
                    <div class="wsu-graph-title-action">
                        <div class="left-graph-view">
                            <?php $lastRecord = wsu_get_last_record(); ?>
                            <label class="wsu-graph-title"></label>
                        </div>
                        <div class="right-graph-view">
                            <!-- <span class="tooltip" id="copytext" data-clipboard-text="THIS IS MY CODE" data-clipboard-target="#ShortUrl">Copy
                                <span class="tooltiptext">Copy to clipboard</span>
                            </span> -->
                            <!-- <span class="wsu-edit-record" id="wsu-edit-record">Edit</span> -->
                        </div>
                    </div>
                    <div class="wsu-graph-fullurl"></div>
                    <div class="wsu-graph-heading" id="ShortUrl">
                        <div class="wp-graph-url" id="wp-graph-url">

                        </div>
                        <!-- /.wp-graph-url -->

                        <ul class="wp-graph-actions">
                            <li class="wp-graph-action-copy">
                                <button class="tooltip" id="copytext" data-clipboard-text="THIS IS MY CODE" data-clipboard-action="copy" data-clipboard-target="#wp-graph-url">
                                    <img src="<?php echo WSURL_URL; ?>/assets/img/copy.png" title="<?php _e('Copy', 'wpshorturl'); ?>" alt="<?php _e('Copy', 'wpshorturl'); ?>" />

                                    <span class="tooltiptext">Copy to clipboard</span>
                                </button>
                            </li>
                            <li class="wp-graph-action-edit" id="edittext">
                                  <button class="tooltip" id="edittext">
                                <img src="<?php echo WSURL_URL; ?>/assets/img/pen.png" title="<?php _e('Edit', 'wpshorturl'); ?>" alt="<?php _e('Edit', 'wpshorturl'); ?>" class="wsu-edit-record" id="wsu-edit-record" />
                                <span class="tooltipother">Edit</span>
                            </li>
                            <li class="wp-graph-action-delete" data-rowid="0" data-type="items" data-action="delete">
                                 <button class="tooltip" id="deletetext">
                                <img src="<?php echo WSURL_URL; ?>/assets/img/delete.png" title="<?php _e('Delete', 'wpshorturl'); ?>" alt="<?php _e('Delete', 'wpshorturl'); ?>" />
                                <span class="tooltipother">Delete</span>
                            </li>
                          </ul>

                        <!-- /.wp-graph-edit-actions -->

                    </div>
                    <div class="wsu-legend-cart">
                        <span data-type="week" class="legend-label active">7 days</span>
                        <span class="legend-label" data-type="month">30 days</span>
                    </div>
                </div>
                <div class="chart-view" GraphId="<?php _e($lastRecord->ID, 'wpshorturl'); ?>">
                    <div id="chartContainer"></div>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="modalOne" class="modal">
    <div class="modal-content">
        <div class="contact-form">
            <a class="close">&times;</a>
            <div class="container">
                <header>
                    <div id="wsu-form-tabs">
                        <a id="tab1-tab" href="#customurl" class="active">Custom URL</a>
                        <a id="tab2-tab" href="#posturl">Post / Page</a>
                        <span class="yellow-bar"></span>
                    </div>
                </header>
                <div class="tab-content">
                    <div id="posturl">
                        <form action="" method="post" id="wsu-shourt-url-form">
                            <input type="hidden" value="0" name="updateid" id="wsu-post-action">
                            <div class="form-field wsu-form-field wsu-post-type-wrap">
                                <label for="post-type"><?php _e('Choose Page or Post', 'wpshorturl'); ?></label>
                                <?php $postType = wsu_get_post_types(); ?>
                                <select name="wsu-post-type" id="wsu-post-type" class="postform">
                                    <option value="-1">Select Post Type</option>
                                    <?php
                                    if (is_array($postType) && count($postType) > 0) :
                                        foreach ($postType as $key => $type) :
                                            echo wp_sprintf('<option class="level-0" value="%s">%s</option>', esc_html($key), esc_html($type->label));
                                        endforeach;
                                    endif;
                                    ?>
                                </select>
                            </div>
                            <div class="form-field wsu-form-field form-required wsu-item-name-wrap">
                                <label for="post-name"><?php _e('Select Post', 'wpshorturl'); ?></label>
                                <select name="wsu-post-name" id="wsu-post-name" class="postform" aria-required="true">
                                </select>
                            </div>

                            <div class="wsu-post-type-main form-field">
                                <span class="wsu-autogerate"> <input type="radio" name="typeofurl" id="typeofurl" value="autogenerate_selection" checked="checked" data-show="wsu-item-name-wrap">Auto Generated URL</span>
                                <span class="wsu-customgerate"><input type="radio" name="typeofurl" id="typeofurl" value="custom_selection" data-show="wsu-custom-short-url-wrap">Custom URL</span>
                            </div>
                            <div class="form-field wsu-form-field form-required">
                                <label for="post-custom-url"><?php _e('Title', 'wpshorturl'); ?></label>
                                <input type="text" name="wsu_custom_title" id="wsu_custom_title" value="" placeholder="Title">
                            </div>

                            <div class="form-field wsu-form-field form-required wsu-custom-short-url-wrap wsu-show-all">
                                <label for="post-custom-url"><?php _e('Add Custom Short Url', 'wpshorturl'); ?></label>
                                <div class="">
                                    <span class="input-group-text"><?php echo wp_sprintf('%s', site_url()); ?>/</span>
                                    <input type="text" name="post_page_custom_url" id="post_page_custom_url" value="" placeholder="Cusotm short Url" class="CustomshortUrlInput">
                                </div>


                            </div>
                            <div class="form-field wsu-form-field wsu-categories-wrap">
                                <label for="post-type"><?php _e('Select Category', 'wpshorturl'); ?></label>
                                <select name="wsu_categories" id="wsu_categories" class="wsu-post-page-cat">
                                    <option value="-1">All</option>
                                    <?php
                                    foreach ($listCat as $label) :
                                        echo wp_sprintf('<option  class="categories-lavel-%s" value="%s">%s</option>', esc_html($label->ID), esc_html($label->ID), esc_html($label->name));
                                    endforeach;
                                    ?>
                                </select>
                            </div>
                            <input type="hidden" name="field_id" value="0">
                            <?php wp_nonce_field('WpShortUrl-nonce'); ?>
                            <?php submit_button(__('Create Short URL', 'wpshorturl'), 'primary', 'wsu_add_item'); ?>
                        </form>

                        <div class="form-field wsu-form-field wsu-ajax-submit-response">

                        </div>

                    </div>
                    <div id="customurl">
                        <form action="" method="post" id="wsu-shourt-url-form-custom-url">
                            <input type="hidden" value="0" name="updateid" id="wsu-custom-action">
                            <div class="form-field wsu-form-field wsu-customurl-wrap">

                                <div class="site_url"><?php _e('Title', 'wpshorturl'); ?></div>
                                <div class="site_url_and_customUrl">
                                    <input type="text" name="customtitle" id="customtitle" value="" placeholder="Title" required="required">
                                </div>

                                <div class="site_url"><?php _e('Target URL', 'wpshorturl'); ?></div>
                                <div class="site_url_and_customUrl">
                                    <input type="text" name="fullcustomurl" id="fullcustomurl" value="" placeholder="Target URL" required="required">
                                </div>

                                <div class="site_url"> <?php _e('Short Url', 'wpshorturl'); ?></div>
                                <div class="site_url_and_customUrl closer">
                                    <span class="input-group-text"><?php echo wp_sprintf('%s', site_url()); ?>/</span>
                                    <input type="text" value="" name="<?php _e('wsu-custom-url', 'wpshorturl'); ?>" id="<?php _e('wsu-custom-url', 'wpshorturl'); ?>" class="CustomshortUrlInput" placeholder="yourlink" required="required">
                                </div>
                            </div>
                            <div class="site_url"><?php _e('Select Category', 'wpshorturl'); ?></div>
                            <div class="site_url_and_customUrl">
                                <select name="wsu_categories" id="wsu_categories">
                                    <option value="-1">All</option>
                                    <?php
                                    foreach ($listCat as $label) :
                                        echo wp_sprintf('<option  class="categories-lavel-%s" value="%s">%s</option>', esc_html($label->ID), esc_html($label->ID), esc_html($label->name));
                                    endforeach;
                                    ?>
                                </select>
                            </div>
                            <input type="hidden" name="field_id" value="0">
                            <?php wp_nonce_field('WpShortUrl-custom-nonce'); ?>
                            <?php submit_button(__('Create Short URL', 'wpshorturl'), 'primary', 'wsu_add_item_custom'); ?>
                        </form>
                        <div class="form-field wsu-form-field wsu-custom-ajax-submit-response">
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
