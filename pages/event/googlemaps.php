<?php 
$guid = get_input('guid');

if($event = get_entity($guid))
{
?>

<script type="text/javascript">

$(function()
{
	$('#event_manager_address_search').submit(function(e)
	{
		searchAddress($('#address_search').val());
		
		e.preventDefault();
	});

	$('#address_search_save').click(function()
	{
		setAddressFields($('#address_search').val());
		setLatLngFields(event_manager_gmap.getCenter());
		
		$.fancybox.close();
	});
});

</script>
<div id="google_maps" style="width: 800px; height: 700px;">
	<div id="map_canvas" style="width: 800px; height: 600px;"></div>
	<?php 
	
	$form_body .= '<label>'.elgg_echo('event_manager:event:edit:maps_address').'</label>'.elgg_view('input/text', array('internalname' => 'address_search', 
																														'internalid'=> 'address_search',
																														'value' => $event->getLocation()));
	$form_body .= elgg_view('input/submit', array('internalname' => 'address_search_submit', 'value' => elgg_echo('search'))).'&nbsp';
	$form_body .= elgg_view('input/button', array('internalname' => 'address_search_save', 'internalid'=> 'address_search_save', 'value' => elgg_echo('save')));
	
	
	echo elgg_view('input/form', array(	'internalid' 	=> 'event_manager_address_search', 
										'internalname' 	=> 'event_manager_address_search',
										'body' 			=> $form_body));
	
	
	?>
</div>
<?php 
}?>