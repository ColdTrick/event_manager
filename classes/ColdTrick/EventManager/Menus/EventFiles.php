<?php

namespace ColdTrick\EventManager\Menus;

use Elgg\Menu\MenuItems;

/**
 * Event Files menu related callbacks
 */
class EventFiles {
	
	/**
	 * Add menu items listing of event files
	 *
	 * @param \Elgg\Event $elgg_event 'register', 'menu:event_files'
	 *
	 * @return null|MenuItems
	 */
	public static function registerFiles(\Elgg\Event $elgg_event): ?MenuItems {
		$event = $elgg_event->getEntityParam();
		if (!$event instanceof \Event) {
			return null;
		}
		
		$files = $event->getFiles();
		if (empty($files)) {
			return null;
		}
		
		$elggfile = new \ElggFile();
		$elggfile->owner_guid = $event->guid;
		
		$use_cookie = ($event->access_id !== ACCESS_PUBLIC);
		$result = $elgg_event->getValue();
		foreach ($files as $file) {
			$elggfile->setFilename($file->file);
			
			if (!$elggfile->exists()) {
				// check old storage location
				$elggfile->setFilename("files/{$file->file}");
			}
			
			$result[] = \ElggMenuItem::factory([
				'name' => $file->title,
				'icon' => 'download',
				'text' => $file->title,
				'href' => elgg_get_download_url($elggfile, $use_cookie),
			]);
		}
		
		return $result;
	}
}
