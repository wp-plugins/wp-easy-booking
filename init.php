<?php
class WPBooking {

     static function wpb_install() {
        global $wpdb; 		
		$create_table1 = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."booking_location_schedule` (
		  `schd_id` int(11) NOT NULL AUTO_INCREMENT,
		  `loc_id` int(11) NOT NULL,
		  `user_id` int(11) NOT NULL,
		  `schd_day` varchar(20) NOT NULL,
		  `schd_time_fr` varchar(50) NOT NULL,
		  `schd_time_to` varchar(50) NOT NULL,
		  `schd_status` enum('Active','Inactive') NOT NULL,
		  PRIMARY KEY (`schd_id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=latin1";
		$wpdb->query($create_table1);
		
		$create_table2 = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."booking_log` (
		  `book_id` int(11) NOT NULL AUTO_INCREMENT,
		  `loc_id` int(11) NOT NULL,
		  `schd_id` int(11) NOT NULL,
		  `schd_date` date NOT NULL,
		  `user_id` int(11) NOT NULL,
		  `c_name` varchar(255) NOT NULL,
		  `c_email` varchar(100) NOT NULL,
		  `c_phone` varchar(50) NOT NULL,
		  `order_date` datetime NOT NULL,
		  `order_price` double(10,2) NOT NULL,
		  `order_status` enum('Paid','Unpaid','Processing','Complete') NOT NULL,
		  PRIMARY KEY (`book_id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=latin1";
		$wpdb->query($create_table2);
		
     }
	  static function wpb_uninstall() { }
}
register_activation_hook( __FILE__, array( 'WPBooking', 'wpb_install' ) );
register_deactivation_hook( __FILE__, array( 'WPBooking', 'wpb_uninstall' ) );