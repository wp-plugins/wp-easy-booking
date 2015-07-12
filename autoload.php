<?php
class booking_altoload{
	public $includes = array('init','settings', 'booking_address','general_class','message_class','schd_sc','booking_admin_panel','paginate_class','booking_log_class','processing_class');
	function __construct(){
		if(is_array($this->includes)){
			foreach($this->includes as $key => $value){
				include_once dirname( __FILE__ ) . '/'.$value.'.php';
			}
		}
	}
}
new booking_altoload;