<?php 

	class EventRegistration extends ElggObject 
	{
		const SUBTYPE = "eventregistration";
		
		protected function initialise_attributes() 
		{
			global $CONFIG;
			parent::initialise_attributes();
			
			$this->attributes["subtype"] = self::SUBTYPE;
		}
	}