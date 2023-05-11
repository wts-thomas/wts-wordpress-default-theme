<?php

// This is what checks the Github Repository for the latest version 
// and gives the update notice to the Theme installed in Wordpress.
require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/wts-thomas/wts-wordpress-rets/',
	__FILE__,
	'wts-elementor'
);

//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('main');

// Custom Admin Styles
function my_admin_head() {
   echo '<link href="'.get_stylesheet_directory_uri().'/wp-admin.css" rel="stylesheet" type="text/css">';
}
add_action('admin_head', 'my_admin_head');

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

// STOPS DEFAULT LAZY LOAD
add_filter( 'wp_lazy_loading_enabled', '__return_false' );

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

// EXPLICIT FIX TO WIDTH AND HEIGHT
add_filter( 'the_content', 'add_image_dimensions' );

function add_image_dimensions( $content ) {

    preg_match_all( '/<img[^>]+>/i', $content, $images);

    if (count($images) < 1)
        return $content;

    foreach ($images[0] as $image) {
        preg_match_all( '/(alt|title|src|width|class|id|height)=("[^"]*")/i', $image, $img );

        if ( !in_array( 'src', $img[1] ) )
            continue;

        if ( !in_array( 'width', $img[1] ) || !in_array( 'height', $img[1] ) ) {
            $src = $img[2][ array_search('src', $img[1]) ];
            $alt = in_array( 'alt', $img[1] ) ? ' alt=' . $img[2][ array_search('alt', $img[1]) ] : '';
            $title = in_array( 'title', $img[1] ) ? ' title=' . $img[2][ array_search('title', $img[1]) ] : '';
            $class = in_array( 'class', $img[1] ) ? ' class=' . $img[2][ array_search('class', $img[1]) ] : '';
            $id = in_array( 'id', $img[1] ) ? ' id=' . $img[2][ array_search('id', $img[1]) ] : '';
            list( $width, $height, $type, $attr ) = getimagesize( str_replace( "\"", "" , $src ) );

            $image_tag = sprintf( '<img src=%s%s%s%s%s width="%d" height="%d" />', $src, $alt, $title, $class, $id, $width, $height );
            $content = str_replace($image, $image_tag, $content);
        }
    }

    return $content;
}


/*  Elementor Edits
________________________________________________________________________*/

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

// ENSURES CUSTOM FONTS ARE LOADED
add_filter( 'elementor_pro/custom_fonts/font_display', function( $current_value, $font_family, $data ) {
	return 'swap';
}, 10, 3 );

// THEME SUPPORT FOR FEATURED IMAGES
add_theme_support( 'post-thumbnails' );

// OVERRIDE DARK MODE EDITOR STYLES - SINCE 3.12.0
function override_elementor_styles_css(){ 
   wp_register_style('override-editor-styles', get_template_directory_uri().'/styles/editor-darkmode-overrides.css');
   wp_enqueue_style('override-editor-styles');
} 
add_action( 'elementor/editor/after_enqueue_scripts', 'override_elementor_styles_css', 9999999 );


/*  ADMIN DASHBOARD LINKS
________________________________________________________________________*/

// Remove Admin features from Dashboard excluding WTS users
// for the default installation of plugins
function wts_remove_menus(){ 
   $current_user = wp_get_current_user(); 
   if( !in_array( $current_user->user_email, array('thomas@wtsks.com','tanner@wtsks.com',) ) ){ 
      /*  	remove_menu_page( 'index.php' );                        //Dashboard 	*/
      /* 	remove_menu_page( 'edit.php' );                         //Posts		*/
      /*  	remove_menu_page( 'upload.php' );                       //Media 		*/
      /*  	remove_menu_page( 'edit.php?post_type=page' );          //Pages 		*/
      /* 	remove_menu_page( 'edit-comments.php' );                //Comments	*/
      remove_menu_page( 'themes.php' );                             //Appearance */
      remove_menu_page( 'plugins.php' );                            //Plugins		*/
      /* 	remove_menu_page( 'users.php' );                        //Users		*/
      remove_menu_page( 'tools.php' );                              //Tools		*/
      remove_menu_page( 'options-general.php' );                    //Settings 	*/
      remove_menu_page( 'edit.php?post_type=acf-field-group' );     //ACF 	      */
      remove_menu_page( 'cptui_main_menu' );                        //CPT UI     */
   } 
} 
add_action( 'admin_menu', 'wts_remove_menus', 9999 );


