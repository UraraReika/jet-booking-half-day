<?php
/*
 * Plugin Name: JetBooking Half Day
 * Description: Adds half-day booking support to JetBooking, allowing split check-in and check-out times with visual calendar indicators.
 * Plugin URI: https://github.com/UraraReika/jet-booking-half-day
 * Version: 1.0.0
 * Author: UraraReika
 * Author URI: https://github.com/UraraReika
 */

add_filter( 'jet-booking/assets/config', function ( $config ) {
	$bookings = jet_abaf()->db->get_future_bookings( $config['post_id'] );

	if ( empty( $bookings ) ) {
		return $config;
	}

	$skip_statuses   = jet_abaf()->statuses->invalid_statuses();
	$skip_statuses[] = jet_abaf()->statuses->temporary_status();
	$dates           = [];

	foreach ( $bookings as $booking ) {
		if ( ! empty( $booking['status'] ) && in_array( $booking['status'], $skip_statuses ) ) {
			continue;
		}

		$from = new \DateTime( date( 'F d, Y', $booking['check_in_date'] ) );
		$to   = new \DateTime( date( 'F d, Y', $booking['check_out_date'] ) );

		$dates[] = [
			'start' => $from->format( 'Y-m-d' ),
			'end'   => $to->format( 'Y-m-d' ),
		];
	}

	if ( ! empty( $dates ) ) {
		$config['booked_date_ranges'] = $dates;
	}

	return $config;
} );

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_script(
		'jb-half-day-script',
		plugin_dir_url( __FILE__ ) . 'assets/js/scripts.js',
		[],
		'1.0.0',
		true
	);

	wp_enqueue_style(
		'jb-half-day-style',
		plugin_dir_url( __FILE__ ) . 'assets/css/styles.css',
	);
} );