<?php

	$event_guid = $vars["event_guid"];
	$question_guid = $vars["question_guid"];
	
	if($event_guid && ($entity = get_entity($event_guid))){
		// assume new question mode
		if(!($entity instanceof Event)){
			unset($entity);
		}
		
	} elseif($question_guid && ($entity = get_entity($question_guid))) {
		// assume question edit mode
		if(!($entity instanceof EventRegistrationQuestion)){
			unset($entity);
		}
	}
	
	
	if($entity instanceof EventRegistrationQuestion)
	{
		// assume day edit mode
		$guid 			= $entity->getGUID();
		$event_guid		= $entity->owner_guid;	
		$title 			= $entity->title;	
		$fieldtype		= $entity->fieldtype;
		$required		= $entity->required;
		$fieldoptions	= $entity->fieldoptions;
	} 
	else 
	{
		$event_guid	= $entity->getGUID();			
	}
	
	if($entity && $entity->canEdit())
	{
		$form_body = '<h3 class="settings">'.elgg_echo('event_manager:editregistration:addfield:title').'</h3>';
		
		$form_body .= elgg_view('input/hidden', array('internalname' => 'event_guid', 'value' => $event_guid));
		$form_body .= elgg_view('input/hidden', array('internalname' => 'question_guid', 'value' => $question_guid));
		
		$form_body .= '<label>'.elgg_echo('event_manager:editregistration:question').'</label>'.elgg_view('input/text', array('internalname' => 'questiontext', 'value' => $title));
		
		$form_body .= '<label>'.elgg_echo('event_manager:editregistration:fieldtype').'</label><br />'.elgg_view('input/pulldown', array('internalid' => 'event_manager_registrationform_question_fieldtype', 'value' => $fieldtype, 'internalname' => 'fieldtype', 'options' => array('Textfield', 'Textarea', 'Dropdown', 'Radiobutton'))).'<br />';
				
		if(!in_array($fieldtype, array('Radiobutton', 'Dropdown')))
		{
			$displayNone = ' style="display:none;"';
		}
		
		$form_body .= '<div id="event_manager_registrationform_select_options" '.$displayNone.'><label>'.elgg_echo('event_manager:editregistration:fieldoptions').'</label> ('.elgg_echo('event_manager:editregistration:commasepetared').')'.elgg_view('input/text', array('internalname' => 'fieldoptions', 'value' => $fieldoptions)).'</div>';
		
		$form_body .= elgg_view('input/checkboxes', array('internalname' => 'required', 'value' => $required, 'options' => array(elgg_echo('event_manager:registrationform:editquestion:required')=>'1')));
		
		$form_body .= elgg_view('input/submit', array('value' => elgg_echo('submit')));
		
			
		$body = elgg_view('input/form', array(	'internalid' 	=> 'event_manager_registrationform_question', 
												'internalname' 	=> 'event_manager_registrationform_question', 
												'action' 		=> 'javascript:event_manager_registrationform_add_field($(\'#event_manager_registrationform_question\'))',
												'body' 			=> $form_body));
		
		
		echo elgg_view('page_elements/contentwrapper', array('body' => $body));
		
	} else {
		echo elgg_echo("error");
	}
	