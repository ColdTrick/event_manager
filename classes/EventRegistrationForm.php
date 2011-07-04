<?php 

	class EventRegistrationForm extends ElggObject 
	{
		const SUBTYPE = "eventregistrationform";
		
		protected function initialise_attributes() 
		{
			global $CONFIG;
			parent::initialise_attributes();
			
			$this->attributes["subtype"] = self::SUBTYPE;
		}
		
		public function getQuestions()
		{
			$entities = $this->getEntitiesFromRelationship('event_registration_questions');
			
			return $entities;
		}
	}