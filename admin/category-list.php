<div class="wrap wsu-category-wrapper">
    <h1 class="wp-heading-inline"><?php _e("Short URL  - Categories", 'wpshorturl'); ?></h1>
    <button type="button" name="wsu-add-category" id="wsu-add-category-btn" class="button button-secondary">Add New</button>
</div>
<div id="col-container" class="wp-clearfix">
    <div id="col-left">
        <div class="col-wrap wsu-append-list" data-row="wsu-query-list-wrapper">
            <?php
            $listCat = wsu_get_categories_list();
            if (!empty($listCat)) :
                foreach ($listCat as $cat) : ?>
                    <div class="wsu-short-url-cat-list" data-row-Id="<?php echo intval($cat->ID); ?>">
                        <div class="wsu-category-item">
                            <label>
                                <input type="text" value="<?php echo wp_sprintf(' %s', $cat->name); ?>" class="wsu-category-input" />
                                <span class="wsu--response-msg"></span>
                            </label>

                            <ul class="wp-category-actions">
                                <li class="wp-category-action-submit" data-rowid="<?php echo intval($cat->ID); ?>" data-type="cat" data-action="update"> <img src="<?php echo WSURL_URL; ?>/assets/img/submit.png" title="<?php _e('Submit', 'wpshorturl'); ?>" alt="<?php _e('Submit', 'wpshorturl'); ?>" class="wsu-edit-category" /></li>
                                <li class="wp-category-action-edit" data-rowid="<?php echo intval($cat->ID); ?>" data-type="cat" data-action="edit"> <img src="<?php echo WSURL_URL; ?>/assets/img/pen.png" title="<?php _e('Edit', 'wpshorturl'); ?>" alt="<?php _e('Edit', 'wpshorturl'); ?>" class="wsu-edit-category" /></li>
                                <li class="wp-category-action-delete" data-rowid="<?php echo intval($cat->ID); ?>" data-type="cat" data-action="delete"> <img src="<?php echo WSURL_URL; ?>/assets/img/delete.png" title="<?php _e('Delete', 'wpshorturl'); ?>" alt="<?php _e('Delete', 'wpshorturl'); ?>" /></li>
                            </ul>
                        </div>
                    </div>
            <?php
                endforeach;
            else :
                _e('<div class="wsu-no-link">There are no categories created yet!</div>', 'wpshorturl');
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

    <div id="col-right" style="background-color:#f3f5fa;">
        <div class="col-wrap">
            <form action="" method="post" id="wsu-shoturl-categories" data-action="add">
                <h2 class="wsu--form-heading"><?php _e('Add New Categories', 'wpshorturl'); ?></h2>
                <div class="form-field wsu--form-fields">
                    <label class="site_url"><?php _e('Category Name', 'wpshorturl'); ?></label>
                    <input type="text" name="wsu__catname" id="wsu__catname" value="" placeholder="" required="required">
                </div>
                <div class="form-field wsu--form-fields">
                    <?php submit_button(__('Submit', 'wpshorturl'), 'primary', 'wsu__add-category'); ?>
                </div>
                <div class="form-field wsu--form-fields wsu--action-status">
                </div>
                <?php wp_nonce_field('wsu__categories', 'wsu__category_field'); ?>
            </form>
        </div>
    </div>
</div>
