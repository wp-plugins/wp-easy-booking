<?php
//*********************//
// Booking Log class V2
//********************//

class booking_log_class {
    
	public $plugin_page;
	public $plugin_page_base;
	public $table;
	public $table2;
	
    function __construct(){
      $this->plugin_page_base = 'wp_booking_log_afo';
	  $this->plugin_page = admin_url('admin.php?page='.$this->plugin_page_base);
	  $this->table = 'booking_log';
	  $this->table2 = 'booking_location_schedule';
    }
	
	function get_table_colums(){
		$colums = array(
		'book_id' => __('ID','wpb'),
		'loc_id' => __('Location','wpb'),
		'schd_id' => __('Schedule','wpb'),
		'schd_date' => __('Booking Date','wpb'),
		'c_email' => __('Customer','wpb'),
		'order_status' => __('Status','wpb'),
		'action' => __('Action','wpb')
		);
		return $colums;
	}
		
	function wrap_div_start(){
		return '<div class="wrap">';
	}
	
	function wrap_div_end(){
		return '</div>';
	}
	
	function table_start(){
		return '<table class="wp-list-table widefat">';
	} 
    
	function table_end(){
		return '</table>';
	}
	
	function get_table_header(){
		$header = $this->get_table_colums();
		$ret .= '<thead>';
		$ret .= '<tr>';
		foreach($header as $key => $value){
			$ret .= '<th>'.$value.'</th>';
		}
		$ret .= '</tr>';
		$ret .= '</thead>';
		return $ret;		
	}
	
	function table_td_column($value){
		if(is_array($value)){
			foreach($value as $vk => $vv){
				$ret .= $this->row_data($vk,$vv,$value);
			}
		}
		
		$ret .= $this->row_actions($value['book_id']);
		return $ret;
	}
	
	function row_actions($id){
		return '<td><a href="'.$this->plugin_page.'&action=edit&id='.$id.'">'.__('View','wpb').'</a></td>';
	}
	
	function row_data($key,$value,$fullvalue){
						
		switch ($key){
			case 'book_id':
			$v = $value;
			break;
			case 'loc_id':
			$v = $this->get_loc_data($value);
			break;
			case 'schd_id':
			$v = $this->get_schd_data($value,$fullvalue['schd_date']);
			break;
			case 'c_email':
			$v = $this->get_customer_data($fullvalue);
			break;
			case 'schd_date':
			$v = $value;
			break;
			case 'order_status':
			$v = $value;
			break;
			default:
			//$v = $value; uncomment this line at your own risk
			break;
		}
		if($v){
			return '<td>'.$v.'</td>';
		}
	}
	
	function get_customer_data($fullvalue){
		$data = '';
		$data .= '<strong>'.__('Email','wpb').'</strong> : '.$fullvalue['c_email'];
		$data .= '<br>';
		$data .= '<strong>'.__('Name','wpb').'</strong> : '.$fullvalue['c_name'];
		$data .= '<br>';
		$data .= '<strong>'.__('Phone','wpb').'</strong> : '.$fullvalue['c_phone'];
		return $data;
	}
	
	function get_schd_data($schd_id = '', $schd_date = ''){
		if($schd_id == '')
		return;
		global $wpdb;
		$data = '';
		$query = "SELECT * FROM ".$wpdb->prefix.$this->table2." WHERE schd_id='".$schd_id."'";
		$result = $wpdb->get_row( $query, OBJECT );
		$user_info = get_userdata($result->user_id);
		
		$data .= '<strong>'.__('Booked To','wpb').'</strong> : '.$user_info->display_name.' ('.$user_info->user_email.')';
		$data .= '<br>';
		$data .= '<strong>'.__('Location Name','wpb').'</strong> : '.$this->get_loc_data($result->loc_id);
		$data .= '<br>';
		$data .= '<strong>'.__('Booking Date','wpb').'</strong> : '.$schd_date.', '.ucfirst($result->schd_day);
		$data .= '<br>';
		$data .= '<strong>'.__('Time Slot','wpb').'</strong> : '.$result->schd_time_fr.' - '.$result->schd_time_to;
		return $data;
	}
	
