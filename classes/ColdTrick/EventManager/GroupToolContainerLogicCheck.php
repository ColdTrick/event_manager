<?php

namespace ColdTrick\EventManager;

use Elgg\Groups\ToolContainerLogicCheck;

/**
 * Prevent events from being created if the group tool option is disabled
 */
class GroupToolContainerLogicCheck extends ToolContainerLogicCheck {

	/**
	 * {@inheritDoc}
	 */
	public function getContentType(): string {
		return 'object';
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function getContentSubtype(): string {
		return \Event::SUBTYPE;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getToolName(): string {
		return 'event_manager';
	}
}
