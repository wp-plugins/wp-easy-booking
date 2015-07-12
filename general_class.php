<?php
class booking_general_class {
	public $sc_statuses = array('Active','Inactive');
	public $order_statuses = array('Paid','Unpaid','Processing','Complete');
	
	function booking_time_selected($sel = ''){
		$ret = '';
		for( $i=1; $i <= 24; $i++ ){
			if($sel == str_pad(number_format($i,2),5,'0',STR_PAD_LEFT)){
				$ret .= '<option value="'.str_pad(number_format($i,2),5,'0',STR_PAD_LEFT).'" selected="selected">'.str_pad(number_format($i,2),5,'0',STR_PAD_LEFT).'</option>';
			} else {
				$ret .= '<option value="'.str_pad(number_format($i,2),5,'0',STR_PAD_LEFT).'">'.str_pad(number_format($i,2),5,'0',STR_PAD_LEFT).'</option>';
			}
		}
		return $ret;
	}
	
	function booking_day_selected($sel = ''){
		global $booking_days_array;
		$ret = '';
		foreach( $booking_days_array as $key => $value ){
			if($sel == $key){
				$ret .= '<option value="'.$key.'" selected="selected">'.$value.'</option>';
			} else {
				$ret .= '<option value="'.$key.'">'.$value.'</option>';
			}
		}
		return $ret;
	}
	
	function sc_status_selected($sel = ''){
		$sc_statuses = $this->sc_statuses;
		$ret = '';
		foreach( $sc_statuses as $key => $value ){
			if($sel == $value){
				$ret .= '<option value="'.$value.'" selected="selected">'.$value.'</option>';
			} else {
				$ret .= '<option value="'.$value.'">'.$value.'</option>';
			}
		}
		return $ret;
	}
	
	function order_status_selected($sel = ''){
		$order_statuses = $this->order_statuses;
		$ret = '';
		foreach( $order_statuses as $key => $value ){
			if($sel == $value){
				$ret .= '<option value="'.$value.'" selected="selected">'.$value.'</option>';
			} else {
				$ret .= '<option value="'.$value.'">'.$value.'</option>';
			}
		}
		return $ret;
	}
	
	function sc_user_selected($sel = ''){
		global $wpdb;
		$ret = '';
		$sc_users = get_users( array( 'fields' => array( 'ID','display_name' ) ) );
		foreach( $sc_users as $key => $value ){
			if($sel == $value->ID){
				$ret .= '<option value="'.$value->ID.'" selected="selected">'.$value->display_name.'</option>';
			} else {
				$ret .= '<option value="'.$value->ID.'">'.$value->display_name.'</option>';
			}
		}
		return $ret;
	}
	
	function get_booking_url($data = array()){
		$schd_id = base64_encode($data['schd_id']);
		$loc_id = base64_encode($data['loc_id']);
		$date = base64_encode($data['date']);
		$booking_page_id = get_option('booking_form_page');
		if(!$booking_page_id)
		return '#';
		$booking_page_url = get_permalink($booking_page_id);
		if(get_option('permalink_structure')){
			$booking_page_url = $booking_page_url.'?schd_id='.$schd_id.'&loc_id='.$loc_id.'&date='.$date;
		} else {
			$booking_page_url = $booking_page_url.'&schd_id='.$schd_id.'&loc_id='.$loc_id.'&date='.$date;
		}
		return $booking_page_url;
	}
	
	function expMonthOptions(){
		$ret = '';
		for($i = 1; $i <= 12; $i++ ){
			$ret .= '<option value="'.str_pad($i, 2, '0', STR_PAD_LEFT).'">'.$i.'</option>';
		}
		return $ret;
	}
	
	function expYearOptions(){
		$current = date('Y');
		$loop = $current + 6;
		$ret = '';
		for($i = $current; $i <= $loop; $i++ ){
			$ret .= '<option value="'.$i.'">'.$i.'</option>';
		}
		return $ret;
	}
	
