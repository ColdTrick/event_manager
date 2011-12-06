<!-- Event Manager -->
<?php 

if(event_manager_has_maps_key()) {
	
	$location = elgg_get_plugin_setting('google_maps_default_location', 'event_manager');
	if(empty($location)){
		$location = "Netherlands";
	}
	
	$zoom_level = elgg_get_plugin_setting('google_maps_default_zoom', 'event_manager');
	if($zoom_level == ""){
		$zoom_level = 10;
	}
	$zoom_level = sanitise_int($zoom_level);
	
	?>
	<script type="text/javascript">
		var EVENT_MANAGER_BASE_LOCATION = "<?php echo $location; ?>";
		var EVENT_MANAGER_BASE_ZOOM = <?php echo $zoom_level; ?>;
	</script>
	<?php 
}

?>
<script type="text/javascript">

$(function () {		
	$('.event_manager_program_slot_delete').live('click', function() {
		if(confirm('<?php echo elgg_echo('deleteconfirm');?>')) {
			slotGuid = $(this).parent().attr("rel");
			if(slotGuid) {
				$slotElement = $("#" + slotGuid); 
				$slotElement.hide();
				$.getJSON('<?php echo EVENT_MANAGER_BASEURL; ?>/proc/slot/delete', {guid: slotGuid}, function(response) {
					if(response.valid) {
						$slotElement.remove();
					} else {
						$slotElement.show();
					}																					
				});
			}
		}
		return false;
		
	});

	$('.event_manager_program_day_delete').live('click', function(e) {
		if(confirm('<?php echo elgg_echo('deleteconfirm');?>')) {
			dayGuid = $(this).parent().attr("rel");
			if(dayGuid) {
				$dayElements = $("#day_" + dayGuid + ", #event_manager_event_view_program li.elgg-state-selected"); 
				$dayElements.hide();
				$.getJSON('<?php echo EVENT_MANAGER_BASEURL; ?>/proc/day/delete', {guid: dayGuid}, function(response) {
					if(response.valid) {
						// remove from DOM
						$dayElements.remove();
						if($("#event_manager_event_view_program li").length > 1){
							$("#event_manager_event_view_program li:first a").click();
						}						
					} else {
						// revert
						$dayElements.show();
					}																					
				});
			}
		}
		
		return false;
	});
	
	$('#event_manager_program_register').click(function() {
		$.getJSON('<?php echo EVENT_MANAGER_BASEURL; ?>/proc/program/register', {event: $('#eventguid').val(), guids: guids.toSource()}, function(response) {
			if(response.valid) {
				$('#register_status').html('<?php echo elgg_echo('event_manager:registration:program:success');?>');
			} else {
				$('#register_status').html('<?php echo elgg_echo('event_manager:registration:program:fail');?>');
			}
		});
	});

	$('.event_manager_questions_delete').live('click', function(e) {
		if(confirm('<?php echo elgg_echo('deleteconfirm');?>')) {
			questionGuid = $(this).attr("rel");
			if(questionGuid) {
				$questionElement = $(this); 
				$questionElement.parent().hide();
				$.getJSON('<?php echo EVENT_MANAGER_BASEURL; ?>/proc/question/delete', {guid: questionGuid}, function(response) {
					if(response.valid) {
						// remove from DOM
						$questionElement.parent().remove();				
					} else {
						// revert
						$questionElement.parent().show();
					}																					
				});
			}
		}
		
		return false;
	});
});
</script>
<!-- End Event Manager -->