/*  REMOVE DASHBOARD META BOXES
_____________________________________________________________________*/

function remove_dashboard_widgets() {
   remove_action( 'welcome_panel', 'wp_welcome_panel' );
   remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
   remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
   remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
   remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
   remove_meta_box( 'dashboard_site_health', 'dashboard', 'normal' );
   remove_meta_box( 'e-dashboard-overview', 'dashboard', 'normal' );
}
add_action( 'wp_dashboard_setup', 'remove_dashboard_widgets' );


/*  DASHBOARD META BOXES - DEFAULT SCREEN OPTIONS
________________________________________________________________________*/

// Hides the other screen option meta boxes.
// Boxes can be activated at any time by the user via Screen Options dropdown.

add_filter( 'hidden_meta_boxes', 'custom_hidden_meta_boxes' );
function custom_hidden_meta_boxes( $hidden ) {
//  $hidden[] = 'dashboard_primary';
    $hidden[] = 'rg_forms_dashboard';
    return $hidden;
}


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
	wp_enqueue_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js', array(), '3.6.3', false);
	wp_enqueue_script( 'viewportHeight', get_template_directory_uri() . '/js/viewportHeight.js#asyncload', array ( 'jquery' ), 1, true);
   wp_enqueue_script( 'responsiveTables', get_template_directory_uri() . '/js/responsiveTables.js#asyncload', array ( 'jquery' ), 1, true);
   wp_enqueue_script( 'jquery.matchHeight', get_template_directory_uri() . '/js/jquery.matchHeight.js#asyncload', array ( 'jquery' ), 1, false);
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

// NOTE: SVG width and height functions are not required since we're using Elementor and its' SVG upload to media library functions.


/*  LOADS ELEMENTOR TO TEMPLATE PAGES
________________________________________________________________________*/

function theme_prefix_register_elementor_locations( $elementor_theme_manager ) {
	$elementor_theme_manager->register_all_core_location();

}
add_action( 'elementor/theme/register_locations', 'theme_prefix_register_elementor_locations' );


/*  SUPPORT CONATACT CARD
________________________________________________________________________*/

function custom_dashboard_help() {
   echo '
   <div style="text-align:center;">
       <a href="https://wtsks.com/help/" title="Contact WTS" target="_blank">
           <img src="'.get_template_directory_uri().'/img/wts-logo_whiteback.png" alt="WTS" style="max-width:100%;width:80%;height:auto;margin:20px auto;">
       </a>
   </div>
   <p>
      Contact <a href="https://wtsks.com/help/" title="Contact WTS" target="_blank">Contact WTS</a> with questions, troubleshooting, edit for requests or alterations, or misc support you have with your custom built website.
   </p>
   <p><strong><a href="https://wtsks.com/help/" title="Contact WTS" target="_blank">Contact WTS</a></strong></p>
   ';
}
function wts_custom_dashboard_widgets() {
   global $wp_meta_boxes;
   wp_add_dashboard_widget('custom_help_widget', 'Website Support', 'custom_dashboard_help');
}
add_action('wp_dashboard_setup', 'wts_custom_dashboard_widgets');


/*  NAVIGATION
________________________________________________________________________*/

