<?php
/*
Plugin Name: WP Booking
Plugin URI: http://aviplugins.com/
Description: This is a schedule booking plugin. Use this plugin as a complete booking solution for your site. Create Locations, Add Schedules to the locations, Let customers book for schedules. Manage bookings from admin panel.
Version: 1.1.0
Author: avimegladon
Author URI: http://avifoujdar.wordpress.com/
*/

/**
	  |||||   
	<(`0_0`)> 	
	()(afo)()
	  ()-()
**/

$booking_status_array = array( 1 => 'Open', 2 => 'Closed' );

$booking_days_array = array(
'sunday' => __('Sun','wpb'),
'monday' => __('Mon','wpb'),
'tuesday' => __('Tue','wpb'),
'wednesday' => __('Wed','wpb'),
'thursday' => __('Thu','wpb'),
'friday' => __('Fri','wpb'),
'saturday' => __('Sat','wpb'),
);

define('BOOKING_CURRENCY','USD');

include_once dirname( __FILE__ ) . '/autoload.php';
new wp_booking_settings;