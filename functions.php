<?php

require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/wts-thomas/wts-wordpress-theme-default/',
	__FILE__,
	'wts-elementor'
);

//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('main');


/*  Performance & Security Edits
_____________________________________________________________________*/

// REMOVES THE WORDPRESS VERSION NUMBER
remove_action('wp_head', 'wp_generator');

// REMOVE WLWMANIFEST
remove_action('wp_head', 'wlwmanifest_link');

// REMOVE RSD
remove_action('wp_head', 'rsd_link');

// CANCELS AUTO UPDATES FOR PLUGINS AND THEMES
add_filter( 'auto_update_plugin', '__return_false' );
add_filter( 'auto_update_theme', '__return_false' );

// REMOVE AVATAR DONATION MESSAGE
remove_action('wpua_donation_message', 'wpua_do_donation_message');

// REMOVE RSS FEEDS AND LINKS
add_action( 'do_feed', 'aioo_crunchify_perf_disable_feed', 1 );
add_action( 'do_feed_rdf', 'aioo_crunchify_perf_disable_feed', 1 );
add_action( 'do_feed_rss', 'aioo_crunchify_perf_disable_feed', 1 );
add_action( 'do_feed_rss2', 'aioo_crunchify_perf_disable_feed', 1 );
add_action( 'do_feed_atom', 'aioo_crunchify_perf_disable_feed', 1 );
add_action( 'do_feed_rss2_comments', 'aioo_crunchify_perf_disable_feed', 1 );
add_action( 'do_feed_atom_comments', 'aioo_crunchify_perf_disable_feed', 1 );

add_action( 'feed_links_show_posts_feed', '__return_false', - 1 );
add_action( 'feed_links_show_comments_feed', '__return_false', - 1 );
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'feed_links_extra', 3 );

// REMOVE WP EMOJI
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

// REMOVE JQUERY MIGRATE
function remove_jquery_migrate( $scripts ) {
   if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
        $script = $scripts->registered['jquery'];
   if ( $script->deps ) { 
// Check whether the script has any dependencies
        $script->deps = array_diff( $script->deps, array( 'jquery-migrate' ) );
 }
 }
 }
add_action( 'wp_default_scripts', 'remove_jquery_migrate' );


/*  Elementor Edits
__________________________________________*/

// REMOVE GOOGLE FRONTS - ELEMENTOR
add_filter( 'elementor/frontend/print_google_fonts', '__return_false' );

// REMOVE ELEMENTOR GLOBAL STYLES
function dequeue_elementor_global__css() {
  wp_dequeue_style('elementor-global');
  wp_deregister_style('elementor-global');
}
add_action('wp_print_styles', 'dequeue_elementor_global__css', 9999);

add_action( 'init',function(){
   remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
   remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );
   remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
} );

// REMOVE GUTENBERG BLOCK LIBRARY CSS
function smartwp_remove_wp_block_library_css(){
   wp_dequeue_style( 'wp-block-library' );
   wp_dequeue_style( 'wp-block-library-theme' );
}
add_action( 'wp_enqueue_scripts', 'smartwp_remove_wp_block_library_css' );

add_filter('use_block_editor_for_post', '__return_false');

function eos_dequeue_gutenberg() {
   wp_dequeue_style( 'wp-core-blocks' );
   wp_dequeue_style( 'wp-block-library' );
   wp_deregister_style( 'wp-core-blocks' );
   wp_deregister_style( 'wp-block-library' );
}
add_action( 'wp_print_styles', 'eos_dequeue_gutenberg' );

/* Turns off Elementor Lazyload since we're using our own below */
add_filter( 'wp_lazy_loading_enabled', '__return_false' );

/*_____________________________________________________________________*/

