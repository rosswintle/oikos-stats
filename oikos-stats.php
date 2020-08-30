<?php
/*
Plugin Name: Oikos Stats
Plugin URI: http://oikos.org.uk/plugins/
Description: A custom post type, taxonomies, meta boxes and some shortcodes for storing and displaying statistics.
Version: 1.0
Author: Ross Wintle
Author URI: http://oikos.org.uk
License: GPL2
*/
?>
<?php
/*  Copyright 2011  ROSS WINTLE (email : plugins@oikos.org.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<?
function oikos_stats_setup () {

	// Add a custom post type for featured items
	$oikos_stats_labels = array (
		'name' => 'Statistics',
		'singular_name' => 'Statistic',
		'add_new_item' => 'Add new statistic',
		'edit_item' => 'Edit statistic',
		'new_item' => 'New statistic',
		'view_item' => 'View statistic',
		'search_items' => 'Search statistics',
		'not_found' => 'No statistics',
		'not_found_in_trash' => 'No statistics found in trash'
	);
	$oikos_stats_supports = array ( 'title', 'revisions', 'page-attributes' );
	$oikos_stats_args= array (
		'label' => 'Statistics',
		'labels' => $oikos_stats_labels,
		'description' => 'List of statistics',
		'public' => true,
		'supports' => $oikos_stats_supports,
		'hierarchical' => true,
		'rewrite' => array ( 'slug' => 'statistics' )
	);
	register_post_type( 'stats', $oikos_stats_args );

	/*$oikos_stats_category_tax_labels = array (
		'name' => 'Categories',
		'singular_name' => 'Category',
		'search_items' => 'Search Categories',
		'popular_items' => 'Popular Categories',
		'all_items' => 'All Categories',
		'parent_item' => 'Parent Category',
		'parent_item_colon' => 'Parent Category: ',
		'edit_item' => 'Edit Category',*/
	$oikos_stats_category_args = array (
		'label' => 'Categories',
		'hierarchical' => true,
		'rewrite' => array (
						'slug' => 'statistics-category',
						'hierarchical' => true )
	);
	register_taxonomy ( 'stats_category', 'stats', $oikos_stats_category_args );
		
	
}

add_action ( 'init', 'oikos_stats_setup');

/**
 * Shows error message about lack of meta box plugin - used by check below
 */
function oikosstats_no_meta_message()
{
    // Only show to admins
    if (current_user_can('manage_options')) {
?>
		<div class="updated">
			<p>The Oikos Stats plugin needs the Meta Box framework plugin to be enabled.</p>
		</div>
<?php
    }
}

/* Include the meta box library */
if (! class_exists('RW_Meta_Box')) {

	add_action('admin_notices', 'oikosstats_no_meta_message');     

}

if ( class_exists('RW_Meta_Box' )) {
	new RW_Meta_Box (
		array(
			'id' => 'stats_source',                            // meta box id, unique per meta box
			'title' => 'Statistic Source',            // meta box title
			'pages' => array('stats'),    // post types, accept custom post types as well, default is array('post'); optional
			'context' => 'normal',                        // where the meta box appear: normal (default), advanced, side; optional
			'priority' => 'high',                        // order of meta box: high (default), low; optional

			'fields' => array(                            // list of meta fields
				array(
					'name' => 'Statistic Source Description',                    // field name
					'desc' => 'Short description of the source of the statistic',    // field description, optional
					'id' => 'stat_source_desc',                // field id, i.e. the meta key
					'type' => 'text',                        // text box
					'std' => '',                    // default value, optional
					'validate_func' => ''            // validate function, created below, inside RW_Meta_Box_Validate class
				),
				array(
					'name' => 'Statistic Source Link',                    // field name
					'desc' => 'URL for the source of the statistic',    // field description, optional
					'id' => 'stat_source_link',                // field id, i.e. the meta key
					'type' => 'text',                        // text box
					'std' => '',                    // default value, optional
					'validate_func' => ''            // validate function, created below, inside RW_Meta_Box_Validate class
				)
			)
		)
	);

	new RW_Meta_Box (
		array(
			'id' => 'stats_meta',                            // meta box id, unique per meta box
			'title' => 'Statistic Details (for widget)',            // meta box title
			'pages' => array('stats'),    // post types, accept custom post types as well, default is array('post'); optional
			'context' => 'normal',                        // where the meta box appear: normal (default), advanced, side; optional
			'priority' => 'high',                        // order of meta box: high (default), low; optional

			'fields' => array(                            // list of meta fields
				array(
					'name' => 'Statistic Date',                    // field name
					'desc' => 'The date of the statistic',    // field description, optional
					'id' => 'stat_date',                // field id, i.e. the meta key
					'type' => 'date',                        // text box
					'format' => 'dd MM yy',
					'std' => date('Y-m-d'),                    // default value, optional
					'validate_func' => ''            // validate function, created below, inside RW_Meta_Box_Validate class
				),
				array(
					'name' => 'Statistic prefix text',                    // field name
					'desc' => 'Text that goes before the statistic number',    // field description, optional
					'id' => 'stat_prefix',                // field id, i.e. the meta key
					'type' => 'text',                        // text box
					'std' => '',                    // default value, optional
					'validate_func' => ''            // validate function, created below, inside RW_Meta_Box_Validate class
				),
				array(
					'name' => 'Statistic Number',                    // field name
					'desc' => 'The statistic value',    // field description, optional
					'id' => 'stat_number',                // field id, i.e. the meta key
					'type' => 'text',                        // text box
					'std' => '',                    // default value, optional
					'validate_func' => ''            // validate function, created below, inside RW_Meta_Box_Validate class
				),
				array(
					'name' => 'Statistic suffix text',                    // field name
					'desc' => 'Text that goes after the statistic number',    // field description, optional
					'id' => 'stat_suffix',                // field id, i.e. the meta key
					'type' => 'text',                        // text box
					'std' => '',                    // default value, optional
					'validate_func' => ''            // validate function, created below, inside RW_Meta_Box_Validate class
				)
			)
		)
	);
}

function oikos_stats_list( $options ) {
	$defaults = array(
					'posts_per_page' => 20,
					'post_type' => 'stats'
					);
	$stats_query = new WP_Query( $defaults );
	if ( $stats_query->have_posts() ) :
	?>
		<ul class="oikos-stats-list">
	<?
			while ( $stats_query->have_posts() ) :
				$stats_query->the_post();
				$stat_link = get_post_meta(get_the_ID(), 'stat_source_link', true);
				$stat_desc = get_post_meta(get_the_ID(), 'stat_source_desc', true);
				$stat_date = get_post_meta(get_the_ID(), 'stat_date', true);
	?>
				<li>
					<h3><a href="<?php echo $stat_link; ?>"><?php the_title(); ?></a></h3>
					<p class="oikos-stat-source">
						Source: <a href="<?php echo $stat_link; ?>"><?php echo $stat_desc; ?></a>
					</p>
					<p class="oikos-stat-meta">
						<span class="oikos-stat-date">Date: <?php echo $stat_date; ?></span>
						<span class="oikos-stat-tags"><?php the_terms( get_the_ID(), 'stats_category' ); ?></span>
					</p>
					
				</li>
	<?php
			endwhile;
			wp_reset_postdata(); 
	?>
		</ul>
	<?php
	endif;
}

add_shortcode( 'oikos_stats_list', 'oikos_stats_list' );
