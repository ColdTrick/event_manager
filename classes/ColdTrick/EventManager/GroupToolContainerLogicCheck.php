<?php

namespace ColdTrick\EventManager;

use Elgg\Groups\ToolContainerLogicCheck;

/**
 * Prevent events from being created if the group tool option is disabled
 */
class GroupToolContainerLogicCheck extends ToolContainerLogicCheck {

	/**
	 * {@inheritdoc}
	 */
	public function getContentType(): string {
		return 'object';
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getContentSubtype(): string {
		return \Event::SUBTYPE;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getToolName(): string {
		return 'event_manager';
	}
}
