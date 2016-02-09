<?php

$widget = elgg_extract('entity', $vars);

$num_display = (int) $widget->num_display;
if ($num_display < 1) {
	$num_display = 5;
}
$event_options = ['limit' => $num_display];

$owner = $widget->getOwnerEntity();

switch ($owner->getType()) {
	case 'group':
		$event_options['container_guid'] = $owner->getGUID();
		break;
	case 'user':
		$event_options['user_guid'] = $owner->getGUID();
		switch ($widget->type_to_show) {
			case 'owning':
				$event_options['owning'] = true;
				break;
			case 'attending':
				$event_options['meattending'] = true;
				break;
		}
		break;
}

$group_guid = $widget->group_guid;
if (is_array($group_guid)) {
	$group_guid = $group_guid[0];
}

if (!empty($group_guid)) {
	$event_options['container_guid'] = $group_guid;
}

$events = event_manager_search_events($event_options);
$content = elgg_view_entity_list($events['entities'], [
	'count' => $events['count'],
	'offset' => 0,
	'limit' => $num_display,
	'pagination' => false,
	'full_view' => false,
	'no_results' => elgg_echo('notfound'),
]);

echo $content;

$user = elgg_get_logged_in_user_entity();
if (empty($user)) {
	return;
}

$add_link = false;

if ($owner instanceof ElggGroup) {
	$who_create_group_events = elgg_get_plugin_setting('who_create_group_events', 'event_manager'); // group_admin, members
	if ((($who_create_group_events == 'group_admin') && $owner->canEdit()) || (($who_create_group_events == 'members') && $owner->isMember($user))) {
		$add_link = '/events/event/new/' . $owner->getGUID();
	}
} else {
	$who_create_site_events = elgg_get_plugin_setting('who_create_site_events', 'event_manager');
	if ($who_create_site_events !== 'admin_only' || elgg_is_admin_logged_in()) {
		$add_link = '/events/event/new';
	}
}

if ($add_link !== false) {
	echo elgg_format_element('div', ['class' => 'elgg-widget-more'], elgg_view('output/url', ['text' => elgg_echo('event_manager:menu:new_event'), 'href' => $add_link]));
}
