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
$etappen_title = get_option('wpsightseeing_setting_etappen_title');
if (!isset($etappen_title) || empty($etappen_title))
        $etappen_title = "All Sights";

$wpsightseeing_setting_etappen_text = get_option("wpsightseeing_setting_etappen_text");
if (!isset($wpsightseeing_setting_etappen_text) || empty($wpsightseeing_setting_etappen_text))
        $wpsightseeing_setting_etappen_text = "All Sights of all tours are shown on the map below";
?>
<article class="post-21 etappen etappen-archiv type-etappen status-publish hentry touren-tour1 entry">

	<header class="entry-header">
        <h1 class="entry-title"><?=$etappen_title?></h1>
        <p><?=$wpsightseeing_setting_etappen_text?></p>
	</header><!-- .entry-header -->

    <div class="entry-content">
        <?php tt_insertMapAllEtappen(tt_getAllEtappen()); ?>

</div><!-- .entry-content -->
</article><!-- #post-<?php the_ID(); ?> -->

<?php get_footer(); ?>