// Remove Admin features from Dashboard. They are still available but hidden
add_action( 'admin_menu', 'remove_menus' );
function remove_menus(){
  
/*  	remove_menu_page( 'index.php' );                  //Dashboard 	*/
/* 	remove_menu_page( 'edit.php' );                   //Posts		*/
/*  	remove_menu_page( 'upload.php' );                 //Media 		*/
/*  	remove_menu_page( 'edit.php?post_type=page' );    //Pages 		*/
/* 	remove_menu_page( 'edit-comments.php' );          //Comments	*/
/*  	remove_menu_page( 'themes.php' );                 //Appearance 	*/
/* 	remove_menu_page( 'plugins.php' );                //Plugins		*/
/* 	remove_menu_page( 'users.php' );                  //Users		*/
/*		remove_menu_page( 'tools.php' );                  //Tools		*/
/* 	remove_menu_page( 'options-general.php' );        //Settings 	*/
  
}

/*_____________________________________________________________________*/



/*  ASYNC FUNCTION FOR SCRIPTS - ENQUEUED BELOW
________________________________________________________________________*/

function site_async_scripts($url)
{
    if ( strpos( $url, '#asyncload') === false )
        return $url;
    else if ( is_admin() )
        return str_replace( '#asyncload', '', $url );
    else
	return str_replace( '#asyncload', '', $url )."' async='async"; 
    }
add_filter( 'clean_url', 'site_async_scripts', 11, 1 );

// add "#asyncload" to the end of the js file name. I.E. nameoffile-morename.js#asyncload


/*  LOAD THEME STYLES AND SCRIPTS
________________________________________________________________________*/

function add_theme_enqueues() {
	wp_enqueue_style( 'style', get_stylesheet_uri() );
	wp_deregister_script('jquery');
	wp_enqueue_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js', array(), '3.6.1', false);
	wp_enqueue_script( 'viewportHeight', get_template_directory_uri() . '/js/viewportHeight.js#asyncload', array ( 'jquery' ), 1, true);
   wp_enqueue_script( 'lazyload', get_template_directory_uri() . '/js/lazy-load.js#asyncload', array ( 'jquery' ), 1, true);
   wp_enqueue_script( 'lazyload-min', get_template_directory_uri() . '/js/lazyload.min.js#asyncload', array ( 'jquery' ), 1, true);
}
add_action( 'wp_enqueue_scripts', 'add_theme_enqueues' );


// DEFER RECAPTCHA
add_filter( 'clean_url', function( $url )
{
    if ( FALSE === strpos( $url, 'www.google.com/recaptcha/api.js' ) )
    { // not our file
        return $url;
    }
    // Must be a ', not "!
    return "$url' defer='defer";
}, 11, 1 );


/*  SVG IMAGES
________________________________________________________________________*/
	
/*  Allows the use of SVGs
	to be uploaded to the Media Library
__________________________________________*/

define( 'ALLOW_UNFILTERED_UPLOADS', true );

