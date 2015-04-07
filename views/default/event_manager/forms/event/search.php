<?php 

	$region_options = event_manager_event_region_options();
	$type_options = event_manager_event_type_options();

	$form_toggle = '<a href="javascript: void(0);" id="event_manager_event_search_advanced_enable"><span>'.elgg_echo('event_manager:list:advancedsearch').'</span><span class="hidden">'.elgg_echo('event_manager:list:simplesearch').'</span></a>';
	
	$form_body = $form_toggle;
	$form_body .= elgg_view('input/hidden', array('name' => 'search_type', 'id' => 'search_type', 'value' => 'list'));
	$form_body .= elgg_view('input/hidden', array('name' => 'latitude', 'id' => 'latitude'));
	$form_body .= elgg_view('input/hidden', array('name' => 'longitude', 'id' => 'longitude'));
	$form_body .= elgg_view('input/hidden', array('name' => 'distance_longitude', 'id' => 'distance_longitude'));
	$form_body .= elgg_view('input/hidden', array('name' => 'distance_latitude', 'id' => 'distance_latitude'));
	$form_body .= elgg_view('input/hidden', array('name' => 'container_guid', 'value' => elgg_get_page_owner_guid()));
	$form_body .= elgg_view('input/text', array('name' => 'search', 'id' => 'search', 'class' => 'event_manager_event_list_search_input mrs'));
	
	$form_body .= '<div id="event_manager_event_search_advanced_container" class="mtm hidden">';
	$form_body .= elgg_view('input/hidden', array('name' => 'advanced_search', 'id' => 'advanced_search', 'value' => 0));
	
	$form_body .= "<table><tr><td class='prl'>";
	
	$form_body .= "<table>";
	$form_body .= "<tr><td class='prm'>" . elgg_echo('event_manager:edit:form:start_day:from') . ':</td><td>' . elgg_view('input/date', array('name' => 'start_day', 'id' => 'start_day')).'</td></tr>';
	$form_body .= "<tr><td class='prm'>" . elgg_echo('event_manager:edit:form:start_day:to').':</td><td>'.elgg_view('input/date', array('name' => 'end_day', 'id' => 'end_day')).'</td></tr>';
	$form_body .= "</table>";
	
	$form_body .= "</td>";
	
	if($region_options || $type_options){
		$form_body .= "<td class='prl'>";
		$form_body .= "<table>";
	
		if($region_options){
			$form_body .= "<tr><td class='prm'>" . elgg_echo('event_manager:edit:form:region') . ':</td><td>' . elgg_view('input/dropdown', array('name' => 'region', 'options' => $region_options)) . "</td></tr>";
		}
	
		if($type_options)	{
			$form_body .= "<tr><td class='prm'>" . elgg_echo('event_manager:edit:form:type') . ':</td><td>' . elgg_view('input/dropdown', array('name' => 'event_type', 'options' => $type_options)) . "</td></tr>";
		}
	
		$form_body .= "</table>";
		$form_body .= "</td>";
	}
	
	if(elgg_is_logged_in()){
		$form_body .= "<td>";
		$form_body .= elgg_view('input/checkboxes', array('id' => 'attending', 'name' => 'attending', 'value' => 0, 'options' => array(elgg_echo('event_manager:list:meattending')=>'1')));
		$form_body .= elgg_view('input/checkboxes', array('id' => 'owning', 'name' => 'owning', 'value' => 0, 'options' => array(elgg_echo('event_manager:list:owning')=>'1')));
		$form_body .= elgg_view('input/checkboxes', array('id' => 'friendsattending', 'name' => 'friendsattending', 'value' => 0, 'options' => array(elgg_echo('event_manager:list:friendsattending')=>'1')));
		$form_body .= "</td>";
	}
	$form_body .= "</tr></table>";
	$form_body .= "</div>";
	
	$form_body .= elgg_view('input/submit', array('value' => elgg_echo('search')));
	
	$form_body .= "<span id='past_events'>";
	$form_body .= elgg_view('input/checkboxes', array('name' => 'past_events', 'value' => 0, 'options' => array(elgg_echo('event_manager:list:includepastevents')=>'1')));
	$form_body .= "</span>";
	
	
	
	$form = elgg_view('input/form', array(	'id' 	=> 	'event_manager_search_form', 
											'name' 	=> 'event_manager_search_form', 
											'action' 		=> '/action/event_manager/event/search',
											'body' 			=> $form_body));
	
	echo elgg_view_module("main", "" , $form);