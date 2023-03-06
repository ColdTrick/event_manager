<?php

namespace ColdTrick\EventManager\Forms;

/**
 * Event mail entity
 */
class EventMail {
	
	protected \Event $entity;
	
	/**
	 * Constructor
	 *
	 * @param \Event $entity related event entity
	 */
	public function __construct(\Event $entity) {
		$this->entity = $entity;
	}
	
	/**
	 * Get the form body vars for the event mail form
	 *
	 * @return array
	 */
	public function __invoke(): array {
		$vars = [
			'title' => '',
			'description' => '',
			'recipients' => [
				EVENT_MANAGER_RELATION_ATTENDING,
			],
			'entity' => $this->entity,
		];
		
		$sticky = elgg_get_sticky_values('event_manager/event/mail');
		if (!empty($sticky)) {
			foreach ($sticky as $name => $value) {
				$vars[$name] = $value;
			}
			
			elgg_clear_sticky_form('event_manager/event/mail');
		}
		
		return $vars;
	}
}
