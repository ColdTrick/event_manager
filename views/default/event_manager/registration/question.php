<?php 

	$question = $vars["entity"];
	$value = $vars["value"];
	
	if(!empty($question) && ($question instanceof EventRegistrationQuestion))
	{
		if($question->canEdit() && $vars['register'] != true)
		{
			$edit_question = " <a href='javascript:void(0);' class='event_manager_questions_edit' rel='" . $question->getGUID() . "'>" . elgg_echo("edit") . "</a>";
			$delete_question = "<a href='javascript:void(0);' class='event_manager_questions_delete' rel='" . $question->getGUID() . "'>" . elgg_echo("delete") . "</a>";
			
			$tools .= $edit_question . " " . $delete_question;
		}
		
		$fieldtypes = event_manager_get_registration_fiedtypes();
		if(array_key_exists($question->fieldtype, $fieldtypes))
		{			
			$field_options = $question->getOptions();
			
			if($question->required)
			{
				$required = ' *';
			}
			
			if($question->fieldtype == 'Checkbox')
			{
				$field_options = array($question->title.$required => '1');
				
				$result = $tools.elgg_view('input/'.$fieldtypes[$question->fieldtype], array('internalname' => 'question_'.$question->getGUID(), 'value' => $value, 'options' => $field_options));
			}
			else 
			{
				$result = '<span class="move"></span> <label>'.$question->title.$required.'</label>'.$tools.'<br />'.elgg_view('input/'.$fieldtypes[$question->fieldtype], array('internalname' => 'question_'.$question->getGUID(), 'value' => $value, 'options' => $field_options));
			}
		}
		
		
		echo '<li id="question_'.$question->getGUID().'">'.$result.'</li>';
	}