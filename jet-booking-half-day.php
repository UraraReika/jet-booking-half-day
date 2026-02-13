<?php
/*
 * Plugin Name: JetBooking Half Day
 * Description: Adds half-day booking support to JetBooking, allowing split check-in and check-out times with visual calendar indicators.
 * Plugin URI: https://github.com/UraraReika/jet-booking-half-day
 * Version: 1.0.0
 * Author: UraraReika
 * Author URI: https://github.com/UraraReika
 */

/**
 * Filters the JetBooking assets configuration to include booked date ranges.
 *
 * This filter modifies the JetBooking configuration by adding a list of booked date ranges
 * for the given post ID. It retrieves future bookings, skips invalid or temporary statuses,
 * and formats the check-in and check-out dates into a range.
 *
 * @since 1.0.0
 *
 * @param array $config The JetBooking assets configuration array.
 *
 * @return array The modified configuration array with booked date ranges.
 */
add_filter( 'jet-booking/assets/config', function ( $config ) {
	// Retrieve future bookings for the given post ID.
	$bookings = jet_abaf()->db->get_future_bookings( $config['post_id'] );

	// If there are no bookings, return the original configuration.
	if ( empty( $bookings ) ) {
		return $config;
	}

	// Get statuses to skip (invalid and temporary statuses).
	$skip_statuses   = jet_abaf()->statuses->invalid_statuses();
	$skip_statuses[] = jet_abaf()->statuses->temporary_status();
	$dates           = [];

	// Iterate through bookings to filter and format date ranges.
	foreach ( $bookings as $booking ) {
		// Skip bookings with invalid or temporary statuses.
		if ( ! empty( $booking['status'] ) && in_array( $booking['status'], $skip_statuses ) ) {
			continue;
		}

		// Format check-in and check-out dates.
		$from = new \DateTime( date( 'F d, Y', $booking['check_in_date'] ) );
		$to   = new \DateTime( date( 'F d, Y', $booking['check_out_date'] ) );

		// Add the formatted date range to the list.
		$dates[] = [
			'start' => $from->format( 'Y-m-d' ),
			'end'   => $to->format( 'Y-m-d' ),
		];
	}

	// If there are valid date ranges, add them to the configuration.
	if ( ! empty( $dates ) ) {
		$config['booked_date_ranges'] = $dates;
	}

	// Return the modified configuration.
	return $config;
} );

/**
 * Enqueues the necessary scripts and styles for the JetBooking Half Day plugin.
 *
 * @since 1.0.0
 */
add_action( 'wp_enqueue_scripts', function () {
	// Enqueue the JavaScript file for the plugin.
	wp_enqueue_script(
		'jb-half-day-script',
		plugin_dir_url( __FILE__ ) . 'assets/js/scripts.js',
		[],
		'1.0.0',
		true
	);

	// Enqueue the CSS file for the plugin.
	wp_enqueue_style(
		'jb-half-day-style',
		plugin_dir_url( __FILE__ ) . 'assets/css/styles.css',
	);
} );