	function get_loc_data($loc_id = '', $data = 'post_title'){
		if($loc_id == '')
		return;
		$loc_data = get_post($loc_id); 
		$title = $loc_data->$data;
		return $title;
	}
	
	function get_table_body($data){
		$cnt = 0;
		if(is_array($data)){
			$ret .= '<tbody id="the-list">';
			foreach($data as $k => $v){
				$ret .= '<tr class="'.($cnt%2==0?'alternate':'').'">';
				$ret .= $this->table_td_column($v);
				$ret .= '</tr>';
				$cnt++;
			}
			$ret .= '</tbody>';
		}
		return $ret;
	}
	
	function get_single_row_data($id){
		global $wpdb;
		$query = "SELECT * FROM ".$wpdb->prefix.$this->table." WHERE book_id='".$id."'";
		$result = $wpdb->get_row( $query, ARRAY_A );
		return $result;
	}
	
	function prepare_data(){
		global $wpdb;
		$query = "SELECT * FROM ".$wpdb->prefix.$this->table." ORDER BY book_id DESC";
		//$data = $wpdb->get_results($query,ARRAY_A);
		$ap = new afo_paginate(1,$this->plugin_page);
		$data = $ap->initialize($query,0);
		return $data;
	}
	
	function search_form(){
	?>
	<form name="sub_search" action="" method="get">
	<input type="hidden" name="page" value="<?php echo $this->plugin_page_base;?>" />
	<input type="hidden" name="search" value="log_search" />
	<table width="100%" border="0">
	  <tr>
		<td width="17%"><input type="text" name="c_email" value="<?php echo $_REQUEST['c_email'];?>" placeholder="<?php _e('Email','wpb');?>"/></td>
        <td width="17%"><input type="text" name="book_id" value="<?php echo $_REQUEST['book_id'];?>" placeholder="<?php _e('Booking ID','wpb');?>"/></td>
		<td width="76%"><input type="submit" name="submit" value="<?php _e('Filter','wpb');?>" class="button"/></td>
	  </tr>
	</table>
	</form>
	<?php
	}
	
	function add_link(){
		return '<a href="'.$this->plugin_page.'&action=add" class="add-new-h2">'.__('Add','wpb').'</a>';
	}
	
	function get_schedule_details($id = ''){
		if($id == '')
		return;
		global $wpdb;
		$query = "SELECT * FROM ".$wpdb->prefix.$this->table." WHERE schd_id='".$id."'";
		$result = $wpdb->get_row( $query, ARRAY_A );
		return $result;
	}
	
	function get_location_selected($sel = ''){
		$ret = '';
		$args = array( 'posts_per_page' => -1, 'post_type' => 'booking_address' );
		$loclist = get_posts( $args );
		foreach($loclist as $key => $value){
			if( $sel == $value->ID ){
				$ret .= '<option value="'.$value->ID.'" selected="selected">'.$value->post_title.'</option>';
			} else {
				$ret .= '<option value="'.$value->ID.'">'.$value->post_title.'</option>';
			}
		}
		return $ret;
	}
	
	function get_user_selected($sel = ''){
		$ret = '';
		$allusers = get_users( array( 'fields' => array( 'display_name', 'ID', 'user_email' ) ) );
		foreach ( $allusers as $usr ) {
			if( $sel == $usr->ID ){
				$ret .= '<option value="'.$usr->ID.'" selected="selected">'.$usr->display_name.' ('.$usr->user_email.')'.'</option>';
			} else {
				$ret .= '<option value="'.$usr->ID.'">'.$usr->display_name.' ('.$usr->user_email.')'.'</option>';
			}
		}
		return $ret;
	}
	
