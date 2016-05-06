<?php

namespace ColdTrick\EventManager;

class Upgrade {

	public static function fixClasses($event, $type, $object) {

		$classes = [
			'\ColdTrick\EventManager\Event\Day',
			'\ColdTrick\EventManager\Event\Slot',
		];
		
		foreach ($classes as $class) {
			if (get_subtype_class('object', $class::SUBTYPE) !== $class) {
				update_subtype('object', $class::SUBTYPE, $class);
			}
		}
	}
}
