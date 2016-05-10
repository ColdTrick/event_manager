<?php

$guid = (int) elgg_extract('guid', $vars);
$event = null;

if (!empty($guid) && ($entity = get_entity($guid))) {
	if ($entity->getSubtype() == Event::SUBTYPE) {
		$event = $entity;
	}
}

if ($event) {
	if ($event->canEdit() && $event->registration_needed) {
		// add title button to edit registration questions
		elgg_register_menu_item("title", ElggMenuItem::factory(array(
			"name" => "editquestions",
			"href" => "events/registrationform/edit/" . $event->getGUID(),
			"text" => elgg_echo("event_manager:event:editquestions"),
			"link_class" => "elgg-button elgg-button-action"
		)));
	}

	// add export button
	elgg_require_js("addthisevent");
	elgg_register_menu_item("title", ElggMenuItem::factory(array(
		"name" => "addthisevent",
		"href" => false,
		"text" => elgg_view("event_manager/event/addthisevent", array("entity" => $event))
	)));

	elgg_set_page_owner_guid($event->getContainerGUID());
	$page_owner = elgg_get_page_owner_entity();
	if ($page_owner instanceof ElggGroup) {
		elgg_push_breadcrumb($page_owner->name, "/events/event/list/" . $page_owner->getGUID());
	}

	$title_text = $event->title;
	elgg_push_breadcrumb($title_text);

	$output = elgg_view_entity($event, array("full_view" => true));

	$sidebar = elgg_view("event_manager/event/sidebar", array("entity" => $event));
	
	$body = elgg_view_layout('content', array(
		'filter' => '',
		'content' => $output,
		'title' => $title_text,
		'sidebar' => $sidebar
	));

	echo elgg_view_page($title_text, $body, 'default');

} else {
	register_error(elgg_echo("InvalidParameterException:GUIDNotFound", array($guid)));
	forward(REFERER);
}