function cc_mime_types($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');


/*  LOADS ELEMENTOR TO TEMPLATE PAGES
________________________________________________________________________*/

function theme_prefix_register_elementor_locations( $elementor_theme_manager ) {
	$elementor_theme_manager->register_all_core_location();

}
add_action( 'elementor/theme/register_locations', 'theme_prefix_register_elementor_locations' );



/*  NAVIGATION
________________________________________________________________________*/

function eg_register_menus() {
	register_nav_menus(
  		array(
			'header_nav_menu' => __( 'Header Menu' ),
			'footer_nav_menu' => __( 'Footer Menu' ),
         'footer_alt_menu' => __( 'Alternate Menu' ),
    	)
	);
}
add_action( 'init', 'eg_register_menus' );


function cleanname($v) {
$v = preg_replace('/[^a-zA-Z0-9s]/', '', $v);
$v = str_replace(' ', '-', $v);
$v = strtolower($v);
return $v;
}



/*  PLUGIN EDITS
________________________________________________________________________*/


/*  ACF
__________________________________________*/

// Removes ACF (custom fields) from the Admin sidebar
//function remove_acf(){
//	remove_menu_page( 'edit.php?post_type=acf' ); 
//}
//add_action( 'admin_menu', 'remove_acf', 100 );


/*  Yoast
__________________________________________*/

// Disable Yoast SEO Primary Category Feature
add_filter( 'wpseo_primary_term_taxonomies', '__return_false' );

// Moves Yoast below Content Editor
function yoasttobottom() {
  return 'low';
}
add_filter( 'wpseo_metabox_prio', 'yoasttobottom');


/*  GRAVITY FORMS
__________________________________________*/

// keeps the viewer at the form to read the confirmation message
// instead of having to scroll to message
add_filter( 'gform_confirmation_anchor', '__return_true' );

// Hides top labels if Placeholders are added - dropdown option
add_filter( 'gform_enable_field_label_visibility_settings', '__return_true' );


/*  LAZY LOAD
________________________________________________________________________*/

//initialize lazy loading
function wts_lazy_load_init() {

   //actions + filters
   add_filter('template_redirect', 'wts_lazy_load', -99999);
   add_action('wp_enqueue_scripts', 'wts_enqueue_lazy_load');
   add_action('wp_footer', 'wts_print_lazy_load_js', PHP_INT_MAX);
   add_action('wp_head', 'wts_print_lazy_load_css', PHP_INT_MAX);
   add_filter('wp_lazy_loading_enabled', '__return_false');
   add_filter('wp_get_attachment_image_attributes', function($attr) {
      unset($attr['loading']);
        return $attr;
   });

}
add_action('wp', 'wts_lazy_load_init');

//initialize lazy loading
function wts_lazy_load() {
	ob_start('wts_lazy_load_buffer');
}

//lazy load buffer
function wts_lazy_load_buffer($html) {

   $buffer = wts_lazy_load_clean_html($html);

   $html = wts_lazy_load_pictures($html, $buffer);
   $html = wts_lazy_load_background_images($html, $buffer);
   $html = wts_lazy_load_images($html, $buffer);
   $html = wts_lazy_load_iframes($html, $buffer);
	$html = wts_lazy_load_videos($html, $buffer);
   $html = wts_lazy_load_css_background_images($html, $buffer);

   return $html;
}

//remove unecessary bits from html for buffer searh
function wts_lazy_load_clean_html($html) {

	//remove existing script tags
	$html = preg_replace('/<script\b(?:[^>]*)>(?:.+)?<\/script>/Umsi', '', $html);
	//remove existing noscript tags
   $html = preg_replace('#<noscript>(?:.+)</noscript>#Umsi', '', $html);

   return $html;
}

//lazy load img tags
function wts_lazy_load_images($html, $buffer) {

	//match all img tags
	preg_match_all('#<img([^>]+?)\/?>#is', $buffer, $images, PREG_SET_ORDER);

	if(!empty($images)) {

		$options = get_option('wts_options');

		$lazy_image_count = 0;
		$exclude_leading_images = $options['lazyload']['exclude_leading_images'] ?? 0;

		//remove any duplicate images
		$images = array_unique($images, SORT_REGULAR);

		//loop through images
        foreach($images as $image) {

        	$lazy_image_count++;

        	if($lazy_image_count <= $exclude_leading_images) {
        		continue;
        	}

        	//prepare lazy load image
            $lazy_image = wts_lazy_load_image($image);

            //replace image in html
            $html = str_replace($image[0], $lazy_image, $html);
        }
	}
		
	return $html;
}

//lazy load picture tags for webp
function wts_lazy_load_pictures($html, $buffer) {

	//match all picture tags
	preg_match_all('#<picture(.*)?>(.*)<\/picture>#isU', $buffer, $pictures, PREG_SET_ORDER);

	if(!empty($pictures)) {

		foreach($pictures as $picture) {

			//get picture tag attributes
			$picture_atts = wts_lazyload_get_atts_array($picture[1]);

			//dont check excluded if forced attribute was found
			if(!wts_lazyload_excluded($picture[1], wts_lazyload_forced_atts())) {

				//skip if no-lazy class is found
				if((!empty($picture_atts['class']) && strpos($picture_atts['class'], 'no-lazy') !== false) || wts_lazyload_excluded($picture[0], wts_lazyload_excluded_atts())) {

					//mark image for exclusion later
					preg_match('#<img([^>]+?)\/?>#is', $picture[0], $image);
					if(!empty($image)) {
						$image_atts = wts_lazyload_get_atts_array($image[1]);
						$image_atts['class'] = (!empty($image_atts['class']) ? $image_atts['class'] . ' ' : '') . 'no-lazy';
						$image_atts_string = wts_lazyload_get_atts_string($image_atts);
						$new_image = sprintf('<img %1$s />', $image_atts_string);                
		            $html = str_replace($image[0], $new_image, $html);
					}
					continue;
				}
			}

			//match all source tags inside the picture
			preg_match_all('#<source(\s.+)>#isU', $picture[2], $sources, PREG_SET_ORDER);

			if(!empty($sources)) {

				//remove any duplicate sources
				$sources = array_unique($sources, SORT_REGULAR);

	            foreach($sources as $source) {

	            	//skip if exluded attribute was found
					if(wts_lazyload_excluded($source[1], wts_lazyload_excluded_atts())) {
						continue;
					}

					//migrate srcet
	                $new_source = preg_replace('/([\s"\'])srcset/i', '${1}data-srcset', $source[0]);

	                //migrate sizes
	                $new_source = preg_replace('/([\s"\'])sizes/i', '${1}data-sizes', $new_source);

	                //replace source in html	                
	                $html = str_replace($source[0], $new_source, $html);
	            }
			}
			else {
				continue;
			}
		}
	}

	return $html;
}

//lazy load background images
function wts_lazy_load_background_images($html, $buffer) {

	//match all elements with inline styles
	preg_match_all('#<(?<tag>div|figure|section|span|li|a)(\s+[^>]*[\'"\s]?style\s*=\s*[\'"].*?[\'"][^>]*)>#is', $buffer, $elements, PREG_SET_ORDER);

	if(!empty($elements)) {

		foreach($elements as $element) {

			//get element tag attributes
			$element_atts = wts_lazyload_get_atts_array($element[2]);

			//dont check excluded if forced attribute was found
			if(!wts_lazyload_excluded($element[2], wts_lazyload_forced_atts())) {

				//skip if no-lazy class is found
				if(!empty($element_atts['class']) && strpos($element_atts['class'], 'no-lazy') !== false) {
					continue;
				}

				//skip if exluded attribute was found
				if(wts_lazyload_excluded($element[2], wts_lazyload_excluded_atts())) {
					continue;
				}
			}

			//skip if no style attribute
			if(!isset($element_atts['style'])) {
				continue;
			}

			//match background-image in style string
			preg_match('#background(-image)?\s*:\s*(\s*url\s*\((?<url>[^)]+)\))\s*;?#is', $element_atts['style'], $url);

			if(!empty($url)) {

				$url['url'] = trim($url['url'], '\'" ');

				//add lazyload class
				$element_atts['class'] = !empty($element_atts['class']) ? $element_atts['class'] . ' ' . 'wts-lazy' : 'wts-lazy';

				//remove background image url from inline style attribute
				$element_atts['style'] = str_replace($url[0], '', $element_atts['style']);

				//migrate src
				$element_atts['data-bg'] = 'url(' . esc_attr($url['url']) . ')';

				//build lazy element attributes string
				$lazy_element_atts_string = wts_lazyload_get_atts_string($element_atts);

				//build lazy element
				$lazy_element = sprintf('<' . $element['tag'] . ' %1$s >', $lazy_element_atts_string);

				//replace element with placeholder
				$html = str_replace($element[0], $lazy_element, $html);

				unset($lazy_element);
			}
			else {
				continue;
			}
		}
	}

	return $html;
}

//prep img tag for lazy loading
function wts_lazy_load_image($image) {

	//if there are no attributes, return original match
	if(empty($image[1])) {
		return $image[0];
	}

	//get image attributes array
	$image_atts = wts_lazyload_get_atts_array($image[1]);

	//get new attributes
	if(empty($image_atts['src']) || (!wts_lazyload_excluded($image[1], wts_lazyload_forced_atts()) && ((!empty($image_atts['class']) && strpos($image_atts['class'], 'no-lazy') !== false) || wts_lazyload_excluded($image[1], wts_lazyload_excluded_atts())))) {
		return $image[0];
	}
	else {

		//add lazyload class
		$image_atts['class'] = !empty($image_atts['class']) ? $image_atts['class'] . ' ' . 'wts-lazy' : 'wts-lazy';

		//migrate src
		$image_atts['data-src'] = $image_atts['src'];

		//add placeholder src
		$width = !empty($image_atts['width']) ? $image_atts['width'] : 0;
		$height = !empty($image_atts['height']) ? $image_atts['height'] : 0;
		$image_atts['src'] = "data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='" . $width . "'%20height='" . $height . "'%20viewBox='0%200%20" . $width . "%20" . $height . "'%3E%3C/svg%3E";

		//migrate srcset
		if(!empty($image_atts['srcset'])) {
			$image_atts['data-srcset'] = $image_atts['srcset'];
			unset($image_atts['srcset']);
		}
		
		//migrate sizes
		if(!empty($image_atts['sizes'])) {
			$image_atts['data-sizes'] = $image_atts['sizes'];
			unset($image_atts['sizes']);
		}

		//unset existing loading attribute
		if(isset($image_atts['loading'])) {
			unset($image_atts['loading']);
		}
	}

	//build lazy image attributes string
	$lazy_image_atts_string = wts_lazyload_get_atts_string($image_atts);

	//replace attributes
	$output = sprintf('<img %1$s />', $lazy_image_atts_string);

	//original noscript image
	$output.= "<noscript>" . $image[0] . "</noscript>";

	return $output;
}

//lazy load iframes
function wts_lazy_load_iframes($html, $buffer) {

	//match all iframes
	preg_match_all('#<iframe(\s.+)>.*</iframe>#iUs', $buffer, $iframes, PREG_SET_ORDER);

	if(!empty($iframes)) {

		//get plugin options
		$wts_options = get_option('lazyload');

		//remove any duplicates
		$iframes = array_unique($iframes, SORT_REGULAR);

		foreach($iframes as $iframe) {

			//get iframe attributes array
			$iframe_atts = wts_lazyload_get_atts_array($iframe[1]);

			//dont check excluded if forced attribute was found
			if(!wts_lazyload_excluded($iframe[1], wts_lazyload_forced_atts())) {

				//skip if exluded attribute was found
				if(wts_lazyload_excluded($iframe[1], wts_lazyload_excluded_atts())) {
					continue;
				}

				//skip if no-lazy class is found
				if(!empty($iframe_atts['class']) && strpos($iframe_atts['class'], 'no-lazy') !== false) {
					continue;
				}
			}

			//skip if no src is found
			if(empty($iframe_atts['src'])) {
				continue;
			}

			$iframe['src'] = trim($iframe_atts['src']);

			//try rendering youtube preview placeholder if we need to
			if(!empty($wts_options['lazyload']['youtube_preview_thumbnails'])) {
				$iframe_lazyload = wts_lazy_load_youtube_iframe($iframe);
			}
					
			//default iframe placeholder
			if(empty($iframe_lazyload)) {

				$iframe_atts['class'] = !empty($iframe_atts['class']) ? $iframe_atts['class'] . ' ' . 'wts-lazy' : 'wts-lazy';

				//migrate src
				$iframe_atts['data-src'] = $iframe_atts['src'];
				unset($iframe_atts['src']);

				//build lazy iframe attributes string
				$lazy_iframe_atts_string = wts_lazyload_get_atts_string($iframe_atts);

				//replace iframe attributes string
				$iframe_lazyload = str_replace($iframe[1], ' ' . $lazy_iframe_atts_string, $iframe[0]);
				
				//add noscript original iframe
				$iframe_lazyload.= '<noscript>' . $iframe[0] . '</noscript>';
			}

			//replace iframe with placeholder
			$html = str_replace($iframe[0], $iframe_lazyload, $html);

			unset($iframe_lazyload);
		}
	}

	return $html;
}

//prep youtube iframe for lazy loading
function wts_lazy_load_youtube_iframe($iframe) {

	if(!$iframe) {
		return false;
	}

	//attempt to get the id based on url
	$result = preg_match('#^(?:https?:)?(?://)?(?:www\.)?(?:youtu\.be|youtube\.com|youtube-nocookie\.com)/(?:embed/|v/|watch/?\?v=)?([\w-]{11})#iU', $iframe['src'], $matches);

	//return false if there is no usable id
	if(!$result || $matches[1] === 'videoseries') {
		return false;
	}

	$youtube_id = $matches[1];

	//parse iframe src url
	$query = wp_parse_url(htmlspecialchars_decode($iframe['src']), PHP_URL_QUERY);

	//clean up the url
	$parsed_url = wp_parse_url($iframe['src'], -1);
	$scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '//';
	$host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
	$path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
	$youtube_url = $scheme . $host . $path;

	//thumbnail resolutions
	$resolutions = array(
		'default'   => array(
			'width'  => 120,
			'height' => 90,
		),
		'mqdefault' => array(
			'width'  => 320,
			'height' => 180,
		),
		'hqdefault' => array(
			'width'  => 480,
			'height' => 360,
		),
		'sddefault' => array(
			'width'  => 640,
			'height' => 480,
		),
		'maxresdefault' => array(
			'width'  => 1280,
			'height' => 720,
		)
	);

	//filter set resolution
	$resolution = apply_filters('wts_lazyload_youtube_thumbnail_resolution', 'hqdefault');

	//finished youtube lazy output
	$youtube_lazyload = '<div class="wts-lazy-youtube" data-src="' . esc_attr($youtube_url) . '" data-id="' . esc_attr($youtube_id) . '" data-query="' . esc_attr($query) . '" onclick="wtsLazyLoadYouTube(this);">';
		$youtube_lazyload.= '<div>';
			$youtube_lazyload.= '<img class="wts-lazy" src="data:image/svg+xml,%3Csvg%20xmlns=\'http://www.w3.org/2000/svg\'%20viewBox=\'0%200%20' . $resolutions[$resolution]['width'] . '%20' . $resolutions[$resolution]['height'] . '%3E%3C/svg%3E" data-src="https://i.ytimg.com/vi/' . esc_attr($youtube_id) .'/' . $resolution . '.jpg" alt="YouTube ' . __('video', 'wts') . '" width="' . $resolutions[$resolution]['width'] . '" height="' . $resolutions[$resolution]['height'] . '" data-pin-nopin="true">';
			$youtube_lazyload.= '<div class="play"></div>';
		$youtube_lazyload.= '</div>';
	$youtube_lazyload.= '</div>';
	$youtube_lazyload.= '<noscript>' . $iframe[0] . '</noscript>';

	return $youtube_lazyload;
}

//lazy load videos
function wts_lazy_load_videos($html, $buffer) {

	//match all videos
	preg_match_all('#<video(\s.+)>.*</video>#iUs', $buffer, $videos, PREG_SET_ORDER);

	if(!empty($videos)) {

		//get plugin options
		$wts_options = get_option('lazyload');

		//remove any duplicates
		$videos = array_unique($videos, SORT_REGULAR);

		foreach($videos as $video) {

			//get video attributes array
			$video_atts = wts_lazyload_get_atts_array($video[1]);

			//dont check excluded if forced attribute was found
			if(!wts_lazyload_excluded($video[1], wts_lazyload_forced_atts())) {

				//skip if exluded attribute was found
				if(wts_lazyload_excluded($video[1], wts_lazyload_excluded_atts())) {
					continue;
				}

				//skip if no-lazy class is found
				if(!empty($video_atts['class']) && strpos($video_atts['class'], 'no-lazy') !== false) {
					continue;
				}
			}

			//skip if no src is found
			if(empty($video_atts['src'])) {
				continue;
			}

			//add lazyload class
			$video_atts['class'] = !empty($video_atts['class']) ? $video_atts['class'] . ' ' . 'wts-lazy' : 'wts-lazy';

			//migrate src
			$video_atts['data-src'] = $video_atts['src'];
			unset($video_atts['src']);

			//build lazy video attributes string
			$lazy_video_atts_string = wts_lazyload_get_atts_string($video_atts);

			//replace video attributes string
			$video_lazyload  = str_replace($video[1], ' ' . $lazy_video_atts_string, $video[0]);

			//add noscript original video
			$video_lazyload .= '<noscript>' . $video[0] . '</noscript>';

			//replace video with placeholder
			$html = str_replace($video[0], $video_lazyload, $html);

			unset($video_lazyload);
		}
	}

	return $html;
}

//lazy load css background images
function wts_lazy_load_css_background_images($html, $buffer) {

	//get plugin options
	$wts_options = get_option('lazyload');

	if(!empty($wts_options['lazyload']['css_background_selectors'])) {

		//match all selectors
		preg_match_all('#<(?>div|section)(\s[^>]*?(' . implode('|', $wts_options['lazyload']['css_background_selectors']) . ').*?)>#i', $buffer, $selectors, PREG_SET_ORDER);

		if(!empty($selectors)) {

			foreach($selectors as $selector) {

				$selector_atts = wts_lazyload_get_atts_array($selector[1]);

				$selector_atts['class'] = !empty($selector_atts['class']) ? $selector_atts['class'] . ' ' . 'wts-lazy-css-bg' : 'wts-lazy-css-bg';

				$selector_atts_string = wts_lazyload_get_atts_string($selector_atts);

				//replace video attributes string
				$selector_lazyload  = str_replace($selector[1], ' ' . $selector_atts_string, $selector[0]);

				//replace video with placeholder
				$html = str_replace($selector[0], $selector_lazyload, $html);

				unset($selector_lazyload);
			}
		}
	}
	return $html;
}

function wts_lazyload_get_atts_array($atts_string) {
	
	if(!empty($atts_string)) {
		$atts_array = array_map(
			function(array $attribute) {
				return $attribute['value'];
			},
			wp_kses_hair($atts_string, wp_allowed_protocols())
		);

		return $atts_array;
	}

	return false;
}

function wts_lazyload_get_atts_string($atts_array) {

	if(!empty($atts_array)) {
		$assigned_atts_array = array_map(
		function($name, $value) {
				if($value === '') {
					return $name;
				}
				return sprintf('%s="%s"', $name, esc_attr($value));
			},
			array_keys($atts_array),
			$atts_array
		);
		$atts_string = implode(' ', $assigned_atts_array);

		return $atts_string;
	}

	return false;
}

//get forced attributes
function wts_lazyload_forced_atts() {
	return apply_filters('wts_lazyload_forced_attributes', array());
}

//get excluded attributes
function wts_lazyload_excluded_atts() {

	//base exclusions
	$attributes = array(
		'data-wts-preload',
		'gform_ajax_frame'
	); 

	//get exclusions added from settings
	$options = get_option('lazyload');
	if(!empty($options['lazyload']['lazy_loading_exclusions']) && is_array($options['lazyload']['lazy_loading_exclusions'])) {
		$attributes = array_unique(array_merge($attributes, $options['lazyload']['lazy_loading_exclusions']));
	}

    return apply_filters('wts_lazyload_excluded_attributes', $attributes);
}

//check for excluded attributes in attributes string
function wts_lazyload_excluded($string, $excluded) {
    if(!is_array($excluded)) {
        (array) $excluded;
    }

    if(empty($excluded)) {
        return false;
    }

    foreach($excluded as $exclude) {
        if(strpos($string, $exclude) !== false) {
            return true;
        }
    }

    return false;
}

//initialize lazy load instance
function wts_print_lazy_load_js() {
	global $wts_options;

	$threshold = apply_filters('wts_lazyload_threshold', (!empty($wts_options['lazyload']['threshold']) ? $wts_options['lazyload']['threshold'] : '0px'));
	if(ctype_digit($threshold)) {
		$threshold.= 'px';
	}

	$output = '<script>';

		$output.= 'document.addEventListener("DOMContentLoaded",function(){';

			//initialize lazy loader
			$output.= 'var lazyLoadInstance=new LazyLoad({elements_selector:"img[data-src],.wts-lazy,.wts-lazy-css-bg",thresholds:"' . $threshold . ' 0px",callback_loaded:function(element){if(element.tagName==="IFRAME"){if(element.classList.contains("loaded")){if(typeof window.jQuery!="undefined"){if(jQuery.fn.fitVids){jQuery(element).parent().fitVids()}}}}}});';

			//dom monitoring
			if(!empty($wts_options['lazyload']['lazy_loading_dom_monitoring'])) { 
				$output.= 'var target=document.querySelector("body");var observer=new MutationObserver(function(mutations){lazyLoadInstance.update()});var config={childList:!0,subtree:!0};observer.observe(target,config);';
			}

		$output.= '});';

		//youtube thumbnails
		if(!empty($wts_options['lazyload']['lazy_loading_iframes']) && !empty($wts_options['lazyload']['youtube_preview_thumbnails'])) {
			$output.= 'function wtsLazyLoadYouTube(e){var t=document.createElement("iframe"),r="ID?";r+=0===e.dataset.query.length?"":e.dataset.query+"&",r+="autoplay=1",t.setAttribute("src",r.replace("ID",e.dataset.src)),t.setAttribute("frameborder","0"),t.setAttribute("allowfullscreen","1"),t.setAttribute("allow","accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"),e.replaceChild(t,e.firstChild)}';
		}
	$output.= '</script>';

	echo $output;
}

//print lazy load styles
function wts_print_lazy_load_css() {

	$options = get_option('lazyload');

	//print noscript styles
	echo '<noscript><style>.wts-lazy[data-src]{display:none !important;}</style></noscript>';

	$styles = '';

	//youtube thumbnails
	if(!empty($options['lazyload']['lazy_loading_iframes']) && !empty($options['lazyload']['youtube_preview_thumbnails'])) {
		$styles.= '.wts-lazy-youtube{position:relative;width:100%;max-width:100%;height:0;padding-bottom:56.23%;overflow:hidden}.wts-lazy-youtube img{position:absolute;top:0;right:0;bottom:0;left:0;display:block;width:100%;max-width:100%;height:auto;margin:auto;border:none;cursor:pointer;transition:.5s all;-webkit-transition:.5s all;-moz-transition:.5s all}.wts-lazy-youtube img:hover{-webkit-filter:brightness(75%)}.wts-lazy-youtube .play{position:absolute;top:50%;left:50%;right:auto;width:68px;height:48px;margin-left:-34px;margin-top:-24px;background:url('.plugins_url('wts/img/youtube.svg').') no-repeat;background-position:center;background-size:cover;pointer-events:none}.wts-lazy-youtube iframe{position:absolute;top:0;left:0;width:100%;height:100%;z-index:99}';
		if(current_theme_supports('responsive-embeds')) {
			$styles.= '.wp-has-aspect-ratio .wp-block-embed__wrapper{position:relative;}.wp-has-aspect-ratio .wts-lazy-youtube{position:absolute;top:0;right:0;bottom:0;left:0;width:100%;height:100%;padding-bottom:0}';
		}
	}

	//fade in effect
	if(!empty($options['lazyload']['fade_in'])) {
		$styles.= '.wts-lazy:not(picture),.wts-lazy>img{opacity:0}.wts-lazy.loaded,.wts-lazy>img.loaded,.wts-lazy[data-was-processed=true],.wts-lazy.loaded>img{opacity:1;transition:opacity ' . apply_filters('wts_fade_in_speed', 500) . 'ms}';
	}

	//css background images
	if(!empty($options['lazyload']['css_background_images'])) {
		$styles.='body .wts-lazy-css-bg:not([data-was-processed="true"]),body .wts-lazy-css-bg:not([data-was-processed="true"]) *,body .wts-lazy-css-bg:not([data-was-processed="true"])::before{background-image:none!important;will-change:transform;transition:opacity 0.025s ease-in,transform 0.025s ease-in!important;}';
	}
	
	//print styles
	if(!empty($styles)) {
		echo '<style>' . $styles . '</style>';
	}
}


/* THIS IS THE END                                                       */
/* --------------------------------------------------------------------- */
/* --------------------------------------------------------------------- */
/* --------------------------------------------------------------------- */