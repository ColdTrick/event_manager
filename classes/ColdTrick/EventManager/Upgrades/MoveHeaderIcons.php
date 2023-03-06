<?php

namespace ColdTrick\EventManager\Upgrades;

use Elgg\Upgrade\AsynchronousUpgrade;
use Elgg\Upgrade\Result;

class MoveHeaderIcons extends AsynchronousUpgrade {

	/**
	 * {@inheritdoc}
	 */
	public function getVersion(): int {
		return 2023030700;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function needsIncrementOffset(): bool {
		return false;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function shouldBeSkipped(): bool {
		return empty($this->countItems());
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function countItems(): int {
		return elgg_count_entities($this->getOptions());
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function run(Result $result, $offset): Result {
		
		/**
		 * Set correct filename for Event icon
		 *
		 * @param \Elgg\Event $event 'entity:icon:file', 'object'
		 *
		 * @return void|\ElggIcon
		 */
		$get_old_icon_location = function (\Elgg\Event $event) {
			$entity = $event->getEntityParam();
			if (!$entity instanceof \Event) {
				return;
			}
			
			$size = $event->getParam('size');
			$returnvalue = $event->getValue();
			$returnvalue->setFilename("{$size}.jpg");
			
			return $returnvalue;
		};
		
		elgg_register_event_handler('entity:icon:file', 'object', $get_old_icon_location);
		
		$events = elgg_get_entities($this->getOptions(['offset' => $offset]));
		/* @var $event \Event */
		foreach ($events as $event) {
			$old_icon = $event->getIcon('master', 'icon');
			if ($old_icon->exists()) {
				$coords = [
					'x1' => $event->x1,
					'y1' => $event->y1,
					'x2' => $event->x2,
					'y2' => $event->y2,
				];
				
				$event->saveIconFromElggFile($old_icon, 'header', $coords);
			}
			
			$event->deleteIcon('icon');
			
			$result->addSuccesses();
		}
		
		elgg_unregister_event_handler('entity:icon:file', 'object', $get_old_icon_location);
		
		return $result;
	}
	
	/**
	 * Get options for elgg_get_entities
	 *
	 * @param array $options additional options
	 *
	 * @return array
	 */
	protected function getOptions(array $options = []) {
		$defaults = [
			'type' => 'object',
			'subtype' => 'event',
			'limit' => 50,
			'batch' => true,
			'metadata_name' => 'icontime',
		];
		
		return array_merge($defaults, $options);
	}
}
