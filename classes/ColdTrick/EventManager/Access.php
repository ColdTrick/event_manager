<?php

namespace ColdTrick\EventManager;

class Access {
	
	/**
	 * After the event is updated in the database make sure the owned entities have the same access_id
	 *
	 * @param string      $event  the name of the event
	 * @param string      $type   the type of the event
	 * @param \ElggObject $entity the affected object
	 *
	 * @return void
	 */
	public static function updateEvent($event, $type, $entity) {
		
		if (!($entity instanceof \Event)) {
			return;
		}
		
		$org_attributes = $entity->getOriginalAttributes();
		if (elgg_extract('access_id', $org_attributes) === null) {
			// access wasn't updated
			return;
		}
		
		// ignore access for this part
		$ia = elgg_set_ignore_access(true);
		
		$days = $entity->getEventDays();
		if (!empty($days)) {
			foreach ($days as $day) {
				$day->access_id = $entity->access_id;
				$day->save();
			
				$slots = $day->getEventSlots();
				if (empty($slots)) {
					continue;
				}
			
				foreach ($slots as $slot) {
					$slot->access_id = $entity->access_id;
					$slot->save();
				}
			}
		}
		
		$questions = $entity->getRegistrationFormQuestions();
		if (!empty($questions)) {
			foreach ($questions as $question) {
				$question->access_id = $entity->access_id;
				$question->save();
			}
		}
		
		// restore access
		elgg_set_ignore_access($ia);
	}
}
