<?php
//************************//
// afo_paginate version 2 //
//***********************//

if (!class_exists('afo_paginate')) {
class afo_paginate{
	public $per_page = 10;
	public $base_url = '';
	public $total_rec = 0;
	public $total_page = 0;
	public $current_page = '';

	function __construct($per_page,$base_url){
		$this->per_page	= $per_page;
		$this->base_url	= $base_url;
	}
	
	function afo_paginate_css(){ ?>
	<style>
	.page_list_cont{
		clear:both;
		margin-top:20px;
	}
	.page_list{
		margin:2px;
		padding:2px 4px 2px 4px;
		text-decoration:none;
		background-color:#FFFFFF;
		border:1px solid #666666;
	}
	.current{
		background-color:#000;
		border:1px solid #000;
		color:#fff;
	}
	</style>
	<?php
	}

	function initialize($query,$current_page = ''){
		global $wpdb;
		if(!$query){
			return;
		}
		$total = $wpdb->get_results($query,ARRAY_A);
		$this->total_rec = $wpdb->num_rows; 
		$this->total_page = ceil($this->total_rec/$this->per_page);
		$this->current_page = $current_page;
		$start = $this->start_list_from();
		$query .= " LIMIT ".$start.", ".$this->per_page."";
		$data = $wpdb->get_results($query,ARRAY_A);
		return $data;
	}

	function start_list_from(){
		if(!$this->current_page){
			$page = 1;
			$this->current_page = 1;
		} else {
			$page = $this->current_page;
		}

		$start = ($page-1)*$this->per_page;
		return $start;
	}

	function paginate($query_str = ''){
		$this->afo_paginate_css();
		$qs = $this->gen_query_str($query_str);
		
		echo '<div class="page_list_cont">';
		for($i=1; $i <= $this->total_page; $i++){
			if($this->current_page == $i){
				echo '<a href="'.$this->base_url.'&paged='.$i.$qs.'" class="page_list current">'.$i.'</a>';
			} else {
				echo '<a href="'.$this->base_url.'&paged='.$i.$qs.'" class="page_list">'.$i.'</a>';
			}
		}
		echo '</div>';
	}
	
	function gen_query_str($query_str = ''){
		if(is_array($query_str)){
			unset($query_str['paged']);
			unset($query_str['page']);
			$qs = '&';
			foreach($query_str as $key => $value){
				$qs .= $key."=".$value."&";
			}
			$qs = rtrim($qs,'&');
		}
		return $qs;
	}
}
}