<?php
/*
Plugin Name: wpCMSdev Testimonial Post Type
Plugin URI:  http://wpcmsdev.com/plugins/testimonial-post-type/
Description: Registers a "Testimonials" custom post type.
Author:      wpCMSdev
Author URI:  http://wpcmsdev.com
Version:     1.0
Text Domain: wpcmsdev-testimonial-post-type
Domain Path: /languages
License:     GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


Copyright (C) 2014  wpCMSdev

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/


/**
 * Registers the "testimonial" post type.
 */
function wpcmsdev_testimonials_post_type_register() {

	$labels = array(
		'name'               => __( 'Testimonials',                    'wpcmsdev-testimonial-post-type' ),
		'singular_name'      => __( 'Testimonial',                     'wpcmsdev-testimonial-post-type' ),
		'all_items'          => __( 'All Testimonials',                'wpcmsdev-testimonial-post-type' ),
		'add_new'            => _x( 'Add New', 'testimonial',          'wpcmsdev-testimonial-post-type' ),
		'add_new_item'       => __( 'Add New Testimonial',             'wpcmsdev-testimonial-post-type' ),
		'edit_item'          => __( 'Edit Testimonial',                'wpcmsdev-testimonial-post-type' ),
		'new_item'           => __( 'New Testimonial',                 'wpcmsdev-testimonial-post-type' ),
		'view_item'          => __( 'View Testimonial',                'wpcmsdev-testimonial-post-type' ),
		'search_items'       => __( 'Search Testimonials',             'wpcmsdev-testimonial-post-type' ),
		'not_found'          => __( 'No testimonials found.',          'wpcmsdev-testimonial-post-type' ),
		'not_found_in_trash' => __( 'No testimonials found in Trash.', 'wpcmsdev-testimonial-post-type' ),
	);

	$args = array(
		'labels'        => $labels,
		'menu_icon'     => 'dashicons-format-quote',
		'menu_position' => 5,
		'public'        => false,
		'show_ui'       => true,
		'supports'      => array(
			'author',
			'custom-fields',
			'editor',
			'page-attributes',
			'revisions',
			'thumbnail',
			'title',
		),
	);

	$args = apply_filters( 'wpcmsdev_testimonial_post_type_args', $args );

	register_post_type( 'testimonial', $args );

}
add_action( 'init', 'wpcmsdev_testimonials_post_type_register' );


/**
 * Loads the translation files.
 */
function wpcmsdev_testimonials_load_translations() {

	load_plugin_textdomain( 'wpcmsdev-testimonial-post-type', false, dirname( plugin_basename( __FILE__ ) ) ) . '/languages/';
}
add_action( 'plugins_loaded', 'wpcmsdev_testimonials_load_translations' );


/**
 * Initializes additional functionality when used with a theme that declares support for the plugin.
 */
function wpmcsdev_testimonials_additional_functionality_init() {

	if ( current_theme_supports( 'wpcmsdev-testimonial-post-type' ) ) {
		add_filter( 'manage_edit-testimonial_columns',        'wpcmsdev_testimonials_manage_posts_columns' );
		add_action( 'manage_testimonial_posts_custom_column', 'wpcmsdev_testimonials_manage_posts_columm_content' );
		add_action( 'admin_enqueue_scripts',                  'wpcmsdev_testimonials_manage_posts_css' );
		add_filter( 'cmb2_meta_boxes',                        'wpcmsdev_testimonials_meta_box' );
	}
}
add_action( 'after_setup_theme', 'wpmcsdev_testimonials_additional_functionality_init', 11 );


/**
 * Registers custom columns for the Manage Testimonials admin page.
 */
function wpcmsdev_testimonials_manage_posts_columns( $columns ) {

	$columns['title'] = __( 'Name', 'wpcmsdev-testimonial-post-type' );

	$column_author_url    = array( 'author_url'    => __( 'Author URL', 'wpcmsdev-testimonial-post-type' ) );
	$column_author_avatar = array( 'author_avatar' => __( 'Author Avatar', 'wpcmsdev-testimonial-post-type' ) );
	$column_order         = array( 'order'         => __( 'Order', 'wpcmsdev-testimonial-post-type' ) );

	$columns = array_slice( $columns, 0, 2, true ) + $column_author_url + $column_author_avatar + $column_order + array_slice( $columns, 2, null, true );

	return $columns;
}


/**
 * Outputs the custom column content for the Manage Testimonials admin page.
 */
function wpcmsdev_testimonials_manage_posts_columm_content( $column ) {

	global $post;

	switch( $column ) {

		case 'author_avatar':
			if ( $author_email = get_post_meta( $post->ID, 'author_email', true ) ) {
				echo get_avatar( $author_email, 50 );
			} elseif ( has_post_thumbnail() ) {
				the_post_thumbnail( 'thumbnail' );
			} else {
				echo get_avatar( '', 50 );
			}
			break;

		case 'order':
			$order = $post->menu_order;
			if ( 0 === $order ) {
				echo '<span class="default-value">' . $order . '</span>';
			} else {
				echo $order;
			}
			break;

		case 'author_url':
			$url = get_post_meta( $post->ID, 'author_url', true );
			if ( $url ) {
				printf( '<a href="%1$s">%1$s</a>', esc_url( $url ) );
			} else {
				echo '&#8212;';
			}
			break;
	}
}


/**
 * Outputs the custom columns CSS used on the Manage Testimonials admin page.
 */
function wpcmsdev_testimonials_manage_posts_css() {

	global $pagenow, $typenow;
	if ( ! ( 'edit.php' == $pagenow && 'testimonial' == $typenow ) ) {
		return;
	}

?>
<style>
	.edit-php .posts .column-author_avatar,
	.edit-php .posts .column-order {
		width: 10%;
	}
	.edit-php .posts .column-author_avatar img {
		width: 50px;
		height: auto;
	}
	.edit-php .posts .column-order .default-value {
		color: #bbb;
	}
</style>
<?php
}


/**
 * Changes the "Enter title here" text on the Testimonial edit screen.
 */
function wpcmsdev_testimonials_enter_title_here( $title ) {

	global $post_type;
	if ( 'testimonial' == $post_type ) {
		$title = __( 'Enter testimonial author name here', 'wpcmsdev-testimonial-post-type' );
	}

	return $title;
}
add_filter( 'enter_title_here', 'wpcmsdev_testimonials_enter_title_here' );


/**
 * Creates the Testimonial Settings meta box and fields.
 */
function wpcmsdev_testimonials_meta_box( $meta_boxes ) {

	$meta_boxes['testimonial-settings'] = array(
		'id'           => 'testimonial-settings',
		'title'        => __( 'Testimonial Settings', 'wpcmsdev-testimonial-post-type' ),
		'object_types' => array( 'testimonial' ),
		'fields'       => array(
			array(
				'name' => __( 'Author URL', 'wpcmsdev-testimonial-post-type' ),
				'id'   => 'author_url',
				'type' => 'text_url',
			),
			array(
				'name' => __( 'Author Email', 'wpcmsdev-testimonial-post-type' ),
				'desc' => sprintf( __( 'For retrieving the user&rsquo;s <a href="%s" target="_blank">Avatar</a>; will not be displayed publicly.', 'wpcmsdev-testimonial-post-type' ), 'http://gravatar.com' ),
				'id'   => 'author_email',
				'type' => 'text_email',
			),
		),
	);

	return $meta_boxes;
}
