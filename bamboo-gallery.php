<?php
/************************************************************************************************************/
/*
Plugin Name: Bamboo Gallery
Plugin URI:  https://www.bamboomanchester.uk/wordpress/bamboo-gallery
Author:      Bamboo
Author URI:  https://www.bamboomanchester.uk
Version:     1.3.1
Description: Replaces the default WordPress gallery with a responsive lightbox style gallery.
*/
/************************************************************************************************************/

	function bamboo_gallery_enqueue_scripts() {

		// Establish the base path.
		$path = plugins_url( '', __FILE__ );

		// Queue up jQuery.
		wp_enqueue_script( 'jquery' );

		// Queue up the gallery js.
    	wp_enqueue_script(
			'bamboo-gallery',
			$path . '/bamboo-gallery.min.js',
			'jquery',
			null,
			true
		);

    	// Queue up the gallery css.
		wp_enqueue_style(
			'bamboo-gallery',
			$path . '/bamboo-gallery.css',
			array(),
			null
		);

	}
	add_action( 'wp_enqueue_scripts', 'bamboo_gallery_enqueue_scripts' );

/************************************************************************************************************/

	// Replace the gallery shortcode with our own.
	function bamboo_gallery_do_shortcode( $atts, $content=null ) {

		// Track the instance of gallery incase there is more than one in the page.
		static $instance = 0;
		$instance++;

		// Get the current post.
		$post = get_post();

		// 'ids' is explicitly ordered, unless you specify otherwise.
		if ( !empty( $atts['ids'] ) ) {
			if ( empty( $atts['orderby'] ) ) {
				$atts['orderby'] = 'post__in';
			}
			$atts['include'] = $atts['ids'];
		}

		// Validate the orderby attribute.
		if ( isset( $atts['orderby'] ) ) {
			$atts['orderby'] = sanitize_sql_orderby( $atts['orderby'] );
			if ( !$atts['orderby'] )
				unset( $atts['orderby'] );
		}

		// Get the shortcode attributes.
		extract( shortcode_atts( array (
			'order'      => 'ASC',
			'orderby'    => 'menu_order ID',
			'id'         => $post->ID,
			'itemtag'    => 'dl',
			'icontag'    => 'dt',
			'captiontag' => 'dd',
			'columns'    => 3,
			'size'       => 'thumbnail',
			'include'    => '',
			'exclude'    => ''
		), $atts ) );

		// Convert the post ID to numeric.
		$id = intval( $id );

		// Translate order 'RAND' to 'none'.
		if ( 'RAND' == $order ) {
			$orderby = 'none';
		}

		// Get the images.
		if ( !empty( $include ) ) {
			$_attachments = get_posts( array( 'include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby ) );
			$attachments = array();
			foreach ( $_attachments as $key => $val ) {
				$attachments[$val->ID] = $_attachments[$key];
			}
		} elseif ( !empty( $exclude ) ) {
			$attachments = get_children( array( 'post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby ) );
		} else {
			$attachments = get_children( array( 'post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby ) );
		}

		// If there are no images return nothing.
		if ( empty( $attachments ) ) {
			return '';
		}

		// If this has been requested as part of a feed just return a list of links to the images.
		if ( is_feed() ) {
			$output = "\n";
			foreach ( $attachments as $att_id => $attachment )
				$output .= wp_get_attachment_link( $att_id, $size, true ) . "\n";
			return $output;
		}

		// Generate the HTML output for the gallery.
		$index = 0;
		$output = "<div id=\"bamboo-gallery-$instance\" class=\"bamboo-gallery columns-$columns\">";
		foreach ( $attachments as $id => $attachment ) {
			$index++;
			$thumb_url = wp_get_attachment_thumb_url( $id );
			$full_url = str_replace( site_url(), '', wp_get_attachment_url( $id ) );
			$filename = basename( get_attached_file( $id ) );

			$image_obj = wp_prepare_attachment_for_js( $id );
			$image = str_replace( site_url(), '', $image_obj['sizes']['thumbnail']['url'] );
			if( ''==$image ) {
				$image = str_replace( site_url(), '', $image_obj['sizes']['medium']['url'] );
			}
			if( ''==$image ) {
				$image = str_replace( site_url(), '', $image_obj['sizes']['large']['url'] );
			}
			if( ''==$image ) {
				$image = str_replace( site_url(), '', $image_obj['sizes']['full']['url'] );
			}

			$output.= "<a class=\"bamboo-gallery-button\" href=\"javascript:bambooGalleryShow($instance,$index);\" data-src=\"$full_url\">";
			$output.= "<div class=\"bamboo-gallery-thumbnail\" style=\"background-image:url('$image');\"></div>";
			$output.= "<div class=\"bamboo-gallery-overlay\"></div>";
			$output.= "</a>";
		}

		$output.= "</div>";

		// Return the HTML.
		return $output;
	}
	remove_shortcode( 'gallery' );
	add_shortcode( 'gallery', 'bamboo_gallery_do_shortcode');

/************************************************************************************************************/
?>
