<?php

	/**
	 * Checks if there are new slots available after updating an event
	 *
	 * @param unknown_type $event
	 * @param unknown_type $type
	 * @param unknown_type $object
	 */
	function event_manager_update_object_handler($event, $type, $object){
		
		if(!empty($object) && ($object instanceof Event)){
			$fillup = false;
			
			if($object->with_program && $object->hasSlotSpotsLeft()){
				$fillup = true;
			} elseif (!$object->with_program && $object->hasEventSpotsLeft()){
				$fillup = true;
			}
			
			if($fillup){
				while($object->generateNewAttendee()){
					continue;
				}
			}
		}
	}