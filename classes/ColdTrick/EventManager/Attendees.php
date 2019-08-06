<?php

namespace ColdTrick\EventManager;

class Attendees {
	
	/**
	 * Exports base attributes for event attendees
	 *
	 * @param \Elgg\Hook $hook 'export_attendee', 'event'
	 *
	 * @return array
	 */
	public static function exportBaseAttributes(\Elgg\Hook $hook) {
		
		$attendee = $hook->getParam('attendee');
		$event = $hook->getParam('event');
		$rel = $hook->getParam('relationship');
		
		$relation = check_entity_relationship($event->guid, $rel, $attendee->guid);
		
		$base_attributes = [
			'guid' => $attendee->guid,
			elgg_echo('name') => $attendee->getDisplayName(),
			elgg_echo('email') => $attendee->email,
			elgg_echo('username') => $attendee->username,
			'registration date' => date("d-m-Y H:i:s", $relation->time_created),
		];
		
		return array_merge((array) $hook->getValue(), $base_attributes);
	}
	
	/**
	 * Exports questiondata for event attendees
	 *
	 * @param \Elgg\Hook $hook 'export_attendee', 'event'
	 *
	 * @return array
	 */
	public static function exportQuestionData(\Elgg\Hook $hook) {
		
		$attendee = $hook->getParam('attendee');
		$event = $hook->getParam('event');
		
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
			
		return array_merge((array) $hook->getValue(), $question_data);
	}
	
	/**
	 * Exports programchoices for event attendees
	 *
	 * @param \Elgg\Hook $hook 'export_attendee', 'event'
	 *
	 * @return array
	 */
	public static function exportProgramData(\Elgg\Hook $hook) {
		
		$attendee = $hook->getParam('attendee');
		$event = $hook->getParam('event');
		
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
					'inverse_relationship' => true,
					'count' => true,
				]);
				
				$value = $count ? 'V': '';
			
				$program_data[$key] = $value;
			}
		}
				
		return array_merge((array) $hook->getValue(), $program_data);
	}
}
