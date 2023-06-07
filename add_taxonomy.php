<?php 

function wporg_register_taxonomy_touren()
{
    $labels = array(
        'name'              => _x( 'Touren', 'taxonomy general name' ),
        'singular_name'     => _x( 'Tour', 'taxonomy singular name' ),
        'search_items'      => __( 'Durchsuche Touren' ),
        'all_items'         => __( 'Alle Touren' ),
        'edit_item'         => __( 'Bearbeite Tour' ),
        'update_item'       => __( 'Speichere Tour' ),
        'add_new_item'      => __( 'Neue Tour anlegen' ),
        'new_item_name'     => __( 'Neuer Touren-Name' ),
        'menu_name'         => __( 'Touren' ),
    );

    $args   = array(
        'hierarchical'      => true, // make it hierarchical (like categories)
        'labels'            => $labels,
        'has_archive' => true,
        'public' => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_quick_edit' => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'query_var'         => true,
        'show_in_rest'      => true,
        'rewrite'           => [ 'slug' => 'touren' ],
        
    );
    register_taxonomy( 'touren', [ "etappen" ], $args );
}

add_action( 'init', 'wporg_register_taxonomy_touren' );

/**
 * Liste alle Etappen einer Tour
 */
add_filter('taxonomy_template', 'tt_get_taxonomy_template');
function tt_get_taxonomy_template( $template )
{
    if (is_tax('touren')) 
        return dirname(  __FILE__  ) . '/templates/archive_touren.php';
    else
        return $template;
}

/**
 * Add image field in taxonomy page
 */
add_action( 'touren_add_form_fields', 'add_custom_taxonomy_image', 10, 2 );
function add_custom_taxonomy_image ( $taxonomy ) {
?>
    <div class="form-field term-group">

        <label for="image_id"><?php _e('Bild der Tour', 'taxt-domain'); ?></label>
        <input type="hidden" id="image_id" name="image_id" class="custom_media_url" value="">

        <div id="image_wrapper"></div>

        <p>
            <input type="button" class="button button-secondary taxonomy_media_button" id="taxonomy_media_button" name="taxonomy_media_button" value="<?php _e( 'Bild setzen/austauschen', 'taxt-domain' ); ?>">
            <input type="button" class="button button-secondary taxonomy_media_remove" id="taxonomy_media_remove" name="taxonomy_media_remove" value="<?php _e( 'Bild entfernen', 'taxt-domain' ); ?>">
        </p>

    </div>
<?php
}

/**
 * Save the taxonomy image field
 */
add_action( 'created_touren', 'save_custom_taxonomy_image', 10, 2 );
function save_custom_taxonomy_image ( $term_id, $tt_id )
{
    if( isset( $_POST['image_id'] ) && '' !== $_POST['image_id'] )
    {
        $image = $_POST['image_id'];
        add_term_meta( $term_id, 'category_image_id', $image, true );
    }
}

/**
 * Add the image field in edit form page
 */
add_action( 'touren_edit_form_fields', 'update_custom_taxonomy_image', 10, 2 );
function update_custom_taxonomy_image ( $term, $taxonomy ) { ?>
    <tr class="form-field term-group-wrap">
        <th scope="row">
            <label for="image_id"><?php _e( 'Bild der Tour', 'taxt-domain' ); ?></label>
        </th>
        <td>

            <?php $image_id = get_term_meta ( $term -> term_id, 'image_id', true ); ?>
            <input type="hidden" id="image_id" name="image_id" value="<?php echo $image_id; ?>">

            <div id="image_wrapper">
            <?php if ( $image_id ) { ?>
               <?php echo wp_get_attachment_image ( $image_id, 'thumbnail' ); ?>
            <?php } ?>

            </div>

            <p>
                <input type="button" class="button button-secondary taxonomy_media_button" id="taxonomy_media_button" name="taxonomy_media_button" value="<?php _e( 'Bild setzen/austauschen', 'taxt-domain' ); ?>">
                <input type="button" class="button button-secondary taxonomy_media_remove" id="taxonomy_media_remove" name="taxonomy_media_remove" value="<?php _e( 'Bild entfernen', 'taxt-domain' ); ?>">
            </p>

        </div></td>
    </tr>
<?php
}

/**
 * Update the taxonomy image field
 */
add_action( 'edited_touren', 'updated_custom_taxonomy_image', 10, 2 );
function updated_custom_taxonomy_image ( $term_id, $tt_id ) 
{
    if( isset( $_POST['image_id'] ) && '' !== $_POST['image_id'] )
    {
        $image = $_POST['image_id'];
        update_term_meta ( $term_id, 'image_id', $image );
    } 
    else
        update_term_meta ( $term_id, 'image_id', '' );
}

/**
 * Enqueue the wp_media library
 */
add_action( 'admin_enqueue_scripts', 'custom_taxonomy_load_media' );
function custom_taxonomy_load_media()
{
    if( ! isset( $_GET['taxonomy'] ) || $_GET['taxonomy'] != 'touren' )
       return;

    wp_enqueue_media();
    wp_enqueue_script( 'handle', plugin_dir_url( __FILE__ ) . '/admin/script.js' );
}


/**
 * Add new column heading
 */
add_filter( 'manage_edit-touren_columns', 'display_custom_taxonomy_image_column_heading' );
function display_custom_taxonomy_image_column_heading( $columns ) {
    $columns['category_image'] = __( 'Touren-Bild', 'taxt-domain' );
    return $columns;
}

/**
 * Display new columns values
 */
add_action( 'manage_touren_custom_column', 'display_custom_taxonomy_image_column_value' , 10, 3);
function display_custom_taxonomy_image_column_value( $columns, $column, $id ) {
    if ( 'category_image' == $column )
    {
        $image_id = esc_html( get_term_meta($id, 'image_id', true) );
        $columns = wp_get_attachment_image ( $image_id, array('50', '50') );
    }
    return $columns;
}

?>