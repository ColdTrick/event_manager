<?php 
	global $CONFIG;
	
	$event = $vars["entity"];
	$owner = $event->getOwnerEntity();
	
	$actions = elgg_view("event_manager/event/actions", $vars);	
	
	if($event->icontime){
		$output .= '<div class="event_manager_event_view_image"><img src="'.$event->getIcon('medium').'" border="0" /></div>';
	}
	
	$output .= '<div class="event_manager_event_view_owner">'.elgg_echo('event_manager:event:view:createdby').'</span> '.$owner->name.' '.friendly_time($event->time_created).'</div>';
	
	// event details
	$event_details = "<table>";
	if($location = $event->getLocation()){
		$event_details .= '<tr><td><b>'.elgg_echo('event_manager:edit:form:location').'</b>:</td><td>';
		$event_details .= $event->getLocation();
		$event_details .= '</td></tr>';
	}
	
	$event_details .= '<tr><td><b>'.elgg_echo('event_manager:edit:form:start_day').'</b>:</td><td>'.date(EVENT_MANAGER_FORMAT_DATE_EVENTDAY, $event->start_day).'</td></tr>';
	
	if(!$event->with_program)
	{
		$event_details .= '<tr><td><b>'.elgg_echo('event_manager:edit:form:start_time').'</b>:</td><td>'.date("H:i", $event->start_time).'</td></tr>';
		$event_details .= '<tr><td><b>'.elgg_echo('event_manager:edit:form:end_time').'</b>:</td><td>'.date("H:i", $event->end_time).'</td></tr>';
	}
	
	if($organizer = $event->organizer){
		$event_details .= '<tr><td><b>'.elgg_echo('event_manager:edit:form:organizer').'</b>:</td><td>'.$organizer.'</td></tr>';
	}
	
	if(!empty($event->max_attendees)){
		$spots_left = ($event->max_attendees - $event->countAttendees());
		$event_details .= '<tr><td><b>'.elgg_echo('event_manager:edit:form:spots_left').'</b>:</td><td>'.(($spots_left<1)?0:$spots_left) . '</td></tr>';
	}
	
	if($description = $event->description){
		$event_details .= '<tr><td><b>'.elgg_echo('event_manager:edit:form:description').'</b>:</td><td>'. $description .'</td></tr>';
	}
	
	if($region = $event->region){
		$event_details .= '<tr><td><b>'.elgg_echo('event_manager:edit:form:region').'</b>:</td><td>'.$region.'</td></tr>';
	}
	
	if($type = $event->event_type){
		$event_details .= '<tr><td><b>'.elgg_echo('event_manager:edit:form:type').'</b>:</td><td>'.$type.'</td></tr>';
	}
	
	$event_details .= "</table>";
	
	$output .= $event_details;
	
	$output .= '<div class="clearfloat"></div>';
	$output .= $actions;
	
	echo elgg_view("page_elements/contentwrapper", array("body" => $output));
	
	if($event->comments_on)
	{	
		$comments = list_annotations($event->getGUID(),'generic_comment');
		$comments .= elgg_view('comments/forms/edit',array('entity' => $event));
		
		echo $comments;
	}
?>