	function schd_booking_form(){
	global $booking_payment_methods;
	$schd_id = base64_decode($_REQUEST['schd_id']);
	$loc_id = base64_decode($_REQUEST['loc_id']);
	$date = base64_decode($_REQUEST['date']);
	$mc = new booking_message_class;
	$mc->show_message();
	if($schd_id == '' or $loc_id == '' or $date == '')
	return;
	?>
    <div id="book_forms">
    <form name="f" action="" method="post">
    <input type="hidden" name="option" value="SchdBookingSubmit">
    <input type="hidden" name="schd_id" value="<?php echo $schd_id;?>">
    <input type="hidden" name="loc_id" value="<?php echo $loc_id;?>">
    <input type="hidden" name="schd_date" value="<?php echo $date;?>">
    <div class="form-group">
        <label for="name"><?php _e('Name','wpb');?> </label>
        <input type="text" name="c_name" required="required" placeholder="<?php _e('Name','wpb');?>"/>
    </div>
    <div class="form-group">
        <label for="email"><?php _e('Email','wpb');?> </label>
        <input type="text" name="c_email" required="required" placeholder="<?php _e('Email','wpb');?>"/>
    </div>
    <div class="form-group">
        <label for="phone"><?php _e('Phone','wpb');?> </label>
        <input type="text" name="c_phone" required="required" placeholder="<?php _e('Phone','wpb');?>"/>
    </div>
    
    <div class="form-group"><input name="submit" type="submit" value="<?php _e('Submit','wpb');?>" /></div>
    </form>
    </div>
    <?php
	}
	
	function set_html_content_type() {
		return 'text/html';
	}
	
	function sendOrderEmail($book_id = ''){
		if($book_id == '')
		return;
		$blc = new booking_log_class;
		$data = $blc->get_single_row_data($book_id);
		if(!is_array($data))
		return;
		
		ob_start();
		?>
        <table width="90%" border="0">
          <tbody>
            <tr>
              <td><h2><?php _e('Order Details','wpb');?> <?php _e('#');?> <?php echo $data['book_id'];?></h2></td>
              <td><p> <strong><?php _e('Date','wpb');?></strong> <?php echo $data['order_date'];?></p></td>
            </tr>
             <tr>
				<td colspan="2"><hr></td>
			</tr>
            <tr>
              <td valign="top"><strong><?php _e('Location Details','wpb');?></strong></td>
              <td><?php echo $blc->get_loc_data($data['loc_id']);?>
              <p><?php echo nl2br(get_post_meta($data['loc_id'],'booking_address',true));?></p>
              </td>
            </tr>
            <tr>
				<td colspan="2"><hr></td>
			</tr>
            <tr>
              <td valign="top"><strong><?php _e('Your Details','wpb');?></strong></td>
              <td><?php echo $data['c_name'];?><br><?php echo $data['c_email'];?><br><?php echo $data['c_phone'];?><br></td>
            </tr>
            <tr>
			<td colspan="2"><hr></td>
		</tr>
        <tr>
			<td valign="top"><strong><?php _e('Schedule Details','wpb');?></strong></td>
			<td><?php echo $blc->get_schd_data($data['schd_id'],$data['schd_date']);?></td>
		</tr> 
         <tr>
			<td colspan="2"><hr></td>
		</tr>
        <tr>
			<td><strong><?php _e('Order Total','wpb');?></strong></td>
			<td><?php echo BOOKING_CURRENCY;?> <?php echo $data['order_price'];?></td>
		</tr>
		<tr>
			<td><strong><?php _e('Status','wpb');?></strong></td>
			<td><?php echo $data['order_status'];?></td>
		</tr>
          </tbody>
        </table>
        <?php
		$body = ob_get_contents();	
		ob_end_clean();		
		
		add_filter( 'wp_mail_content_type', array( $this, 'set_html_content_type') );
		$to = $data['c_email'];
		$multiple_tos = array(
			$data['c_email'],
			get_option('booking_admin_email')
		);
					
		$headers[] = 'From: '.get_option('booking_admin_email_from_name').' <'.get_option('booking_admin_email').'>';
		$subject = __('Booking Order Status');
		wp_mail( $multiple_tos, $subject, $body, $headers );
		remove_filter( 'wp_mail_content_type', array( $this, 'set_html_content_type') );
		return;
	}
}