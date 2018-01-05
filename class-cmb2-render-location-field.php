<?php

/**
 * Handles 'location' custom field type.
 */
class CMB2_Render_Location_Field extends CMB2_Type_Base {

	public static function init() {
		add_filter( 'cmb2_render_class_location', array( __CLASS__, 'class_name' ) );
		add_filter( 'cmb2_sanitize_location', array( __CLASS__, 'maybe_save_split_values' ), 12, 4 );

		/**
		 * The following snippets are required for allowing the location field
		 * to work as a repeatable field, or in a repeatable group
		 */
		add_filter( 'cmb2_sanitize_location', array( __CLASS__, 'sanitize' ), 10, 5 );
		add_filter( 'cmb2_types_esc_location', array( __CLASS__, 'escape' ), 10, 4 );

    add_action('admin_enqueue_scripts', function() {

			// $screen = get_current_screen();
			// if ($screen->parent_base !== 'edit' || get_post_type(get_the_ID() !== 'location'))
			// 	return;

			$google_maps_key = false;

			$field_settings = get_option('doin_cmb2_location_field_settings');

			if (isset($field_settings['google_maps_api_key']) && !empty($field_settings['google_maps_api_key']))
				$google_maps_key = $field_settings['google_maps_api_key'];

			if (!isset($google_maps_key) || empty($google_maps_key))
				$google_maps_key = 'AIzaSyB1QXxJn5eluFWGCtKtU9hsJhzTyYc8BN4'; // @todo - Nonono, this is stupid.

			$screen = get_current_screen();

			if ($screen->post_type != 'location')
				return;

      wp_enqueue_script( 'cmb2-location-field-google-maps', 'https://maps.googleapis.com/maps/api/js?callback=init_cmb2_location_map&key='.$google_maps_key.'&libraries=places', array('jquery'), 1.0, true);
      wp_enqueue_script('cmb2-location-field', plugin_dir_url(__FILE__) . 'assets/js/cmb2-location-field.js');

      wp_enqueue_style('cmb2-location-field-style', plugin_dir_url(__FILE__) . 'assets/css/cmb2-location-field.css');

    });
	}

	public static function class_name() { return __CLASS__; }

	/**
	 * Handles outputting the location field.
	 */
	public function render() {
		$value = wp_parse_args( $this->field->escaped_value(), array(
			'address' => '',
			'lat'     => '',
			'lng'     => '',
		) );

		ob_start();
		// Do html
		?>
		<div>
      <div id="cmb2-location-field-map" class="cmb2-location-field-map">

      </div>
			<?php echo $this->types->input( array(
				'name'  => $this->_name( '[address]' ),
				'id'    => $this->_id( '_address' ),
				'value' => $value['address'],
				'desc'  => '',
			   ) );

       echo $this->types->input( array(
 				'name'  => $this->_name( '[lat]' ),
 				'id'    => $this->_id( '_lat' ),
 				'value' => $value['lat'],
 				'type'  => 'hidden',
 			  ) );

      echo $this->types->input( array(
				'name'  => $this->_name( '[lng]' ),
				'id'    => $this->_id( '_lng' ),
				'value' => $value['lng'],
				'type'  => 'hidden',
			  ) );
        ?>
		</div>
		<p class="clear">
			<?php echo $this->_desc();?>
		</p>
		<?php

		// grab the data from the output buffer.
		return $this->rendered( ob_get_clean() );
	}

	/**
	 * Optionally save the Location values into separate fields
	 */
	public static function maybe_save_split_values( $override_value, $value, $object_id, $field_args ) {
		if ( ! isset( $field_args['split_values'] ) || ! $field_args['split_values'] ) {
			// Don't do the override
			return $override_value;
		}

		$location_keys = array( 'address', 'lat', 'lng');

		foreach ( $location_keys as $key ) {
			if ( ! empty( $value[ $key ] ) ) {
				update_post_meta( $object_id, $field_args['id'] . 'addr_'. $key, sanitize_text_field( $value[ $key ] ) );
			}
		}

		remove_filter( 'cmb2_sanitize_location', array( __CLASS__, 'sanitize' ), 10, 5 );

		// Tell CMB2 we already did the update
		return true;
	}

	public static function sanitize( $check, $meta_value, $object_id, $field_args, $sanitize_object ) {

		// if not repeatable, bail out.
		if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
			return $check;
		}

		foreach ( $meta_value as $key => $val ) {
			$meta_value[ $key ] = array_filter( array_map( 'sanitize_text_field', $val ) );
		}

		return array_filter($meta_value);
	}

	public static function escape( $check, $meta_value, $field_args, $field_object ) {
		// if not repeatable, bail out.
		if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
			return $check;
		}

		foreach ( $meta_value as $key => $val ) {
			$meta_value[ $key ] = array_filter( array_map( 'esc_attr', $val ) );
		}

		return array_filter($meta_value);
	}

}
