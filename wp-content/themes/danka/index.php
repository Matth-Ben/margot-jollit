<?php

global $wp_query;

$context = \Timber\Timber::context();
$templates = array( 'index.twig' );

if ( function_exists( 'get_fields' ) ) {
	$context['options'] = get_fields( 'option' );
}

if ( isset( $_GET['styleguide'] ) ) {
	\Timber\Timber::render( array( '_styleguide.twig' ), $context );
}

// home
elseif ( is_front_page() ) {
	$timber_post = \Timber\Timber::get_post( false, get_custom_timber_post() );
	$context['post'] = $timber_post;
	
	array_unshift( $templates, 'front-page.twig', 'home.twig' );
}

// page
elseif ( $wp_query->is_page ) {
	$timber_post = \Timber\Timber::get_post( false, get_custom_timber_post() );
	$context['post'] = $timber_post;

	if ( post_password_required( $timber_post->ID ) ) {
		$templates = array( 'single-password.twig' );
	} else {
		$templates = array( 'page-' . $timber_post->post_name . '.twig', 'page.twig' );
	}
}

// post
elseif ( $wp_query->is_single ) {
	$timber_post = \Timber\Timber::get_post( false, get_custom_timber_post() );
	$context['post'] = $timber_post;

	if ( post_password_required( $timber_post->ID ) ) {
		$templates = array( 'single-password.twig' );
	} else {
		$templates = array( 'single-' . $timber_post->ID . '.twig', 'single-' . $timber_post->post_type . '.twig', 'single-' . $timber_post->slug . '.twig', 'single.twig' );
	}
}

// archive
elseif ( $wp_query->is_archive ) {
	$templates = array( 'archive.twig', 'index.twig' );
	$context['title'] = 'Archive';

	if ( is_day() ) {
		$context['title'] = 'Archive: ' . get_the_date( 'D M Y' );
	} elseif ( is_month() ) {
		$context['title'] = 'Archive: ' . get_the_date( 'M Y' );
	} elseif ( is_year() ) {
		$context['title'] = 'Archive: ' . get_the_date( 'Y' );
	} elseif ( is_tag() ) {
		$context['title'] = single_tag_title( '', false );
	} elseif ( is_category() ) {
		$context['title'] = single_cat_title( '', false );
		array_unshift( $templates, 'archive-' . get_query_var( 'cat' ) . '.twig' );
	} elseif ( is_post_type_archive() ) {
		$context['title'] = post_type_archive_title( '', false );
		array_unshift( $templates, 'archive-' . get_post_type() . '.twig' );
	}

	$context['post_type'] = get_queried_object();
	$context['posts'] = new \Timber\PostQuery();
}

// search
elseif ( $wp_query->is_search ) {
	global $wp_query;
	
	$templates = array( 'search.twig', 'archive.twig', 'index.twig' );
	$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
	$big = 999999999; // need an unlikely integer
	$pagination = paginate_links([
		'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
		'format' => '?paged=%#%',
		'current' => max(1, get_query_var('paged')),
		'total' => $wp_query->max_num_pages,
		'prev_text' => '<span class="icon-arrow-left"></span>',
		'next_text' => '<span class="icon-arrow-right"></span>',
	]);

	$context['search_query'] = get_search_query();
	$context['posts'] = new \Timber\PostQuery();
	$context['pagination'] = $pagination;

	if ( isset( $_GET['async'] ) ) {
		echo json_encode( iterator_to_array( $context['posts'] ) );
		wp_die();
	}
}

// 404
elseif ( $wp_query->is_404 ) {
	$templates = array( '404.twig' );
}

$context['wp_query'] = $wp_query;
\Timber\Timber::render( $templates, $context );
