<?php
/**
 * Template Functions
 *
 * @package dtbaker-elementor
 *
 * (just the do_content hook for the elementor widget, maybe more later on)
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;

if ( ! function_exists( 'dtbaker_elementor_page_content' ) ) {

	/**
	 * Renderes the_content() from our Elementor widget hook.
	 * Other plugins can hook in before stylepress/render-inner@20 to show content before/after
	 *
	 * @param array $settings Elementor settings from this particular widget. Empty for now but may contain settings down the track.
	 */
	function dtbaker_elementor_page_content( $settings = array() ) {

		$current_page_type = DtbakerElementorManager::get_instance()->get_current_page_type();

		if(!empty($GLOBALS['stylepress_rendering_inner'])){
			\DtbakerElementorManager::get_instance()->debug_message("Nested inner content for ". $current_page_type .". Running do_shortcode( get_the_" . ( $current_page_type == 'archive' ? 'excerpt' : 'content' ) ."() ); ");

			// save and restore global post entry while we do this.
			if ( isset( $GLOBALS['post'] ) ) {
				$global_post = $GLOBALS['post'];
			}

			if(!empty($GLOBALS['stylepress_post_for_dynamic_fields'])){
				if(is_object($GLOBALS['stylepress_post_for_dynamic_fields'])){
					$GLOBALS['post'] = $GLOBALS['stylepress_post_for_dynamic_fields'];
					setup_postdata($GLOBALS['post']);
				}
			}
			echo '<!-- Start Inner Render Content for ID '.(int)get_the_ID().' --> ';
			$GLOBALS['twodeep'] = true;
			// is this page we're trying to edit an elementor page?

			// todo: make these options in the settings array.
			switch($current_page_type){
				case 'archive':
					echo do_shortcode( get_the_excerpt() );
					break;
				default:
//					echo do_shortcode( get_the_content() );
					the_content();
//					echo Elementor\Plugin::instance()->frontend->get_builder_content_for_display( get_the_ID() );
			}

			// Restore global post
			if ( isset( $global_post ) ) {
				$GLOBALS['post'] = $global_post;
				setup_postdata($GLOBALS['post']);
			} else {
				unset( $GLOBALS['post'] );
			}

			$GLOBALS['twodeep'] = false;
			echo '<!-- End Inner Render Content --> ';
			return;
		}
		echo '<!-- Start StylePress Render --> ';
		$GLOBALS['stylepress_rendering_inner'] = true;


		\DtbakerElementorManager::get_instance()->debug_message("Current page type for inner content style lookup is: $current_page_type ");

		$style_settings = DtbakerElementorManager::get_instance()->get_settings();

		$component_template = $current_page_type . '_inner';
		if( is_home() || is_front_page() ){
			// home page or blog output page.
			if ( 'page' == get_option( 'show_on_front' ) && is_front_page() && get_option( 'page_on_front' ) ) {
				//
			}else if($component_template != 'archive_inner'){
				$component_template = 'archive_inner';
				\DtbakerElementorManager::get_instance()->debug_message("We're showing blog post output on home page, using inner style $component_template instead");
			}
		}

		while ( have_posts() ) : the_post();

			global $post;
			$debug_info = "Rendering Post ID <code>".$post->ID."</code> ";

			$GLOBALS['stylepress_post_for_dynamic_fields'] = $post;

			$style_id = false;
			if( $component_template ){
				// loading this component/
				if(!empty($style_settings['defaults'][$component_template])){
					$style_id = (int) $style_settings['defaults'][$component_template];
					$debug_info .= " with the $component_template style ";
				}else{
					// we use the global inner settings.
					$debug_info .= " using the Global Inner style ";
					if(!empty($style_settings['defaults']['_global_inner'])){
						$style_id = (int) $style_settings['defaults']['_global_inner'];
					}else{

					}

				}

			}
			if(!$style_id){
				$debug_info .= " with a call to the_content() because no custom inner style was defined ";
			}else{
				if($style_id == -1){
					$debug_info .= " plain the_content() ";
				}else if($style_id == -2){
					$debug_info .= " theme default inner content. ";
				}else{
					$debug_info .= '<a href="'.get_permalink($style_id).'">' . esc_html(get_the_title($style_id)) .'</a>';
				}
			}
			\DtbakerElementorManager::get_instance()->debug_message($debug_info);

			if($style_id) {
				echo Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $style_id );
			}else{
				the_content();
			}

			$GLOBALS['stylepress_post_for_dynamic_fields'] = false;

		endwhile;

		// work out if we have an inner component for this particular post style.
		echo '<!-- End StylePress Render --> ';
		$GLOBALS['stylepress_rendering_inner'] = false;

	}
}
add_action( 'stylepress/render-inner', 'dtbaker_elementor_page_content', 20 );
