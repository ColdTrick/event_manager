<?php
/**
 * Unregister classes for ElggObject subtypes on plugin deactivation
 */

$classes = array(
	'Event',
	'\ColdTrick\EventManager\Event\Day',
	'\ColdTrick\EventManager\Event\Slot',
	'EventRegistrationQuestion',
	'EventRegistration',
);

foreach ($classes as $class) {
	update_subtype('object', $class::SUBTYPE);
}
