<?php

namespace ColdTrick\EventManager\Controllers;

use Elgg\Database\Select;
use Elgg\Exceptions\Http\EntityNotFoundException;

/**
 * Export attendees
 *
 * @since 7.0
 */
class ExportAttendees extends \Elgg\Controllers\CsvDownloadAction {

	protected \Event $entity;
	
	protected array $data;

	/**
	 * {@inheritdoc}
	 */
	protected function validate(): void {
		$this->entity = elgg_entity_gatekeeper((int) get_input('guid'), 'object', \Event::SUBTYPE, true);
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function getFilename(): string {
		return 'attendees-' . elgg_get_friendly_title($this->entity->getDisplayName()) . '.csv';
	}
	
	/**
	 * Fetch all data
	 *
	 * @return array|mixed
	 */
	protected function getData() {
		if (isset($this->data)) {
			return $this->data;
		}
		
		$this->data = elgg_call(ELGG_IGNORE_ACCESS, function() {
			$rows = [];

			$rel = $this->request->getParam('rel', EVENT_MANAGER_RELATION_ATTENDING);

			$attendees = elgg_get_entities([
				'relationship' => $rel,
				'relationship_guid' => $this->entity->guid,
				'inverse_relationship' => false,
				'limit' => false,
				'batch' => true,
			]);

			foreach ($attendees as $attendee) {
				$rowdata = elgg_trigger_event_results('export_attendee', 'event', [
					'event' => $this->entity,
					'attendee' => $attendee,
					'relationship' => $rel,
				], []);
				
				if (empty($rowdata)) {
					continue;
				}

				$rows[] = $rowdata;
			}

			return $rows;
		});
		
		return $this->data;
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function getContentHeaders(): array {
		$rows = $this->getData();
		if (empty($rows)) {
			throw new EntityNotFoundException(elgg_echo('event_manager:action:attendees:export:no_data'));
		}

		return array_keys($rows[0]);
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function getContentRows(): array {
		$results = [];
		
		$rows = $this->getData();
		foreach ($rows as $row) {
			$results[] = array_map(function($row) {
				return html_entity_decode((string) $row);
			}, array_values($row));
		}
		
		return $results;
	}
}
