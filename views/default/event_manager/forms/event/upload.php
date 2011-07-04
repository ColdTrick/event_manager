<?php 
	
	if($vars['entity'])
	{		
		$guid 		= $vars['entity']->getGUID();
	
		$form_body .= 	elgg_view('input/hidden', array('internalname' => 'guid', 'value' => $guid));
		
		$form_body .= '<label>'.elgg_echo('event_manager:edit:form:title').' *</label><br />'.
						elgg_view('input/text', array('internalname' => 'title', 'value' => '')).'<br />';
						
		$form_body .= 	'<label>'.elgg_echo('event_manager:edit:form:file').' *</label><br />'.
						elgg_view('input/file', array('internalname' => 'file')).'<br />';
		
		$form_body .= 	elgg_view('input/submit', array('value' => elgg_echo('upload')));
		
		$form_body .= 	'<br />(* = '.elgg_echo('requiredfields').')';
		
		$form = 		elgg_view('input/form', array(	'internalid' 	=> 'event_manager_event_upload', 
															'internalname' 	=> 'event_manager_event_upload', 
															'action' 		=> $vars['url'].'action/event_manager/event/upload', 
															'enctype' 		=> 'multipart/form-data', 
															'body' 			=> $form_body));
		
		echo elgg_view('page_elements/contentwrapper', array('body' => $form));
	}