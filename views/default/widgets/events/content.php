<?php

/* @var $widget \ElggWidget */
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
	'sort_by' => [
		'property' => 'event_start',
		'direction' => 'ASC',
		'signed' => true,
	],
	'no_results' => elgg_echo('event_manager:list:noresults'),
];

$tag_filter = $widget->tag ? elgg_string_to_array($widget->tag)[0] : null;

$more_link = elgg_generate_url('collection:object:event:upcoming', [
	'tag' => $tag_filter,
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

if (!empty($tag_filter)) {
	$event_options['metadata_name_value_pairs'][] = [
		'name' => 'tags',
		'value' => $tag_filter,
		'case_sensitive' => false,
	];
}

$owner = $widget->getOwnerEntity();

switch ($widget->context) {
	case 'groups':
		$event_options['container_guid'] = $owner->guid;
		$more_link = elgg_generate_url($group_route_name, [
			'guid' => $owner->guid,
			'tag' => $tag_filter,
		]);
		break;
	case 'profile':
	case 'dashboard':
		switch ($widget->type_to_show) {
			case 'owning':
				$event_options['owner_guid'] = $owner->guid;
				$more_link = elgg_generate_url('collection:object:event:owner', [
					'username' => $owner->username,
					'tag' => $tag_filter,
				]);
				break;
			case 'attending':
				$event_options['relationship'] = EVENT_MANAGER_RELATION_ATTENDING;
				$event_options['relationship_guid'] = $owner->guid;
				$event_options['inverse_relationship'] = true;
				
				$more_link = elgg_generate_url('collection:object:event:attending', [
					'username' => $owner->username,
					'tag' => $tag_filter,
				]);
				break;
		}
		break;
}

if ($widget->group_guid) {
	$event_options['container_guid'] = $widget->group_guid;
}

$more_text = elgg_echo('event_manager:list:more');
if (!empty($tag_filter)) {
	$more_text = elgg_echo('event_manager:list:more:with_tag', [$tag_filter]);
}

$event_options['widget_more'] = elgg_view_url($more_link, $more_text);

echo elgg_list_entities($event_options);
