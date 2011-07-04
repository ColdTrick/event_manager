<?php 

	class EventRegistrationQuestion extends ElggObject 
	{
		const SUBTYPE = "eventregistrationquestion";
		
		protected function initialise_attributes() 
		{
			global $CONFIG;
			parent::initialise_attributes();
			
			$this->attributes["subtype"] = self::SUBTYPE;
		}
		
		public function getAllAnswers()
		{
			$result = false;
			
			if($annotations = get_annotations($this->getGUID(), '', '', 'answer_to_event_registration'))
			{
				$result = $annotations;
			}
			
			return $result;
		}
		
		public function getAnswerFromUser($user_guid = null)
		{
			$result = false;
			
			if($user_guid == null)
			{
				$user_guid = get_loggedin_userid();
			}
			
			if($annotations = get_annotations($this->getGUID(), '', '', 'answer_to_event_registration', '', $user_guid))
			{
				$result = $annotations[0];
			}
			
			return $result;
		}
		
		public function deleteAnswerFromUser($user_guid = null)
		{			
			if($user_guid == null)
			{
				$user_guid = get_loggedin_userid();
			}
			
			if($annotations = get_annotations($this->getGUID(), '', '', 'answer_to_event_registration', '', $user_guid))
			{
				$annotations[0]->delete();
			}
		}
		
		public function updateAnswerFromUser($event, $new_answer, $user_guid = null)
		{	
			if($user_guid == null)
			{
				$user_guid = get_loggedin_userid();
			}
			
			if(($old_answer = $this->getAnswerFromUser()) && (($user = get_entity($user_guid)) instanceof ElggUser))
			{
				if(!empty($new_answer))
				{
					update_annotation($old_answer->id, 'answer_to_event_registration', $new_answer, '', $user_guid, $event->access_id);
				}
				else
				{
					delete_annotation($old_answer->id);
				}
			}
			else
			{
				
				$this->annotate('answer_to_event_registration', $new_answer, $event->access_id, $user_guid);
			}
		}
		
		public function getOptions()
		{
			$field_options = array();
			
			if(!empty($this->fieldoptions))
			{
				$options_explode = explode(',', $this->fieldoptions);
				array_walk($options_explode, 'trim_array_values');
				
				$field_options = $options_explode;
			}
			
			return $field_options;
		}
	}

?>