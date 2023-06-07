<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_One
 * @since Twenty Twenty-One 1.0
 */

get_header();


/* Start the Loop */
while ( have_posts() ) :
	
	$_lat = get_post_meta(get_the_ID(), "etappe_geo_lat", true);
	$_long = get_post_meta(get_the_ID(), "etappe_geo_long", true);
	$_id = get_the_ID();

	if (!empty($_lat) && !empty($_long))
	{
		echo '<div id="tt-tour-current" class="tt-hide" data-lat="'.$_lat.'" data-long="'.$_long.'" data-id="'.$_id.'"></div>';
	}
	
	$vsToursMap = tt_getToursArray();

	the_post();

	get_template_part( 'template-parts/content/content-single' );

	if ( is_attachment() ) 
	{
		// Parent post navigation.
		the_post_navigation(
			array(
				/* translators: %s: Parent post link. */
				'prev_text' => sprintf( __( '<span class="meta-nav">Published in</span><span class="post-title">%s</span>', 'twentytwentyone' ), '%title' ),
			)
		);
	}

	// If comments are open or there is at least one comment, load up the comment template.
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}

	tt_insertMap($vsToursMap, false);
	
endwhile; // End of the loop.

get_footer();
