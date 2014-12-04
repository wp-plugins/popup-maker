<?php


function popmake_init_popups() {
	global $popmake_loaded_popups, $popmake_loaded_popup_ids, $popmake_enqueued_popups;

	if(!$popmake_loaded_popups instanceof WP_Query) {
		$popmake_loaded_popups = new WP_Query();
		$popmake_loaded_popups->posts = array();
	}
	if(!$popmake_loaded_popup_ids || !is_array($popmake_loaded_popup_ids)) {
		$popmake_loaded_popup_ids = array();
	}
	if(!$popmake_enqueued_popups || !is_array($popmake_enqueued_popups)) {
		$popmake_enqueued_popups = array();
	}
}
add_action('plugins_loaded', 'popmake_init_popups');


function popmake_load_popup( int $id ) {
	global $popmake_loaded_popups, $popmake_loaded_popup_ids, $popmake_enqueued_popups;
	if( did_action( 'wp_head' ) && !in_array( $id, $popmake_loaded_popup_ids ) ) {
		$args1 = array_merge($base_args, array(
			'post_type' => 'popup',
			'p' => $id
		));
		$query1 = new WP_Query( $args1 );
		if ( $query1->have_posts() ) {
			while ( $query1->have_posts() ) : $query1->next_post();
				do_action( 'popmake_preload_popup', $query1->post->ID );
				$popmake_loaded_popups->posts[] = $query1->post;
				$popmake_loaded_popups->post_count++;
			endwhile;
		}
	}
	elseif( !did_action( 'wp_head' ) && !in_array( $id, $popmake_enqueued_popups ) ) {
		$popmake_enqueued_popups[] = $id;
	}
	return;
}


function popmake_enqueue_popup( int $id ) {
	return popmake_load_popup( $id );
}


function get_enqueued_popups() {
	global $popmake_enqueued_popups;
	$popmake_enqueued_popups = apply_filters('popmake_get_enqueued_popups', $popmake_enqueued_popups);
	return $popmake_enqueued_popups;
}


function popmake_preload_popups() {
	global $popmake_loaded_popups, $popmake_loaded_popup_ids;

	$query = new WP_Query( array(
		'post_type' => 'popup',
		'posts_per_page' => -1
	) );

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) : $query->next_post();
			if( popmake_popup_is_loadable( $query->post->ID ) ) {
				do_action( 'popmake_preload_popup', $query->post->ID );
				$popmake_loaded_popups->posts[] = $query->post;
				$popmake_loaded_popups->post_count++;
			}
		endwhile;

	}
}
add_action('wp_head', 'popmake_preload_popups', 1000);
add_action('wp_footer', 'popmake_render_popups', 1);


function popmake_render_popups() {
	global $popmake_loaded_popups;
	if ( $popmake_loaded_popups->have_posts() ) {
		while ( $popmake_loaded_popups->have_posts() ) : $popmake_loaded_popups->the_post();
			popmake_get_template_part('popup');
		endwhile;
		wp_reset_postdata();
	}
}