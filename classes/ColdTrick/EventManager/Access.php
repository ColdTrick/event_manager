<?php

namespace ColdTrick\EventManager;

/**
 * Access related callbacks
 */
class Access {
	
	/**
	 * After the event is updated in the database make sure the owned entities have the same access_id
	 *
	 * @param \Elgg\Event $event 'update:after', 'object'
	 *
	 * @return void
	 */
	public static function updateEvent(\Elgg\Event $event): void {
		$entity = $event->getObject();
		if (!$entity instanceof \Event) {
			return;
		}
		
		$org_attributes = $entity->getOriginalAttributes();
		if (elgg_extract('access_id', $org_attributes) === null) {
			// access wasn't updated
			return;
		}
		
		elgg_call(ELGG_IGNORE_ACCESS, function() use ($entity) {
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
		});
	}
}
