<?php

namespace ColdTrick\EventManager\Controllers;

use Elgg\Exceptions\Http\EntityNotFoundException;
use Elgg\Http\ResponseBuilder;
use Elgg\Request;

/**
 * Controller to handle /event_manager/calendar requests
 */
class Calendar {
	
	/**
	 * Respond to a request
	 *
	 * @param Request $request the HTTP request
	 *
	 * @return ResponseBuilder
	 * @throws EntityNotFoundException
	 */
	public function __invoke(Request $request) {
		$events_options = [
			'type' => 'object',
			'subtype' => \Event::SUBTYPE,
			'limit' => 999, // not wise to leave unlimited
			'batch' => true,
			'metadata_name_value_pairs' => [],
		];
		
		$start = get_input('start');
		$end = get_input('end');
		$guid = (int) get_input('guid');
		$resource = get_input('resource');
		$tag = get_input('tag');
		
		if (empty($start) && empty($end)) {
			return elgg_ok_response(json_encode([]));
		}
		
		if (!empty($start)) {
			$events_options['metadata_name_value_pairs'][] = [
				'name' => 'event_end',
				'value' => strtotime($start),
				'operand' => '>='
			];
		}
		
		if (!empty($end)) {
			$events_options['metadata_name_value_pairs'][] = [
				'name' => 'event_start',
				'value' => strtotime($end),
				'operand' => '<='
			];
		}
		
		if (!empty($tag)) {
			$events_options['metadata_name_value_pairs'][] = [
				'name' => 'tags',
				'value' => $tag,
				'case_sensitive' => false,
			];
		}
		
		$entity = get_entity($guid);
		if ($entity instanceof \ElggGroup) {
			$events_options['container_guid'] = $entity->guid;
		}
		
		switch ($resource) {
			case 'owner':
				if (!$entity instanceof \ElggUser) {
					return elgg_ok_response(json_encode([]));
				}
				
				$events_options['owner_guid'] = $entity->guid;
				break;
			case 'attending':
				if (!$entity instanceof \ElggUser) {
					return elgg_ok_response(json_encode([]));
				}
				
				$events_options['relationship'] = EVENT_MANAGER_RELATION_ATTENDING;
				$events_options['relationship_guid'] = $entity->guid;
				$events_options['inverse_relationship'] = true;
				break;
		}
		
		// let others extend this
		$params = [
			'resource' => $resource,
			'guid' => $guid,
			'start' => $start,
			'end' => $end,
		];
		$events_options = elgg_trigger_event_results('calendar_data:options', 'event_manager', $params, $events_options);
		
		// fetch data
		$events = elgg_get_entities($events_options);
		
		$result = [];
		
		/* @var $event \Event */
		foreach ($events as $event) {
			$start = $event->getStartDate();
			$end = $event->getEndDate('c');
			
			$all_day = $event->isMultiDayEvent();
			if ($all_day) {
				// needed for fullcalendar behaviour of allday events
				$end = date('c', strtotime($end . ' +1 day'));
			}
			
			$classes = [];
			if ($event->owner_guid === elgg_get_logged_in_user_guid()) {
				$classes[] = 'event-manager-event-owner';
			} elseif ($event->getRelationshipByUser()) {
				$classes[] = 'event-manager-event-attending';
			}
			
			$event_result = [
				'guid' => $event->guid,
				'title' => $event->getDisplayName(),
				'start' => $start,
				'end' => $end,
				'allDay' => $all_day,
				'url' => $event->getURL(),
				'className' => $classes,
			];
			
			$result[] = $event_result;
		}
		
		return elgg_ok_response(json_encode($result));
	}
}
