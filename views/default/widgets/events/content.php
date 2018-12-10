<?php

/* @var $widget ElggWidget */
$widget = elgg_extract('entity', $vars);

$num_display = (int) $widget->num_display;
if ($num_display < 1) {
	$num_display = 5;
}

$event_options = [
	'type' => 'object',
	'subtype' => 'event',
	'limit' => $num_display,
	'pagination' => false,
];

$owner = $widget->getOwnerEntity();

$more_link = elgg_generate_url('default:object:event');

switch ($owner->getType()) {
	case 'group':
		$event_options['container_guid'] = $owner->guid;
		$more_link = elgg_generate_url('collection:object:event:group', [
			'guid' => $owner->guid,
		]);
		break;
	case 'user':
		
		switch ($widget->type_to_show) {
			case 'owning':
				$event_options['owner_guid'] = $owner->guid;
				$more_link = elgg_generate_url('collection:object:event:owner', [
					'username' => $owner->username,
				]);
				break;
			case 'attending':
				$event_options['relationship'] = EVENT_MANAGER_RELATION_ATTENDING;
				$event_options['relationship_guid'] = $owner->guid;
				$event_options['inverse_relationship'] = true;
				
				$more_link = elgg_generate_url('collection:object:event:attending', [
					'username' => $owner->username,
				]);
				break;
		}
		break;
}

$group_guid = $widget->group_guid;
if (is_array($group_guid)) {
	$event_options['container_guid'] = $group_guid[0];
}

$content = elgg_list_entities($event_options);
if (empty($content)) {
	echo elgg_echo('event_manager:list:noresults');
	return;
}

echo $content;

echo elgg_format_element('div', ['class' => 'elgg-widget-more'], elgg_view('output/url', [
	'text' => elgg_echo('event_manager:group:more'),
	'href' => $more_link,
]));
