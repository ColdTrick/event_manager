<?php

	$guid = (int) get_input("guid");
	$code = get_input("code");
	
	$forward = true;
	
	if (!empty($guid) && !empty($code)) {
		if (($registration = get_entity($guid)) && elgg_instanceof($registration, "object", EventRegistration::SUBTYPE)) {
			$event = $registration->getOwnerEntity();
			$verify_code = event_manager_create_unsubscribe_code($registration, $event);
			
			if ($code === $verify_code) {
				// don't forward
				$forward = false;
				
				// set page owner
				elgg_set_page_owner_guid($event->getContainerGUID());
				
				// make breadcrumb
				elgg_push_breadcrumb($event->title, $event->getURL());
				elgg_push_breadcrumb(elgg_echo("event_manager:menu:unsubscribe_confirm"));
				
				// make page elements
				$title_text = elgg_echo("event_manager:unsubscribe_confirm:title", array($event->title));
				
				if ($event->hide_owner_block) {
					?>
						<style type='text/css'>
							.elgg-sidebar .elgg-owner-block {
								display: none;
							}
						</style>
					<?php 
				}
				$body_vars = array(
					"entity" => $event, 
					"registration" => $registration,
					"code" => $code
				);
				$body = elgg_view_form("event_manager/event/unsubscribe_confirm", array(), $body_vars);
				
				// make page
				$page_data = elgg_view_layout("content", array(
					"title" => $title_text,
					"content" => $body,
					"filter" => ""
				));
				
				// draw page
				echo elgg_view_page($title_text, $page_data);
			} else {
				register_error(elgg_echo("event_manager:unsubscribe_confirm:error:code"));
			}
		} else {
			register_error(elgg_echo("ClassException:ClassnameNotClass", array($guid, elgg_echo("item:object:" . EventRegistration::SUBTYPE))));
		}
	}
	
	if($forward) {
		forward(REFERER);
	}