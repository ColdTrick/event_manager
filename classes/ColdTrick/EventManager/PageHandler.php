<?php

namespace ColdTrick\EventManager;

class PageHandler {

	/**
	 * Page handler for events
	 *
	 * @param array $page page elements
	 *
	 * @return boolean
	 */
	public static function events($page) {
		elgg_push_breadcrumb(elgg_echo('event_manager:menu:events'), 'events');
		
		if (empty($page)) {
			echo elgg_view_resource('events/event/list');
			return true;
		}
		
		switch ($page[0]) {
			case 'owner':
				$username = elgg_extract(1, $page);
				$user = get_user_by_username($username);
				if ($user) {
					elgg_set_page_owner_guid($user->guid);
					echo elgg_view_resource('events/event/owner');
					return true;
				}
			case 'attending':
				$username = elgg_extract(1, $page);
				$user = get_user_by_username($username);
				if ($user) {
					elgg_set_page_owner_guid($user->guid);
					echo elgg_view_resource('events/event/attending');
					return true;
				}
			case 'unsubscribe':
				if (isset($page[1])) {
					if ($page[1] == 'confirm') {
						echo elgg_view_resource('events/unsubscribe/confirm', [
							'guid' => (int) elgg_extract(2, $page),
							'code' => elgg_extract(3, $page),
						]);
						return true;
					} else {
						echo elgg_view_resource('events/unsubscribe/request', [
							'guid' => (int) elgg_extract(1, $page),
						]);
						return true;
					}
				}
				break;
			case 'registration':
				if (isset($page[1])) {
					switch ($page[1]) {
						case 'confirm':
							echo elgg_view_resource('events/registration/confirm', [
								'event_guid' => elgg_extract(2, $page),
								'user_guid' => (int) get_input('user_guid'),
								'code' => get_input('code'),
							]);
							return true;
						case 'completed':
							echo elgg_view_resource('events/registration/completed', [
								'event_guid' => elgg_extract(2, $page),
								'object_guid' => elgg_extract(3, $page),
							]);
							return true;
						case 'view':
							echo elgg_view_resource('events/registration/view', [
								'guid' => elgg_extract(2, $page),
								'k' => get_input('k'),
								'u_g' => (int) get_input('u_g', elgg_get_logged_in_user_guid()),
							]);
							return true;
					}
				}
			case 'event':
				switch ($page[1]) {
					case 'list':
						$owner_guid = (int) elgg_extract(2, $page);
						if ($owner_guid) {
							elgg_set_page_owner_guid($owner_guid);
						}
						echo elgg_view_resource('events/event/list');
						return true;
					case 'view':
						
						$guid = (int) elgg_extract(2, $page);
						
						// setting input to be used in user_hover menu
						set_input('guid', $guid);
						
						echo elgg_view_resource('events/event/view', [
							'guid' => $guid,
						]);
						return true;
					case 'waitinglist':
						echo elgg_view_resource('events/event/waitinglist', [
							'guid' => (int) elgg_extract(2, $page),
						]);
						return true;
					case 'register':
						echo elgg_view_resource('events/event/register', [
							'guid' => (int) elgg_extract(2, $page),
							'relation' => (int) elgg_extract(3, $page),
						]);
						return true;
					case 'new':
						$owner_guid = (int) elgg_extract(2, $page);
						if ($owner_guid) {
							elgg_set_page_owner_guid($owner_guid);
						}
						echo elgg_view_resource('events/event/edit');
						return true;
					case 'edit':
						echo elgg_view_resource('events/event/edit', [
							'guid' => (int) elgg_extract(2, $page),
						]);
						return true;
					case 'edit_program':
						echo elgg_view_resource('events/event/edit_program', [
							'guid' => (int) elgg_extract(2, $page),
						]);
						return true;
					case 'upload':
						echo elgg_view_resource('events/event/upload', [
							'guid' => (int) elgg_extract(2, $page),
						]);
						return true;
				}
			default:
				forward('events');
		}
		
		echo elgg_view_resource('events/event/list');
		return true;
	}
}