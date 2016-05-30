<?php

$widget = elgg_extract('entity', $vars);

$num_display = (int) $widget->num_display;
if ($num_display < 1) {
	$num_display = 5;
}
$event_options = ['limit' => $num_display];

$owner = $widget->getOwnerEntity();

$more_link = '/events';

switch ($owner->getType()) {
	case 'group':
		$event_options['container_guid'] = $owner->getGUID();
		$more_link = '/events/event/list/' . $widget->getOwnerGUID();
		break;
	case 'user':
		$event_options['user_guid'] = $owner->getGUID();
		switch ($widget->type_to_show) {
			case 'owning':
				$event_options['owning'] = true;
				$more_link = '/events/owner/' . $owner->username;
				break;
			case 'attending':
				$event_options['meattending'] = true;
				$more_link = '/events/attending/' . $owner->username;
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

if (empty($events['count'])) {
	return;
}

echo elgg_format_element('div', ['class' => 'elgg-widget-more'], elgg_view('output/url', ['text' => elgg_echo('event_manager:group:more'), 'href' => $more_link]));
