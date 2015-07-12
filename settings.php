<?php
class wp_booking_settings {
	
	function __construct() {
		add_action( 'admin_menu', array( $this, 'wp_booking_afo_menu' ) );
		add_action( 'plugins_loaded',  array( $this, 'wp_booking_text_domain' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_booking_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'wp_booking_scripts_admin' ) );
	}
	
	function wp_booking_scripts() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'wp-booking', plugins_url( 'wp-booking.js' , __FILE__ ), array(), '1.0.0', true );
		wp_register_style( 'jquery-ui', plugins_url('assets/jquery-ui.css', __FILE__) );
		wp_register_style( 'stylebook', plugins_url('style.css', __FILE__) );
		wp_enqueue_style( 'jquery-ui' );
		wp_enqueue_style( 'stylebook' );
	}
	
	function wp_booking_scripts_admin() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'wp-booking', plugins_url( 'wp-booking.js' , __FILE__ ), array(), '1.0.0', true );
		wp_register_style( 'jquery-ui', plugins_url('assets/jquery-ui.css', __FILE__) );
		wp_register_style( 'stylebook', plugins_url('admin_style.css', __FILE__) );
		wp_enqueue_style( 'jquery-ui' );
		wp_enqueue_style( 'stylebook' );
	}
	
	function wp_booking_text_domain(){
		load_plugin_textdomain('wpb', FALSE, basename( dirname( __FILE__ ) ) .'/languages');
	}
	
	function wp_booking_afo_menu () {
		add_options_page( 'WP Booking', 'WP Booking', 'activate_plugins', 'wp_booking_afo', array( 'booking_admin_panel', 'wp_booking_afo_options' ));
		add_options_page( 'WP Booking Log', 'WP Booking Log', 'activate_plugins', 'wp_booking_log_afo', array( 'booking_admin_panel', 'wp_booking_log_data' ));
	}
	
}