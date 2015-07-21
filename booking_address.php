<?php
class booking_addresses_class {
	
	function __construct(){
		add_action( 'init', array( $this, 'codex_booking_address_init' ) );
		add_action( 'add_meta_boxes_booking_address', array( $this, 'booking_extra_boxes' ) );
		add_action( 'save_post', array( $this, 'booking_save_meta_box_data' ) );
	}
	
	function booking_save_meta_box_data( $post_id ) {
 	global $wpdb;
	if ( ! isset( $_POST['booking_meta_box_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['booking_meta_box_nonce'], 'booking_meta_box' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}
	
	update_post_meta($post_id, 'booking_address', $_REQUEST['booking_address']);
	
	$schd_ids = $_REQUEST['schd_ids'];
	$sc_users = $_REQUEST['sc_users'];
	$open_days = $_REQUEST['open_days'];
	$open_time_fr = $_REQUEST['open_time_fr'];
	$open_time_to = $_REQUEST['open_time_to'];
	$sc_status = $_REQUEST['sc_status'];
	
	if(is_array($schd_ids)){
		foreach($schd_ids as $key => $value){
			if($value == ''){ // insert new data 
				$data['loc_id'] = $post_id;
				$data['user_id'] = $sc_users[$key];
				$data['schd_day'] = $open_days[$key];
				$data['schd_time_fr'] = $open_time_fr[$key];
				$data['schd_time_to'] = $open_time_to[$key];
				$data['schd_status'] = $sc_status[$key];
				$wpdb->insert( $wpdb->prefix.'booking_location_schedule', $data );
			} else { // update data 
				$data['loc_id'] = $post_id;
				$data['user_id'] = $sc_users[$key];
				$data['schd_day'] = $open_days[$key];
				$data['schd_time_fr'] = $open_time_fr[$key];
				$data['schd_time_to'] = $open_time_to[$key];
				$data['schd_status'] = $sc_status[$key];
				$where = array('schd_id' => $value );
				$wpdb->update( $wpdb->prefix.'booking_location_schedule', $data, $where );
			}
		}
	}
}
	
	function booking_extra_boxes() {
		add_meta_box(
			'booking_sectionid',
			__( 'Schedule', 'wpb' ),
			array( $this, 'booking_meta_box_callback' ) 
		);
		add_meta_box(
			'booking_address',
			__( 'Address', 'wpb' ),
			array( $this, 'booking_address_meta_box_callback' ),
			'booking_address',
			'side'
		);
		add_meta_box(
			'booking_cal_help',
			__( 'Shortcode', 'wpb' ),
			array( $this, 'booking_help_meta_box_callback' ),
			'booking_address',
			'side'
		);
	}

	
	function get_schedule_data($loc_id = ''){
		global $wpdb;
		$gc = new booking_general_class;
		$ret = '';
		if($loc_id == ''){
			return;
		}
		$schds = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."booking_location_schedule WHERE loc_id='".$loc_id."' ORDER BY schd_id" );
		if(is_array($schds)){
			foreach($schds as $key => $value){
				
				$ret .= '
					<div class="sc_list">
					<input type="hidden" name="schd_ids[]" value="'.$value->schd_id.'">
					<label for="sc_user">'.__('User','wpb').'</label>
					<select name="sc_users[]">
					'.$gc->sc_user_selected($value->user_id).'
					</select>	
					  
					<label for="open_days">'.__('Day','wpb').'</label>
					<select name="open_days[]">
					'.$gc->booking_day_selected($value->schd_day).'
					</select>		  
					
					<label for="time_fr">'.__('From','wpb').'</label>
					<select name="open_time_fr[]">
					'.$gc->booking_time_selected($value->schd_time_fr).'
					</select>
					
					<label for="time_to">'.__('To','wpb').'</label>
					<select name="open_time_to[]">
					'.$gc->booking_time_selected($value->schd_time_to).'
					</select>
					
					<label for="status">'.__('Status','wpb').'</label>
					<select name="sc_status[]">
					'.$gc->sc_status_selected($value->schd_status).'
					</select>
					
					<a href="javascript:void(0);" class="remove_sc_list" data="'.$value->schd_id.'">Delete</a>
					</div>';
			}
		}
		return $ret;
	}
	
	function booking_meta_box_callback( $post ) {
	   wp_nonce_field( 'booking_meta_box', 'booking_meta_box_nonce' );
	   $sc_data = $this->get_schedule_data($post->ID);
	   echo '<table width="100%" border="0">
		  <tbody>
			<tr>
			  <td align="right"><a href="javascript:void(0);" onclick="AddNewSchedule();" class="add-new-h2">Add</a></td>
			</tr>
			<tr>
			  <td>&nbsp;</td>
			</tr>
			<tr>
			  <td><div id="schedule_list">'.$sc_data.'</div></td>
			</tr>
		  </tbody>
		</table>';
	}
	
	function booking_address_meta_box_callback( $post ) {
	   wp_nonce_field( 'booking_meta_box', 'booking_meta_box_nonce' );
	   $booking_address = get_post_meta($post->ID,'booking_address',true);
	   echo '<table width="100%" border="0">
		  <tbody>
			<tr>
			  <td><textarea name="booking_address" style="width:100%;">'.$booking_address.'</textarea></td>
			</tr>
		  </tbody>
		</table>';
	}
	
	function booking_help_meta_box_callback( $post ) {
	   echo '<table width="100%" border="0">
		  <tbody>
			<tr>
			  <td><span style="color:red">Note*</span> Use this shortcode <strong>[schd_calendar no_of_month="2"]</strong> in the content to display the booking calendar. Calendar will be visible in the  Details page. Without this shortcode users will not be able to Book a Schedule.</td>
			</tr>
		  </tbody>
		</table>';
	}
	
	function codex_booking_address_init() {
		$labels = array(
			'name'               => _x( 'Booking Addresses', 'post type general name', 'wpb' ),
			'singular_name'      => _x( 'Booking Address', 'post type singular name', 'wpb' ),
			'menu_name'          => _x( 'Booking Addresses', 'admin menu', 'wpb' ),
			'name_admin_bar'     => _x( 'Booking Addresses', 'add new on admin bar', 'wpb' ),
			'add_new'            => _x( 'Add New', 'address', 'wpb' ),
			'add_new_item'       => __( 'Add New Address', 'wpb' ),
			'new_item'           => __( 'New Booking Address', 'wpb' ),
			'edit_item'          => __( 'Edit Booking Address', 'wpb' ),
			'view_item'          => __( 'View Booking Address', 'wpb' ),
			'all_items'          => __( 'All Booking Addresses', 'wpb' ),
			'search_items'       => __( 'Search Booking Addresses', 'wpb' ),
			'not_found'          => __( 'No addresses found.', 'wpb' ),
			'not_found_in_trash' => __( 'No addresses found in Trash.', 'wpb' )
		);
	
		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'booking-address' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor' )
		);
	
		register_post_type( 'booking_address', $args );
	}
}


new booking_addresses_class;