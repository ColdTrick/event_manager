<?php 
$day = $vars["eventday"];

echo "<div class='event_manager_program' id='dayguid_" . $day->getGUID() . "'>";
echo "<div class='event_manager_program_slots'>";
echo "<div class='event_manager_program_day'>";
echo $day->title . " (" . date(EVENT_MANAGER_FORMAT_DATE_EVENTDAY, $day->date) . ")";

$checked = "";
if ($vars['registered'] != true) {
	$checked = 'checked=checked';
}

echo "<input " . $checked . " type='checkbox' class='event_manager_program_day_select' value='" . $day->getGUID() . "' />";
echo "</div>";
echo "<div class='clearfloat'></div>";

$eventDaySlots = $day->getEventSlots();
if ($eventDaySlots) {
	foreach ($eventDaySlots as $eventSlot) {
		if (!empty($eventSlot)) {
			echo elgg_view("event_manager/program/register/slot", array(
				"slot" => $eventSlot, 
				"dayguid" => $day->getGUID(), 
				"registered" => $vars["registered"]
			));
		}
	}
}
echo "</div>";
echo "</div>";
