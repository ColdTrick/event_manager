<?php

namespace ColdTrick\EventManager;

/**
 * Attendees related callbacks
 */
class Attendees {
	
	/**
	 * Exports base attributes for event attendees
	 *
	 * @param \Elgg\Event $elgg_event 'export_attendee', 'event'
	 *
	 * @return array
	 */
	public static function exportBaseAttributes(\Elgg\Event $elgg_event) {
		
		$attendee = $elgg_event->getParam('attendee');
		$event = $elgg_event->getParam('event');
		$rel = $elgg_event->getParam('relationship');
		
		$base_attributes = [
			'guid' => $attendee->guid,
			elgg_echo('name') => $attendee->getDisplayName(),
			elgg_echo('email') => $attendee->email,
			elgg_echo('username') => $attendee->username,
		];
		
		$relation = $event->getRelationship($attendee->guid, $rel);
		if ($relation instanceof \ElggRelationship) {
			$base_attributes['registration date'] = date('d-m-Y H:i:s', $relation->time_created);
		}
		
		return array_merge((array) $elgg_event->getValue(), $base_attributes);
	}
	
	/**
	 * Exports questiondata for event attendees
	 *
	 * @param \Elgg\Event $elgg_event 'export_attendee', 'event'
	 *
	 * @return array
	 */
	public static function exportQuestionData(\Elgg\Event $elgg_event) {
		
		$attendee = $elgg_event->getParam('attendee');
		$event = $elgg_event->getParam('event');
		
		if (!$event->registration_needed) {
			return;
		}
		
		$questions = $event->getRegistrationFormQuestions();
		if (empty($questions)) {
			return;
		}
		
		$question_data = [];
		foreach ($questions as $question) {
			$value = null;
			$answer = $question->getAnswerFromUser($attendee->guid);
			if ($answer) {
				$value = $answer->value;
			}
			
			$question_data[$question->getDisplayName()] = $value;
		}
			
		return array_merge((array) $elgg_event->getValue(), $question_data);
	}
	
	/**
	 * Exports programchoices for event attendees
	 *
	 * @param \Elgg\Event $elgg_event 'export_attendee', 'event'
	 *
	 * @return array
	 */
	public static function exportProgramData(\Elgg\Event $elgg_event) {
		
		$attendee = $elgg_event->getParam('attendee');
		$event = $elgg_event->getParam('event');
		
		if (!$event->with_program) {
			return;
		}
		
		$days = $event->getEventDays();
		if (empty($days)) {
			return;
		}
		
		$program_data = [];
		foreach ($days as $day) {
			$slots = $day->getEventSlots();
			if (empty($slots)) {
				continue;
			}
			
			$date = event_manager_format_date($day->date);
			
			foreach ($slots as $slot) {
				$start = date('H:i', $slot->start_time);
				$end = date('H:i', $slot->end_time);
				
				$key = "Event activity: '{$slot->getDisplayName()}' $date ($start - $end)";
				
				$count = $slot->getEntitiesFromRelationship([
					'guid' => $attendee->guid,
					'relationship' => EVENT_MANAGER_RELATION_SLOT_REGISTRATION,
					'inverse_relationship' => true,
					'count' => true,
				]);
				
				$value = $count ? 'V' : '';
			
				$program_data[$key] = $value;
			}
		}
				
		return array_merge((array) $elgg_event->getValue(), $program_data);
	}
}
