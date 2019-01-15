<?php
if ( ! defined( 'BASED_TREE_URI' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $category
 * @var $orderby
 * @var $options
 * @var $limit
 * @var $el_class
 * @var $el_id
 * Shortcode class
 * @var $this MCMSBakeryShortCode_VC_Wp_Links
 */
$category = $options = $orderby = $limit = $el_class = $el_id = '';
$output = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$options = explode( ',', $options );
if ( in_array( 'images', $options ) ) {
	$atts['images'] = true;
}
if ( in_array( 'name', $options ) ) {
	$atts['name'] = true;
}
if ( in_array( 'description', $options ) ) {
	$atts['description'] = true;
}
if ( in_array( 'rating', $options ) ) {
	$atts['rating'] = true;
}

$el_class = $this->getExtraClass( $el_class );
$wrapper_attributes = array();
if ( ! empty( $el_id ) ) {
	$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"';
}
$output = '<div ' . implode( ' ', $wrapper_attributes ) . ' class="vc_mcms_links mcmsb_content_element' . esc_attr( $el_class ) . '">';
$type = 'MCMS_Widget_Links';
$args = array();
global $mcms_widget_factory;
// to avoid unwanted warnings let's check before using widget
if ( is_object( $mcms_widget_factory ) && isset( $mcms_widget_factory->widgets, $mcms_widget_factory->widgets[ $type ] ) ) {
	ob_start();
	the_widget( $type, $atts, $args );
	$output .= ob_get_clean();

	$output .= '</div>';

	echo $output;
} else {
	echo $this->debugComment( 'Widget ' . esc_attr( $type ) . 'Not found in : vc_mcms_links' );
}