<?php
class booking_admin_panel{
	function  wp_booking_afo_options() {
		global $wpdb;
		$booking_admin_email = get_option('booking_admin_email');
		$booking_form_page = get_option('booking_form_page');
		$bool_open_till = get_option('bool_open_till');
		$booking_admin_email_from_name = get_option('booking_admin_email_from_name');
		?>
		<form name="f" method="post" action="">
		<input type="hidden" name="option" value="wp_booking_save_settings" />
		<table width="100%" border="0" style="background:#FFFFFF; width:98%; padding:10px; margin-top:20px;"> 
		  <tr>
			<td colspan="2"><h1><?php echo __('WP Booking','wpb');?></h1></td>
		  </tr> 
          
          <tr>
			<td valign="top"><strong><?php echo __('Booking Open Till','wpb');?>:</strong></td>
			<td><input type="text" name="bool_open_till" value="<?php echo $bool_open_till;?>" size="2" />
            <i><?php _e('Enter the month number till the booking is open. Example - "2". If set to "2" then Booking will be available for next 2 months. Default is for 1 Month.','wpb');?></i>
            </td>
		  </tr>
          <tr>
			<td valign="top"><strong><?php echo __('Booking Form Page','wpb');?>:</strong></td>
			<td>
			<?php
				$args = array(
				'depth'            => 0,
				'selected'         => $booking_form_page,
				'echo'             => 1,
				'show_option_none' => '-',
				'id' 			   => 'booking_form_page',
				'name'             => 'booking_form_page'
				);
				wp_dropdown_pages( $args ); 
			?><i><font color="#FF0000"><?php _e('Important','wpb');?></font></i>
				<br />
				<i><?php _e('Please create a new page, put this shortcode <strong>[schd_booking_form]</strong> in the page and select the page as the "Booking Form Page". You don\'t have to put that page in navigation.','wpb');?></i></td>
		  </tr>
          
      </tr>
      <tr>
		<td colspan="2"><hr /><h2><?php _e('Mail Settings','wpb');?></h2></td>
	  </tr>
      <tr>
			<td valign="top"><strong><?php echo __('Admin Email','wpb');?>:</strong></td>
			<td>
			<input type="text" name="booking_admin_email" value="<?php echo $booking_admin_email;?>" />
				<i><?php _e('Booking related emails will be sent here.','wpb');?></i></td>
		  </tr>
          <tr>
			<td valign="top"><strong><?php echo __('Admin Email From Name','wpb');?>:</strong></td>
			<td><input type="text" name="booking_admin_email_from_name" value="<?php echo $booking_admin_email_from_name;?>" />
            <i><?php _e('From name in emails.','wpb');?></i>
            </td>
		  </tr>
		  <tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="submit" value="Save" class="button button-primary button-large" /></td>
		  </tr>
           <tr>
			<td colspan="2"><hr /><h2>Shortcodes</h2></td>
		  </tr>
          <tr>
			<td colspan="2">
            <p>1. <strong>[schd_booking_locations]</strong> This will display all the available locations.</p>
            <p>2. <strong>[schd_calendar no_of_month="2"]</strong> Put this in the <strong>Booking Address</strong> page. This will let users to book a schedule form a jQuery UI Calendar. Shortcode instructions are in the <strong>Booking Address</strong> page as well.</p>
            </td>
		  </tr>
		</table>
		</form>
		<?php 
	}
	
	function  wp_booking_log_data() {
		$blc = new booking_log_class;
		$blc->display_list();
	}
}