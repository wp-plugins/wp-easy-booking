<?php
class schd_calendar {
	
	function __construct($loc_id = '', $no_of_month = ''){
		if(!$loc_id)
		return;
		if($no_of_month == '')
		$no_of_month = 1;
		global $wpdb;
		?>
		<div id="sc_cal"></div>
		<div id="sd_list" style="margin-top:10px;"></div>
		<?php
		$book_till = get_option('bool_open_till');
		if($book_till == ''){
			$book_till = 30;
		} else {
			$book_till = $book_till * 30;
		}
		$schds = $wpdb->get_results( "SELECT `schd_day` FROM ".$wpdb->prefix."booking_location_schedule WHERE loc_id='".$loc_id."' GROUP BY `schd_day` ORDER BY schd_id" );
		if(is_array($schds)){
			foreach($schds as $key => $value){		
				$next = date("Y-m-d",strtotime( "next ".$value->schd_day ));
				$a_days[] = str_pad($next,12,'"',STR_PAD_BOTH);
				$loop = 0;
				while( $loop <= $book_till ){
					$loop = $loop+7;
					$next_str = strtotime($next . " +7 day");
					$next = date("Y-m-d",$next_str);
					$a_days[] = str_pad($next,12,'"',STR_PAD_BOTH);
				}
			}
		}
		if(is_array($a_days))
		$a_days = implode(',',$a_days);
		?>
		<script>
		var disabledDays = [<?php echo $a_days;?>];
		
		jQuery(function(){
			jQuery( "#sc_cal" ).datepicker({
			 dateFormat: "yy-mm-dd",
			 numberOfMonths: <?php echo $no_of_month;?>,
			 onSelect: function(date) {
					jQuery.ajax({
					type: "POST",
					beforeSend: function(){
						jQuery('#sd_list').html('<center>loading..</center>');
					},
					data: { option: "getSchdInfo", date: date, loc_id: <?php echo $loc_id;?> }
					})
					.done(function( data ) {
						jQuery('#sd_list').html(data);
					});
			 },
			 beforeShowDay: function(date) {
				var y = date.getFullYear().toString(); 
				var m = (date.getMonth() + 1).toString();
				var d = date.getDate().toString();
				if(m.length == 1){ m = '0' + m; }
				if(d.length == 1){ d = '0' + d; }
				var currDate = y+'-'+m+'-'+d;
	
				if(jQuery.inArray(currDate,disabledDays) != -1) {
					return [true, "ui-highlight"];	
				} else {
					return [false, ""];
			   }
			}
		  });
		});
		</script>
		<?php
	}	
}

function schd_booking_calendar_shortcode( $atts ) {
     global $post;
	 extract( shortcode_atts( array(
		  'no_of_month' => ''		  
     ), $atts ) );
     
	ob_start();
	
	new schd_calendar($post->ID, $no_of_month);
	$ret = ob_get_contents();	
	ob_end_clean();
	return $ret;
}
add_shortcode( 'schd_calendar', 'schd_booking_calendar_shortcode' );

function schd_booking_form_shortcode( $atts ) {
     global $post;
	 extract( shortcode_atts( array(
	      'title' => '',
     ), $atts ) );
     
	ob_start();
	if($title){
		echo '<h2>'.$title.'</h2>';
	}
	$gc = new booking_general_class;
	$gc->schd_booking_form();
	$ret = ob_get_contents();	
	ob_end_clean();
	return $ret;
}
add_shortcode( 'schd_booking_form', 'schd_booking_form_shortcode' );



function schd_booking_locations_shortcode( $atts ) {
     global $post;
	 extract( shortcode_atts( array(
	      'show' => '10',
     ), $atts ) );
     
	ob_start();
	$args = array( 'posts_per_page' => $show, 'post_type' => 'booking_address' );
	$locations = get_posts( $args );
	if(is_array($locations)){
	echo '<ul class="add-lists columns-2">';
		foreach ( $locations as $loc ) { ?>
			<li>
            	<h3><a href="<?php echo get_permalink($loc->ID);?>"><?php echo $loc->post_title;?></a></h3>
            	<p><?php echo nl2br(get_post_meta($loc->ID,'booking_address',true));?></p>
            </li>
	<?php } 
	echo '</ul>';
	}
	wp_reset_postdata();	
	$ret = ob_get_contents();	
	ob_end_clean();
	return $ret;
}
add_shortcode( 'schd_booking_locations', 'schd_booking_locations_shortcode' );