<?php

function tt_getEtappenByTourId($termId)
{
	$tourLists = array();

	// the query
	$wpb_all_query = new WP_Query(array('post_type'=>'etappen', 'post_status'=>'publish', 'posts_per_page'=>-1,
		'orderby'   => 'title',
		'order' => 'ASC'
	)); 
	
	if ( $wpb_all_query->have_posts() )
	{
		
		while ( $wpb_all_query->have_posts() ) : $wpb_all_query->the_post(); 

		$terms = get_the_terms(get_the_ID(), 'touren' );

		if (isset($terms) && !empty($terms)) 
		{
			foreach ( $terms as $term ) 
			{
				if ($termId < 0 || $term->term_id == $termId)
				{

					$_title = get_the_title();
					$_url = get_post_permalink();
					$_image = get_the_post_thumbnail_url();
					$_lat = get_post_meta(get_the_ID(), "etappe_geo_lat", true);
					$_long = get_post_meta(get_the_ID(), "etappe_geo_long", true);
					$_id = get_the_ID();
					$_name = $term->name;
					$_term_id = $term->term_id;

					if (!isset($tourLists["t".$_term_id]))
					{
						$tourLists["t".$_term_id] = array(
							"name" => $_name,
							"etappen" => array()
						);
					}

					$tourLists["t".$_term_id]["etappen"][] = array(
						"title" => $_title,
						"image" => $_image,
						"long" => $_long,
						"lat" => $_lat,
						"ignore" => false,
						"url" => $_url,
						"id" => $_id
					);
					
				
				}
			}
		}
		
		endwhile;
	}

	wp_reset_postdata(); 

	return $tourLists;
 }

function tt_getAllEtappen()
{
	$tourLists = array();

	// the query
	$wpb_all_query = new WP_Query(array('post_type'=>'etappen', 'post_status'=>'publish', 'posts_per_page'=>-1,
		'orderby'   => 'title',
		'order' => 'ASC'
	)); 
	
	if ( $wpb_all_query->have_posts() )
	{
		while ( $wpb_all_query->have_posts() ) : $wpb_all_query->the_post(); 

		$terms = get_the_terms(get_the_ID(), 'touren' );

		if (isset($terms) && !empty($terms)) 
		{
			$_title = get_the_title();
			$_text = wp_strip_all_tags( get_the_excerpt(), true );
			$_url = get_post_permalink();
			$_image = get_the_post_thumbnail_url();
			$_lat = get_post_meta(get_the_ID(), "etappe_geo_lat", true);
			$_long = get_post_meta(get_the_ID(), "etappe_geo_long", true);
			$_addr = get_post_meta(get_the_ID(), "etappe_address", true);
			$_title_diff = get_post_meta(get_the_ID(), "etappe_name_dif", true);
			$_id = get_the_ID();
			if (!empty($_title_diff))
				$_title = $_title_diff;
	
			if (!isset($tourLists["p".get_the_ID()]))
			{
				$tourLists["p".get_the_ID()] = array(
					"title" => $_title,
					"text" => $_text,
					"address" => $_addr,
					"image" => $_image,
					"long" => $_long,
					"lat" => $_lat,
					"ignore" => false,
					"url" => $_url,
					"id" => $_id,
					"etappen" => array()
				);
			}

			foreach ( $terms as $term ) 
				$tourLists["p".get_the_ID()]["etappen"][] = $term->name;
		}
	
		endwhile;
	}

	wp_reset_postdata(); 

	return $tourLists;
 }

