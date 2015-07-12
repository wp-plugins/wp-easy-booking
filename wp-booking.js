jQuery(document).on('click','.remove_sc_list', function() { 
	var attr = jQuery(this).attr('data');
	if (typeof attr !== typeof undefined && attr !== false) {
		var schd_id = jQuery(this).attr('data');
		jQuery.ajax({
		type: "POST",
		data: { option: "RemoveSchdData", schd_id: schd_id }
		})
		.done(function() {});
	}
    jQuery(this).parent('div').remove();
});

function AddNewSchedule(){
	jQuery.ajax({
	type: "POST",
	data: { option: "AddNewSchedule" }
	})
	.done(function( data ) {
		jQuery('#schedule_list').prepend(data);
		jQuery( '#schedule_list div' ).first().css( "background-color", "#DCDB36" );
		jQuery( '#schedule_list div' ).animate({ "background-color": "#ffffff" }, { duration: 1000 } );
	});
	return true;
}

function LoadSchedules(loc_id){
	jQuery.ajax({
	type: "POST",
	data: { option: "LoadSchedules", loc_id: loc_id }
	})
	.done(function( data ) {
		jQuery('#load_schd_list').html(data);
	});
	return true;
}