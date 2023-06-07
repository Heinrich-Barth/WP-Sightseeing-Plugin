<?php

function tt__head_css()
{
    echo "<link rel='stylesheet' id='wpsightseeing-styles-css'  href='".plugin_dir_url( __FILE__ )."assets/styles.css' media='all' />";
    echo "<link rel='stylesheet' id='wpsightseeing-styles-css'  href='".plugin_dir_url( __FILE__ )."assets/theme.css' media='all' />";
    echo '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css" integrity="sha512-hoalWLoI8r4UszCkZ5kL8vayOGVae1oxXe/2A4AO6J9+580uKHDO3JdHb7NzwwzK5xr/Fs0W40kiNHxM9vyTtQ==" crossorigin=""/>';
}

function tt__footer()
{
    echo '<script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js" integrity="sha512-BB3hKbKWOc9Ez/TAwyWxNXeoV9c1v6FIeYiBieIWkpLjauysF18NzgR1MBNBXf8/KABdlkX68nAhlwcDFLGPCQ==" crossorigin=""></script>';
    echo '<script src="/wp-content/plugins/wpsightseeing-tourt-plugin/assets/maps.js"></script>';
}

function tt__editorScript()
{
    wp_enqueue_script('tt__editorscript', plugin_dir_url( __FILE__ ).'assets/editor.js');
}

add_action( 'wp_head', 'tt__head_css' );
add_action( 'wp_footer', 'tt__footer' );
add_action( 'admin_enqueue_scripts', 'tt__editorScript' );

?>