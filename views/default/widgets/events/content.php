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
	'metadata_name_value_pairs' => [
		[
			'name' => 'event_start',
			'value' => time(),
			'operand' => '>=',
		],
	],
	'order_by_metadata' => [
		'name' => 'event_start',
		'direction' => 'ASC',
		'as' => 'integer'
	],
];

$more_link = elgg_generate_url('collection:object:event:upcoming', [
	'tag' => $widget->tag,
]);

$group_route_name = 'collection:object:event:group';

if ($widget->event_status === 'live') {
	$event_options['metadata_name_value_pairs'] = [
		[
			'name' => 'event_start',
			'value' => time(),
			'operand' => '<=',
		],
		[
			'name' => 'event_end',
			'value' => time(),
			'operand' => '>=',
		],
	];
	
	$more_link = elgg_generate_url('collection:object:event:live');
	$group_route_name = 'collection:object:event:live';
}

if (!empty($widget->tag)) {
	$event_options['metadata_name_value_pairs'][] = [
		'name' => 'tags',
		'value' => $widget->tag,
		'case_sensitive' => false,
	];
}

$owner = $widget->getOwnerEntity();

switch ($widget->context) {
	case 'groups':
		$event_options['container_guid'] = $owner->guid;
		$more_link = elgg_generate_url($group_route_name, [
			'guid' => $owner->guid,
			'tag' => $widget->tag,
		]);
		break;
	case 'profile':
	case 'dashboard':
		
		switch ($widget->type_to_show) {
			case 'owning':
				$event_options['owner_guid'] = $owner->guid;
				$more_link = elgg_generate_url('collection:object:event:owner', [
					'username' => $owner->username,
					'tag' => $widget->tag,
				]);
				break;
			case 'attending':
				$event_options['relationship'] = EVENT_MANAGER_RELATION_ATTENDING;
				$event_options['relationship_guid'] = $owner->guid;
				$event_options['inverse_relationship'] = true;
				
				$more_link = elgg_generate_url('collection:object:event:attending', [
					'username' => $owner->username,
					'tag' => $widget->tag,
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
