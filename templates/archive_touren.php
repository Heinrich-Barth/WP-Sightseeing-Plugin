<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_One
 * @since Twenty Twenty-One 1.0
 */

get_header();

$description = get_the_archive_description();
$tt_title_pattern = get_option('wpsightseeing_setting_tour_title_pattern');
if (!isset($tt_title_pattern) || empty($tt_title_pattern))
    $tt_title_pattern = "%s";

$tt_titletour_pattern = get_option('wpsightseeing_setting_tour_tourlist_pattern');
if (!isset($tt_titletour_pattern) || empty($tt_titletour_pattern))
    $tt_titletour_pattern = "%s";
        
?>
<article class="post-21 etappen type-etappen status-publish hentry touren-tour1 entry">

	<header class="entry-header">
        <h1 class="entry-title default-max-width"><? echo sprintf($tt_title_pattern, single_cat_title("", false))?></h1>
        <?php if ( $description ) : ?>
			<div class="tt-left"><?php echo wp_kses_post( wpautop( $description ) ); ?></div>
		<?php endif; ?>
	</header><!-- .entry-header -->

    <div class="entry-content">
        <div class="tt-left">
        <p><? echo sprintf($tt_titletour_pattern, single_cat_title("", false))?></p>
        </div>
    <?php
        tt_insertMap(tt_getEtappenByTourId(get_queried_object()->term_id), false, null);
    ?>
</div><!-- .entry-content -->
</article><!-- #post-<?php the_ID(); ?> -->



<?php get_footer(); ?>
