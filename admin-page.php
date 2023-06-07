<?php

function wpsightseeing_settings_init() {
 
    // register a new section in the "reading" page
    add_settings_section(
        'wpsightseeing_settings_section',
        'wpsightseeing Tour Settings Section', 'wpsightseeing_settings_section_callback',
        'reading'
    );
 
    // register a new field in the "wpsightseeing_settings_section" section, inside the "reading" page
    // register a new setting for "reading" page
    register_setting('reading', 'wpsightseeing_setting_etappen_title');
    add_settings_field(
        'wpsightseeing_settings_field',
        'Sight Overview - Title Text', 'wpsightseeing_settings_field_callback',
        'reading',
        'wpsightseeing_settings_section'
    );

    register_setting('reading', 'wpsightseeing_setting_etappen_text');
    add_settings_field(
        'wpsightseeing_settings_field_etappen_text',
        'Sight Overview - Introduction Paragraph', 'wpsightseeing_settings_field_etappen_text_callback',
        'reading',
        'wpsightseeing_settings_section'
    );

    register_setting('reading', 'wpsightseeing_setting_tour_title_pattern');
    add_settings_field(
        'wpsightseeing_setting_tour_title_pattern',
        'Tour Detail - Title Pattern', 'wpsightseeing_setting_tour_title_pattern_callback',
        'reading',
        'wpsightseeing_settings_section'
    );
    register_setting('reading', 'wpsightseeing_setting_tour_tourlist_pattern');
    add_settings_field(
        'wpsightseeing_setting_tour_tourlist_pattern',
        'Tour Detail - Tour List Title Pattern', 'wpsightseeing_setting_tour_tourlist_pattern_callback',
        'reading',
        'wpsightseeing_settings_section'
    );
    register_setting('reading', 'wpsightseeing_setting_tour_additional_pattern');
    add_settings_field(
        'wpsightseeing_setting_tour_additional_pattern',
        'Tour Detail - Additional Sights of Current Tour Pattern', 'wpsightseeing_setting_tour_additional_pattern_callback',
        'reading',
        'wpsightseeing_settings_section'
    );
}
 
/**
 * register wpsightseeing_settings_init to the admin_init action hook
 */
add_action('admin_init', 'wpsightseeing_settings_init');
 
/**
 * callback functions
 */
 
/* Introduction */
function wpsightseeing_settings_section_callback() {
    echo '<p>Labels for Sight Overview page.</p>';
}
 
// field content cb
function wpsightseeing_settings_field_callback() 
{
    $setting = get_option('wpsightseeing_setting_etappen_title');
    // Alle Sehenswürdigkeiten im Überblick
    ?>
    <input type="text" name="wpsightseeing_setting_etappen_title" placeholder="All Sights" value="<?php echo isset( $setting ) ? esc_attr( $setting ) : ''; ?>">
    <?php
}

function wpsightseeing_settings_field_etappen_text_callback()
{
    $setting = get_option('wpsightseeing_setting_etappen_text');
    // Die Sehenswürdigkeiten aller vorhandenen Touren sind auf der Karte dargestellt...
    ?>
    <textarea type="text" name="wpsightseeing_setting_etappen_text" placeholder="All Sights"><?php echo isset( $setting ) ? esc_attr( $setting ) : ''; ?></textarea>
    <?php
}

function wpsightseeing_setting_tour_title_pattern_callback()
{
    $setting = get_option('wpsightseeing_setting_tour_title_pattern');
    // Die Sehenswürdigkeiten aller vorhandenen Touren sind auf der Karte dargestellt...
    ?>
    <input type="text" name="wpsightseeing_setting_tour_title_pattern" placeholder="%s" value="<?php echo isset( $setting ) ? esc_attr( $setting ) : ''; ?>">
    <p>This text will be parsed by the PHP function <code>printf</code></p>
    <?php
}

function wpsightseeing_setting_tour_tourlist_pattern_callback()
{
    $setting = get_option('wpsightseeing_setting_tour_tourlist_pattern');
    // Die Tour <strong>%s</strong> besteht aus diesen Etappen:
    ?>
    <input type="text" name="wpsightseeing_setting_tour_tourlist_pattern" placeholder="%s" value="<?php echo isset( $setting ) ? esc_attr( $setting ) : ''; ?>">
    <p>This text will be parsed by the PHP function <code>printf</code></p>
    <?php
}


function wpsightseeing_setting_tour_additional_pattern_callback()
{
    $setting = get_option('wpsightseeing_setting_tour_additional_pattern');
    // Die Tour <strong>%s</strong> besteht aus diesen Etappen:
    ?>
    <input type="text" name="wpsightseeing_setting_tour_additional_pattern" placeholder="%s" value="<?php echo isset( $setting ) ? esc_attr( $setting ) : ''; ?>">
    <p>This text will be parsed by the PHP function <code>printf</code></p>
    <?php
}


// Weitere Etappen der Tour
// Weitere Etappen der Tour &bdquo;%s&rdquo;