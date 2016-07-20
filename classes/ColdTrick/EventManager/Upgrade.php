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
	
	public static function migrateFilesFromUserToEvent($event, $type, $object) {
		$ia = elgg_set_ignore_access(true);
		
		$path = 'admin/upgrades/migrate_files_to_event';
		$upgrade = new \ElggUpgrade();
		if (!$upgrade->getUpgradeFromPath($path)) {
			$upgrade->setPath($path);
			$upgrade->title = elgg_echo('admin:upgrades:migrate_files_to_event');
			$upgrade->description = elgg_echo('admin:upgrades:migrate_files_to_event:description');
				
			$upgrade->save();
		}
		
		elgg_set_ignore_access($ia);
	}
	
	public static function convertTimestamps($event, $type, $object) {
		$ia = elgg_set_ignore_access(true);
		
		$path = 'admin/upgrades/convert_timestamps';
		$upgrade = new \ElggUpgrade();
		if (!$upgrade->getUpgradeFromPath($path)) {
			$upgrade->setPath($path);
			$upgrade->title = elgg_echo('admin:upgrades:convert_timestamps');
			$upgrade->description = elgg_echo('admin:upgrades:convert_timestamps:description');
				
			$upgrade->save();
		}
		
		elgg_set_ignore_access($ia);
	}
	
	
}
