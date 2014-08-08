<?php 
$slot = $vars["slot"];

if ($vars['registered'] != true) {
	$checked = 'checked=checked';
} elseif (check_entity_relationship(elgg_get_logged_in_user_guid(), EVENT_MANAGER_RELATION_SLOT_REGISTRATION, $vars['slot']->getGUID())) {
	$checked = 'checked=checked';
	?>
	<script type="text/javascript">
		$(function() {
			$('#slotguid_<?php echo $slot->getGUID();?>').parent().parent().find('.event_manager_program_day input[type=checkbox]').attr('checked', true);
		});
	</script>
	<?php 
}

echo "<div class='event_manager_program_slot_view'>";

echo "<div class='event_manager_program_slot_view_time'>";
echo $slot->start_time . " - " . $slot->end_time;
echo "</div>";

echo "<div class='event_manager_program_slot_view_info'>";

echo "<div class='event_manager_program_slot_view_info_location'>";
echo $slot->location;
echo "</div>";

echo $slot->title . "<br />";
echo "<span>" . nl2br($slot->description) . "</span>";

echo "</div>";

echo "<input id='slotguid_" . $slot->getGUID() . "' name='guid' type='checkbox' class='event_manager_program_slot_select' value='" . $slot->getGUID() . "' " . $checked . " />";
echo "<div class='clearfloat'></div>";

echo "</div>";
