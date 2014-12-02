<?php
/**
 * Unregister classes for ElggObject subtypes on plugin deactivation
 */

$classes = array(
	'Event',
	'EventDay',
	'EventSlot',
	'EventRegistrationQuestion',
	'EventRegistration',
);

foreach ($classes as $class) {
	update_subtype('object', $class::SUBTYPE);
}
