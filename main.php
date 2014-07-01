<?php
/**
Plugin Name: WPB woo product accordion
Plugin URI: http://demo.wpbean.com/wpb-woocommerce-accordain/
Description: Just install this plugin & put shortcode [wpb-accordain-latest posts="5"] . This plugin will add a nice and animated Woocommerce product accordain on your wordpress sidebar by shortcode. On accordain content it will show product details, image, price, add to cart button.; jQuery Plugin by: <a href="http://tympanus.net/">MARY LOU</a>.
Author: wpbean
Version: 1.0
Author URI: http://wpbean.com
*/


//--------------- Adding no js ------------//

function wpb_wpa_no_js(){
?>
<noscript>
	<style>
		.st-accordion ul li{ height:auto;}
		.st-accordion ul li > a span{ visibility:hidden;}
	</style>
</noscript>
<?php
}
add_action('wp_head','wpb_wpa_no_js');

//--------------- trigger accordain js ------------//
function trigger_wpb_wpa(){
?>
<script type="text/javascript">
jQuery.noConflict();
(function( $ ) {
  $(function() {

	$('#st-accordion').accordion({
		// index of opened item. -1 means all are closed by default.
    open            : -1,
    // if set to true, only one item can be opened. 
    // Once one item is opened, any other that is 
    // opened will be closed first
    oneOpenedItem   : true,
    // speed of the open / close item animation
    speed           : 600,
    // easing of the open / close item animation
    easing          : 'easeInOutExpo',
    // speed of the scroll to action animation
    scrollSpeed     : 900,
    // easing of the scroll to action animation
    scrollEasing    : 'easeInOutExpo'
	});

  });
  
$( 'a[href="#"]' ).click( function(e) {
      e.preventDefault();
   } );


})(jQuery);
</script>
<?php
}
add_action('wp_footer','trigger_wpb_wpa');

//--------------- Adding Latest jQuery------------//
function wpb_wpa_jquery() {
	wp_enqueue_script('jquery');
}
add_action('init', 'wpb_wpa_jquery');


//-------------- include js files---------------//
function wpb_wpa_adding_scripts() {
	wp_register_script('wpb_accordion', plugins_url('assets/js/jquery.accordion.js', __FILE__), array('jquery'),'1.1', true);
	wp_register_script('wpb_easing', plugins_url('assets/js/jquery.easing.1.3.js', __FILE__), array('jquery'),'1.3', true);

	wp_enqueue_script('wpb_accordion');
	wp_enqueue_script('wpb_easing');

}
add_action( 'wp_enqueue_scripts', 'wpb_wpa_adding_scripts' ); 


//------------ include css files-----------------//
function wpb_wpa_adding_style() {
	wp_register_style('wpb_wpa_main_style', plugins_url('assets/css/main.css', __FILE__),'','1.0', false);
	wp_enqueue_style('wpb_wpa_main_style');
}
add_action( 'init', 'wpb_wpa_adding_style' ); 



 /* ==========================================================================
    Latest Product Accordain
    ========================================================================== */
	
	
add_image_size( 'wpb-aw-thumb', 300 );


function wpb_aw_shortcode($atts){
   extract(shortcode_atts(array(
      'posts' => 6,
   ), $atts));


    $return_string = '<div class="aw_area"><div id="st-accordion" class="st-accordion">';
    $return_string .= '<ul>';
	
    $args = array(
				'post_type' => 'product',
		   'posts_per_page' => $posts,
					);
					
	$loop = new WP_Query( $args );
		if ( $loop->have_posts() ) {
			while ( $loop->have_posts() ) : $loop->the_post();
			
			
	global $product;
	
	
        $return_string .= '<li>';
		if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
		global $post, $product;
		
		$return_string .= '<a href="#a_p">';
		if (strlen($post->post_title) > 20) {
			$return_string .= substr(the_title($before = '', $after = '', FALSE), 0, 28) . '...';
		}else{
			$return_string .= get_the_title();
		}
		$return_string .= '<span class="st-arrow">Open or Close</span></a>';
		$return_string .= '<div class="st-content">';
        
        $return_string .= get_the_excerpt();
        
        if (has_post_thumbnail( $loop->post->ID )){
			$return_string .= get_the_post_thumbnail($loop->post->ID,'wpb-aw-thumb', array('class' => "wpb_aw_img"));
		}else{
		    $return_string .= '<img id="place_holder_thm" src="'.woocommerce_placeholder_img_src().'" alt="Placeholder" />';
		}
		
		$return_string .= '<div class="aw_price_area">'.do_shortcode('[add_to_cart id="'.get_the_ID().'"]').'</div>'; // call cart btn and price 
		
        $return_string .= '</div>';
		
		$return_string .= '</li>';
		 
    endwhile;
	} else {
	echo __( 'No products found' );
	}
	wp_reset_postdata();
			
    $return_string .= '</ul>';
    $return_string .= '</div></div>';

    wp_reset_query();
    return $return_string;   
	   
	   
}

function wpb_aw_register_shortcodes_latest(){
   add_shortcode('wpb-accordain-latest', 'wpb_aw_shortcode');
}
add_action( 'init', 'wpb_aw_register_shortcodes_latest');

// text widget support 
add_filter('widget_text', 'do_shortcode');