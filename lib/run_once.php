<?php 

	function event_manager_run_once_subtypes()
	{
		add_subtype('object', Event::SUBTYPE, EVENT_MANAGER_EVENT_CLASSNAME);
		add_subtype('object', EventDay::SUBTYPE, EVENT_MANAGER_EVENTDAY_CLASSNAME);
		add_subtype('object', EventSlot::SUBTYPE, EVENT_MANAGER_EVENTSLOT_CLASSNAME);
		add_subtype('object', EventRegistrationForm::SUBTYPE, EVENT_MANAGER_EVENTQUESTIONS_CLASSNAME);
		add_subtype('object', EventRegistrationQuestion::SUBTYPE, EVENT_MANAGER_EVENTREGISTRATIONQUESTION_CLASSNAME);
		add_subtype('object', EventRegistration::SUBTYPE, EVENT_MANAGER_REGISTRATION_CLASSNAME);
	}