function eg_register_menus() {
	register_nav_menus(
  		array(
			'header_nav_menu' => __( 'Header Menu' ),
			'footer_nav_menu' => __( 'Footer Menu' ),
         'footer_alt_menu' => __( 'Alternate Menu' ),
         'content_altTwo_menu' => __( 'Alternate Menu - 2' ),
         'content_altThr_menu' => __( 'Alternate Menu - 3' ),
         'content_altFou_menu' => __( 'Alternate Menu - 4' ),
         'content_altFiv_menu' => __( 'Alternate Menu - 5' ),
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


/*  WIDGETS
________________________________________________________________________*/

function wtstheme_sidebar() {
	register_sidebar(
		array (
			'name' => __( 'Default Sidebar', 'wts-elementor-default' ),
			'id' => 'custom_sidebar_01',
			'description' => __( 'Custom sidebar that can be used with Elementor templates.', 'wts-elementor-default' ),
			'before_widget' => '<div class="widget-content">',
			'after_widget' => "</div>",
		)
	);
}
add_action( 'widgets_init', 'wtstheme_sidebar' );

// Does not show Widget titles on page
add_filter('widget_title','my_widget_title'); 
function my_widget_title($t)
{
   return null;
}

/*  PLUGIN EDITS
________________________________________________________________________*/

/*  Yoast
__________________________________________*/

// Disable Yoast SEO Primary Category Feature
add_filter( 'wpseo_primary_term_taxonomies', '__return_false' );

// Moves Yoast below Content Editor
function yoasttobottom() {
  return 'low';
}
add_filter( 'wpseo_metabox_prio', 'yoasttobottom');

/*  Tablepress
__________________________________________*/

// Removes the Tablepress Admin links on site
add_filter( 'tablepress_edit_link_below_table', '__return_false' );

/*  GRAVITY FORMS
__________________________________________*/

// keeps the viewer at the form to read the confirmation message
// instead of having to scroll to message
add_filter( 'gform_confirmation_anchor', '__return_true' );

// Hides top labels if Placeholders are added - dropdown option
add_filter( 'gform_enable_field_label_visibility_settings', '__return_true' );

/*  ACF FIELD - FUNCTIONS
________________________________________________________________________*/

// Adds a comma to number field(s)
// Field names should be consistent accross Real Estate websites
// Return ACF Number Fields Formatted with Commas on the Frontend
add_filter('acf/format_value/name=property_listing_price', 'acf_number_comma', 20, 3);
// add_filter('acf/format_value/name=ANOTHER_ACF_FIELD_NAME', 'acf_number_comma_decimal', 20, 3);

// Without Decimal
function acf_number_comma($value, $post_id, $field) {
  $value = number_format(floatval($value));
  return $value;
}
// With Decimal
function acf_number_comma_decimal($value, $post_id, $field) {
  $value = number_format(floatval($value), 2);
  return $value;
}

/*  BREADCRUMBS
________________________________________________________________________*/

function page_breadcrumbs() {
       
   // Settings
   $separator          = '|';
   $breadcrums_id      = 'breadcrumbs';
   $breadcrums_class   = 'breadcrumbs';
   $home_title         = 'Home';
     
   // Any custom post types with custom taxonomies, put the taxonomy name below (e.g. product_cat)
   // $custom_taxonomy    = 'name_posttype';
   // $custom_taxonomy	= 'another_posttypename';
      
   // Get the query & post information
   global $post,$wp_query;
      
   // Do not display on the homepage
   if ( !is_front_page() ) {
      
       // Build the breadcrums
       echo '<ul id="' . $breadcrums_id . '" class="' . $breadcrums_class . '">';
          
       // Home page
       echo '<li class="item-home"><a class="bread-link bread-home" href="' . get_home_url() . '" title="' . $home_title . '">' . $home_title . '</a></li>';
       echo '<li class="separator separator-home"> ' . $separator . ' </li>';
          
       if ( is_archive() && !is_tax() && !is_category() && !is_tag() ) {
             
           echo '<li class="item-current item-archive"><strong class="bread-current bread-archive">' . post_type_archive_title($prefix, false) . '</strong></li>';
             
       } else if ( is_archive() && is_tax() && !is_category() && !is_tag() ) {
             
           // If post is a custom post type
           $post_type = get_post_type();
             
           // If it is a custom post type display name and link
           if($post_type != 'post') {
                 
               $post_type_object = get_post_type_object($post_type);
               $post_type_archive = get_post_type_archive_link($post_type);
             
               echo '<li class="item-cat item-custom-post-type-' . $post_type . '"><a class="bread-cat bread-custom-post-type-' . $post_type . '" href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '">' . $post_type_object->labels->name . '</a></li>';
               echo '<li class="separator"> ' . $separator . ' </li>';
             
           }
             
           $custom_tax_name = get_queried_object()->name;
           echo '<li class="item-current item-archive"><strong class="bread-current bread-archive">' . $custom_tax_name . '</strong></li>';
             
       } else if ( is_single() ) {
             
           // If post is a custom post type
           $post_type = get_post_type();
             
           // If it is a custom post type display name and link
           if($post_type != 'post') {
                 
               $post_type_object = get_post_type_object($post_type);
               $post_type_archive = get_post_type_archive_link($post_type);
             
               echo '<li class="item-cat item-custom-post-type-' . $post_type . '"><a class="bread-cat bread-custom-post-type-' . $post_type . '" href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '">' . $post_type_object->labels->name . '</a></li>';
               echo '<li class="separator"> ' . $separator . ' </li>';
             
           }
             
           // Get post category info
           $category = get_the_category();
            
           if(!empty($category)) {
             
               // Get last category post is in
               $last_category = end(array_values($category));
                 
               // Get parent any categories and create array
               $get_cat_parents = rtrim(get_category_parents($last_category->term_id, true, ','),',');
               $cat_parents = explode(',',$get_cat_parents);
                 
               // Loop through parent categories and store in variable $cat_display
               $cat_display = '';
               foreach($cat_parents as $parents) {
                   $cat_display .= '<li class="item-cat">'.$parents.'</li>';
                   $cat_display .= '<li class="separator"> ' . $separator . ' </li>';
               }
            
           }
             
           // If it's a custom post type within a custom taxonomy
           $taxonomy_exists = taxonomy_exists($custom_taxonomy);
           if(empty($last_category) && !empty($custom_taxonomy) && $taxonomy_exists) {
                  
               $taxonomy_terms = get_the_terms( $post->ID, $custom_taxonomy );
               $cat_id         = $taxonomy_terms[0]->term_id;
               $cat_nicename   = $taxonomy_terms[0]->slug;
               $cat_link       = get_term_link($taxonomy_terms[0]->term_id, $custom_taxonomy);
               $cat_name       = $taxonomy_terms[0]->name;
              
           }
             
           // Check if the post is in a category
           if(!empty($last_category)) {
               echo $cat_display;
               echo '<li class="item-current item-' . $post->ID . '"><strong class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</strong></li>';
                 
           // Else if post is in a custom taxonomy
           } else if(!empty($cat_id)) {
                 
               echo '<li class="item-cat item-cat-' . $cat_id . ' item-cat-' . $cat_nicename . '"><a class="bread-cat bread-cat-' . $cat_id . ' bread-cat-' . $cat_nicename . '" href="' . $cat_link . '" title="' . $cat_name . '">' . $cat_name . '</a></li>';
               echo '<li class="separator"> ' . $separator . ' </li>';
               echo '<li class="item-current item-' . $post->ID . '"><strong class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</strong></li>';
             
           } else {
                 
               echo '<li class="item-current item-' . $post->ID . '"><strong class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</strong></li>';
                 
           }
             
       } else if ( is_category() ) {
              
           // Category page
           echo '<li class="item-current item-cat"><strong class="bread-current bread-cat">' . single_cat_title('', false) . '</strong></li>';
              
       } else if ( is_page() ) {
              
           // Standard page
           if( $post->post_parent ){
                  
               // If child page, get parents 
               $anc = get_post_ancestors( $post->ID );
                  
               // Get parents in the right order
               $anc = array_reverse($anc);
                  
               // Parent page loop
               if ( !isset( $parents ) ) $parents = null;
               foreach ( $anc as $ancestor ) {
                   $parents .= '<li class="item-parent item-parent-' . $ancestor . '"><a class="bread-parent bread-parent-' . $ancestor . '" href="' . get_permalink($ancestor) . '" title="' . get_the_title($ancestor) . '">' . get_the_title($ancestor) . '</a></li>';
                   $parents .= '<li class="separator separator-' . $ancestor . '"> ' . $separator . ' </li>';
               }
                  
               // Display parent pages
               echo $parents;
                  
               // Current page
               echo '<li class="item-current item-' . $post->ID . '"><strong title="' . get_the_title() . '"> ' . get_the_title() . '</strong></li>';
                  
           } else {       
               // Just display current page if not parents
               echo '<li class="item-current item-' . $post->ID . '"><strong class="bread-current bread-' . $post->ID . '"> ' . get_the_title() . '</strong></li>';
                  
           }
              
       } else if ( is_tag() ) {     
           // Tag page
              
           // Get tag information
           $term_id        = get_query_var('tag_id');
           $taxonomy       = 'post_tag';
           $args           = 'include=' . $term_id;
           $terms          = get_terms( $taxonomy, $args );
           $get_term_id    = $terms[0]->term_id;
           $get_term_slug  = $terms[0]->slug;
           $get_term_name  = $terms[0]->name;
              
           // Display the tag name
           echo '<li class="item-current item-tag-' . $get_term_id . ' item-tag-' . $get_term_slug . '"><strong class="bread-current bread-tag-' . $get_term_id . ' bread-tag-' . $get_term_slug . '">' . $get_term_name . '</strong></li>';
          
       } elseif ( is_day() ) {         
           // Day archive
              
           // Year link
           echo '<li class="item-year item-year-' . get_the_time('Y') . '"><a class="bread-year bread-year-' . get_the_time('Y') . '" href="' . get_year_link( get_the_time('Y') ) . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</a></li>';
           echo '<li class="separator separator-' . get_the_time('Y') . '"> ' . $separator . ' </li>';
              
           // Month link
           echo '<li class="item-month item-month-' . get_the_time('m') . '"><a class="bread-month bread-month-' . get_the_time('m') . '" href="' . get_month_link( get_the_time('Y'), get_the_time('m') ) . '" title="' . get_the_time('M') . '">' . get_the_time('M') . ' Archives</a></li>';
           echo '<li class="separator separator-' . get_the_time('m') . '"> ' . $separator . ' </li>';
              
           // Day display
           echo '<li class="item-current item-' . get_the_time('j') . '"><strong class="bread-current bread-' . get_the_time('j') . '"> ' . get_the_time('jS') . ' ' . get_the_time('M') . ' Archives</strong></li>';
              
       } else if ( is_month() ) {       
           // Month Archive
              
           // Year link
           echo '<li class="item-year item-year-' . get_the_time('Y') . '"><a class="bread-year bread-year-' . get_the_time('Y') . '" href="' . get_year_link( get_the_time('Y') ) . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</a></li>';
           echo '<li class="separator separator-' . get_the_time('Y') . '"> ' . $separator . ' </li>';
              
           // Month display
           echo '<li class="item-month item-month-' . get_the_time('m') . '"><strong class="bread-month bread-month-' . get_the_time('m') . '" title="' . get_the_time('M') . '">' . get_the_time('M') . ' Archives</strong></li>';
              
       } else if ( is_year() ) {     
           // Display year archive
           echo '<li class="item-current item-current-' . get_the_time('Y') . '"><strong class="bread-current bread-current-' . get_the_time('Y') . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</strong></li>';
              
       } else if ( is_author() ) {    
           // Auhor archive  
           // Get the author information
           global $author;
           $userdata = get_userdata( $author );  
           // Display author name
           echo '<li class="item-current item-current-' . $userdata->user_nicename . '"><strong class="bread-current bread-current-' . $userdata->user_nicename . '" title="' . $userdata->display_name . '">' . 'Author: ' . $userdata->display_name . '</strong></li>';
       
     } else if ( get_query_var('paged') ) {     
           // Paginated archives
           echo '<li class="item-current item-current-' . get_query_var('paged') . '"><strong class="bread-current bread-current-' . get_query_var('paged') . '" title="Page ' . get_query_var('paged') . '">'.__('Page') . ' ' . get_query_var('paged') . '</strong></li>';
       
     } else if ( is_search() ) { 
           // Search results page
           echo '<li class="item-current item-current-' . get_search_query() . '"><strong class="bread-current bread-current-' . get_search_query() . '" title="Search results for: ' . get_search_query() . '">Search results for: ' . get_search_query() . '</strong></li>';
       
     } elseif ( is_404() ) {      
           // 404 page
           echo '<li>' . 'Error 404' . '</li>';
       }
       echo '</ul>';       
   }     
}
add_shortcode('breadcrumbs', 'page_breadcrumbs');


/*  LAZY LOAD
________________________________________________________________________*/



/* THIS IS THE END                                                       */
/* --------------------------------------------------------------------- */
/* --------------------------------------------------------------------- */
/* --------------------------------------------------------------------- */