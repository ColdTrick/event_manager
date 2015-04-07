<?php

	$guid = (int) get_input("guid");
	$ebtity = false;
	
	if (!empty($guid) && ($entity = get_entity($guid))) {
		if (!elgg_instanceof($entity, "object", Event::SUBTYPE)) {
			$entity = false;
			register_error(elgg_echo("ClassException:ClassnameNotClass", array($guid, elgg_echo("item:object:" . Event::SUBTYPE))));
		}
	} else {
		register_error(elgg_echo("InvalidParameterException:NoEntityFound"));
	}
	
	if($entity && $entity->register_nologin) {
		// set page owner
		 elgg_set_page_owner_guid($entity->getContainerGuid());
		 
		 // make breadcrumb
		 elgg_push_breadcrumb($entity->title, $entity->getURL());
		 elgg_push_breadcrumb(elgg_echo("event_manager:menu:unsubscribe"));
		 
		 // build page elements
		 $title_text = elgg_echo("event_manager:unsubscribe:title", array($entity->title));
		 
		 if ($entity->hide_owner_block) {
		 	?>
 				<style type='text/css'>
 					.elgg-sidebar .elgg-owner-block {
 						display: none;
 					}
 				</style>
 			<?php 
 		 }
		 			
		 $body = elgg_view_form("event_manager/event/unsubscribe", array(), array("entity" => $entity));
		 
		 $page_data = elgg_view_layout("content", array(
		 	"title" => $title_text,
		 	"content" => $body,
		 	"filter" => ""
		 ));
		 
		 echo elgg_view_page($title_text, $page_data);
	} else {
		forward(REFERER);
	}