function tt_getToursArray()
{
	$g_currentPostId = get_the_ID();

	$vsTourIds = array('relation' => 'OR');

	$terms = get_the_terms( get_the_ID(), 'touren' );
    if (isset($terms) && !empty($terms)) 
	{
		foreach ( $terms as $term ) 
		{
			$vsTourIds[] = array(
				'taxonomy' => 'touren',  
				'field' => 'term_id',    
				'terms' => $term->term_id,
			);
		}
	}

	$query = new WP_Query( array(
		'post_type' => 'etappen',          // name of post type.
		'tax_query' => array($vsTourIds)
	) );

	$tourLists = array();
	
	while ( $query->have_posts() ) : $query->the_post();
    	// do stuff here....
		$_title = get_the_title();
		$_text = wp_strip_all_tags( get_the_excerpt(), true );
		$_thisid = get_the_ID();
		$_url = get_post_permalink();
		$_image = get_the_post_thumbnail_url();
		$_lat = get_post_meta(get_the_ID(), "etappe_geo_lat", true);
		$_long = get_post_meta(get_the_ID(), "etappe_geo_long", true);
		$_addr = get_post_meta(get_the_ID(), "etappe_address", true);
		$_title_diff = get_post_meta(get_the_ID(), "etappe_name_dif", true);
		$_id = get_the_ID();

		if (!empty($_title_diff))
			$_title = $_title_diff;

		$terms = get_the_terms( get_the_ID(), 'touren' );
		if (isset($terms) && !empty($terms)) 
		{
			foreach ( $terms as $term ) 
			{
				$_name = $term->name;
				$_term_id = $term->term_id;

				if (!isset($tourLists["t".$_term_id]))
				{
					$tourLists["t".$_term_id] = array(
						"name" => $_name,
						"etappen" => array()
					);
				}

				$tourLists["t".$_term_id]["etappen"][] = array(
					"title" => $_title,
					"text" => $_text,
					"address" => $_addr,
					"image" => $_image,
					"long" => $_long,
					"lat" => $_lat,
					"id" => $_id,
					"ignore" => $_thisid == $g_currentPostId,
					"url" => $_url
				);
			}
		}
	endwhile;

	wp_reset_query();

	return $tourLists;
}

function tt_insertMapAllEtappen($vsToursMap, $bMapFirst = true)
{
	if ($bMapFirst)
		tt_injectMap();

	echo '<div class="tt-wrap" data-is-single-list="true">';
	foreach ($vsToursMap as $_tid => $_etappe)
	{
		?>
			<div class="tt-box tt-kachel-etappe">
				<?php
					foreach ($_etappe["etappen"] as $__tour)
						echo '<span class="tt-tour-name tt-tour-name-single">'.$__tour."</span>";
				?>

				<div class="tt-boxInner">
					<?php if (!empty($_etappe["image"])) { ?>
						<img alt="Impressionsbild" src="<?=$_etappe["image"]?>">
					<?php } ?>
				</div>
				<a class="tt-titleBox" href="<?=$_etappe["url"]?>" data-id="<?= $_etappe["id"] ?>" data-geo-lat="<?= $_etappe["lat"] ?>" data-geo-long="<?= $_etappe["long"] ?>">
					<span data-type="titel"><?= $_etappe["title"] ?></span>
					<?php
						if (!empty($_etappe["address"]))
							echo '<span class="tt-hide" data-type="addresse">'.str_replace("\n","<br>",$_etappe["address"])."</span>";
					?>
				</a>

			</div>
		<?php
	}
	echo "</div>";

	if (!$bMapFirst)
		tt_injectMap();

}

function tt_injectMap()
{
	echo '<div id="mapContainer" class="tt-map-container tt-hide"><div id="map" class="tt-map"></div></div>';
}

function tt_insertMap($vsToursMap, $bMapFirst = true)
{
	if ($bMapFirst)
		tt_injectMap();

	$sectionTitle = get_option('wpsightseeing_setting_tour_additional_pattern');
	if (!isset($sectionTitle) || empty($sectionTitle))
        $sectionTitle = "Other Sights of the tour %s";

	foreach ($vsToursMap as $_tid => $_tour)
	{
		if (sizeof($_tour["etappen"]) < 2)
			continue;

		echo '<div class="tt-wrap">';
		
		if ($sectionTitle != null)
		{
			echo "<h2>";
			echo sprintf($sectionTitle, $_tour["name"]);
			echo "</h2>";
		}

		echo '<span class="tt-tour-name">'.$_tour["name"]."</span>";
    
		foreach ($_tour["etappen"] as $_etappe)
		{
			?>

			<div class="tt-box tt-kachel-etappe <?php echo $_etappe["ignore"] ? "tt-hide" : ""; ?>">
			  <div class="tt-boxInner">
				<?php if (!empty($_etappe["image"])) { ?>
					<img alt="Impressionsbild" src="<?=$_etappe["image"]?>">
				<?php } ?>
			  </div>
			  <a class="tt-titleBox" href="<?=$_etappe["url"]?>" data-id="<?= $_etappe["id"] ?>" data-geo-lat="<?= $_etappe["lat"] ?>" data-geo-long="<?= $_etappe["long"] ?>">
			  	<span data-type="titel"><?= $_etappe["title"] ?></span>
				  <?php
					if (!empty($_etappe["address"]))
						echo '<span class="tt-hide" data-type="addresse">'.str_replace("\n","<br>",$_etappe["address"])."</span>";
					?>
			  </a>
			</div>
			<?php
		}

		echo "</div>";
	}

	if (!$bMapFirst)
		tt_injectMap();
}

?>