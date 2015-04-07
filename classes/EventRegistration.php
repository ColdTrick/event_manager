<?php

	class EventRegistration extends ElggObject {
		const SUBTYPE = "eventregistration";
		
		protected function initializeAttributes() {
			parent::initializeAttributes();
			
			$this->attributes["subtype"] = self::SUBTYPE;
			$this->attributes["access_id"] = ACCESS_PUBLIC;
		}
	}