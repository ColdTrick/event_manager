<?php
/**
 * Form fields for editing event details
 */

$entity = elgg_extract('entity', $vars);

$shortdescription = elgg_extract('shortdescription', $vars);
$description = elgg_extract('description', $vars);
$tags = elgg_extract('tags', $vars);

$output = '';

$output .= elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('event_manager:edit:form:shortdescription'),
	'#help' => elgg_echo('event_manager:edit:form:shortdescription:help'),
	'name' => 'shortdescription',
	'value' => $shortdescription,
]);

$output .= elgg_view_field([
	'#type' => 'longtext',
	'#label' => elgg_echo('description'),
	'#help' => elgg_echo('event_manager:edit:form:description:help'),
	'#class' => 'event-manager-forms-label-inline',
	'name' => 'description',
	'value' => $description,
]);

$output .= elgg_view_field([
	'#type' => 'tags',
	'#label' => elgg_echo('tags'),
	'#help' => elgg_echo('event_manager:edit:form:tags:help'),
	'name' => 'tags',
	'value' => $tags,
]);

$output .= elgg_view_field([
	'#type' => 'file',
	'#label' => elgg_echo('event_manager:edit:form:icon'),
	'#help' => elgg_echo('event_manager:edit:form:icon:help'),
	'name' => 'icon',
]);

$current_icon_content = '';

if ($entity && $entity->icontime) {
	$current_icon = elgg_view('output/img', [
		'src' => $entity->getIconURL(),
		'alt' => $entity->title,
	]);

	$remove_icon_input = elgg_view('input/checkboxes', [
		'name' => 'delete_current_icon',
		'id' => 'delete_current_icon',
		'value' => 0,
		'options' => [
			elgg_echo('event_manager:edit:form:delete_current_icon') => '1'
		],
	]);
	
	$output .= elgg_view('elements/forms/field', [
		'label' => elgg_view('elements/forms/label', [
			'label' => elgg_echo('event_manager:edit:form:currenticon'),
			'id' => 'delete_current_icon',
		]),
		'input' => $current_icon . $remove_icon_input,
	]);
}

$type_options = event_manager_event_type_options();
if ($type_options) {
	$output .= elgg_view_field([
		'#type' => 'select',
		'#label' => elgg_echo('event_manager:edit:form:type'),
		'#help' => elgg_echo('event_manager:edit:form:type:help'),
		'name' => 'event_type',
		'value' => $vars['event_type'],
		'options' => $type_options,
	]);
}

$output .= elgg_view_field([
	'#type' => 'checkboxes',
	'#help' => elgg_echo('event_manager:edit:form:comments_on:help'),
	'name' => 'comments_on',
	'value' => $vars['comments_on'],
	'options' => [
		elgg_echo('event_manager:edit:form:comments_on') => '1',
	],
]);

$output .= elgg_view_field([
	'#type' => 'access',
	'#label' => elgg_echo('access'),
	'name' => 'access_id',
	'value' => $vars['access_id'],
]);

echo $output;
