<?php

namespace ColdTrick\EventManager;

class Attendees {
	
	/**
	 * Exports base attributes for event attendees
	 *
	 * @param string $hook        hook name
	 * @param string $entity_type hook type
	 * @param array  $returnvalue current return value
	 * @param array  $params      parameters
	 *
	 * @return array
	 */
	public static function exportBaseAttributes($hook, $entity_type, $returnvalue, $params) {
		
		$attendee = elgg_extract('attendee', $params);
		$event = elgg_extract('event', $params);
		$rel = elgg_extract('relationship', $params);
		
		$relation = check_entity_relationship($event->guid, $rel, $attendee->guid);
		
		$base_attributes = [
			'guid' => $attendee->guid,
			elgg_echo('name') => $attendee->name,
			elgg_echo('email') => $attendee->email,
			elgg_echo('username') => $attendee->username,
			'registration date' => date("d-m-Y H:i:s", $relation->time_created),
		];
		
		return array_merge((array) $returnvalue, $base_attributes);
	}
	
	/**
	 * Exports questiondata for event attendees
	 *
	 * @param string $hook        hook name
	 * @param string $entity_type hook type
	 * @param array  $returnvalue current return value
	 * @param array  $params      parameters
	 *
	 * @return array
	 */
	public static function exportQuestionData($hook, $entity_type, $returnvalue, $params) {
		
		$attendee = elgg_extract('attendee', $params);
		$event = elgg_extract('event', $params);
		
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
			$answer = $question->getAnswerFromUser($attendee->getGUID());
			if ($answer) {
				$value = $answer->value;
			}
			
			$question_data[$question->title] = $value;
		}
			
		return array_merge((array) $returnvalue, $question_data);
	}
	
	/**
	 * Exports programchoices for event attendees
	 *
	 * @param string $hook        hook name
	 * @param string $entity_type hook type
	 * @param array  $returnvalue current return value
	 * @param array  $params      parameters
	 *
	 * @return array
	 */
	public static function exportProgramData($hook, $entity_type, $returnvalue, $params) {
		
		$attendee = elgg_extract('attendee', $params);
		$event = elgg_extract('event', $params);
		
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
				
				$key = "Event activity: '{$slot->title}' $date ($start - $end)";
				
				$count = $slot->getEntitiesFromRelationship([
					'guid' => $attendee->guid,
					'inverse_relationship' => true,
					'count' => true,
				]);
				
				$value = $count ? 'V': '';
			
				$program_data[$key] = $value;
			}
		}
				
		return array_merge((array) $returnvalue, $program_data);
	}
}
