jQuery( document ).ready( function() {
	const selectedRanges = window.JetABAFData.booked_date_ranges;

	window.JetPlugins.hooks.addFilter( "jet-booking.date-range-picker.date-show-params", "jetBooking", ( params, date ) => {
		const currentDate = moment( date );
		let classes = '';

		selectedRanges.forEach( range => {
			const start = moment( range.start );
			const end = moment( range.end );

			if ( currentDate.isSame( start, 'day' ) ) {
				classes = 'booked-first-day';
			} else if ( currentDate.isSame( end, 'day' ) ) {
				classes = 'booked-last-day';
			} else if ( currentDate.isBetween( start, end ) ) {
				classes = 'booked-range-day';
			}
		} );

		params[1] = classes;

		return params;
	} );
} );