	function edit(){
	$gc= new booking_general_class;
	$mc = new booking_message_class;
	$id = $_REQUEST['id'];
	$data = $this->get_single_row_data($id);
	$mc->show_message();
	?>
	<form name="f" action="" method="post">
	<input type="hidden" name="book_id" value="<?php echo $id;?>" />
	<input type="hidden" name="action" value="booking_log_edit" />
	<table width="95%" border="0" cellspacing="10" style="background-color:#FFFFFF; margin:2%; padding:5px; border:1px solid #CCCCCC;">
        <tr>
			<td colspan="2">
            <h2><?php _e('Order Details','wpb');?> <?php _e('#');?> <?php echo $id;?></h2>
    		<p> <strong><?php _e('Date','wpb');?></strong> <?php echo $data['order_date'];?></p>
            </td>
		</tr>
        <tr>
			<td colspan="2"><hr></td>
		</tr>
        <tr>
			<td valign="top"><strong><?php _e('Location','wpb');?></strong></td>
			<td><?php echo $this->get_loc_data($data['loc_id']);?>
              <p><?php echo nl2br(get_post_meta($data['loc_id'],'booking_address',true));?></p>
              </td>
		</tr>
        <tr>
			<td colspan="2"><hr></td>
		</tr>
        <tr>
			<td valign="top"><strong><?php _e('User Info','wpb');?></strong></td>
			<td>
			<table width="100%" border="0">
              <tbody>
              	<tr>
                  <td><?php _e('User');?></td>
                  <td><?php echo $data['user_id'];?></td>
                </tr>
                <tr>
                  <td><?php _e('Name');?></td>
                  <td><?php echo $data['c_name'];?></td>
                </tr>
                <tr>
                  <td><?php _e('Email');?></td>
                  <td><?php echo $data['c_email'];?></td>
                </tr>
                <tr>
                  <td><?php _e('Phone');?></td>
                  <td><?php echo $data['c_phone'];?></td>
                </tr>
              </tbody>
            </table>
            </td>
		</tr>
        <tr>
			<td colspan="2"><hr></td>
		</tr>
        <tr>
			<td valign="top"><strong><?php _e('Schedule Details','wpb');?></strong></td>
			<td><?php echo $this->get_schd_data($data['schd_id'],$data['schd_date']);?></td>
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
			<td><select name="order_status">
				<?php echo $gc->order_status_selected( $data['order_status'] );?>
            </select></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="submit" value="<?php _e('Submit','wpb');?>" class="button" /></td>
		</tr>
	</table>
	</form>
	<?php
	}
	
	function lists(){
	?>
	<h2><?php _e('Booking Log','wpb');?></h2>
	<?php
		global $wpdb;
		
		if(isset($_REQUEST['search']) and $_REQUEST['search'] == 'log_search'){
			if($_REQUEST['c_email']){
				$srch_extra .= " AND c_email = '".$_REQUEST['c_email']."'";
			}
			if($_REQUEST['book_id']){
				$srch_extra .= " AND book_id = '".$_REQUEST['book_id']."'";
			}
			
		}
		$query = "SELECT * FROM ".$wpdb->prefix.$this->table." WHERE book_id <> 0 ".$srch_extra." ORDER BY book_id DESC";
		$ap = new afo_paginate(10,$this->plugin_page);
		$data = $ap->initialize($query,$_REQUEST['paged']);
		
		echo $this->search_form();
		echo $this->table_start();
		echo $this->get_table_header();
		echo $this->get_table_body($data);
		echo $this->table_end();
		echo $ap->paginate($_REQUEST);
	}
	
    function display_list() {
		$mc = new booking_message_class;
		$mc->show_message();
		
		echo $this->wrap_div_start();
		if(isset($_REQUEST['action']) and $_REQUEST['action'] == 'edit'){
			$this->edit();
		} elseif(isset($_REQUEST['action']) and $_REQUEST['action'] == 'add'){ 
			$this->add();
		} else{
			$this->lists();
		}
		echo $this->wrap_div_end();
		
  }

}