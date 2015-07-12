<?php
class booking_message_class {
	function __construct(){
		if(!session_id()){
			@session_start();
		}
	}
	
	function show_message(){
		if(isset($_SESSION['add_booking_msg']) and $_SESSION['add_booking_msg']){
			echo '<div class="'.$_SESSION['add_booking_msg_class'].'">'.$_SESSION['add_booking_msg'].'</div>';
			unset($_SESSION['add_booking_msg']);
			unset($_SESSION['add_booking_msg_class']);
		}
	}
	
	function add_message($msg = '', $class = ''){
		$_SESSION['add_booking_msg'] = $msg;
		$_SESSION['add_booking_msg_class'] = $class;		
	}
}