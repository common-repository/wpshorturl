<?php

/**
 * Here we create database table which is used in plugin
 */
function wsu_create_database_table_callback()
{
    global $wpdb;

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $charsetCollate = $wpdb->get_charset_collate();
    $tblName = $wpdb->prefix . 'wsu_items';
    $tableName = $wpdb->prefix . 'wsu_items_history';
    $categories = $wpdb->prefix. 'wsu_categories';

    $tblSql = "CREATE TABLE $tblName ( 
            ID BIGINT(20) NOT NULL AUTO_INCREMENT , 
            post_id BIGINT(20) NOT NULL , 
            post_type VARCHAR(100) NOT NULL ,
            title TEXT NOT NULL,
            custom_url TEXT NOT NULL , 
            short_slug TEXT NOT NULL , 
            catid TINYINT(5) NOT NULL , 
            status TINYINT(5) NOT NULL , 
            created_at INT NOT NULL , 
            PRIMARY KEY (ID)
        ) $charsetCollate;";

    dbDelta($tblSql);

    $tblSql2 = "CREATE TABLE $tableName ( 
            ID BIGINT(20) NOT NULL AUTO_INCREMENT , 
            wsu_item_id BIGINT(20) NOT NULL , 
            url_hit BIGINT(20) NOT NULL ,
            hit_date varchar(100) , 
            ip VARCHAR(100)  , 
            PRIMARY KEY (ID)
        ) $charsetCollate;";

    dbDelta($tblSql2);

    $tblSql3 = "CREATE TABLE $categories ( 
            ID BIGINT(20) NOT NULL AUTO_INCREMENT, 
            name VARCHAR(255), 
            PRIMARY KEY (ID)
        ) $charsetCollate;";

    dbDelta($tblSql3);

}
register_activation_hook(WSURL_PLUGIN_FILE, 'wsu_create_database_table_callback');
