<?php
/*
 * Plugin Name: CMB2 Custom Field Type - Location
 * Description: A Location Field
 * Author: Doin
 * Author URI: https://doin.io
 * Version: 0.1.0
 */

function cmb2_init_location_field() {
	require_once dirname( __FILE__ ) . '/class-cmb2-render-location-field.php';
	CMB2_Render_Location_Field::init();
}
add_action( 'cmb2_init', 'cmb2_init_location_field' );

function doin_cmb2_location_field_register_menu() {

	$cmb = new_cmb2_box( array(
		'id'           => 'doin_cmb2_location_field_settings',
		'title'        => esc_html__( 'CMB2 Location Field', 'cmb2' ),
		'object_types' => array( 'options-page' ),
		'option_key'      => 'doin_cmb2_location_field_settings',
		'parent_slug'     => 'options-general.php',
	) );

	$cmb->add_field( array(
		'name'    => esc_html__( 'Google Maps API Key', 'cmb2' ),
		'desc'    => 'The location field works with Google Maps and needs the geolocation and maps API. <br/><a href="https://developers.google.com/maps/documentation/javascript/get-api-key">Get an API key.</a>',
		'id'      => 'google_maps_api_key',
		'type'    => 'text',
	) );
}
add_action( 'cmb2_admin_init', 'doin_cmb2_location_field_register_menu' );
