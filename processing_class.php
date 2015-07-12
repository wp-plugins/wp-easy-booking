<?php

class booking_processing{
	
	function __construct(){
		add_action('init', array($this, 'booking_front_user_data') );
		add_action('admin_init', array($this, 'booking_admin_ajax') );
	}
	
	function booking_admin_ajax(){
		if( isset($_REQUEST['option']) and $_REQUEST['option'] == 'RemoveSchdData'){
			global $wpdb;
			$schd_id = $_REQUEST['schd_id'];
			$where = array( 'schd_id' => $schd_id );
			$wpdb->delete( $wpdb->prefix.'booking_location_schedule', $where );
			exit;
		}
		
		if( isset($_REQUEST['option']) and $_REQUEST['option'] == 'AddNewSchedule'){
			$gc = new booking_general_class;
			echo '
			<div class="sc_list">
			<input type="hidden" name="schd_ids[]" value="">
			<label for="sc_user">'.__('User','wpb').'</label>
			<select name="sc_users[]">
			'.$gc->sc_user_selected().'
			</select>	
			  
			<label for="open_days">'.__('Day','wpb').'</label>
			<select name="open_days[]">
			'.$gc->booking_day_selected().'
			</select>		  
			
			<label for="time_fr">'.__('From','wpb').'</label>
			<select name="open_time_fr[]">
			'.$gc->booking_time_selected().'
			</select>
			
			<label for="time_to">'.__('To','wpb').'</label>
			<select name="open_time_to[]">
			'.$gc->booking_time_selected().'
			</select>
			
			<label for="status">'.__('Status','wpb').'</label>
			<select name="sc_status[]">
			'.$gc->sc_status_selected().'
			</select>
			
			<a href="javascript:void(0);" class="remove_sc_list">Delete</a>
			</div>';
			exit;
		}
		
		if( isset($_REQUEST['option']) and $_REQUEST['option'] == 'LoadSchedules'){
			global $wpdb;
			$gc = new booking_general_class;
			$ret = '';
			$loc_id = $_REQUEST['loc_id'];
			$schds = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."booking_location_schedule WHERE loc_id='".$loc_id."' ORDER BY schd_id" );
			if(is_array($schds)){
				foreach($schds as $key => $value){
					  $ret .= '<input type="radio" name="schd_id" value="'.$value->schd_id.'"><label for="'.$value->schd_id.'">'.$value->user_id. ' ' .$value->schd_day. ' From '.$value->schd_time_fr. ' To ' .$value->schd_time_to.'</label>';
					$ret .= '<br>';
				}
			}
			echo $ret;
			exit;
		}
	
		if(isset($_REQUEST['action']) and $_REQUEST['action'] == 'booking_log_edit'){
			global $wpdb;
			$blc = new booking_log_class;
			$mc = new booking_message_class;
			$gc = new booking_general_class;
			$update = array('order_status' => $_REQUEST['order_status']);
			$where = array('book_id' => $_REQUEST['book_id']);
			$wpdb->update( $wpdb->prefix.$blc->table, $update, $where );
			// send order update mail to user 
				$gc->sendOrderEmail($_REQUEST['book_id']);
			// send order update mail to user 
			$mc->add_message(__('Booking order successfully updated.','wpb'), 'success');
			wp_redirect($blc->plugin_page."&action=edit&id=".$_REQUEST['book_id']);
			exit;
		}
		
		if(isset($_POST['option']) and $_POST['option'] == "wp_booking_save_settings"){
			global $booking_payment_methods;
			update_option( 'booking_admin_email', sanitize_text_field($_POST['booking_admin_email']) );
			update_option( 'booking_admin_email_from_name', sanitize_text_field($_POST['booking_admin_email_from_name']) );
			update_option( 'booking_form_page', sanitize_text_field($_POST['booking_form_page']) );
			update_option( 'bool_open_till', sanitize_text_field($_POST['bool_open_till']) );
		}
		
	}


	function booking_front_user_data(){
		
		if( isset($_REQUEST['option']) and $_REQUEST['option'] == 'getSchdInfo'){
			global $wpdb;
			$gc = new booking_general_class;
			$ret = '<table width="100%" border="0">';
			$date = $_REQUEST['date'];
			$loc_id = $_REQUEST['loc_id'];
			$day = strtolower(date('l',strtotime($date)));
			
			$schds = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."booking_location_schedule WHERE loc_id='".$loc_id."' AND schd_day = '".$day."' AND schd_status = 'Active' ORDER BY schd_id" );
			if(is_array($schds)){
				foreach($schds as $key => $value){	
				  $user_info = get_userdata($value->user_id);
				  $ret .= '<tr>
				  <td><strong>'.$user_info->display_name.'</strong>'.($user_info->description != ''?'<p>'.nl2br($user_info->description).'</p>':'').'</td>
				  <td>'.ucfirst($value->schd_day).' ('.$date.') '.__('From','wpb').' '.$value->schd_time_fr.' '.__('To','wpb').' '.$value->schd_time_to.' '.__('Hrs','wpb').'</td>
				  <td><a href="'.$gc->get_booking_url(array( 'schd_id' => $value->schd_id, 'loc_id' => $loc_id, 'date' => $date)).'">'.__('Book Now','wpb').'</a></td>
				</tr>';
				}
			}
			$ret .= '</table>';
			echo $ret;		
			exit;
		}
		
		if( isset($_REQUEST['option']) and $_REQUEST['option'] == 'SchdBookingSubmit'){
			if(!session_id())
			@session_start();
			
			if($_REQUEST['loc_id'] == '' or $_REQUEST['schd_id'] == ''){
				wp_die('Location not selected.');
			}
			
			global $wpdb,$booking_payment_methods;
			$gc = new booking_general_class;
			$mc = new booking_message_class;
			$blc = new booking_log_class;
			$log_data['loc_id'] = $_REQUEST['loc_id']; 
			$log_data['schd_id'] = $_REQUEST['schd_id'];
			$log_data['schd_date'] = $_REQUEST['schd_date']; 
			$log_data['user_id'] = get_current_user_id();
			$log_data['c_name'] = $_REQUEST['c_name']; 
			$log_data['c_email'] = $_REQUEST['c_email']; 
			$log_data['c_phone'] = $_REQUEST['c_phone']; 
			$log_data['order_date'] = date("Y-m-d H:i:s"); 
			$log_data['order_price'] = get_option('schd_booking_price'); 
			$log_data['order_status'] = 'Processing';
			$wpdb->insert( $wpdb->prefix."booking_log", $log_data );
			$log_id = $wpdb->insert_id;
			
			// put data in session //
				$_SESSION['b_order']['name'] = $blc->get_loc_data($log_data['loc_id']);
				$_SESSION['b_order']['price'] = get_option('schd_booking_price');
				$_SESSION['b_order']['log_id'] = $log_id;
			// put data in session //
			
			if( get_option('schd_booking_price') == '' || 
			    get_option('schd_booking_price') == '0' || 
			    get_option('schd_booking_price') == '0.00'
			   ){ // booking is free
				 
				// send email to user //
				$gc->sendOrderEmail($log_id);
				
				$mc->add_message(__('Booking successfully registered. Please check your email for details.','wpb'));		$booking_form_page = get_option('booking_form_page');
				wp_redirect(get_permalink($booking_form_page));
				exit;
			} else {
				wp_die('Payment not allowed!');		
			}
		}
	}
}

new booking_processing;