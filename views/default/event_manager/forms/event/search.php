<?php 

	$form_toggle = '<a href="javascript: void(0);" id="event_manager_event_search_advanced_enable"><span>'.elgg_echo('event_manager:list:advancedsearch').'</span><span style="display:none;">'.elgg_echo('event_manager:list:simplesearch').'</span></a>';
	
	$form_body .= '<h3 class="settings">'.$form_toggle.elgg_echo('event_manager:list:searchevents').'</h3>';
	$form_body .= elgg_view('input/hidden', array('internalname' => 'search_type', 'internalid' => 'search_type', 'value' => 'list'));
	$form_body .= elgg_view('input/hidden', array('internalname' => 'container_guid', 'value' => page_owner()));
	$form_body .= elgg_view('input/text', array('value' => '', 'internalname' => 'search', 'internalid' => 'search', 'class' => 'event_manager_event_list_search_input')).'&nbsp;';
	
	$form_body .= elgg_view('input/submit', array('value' => elgg_echo('search')));
	
	$form_body .= "<span id='past_events'>";
	$form_body .= elgg_view('input/checkboxes', array('internalname' => 'past_events', 'value' => 0, 'options' => array(elgg_echo('event_manager:list:includepastevents')=>'1')));
	$form_body .= "</span>";
	
	$form_body .= '<div id="event_manager_event_search_advanced_container">';
	$form_body .= elgg_view('input/hidden', array('internalname' => 'advanced_search', 'internalid' => 'advanced_search', 'value' => 1));
	
	$form_body .= elgg_echo('event_manager:edit:form:start_day').' from: '.elgg_view('input/event_datepicker', array('internalname' => 'start_day', 'internalid' => 'start_day', 'value' => date(EVENT_MANAGER_FORMAT_DATE_EVENTDAY,''))).'&nbsp;';
	$form_body .= elgg_echo('event_manager:edit:form:start_day').' to: '.elgg_view('input/event_datepicker', array('internalname' => 'end_day', 'internalid' => 'end_day', 'value' => date(EVENT_MANAGER_FORMAT_DATE_EVENTDAY,''))).'<br /><br />';

	
	$form_body .= "<div>";
	if($region_options = event_manager_event_region_options())
	{
		$form_body .= elgg_echo('event_manager:edit:form:region') . ': ' . elgg_view('input/pulldown', array('internalname' => 'region', 'value' => $fields["region"], 'options' => $region_options)).' ';
	}
	
	if($type_options = event_manager_event_type_options())
	{
		$form_body .= elgg_echo('event_manager:edit:form:type') . ': ' . elgg_view('input/pulldown', array('internalname' => 'event_type', 'value' => $fields["event_type"], 'options' => $type_options));
	}
	$form_body .= "</div>";
	
	if(isloggedin())
	{
		$form_body .= elgg_view('input/checkboxes', array('internalid' => 'attending', 'internalname' => 'attending', 'value' => 0, 'options' => array(elgg_echo('event_manager:list:meattending')=>'1')));
		$form_body .= elgg_view('input/checkboxes', array('internalid' => 'owning', 'internalname' => 'owning', 'value' => 0, 'options' => array(elgg_echo('event_manager:list:owning')=>'1')));
		$form_body .= elgg_view('input/checkboxes', array('internalid' => 'friendsattending', 'internalname' => 'friendsattending', 'value' => 0, 'options' => array(elgg_echo('event_manager:list:friendsattending')=>'1')));
	}
	
	$form_body .= '</div>';
	
	$form = elgg_view('input/form', array(	'internalid' 	=> 	'event_manager_search_form', 
											'internalname' 	=> 'event_manager_search_form', 
											'action' 		=> $vars['url'].'action/event_manager/event/search',
											'body' 			=> $form_body));
	
	echo elgg_view('page_elements/contentwrapper', array('body' => $form));