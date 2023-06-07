<?php
/*
    Plugin Name: wpsightseeing Plugin
    Description: Tours, Sights and Events
*/
require "add_taxonomy.php";
require "admin-page.php";

class WPSightseeingPlugin
{
    public static function tt_etappe_create_posttype() 
    {
        register_post_type( 'etappen',
            // CPT Options
            array(
                'labels' => array(
                    'name' => __( 'Etappen' ),
                    'singular_name' => __( 'Etappe' )
                ),
                'public' => true,
                'has_archive' => true,
                'rewrite' => array('slug' => 'etappen'),
                'show_ui'      => true,
                'show_in_rest' => true,
                'show_in_menu'        => true,
                'show_in_nav_menus'   => true,
                'menu_position'       => 5,
                'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions', 'custom-fields', ),
                'taxonomies'          => array( 'touren'  ),
            )
        );
    }
    
    public static function tt_get_etappen_single_template( $template )
    {
        if (is_singular( 'etappen' ) )
            return dirname(  __FILE__  ) . '/templates/single-etappen.php';
        else
            return $template;
    }

    public static function tt_add_my_post_types_to_query( $query )
    {
        if ( is_home() && $query->is_main_query() )
            $query->set( 'post_type', array( 'post', 'etappen' ) );
    
        return $query;
    }

    public static function tt_meta_box_for_etappen( $post )
    {
        add_meta_box( 'my_meta_box_custom_id', __( 'Geo Koordinaten', 'textdomain' ), 'WPSightseeingPlugin::tt_my_custom_meta_box_html_output', 'etappen');
    }

    public static function tt_my_custom_meta_box_html_output( $post )
    {
        $lat = get_post_meta( $post->ID, 'etappe_geo_lat', true );
        $long = get_post_meta( $post->ID, 'etappe_geo_long', true );
        $etappe_name_dif = get_post_meta( $post->ID, 'etappe_name_dif', true );
        $etappe_address = get_post_meta( $post->ID, 'etappe_address', true );
        
        wp_nonce_field( basename( __FILE__ ), 'my_custom_meta_box_nonce' ); //used later for security
        echo '
            <table style="width:100%">
            <tbody>
                <tr>
                    <td style="width:25%">
                        <label for="etappe_name_dif" style="display:inline">Name of Sight (if different from page title, see above):</label>
                    </td>
                    <td>
                        <input type="text" name="etappe_name_dif" id="etappe_name_dif" value="'.$etappe_name_dif.'" style="width:100%" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="etappe_address" style="display:inline">Adress:</label>
                    </td>
                    <td>
                        <textarea type="text" name="etappe_address" id="etappe_address" style="width:100%">'.$etappe_address.'</textarea>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td><br><smaller>Geo-Corrdinates can be copy&amp;pasted from Maps:</smaller></td>
                </tr>
                <tr>
                    <td>
                        <label for="etappe_geo_lat" style="display:inline">Geo-Longitude (1st value):</label>
                    </td>
                    <td>
                        <input type="text" name="etappe_geo_lat" id="tt_etappe_geo_lat" value="'.$lat.'" style="width:100%" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="etappe_geo_long" style="display:inline">Geo-Latitude (2nd value)</label>
                    </td>
                    <td>
                        <input type="text" name="etappe_geo_long" id="tt_etappe_geo_long" value="'.$long.'" style="width:100%" />
                    </td>
                </tr>
            </tbody>
        </table>';
    }

    public static function team_member_save_meta_boxes_data( $post_id )
    {
        // check for nonce to top xss
        if ( !isset( $_POST['my_custom_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['my_custom_meta_box_nonce'], basename( __FILE__ ) ) ){
            return;
        }
    
        // check for correct user capabilities - stop internal xss from customers
        if ( ! current_user_can( 'edit_post', $post_id ) ){
            return;
        }
    
        // update fields
        if ( isset( $_REQUEST['etappe_name_dif'] ) ) {
            update_post_meta( $post_id, 'etappe_name_dif', sanitize_text_field( $_POST['etappe_name_dif'] ) );
        }
    
        // update fields
        if ( isset( $_REQUEST['etappe_address'] ) ) {
            update_post_meta( $post_id, 'etappe_address', sanitize_text_field( $_POST['etappe_address'] ) );
        }
    
        // update fields
        if ( isset( $_REQUEST['etappe_geo_lat'] ) ) {
            update_post_meta( $post_id, 'etappe_geo_lat', sanitize_text_field( $_POST['etappe_geo_lat'] ) );
        }
    
        // update fields
        if ( isset( $_REQUEST['etappe_geo_long'] ) ) {
            update_post_meta( $post_id, 'etappe_geo_long', sanitize_text_field( $_POST['etappe_geo_long'] ) );
        }
    }

    public static function get_category_template( $template )
    {
        if ("etappen" == get_post_type()) 
            return dirname(  __FILE__  ) . '/templates/archive_etappen.php';
        else
            return $template;
    }
}

/**
 * Our custom post type function
 */
add_action( 'init', 'WPSightseeingPlugin::tt_etappe_create_posttype' );

/**
 * Liste alle Etappen einer Tour
 */
add_filter('single_template', 'WPSightseeingPlugin::tt_get_etappen_single_template');

/**
 * Add Etappen to postings list
 */
add_action( 'pre_get_posts', 'WPSightseeingPlugin::tt_add_my_post_types_to_query' );


add_action( 'add_meta_boxes', 'WPSightseeingPlugin::tt_meta_box_for_etappen' );


add_action( 'save_post_etappen', 'WPSightseeingPlugin::team_member_save_meta_boxes_data', 10, 2 );


/**
 * Liste alle Etappen einer Tour
 */
add_filter('archive_template', 'WPSightseeingPlugin::get_category_template');

require "assets.php";
require "functions.php";
  
/* Stop Adding Functions Below this Line */
?>
