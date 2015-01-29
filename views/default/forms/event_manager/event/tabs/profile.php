<?php
/**
 * Form fields for editing event details
 */

$entity = elgg_extract('entity', $vars);

$short_description_label = elgg_echo('event_manager:edit:form:shortdescription');
$short_description_input = elgg_view('input/text', array(
	'name' => 'shortdescription',
	'value' => $vars["shortdescription"]
));

$description_label = elgg_echo('description');
$description_input = elgg_view('input/longtext', array(
	'name' => 'description',
	'value' => $vars["description"],
));

$tags_label = elgg_echo('tags');
$tags_input = elgg_view('input/tags', array(
	'name' => 'tags',
	'value' => $vars["tags"],
));

$icon_label = elgg_echo('event_manager:edit:form:icon');
$icon_input = elgg_view('input/file', array('name' => 'icon'));

$current_icon_content = '';

if ($entity && $entity->icontime) {

	$current_icon_label = elgg_echo('event_manager:edit:form:currenticon');
	
	$current_icon = elgg_view('output/img', array(
		'src' => $entity->getIconURL(),
		'alt' => $entity->title,
	));

	$remove_icon_input = elgg_view('input/checkboxes', array(
		'name' => 'delete_current_icon',
		'id' => 'delete_current_icon',
		'value' => 0,
		'options' => array(
			elgg_echo('event_manager:edit:form:delete_current_icon') => '1'
		)
	));

	$current_icon_content = <<<CURRENT
<div>
	<label>$current_icon_label</label>
	<div>$current_icon</div>
	$remove_icon_input
</div>
CURRENT;
}


$type_label = '';
$type_input = '';
$type_options = event_manager_event_type_options();
if ($type_options) {
	$type_label = elgg_echo('event_manager:edit:form:type');
	$type_input = elgg_view('input/dropdown', array(
		'name' => 'event_type',
		'value' => $fields["event_type"],
		'options' => $type_options
	));
}

$access_label = elgg_echo('access');
$access_input = elgg_view('input/access', array(
	'name' => 'access_id',
	'value' => $vars["access_id"]
));


echo <<<HTML
	<div>
		<label>$short_description_label</label>
		$short_description_input
	</div>
	<div>
		<label>$description_label</label>
		$description_input
	</div>
	<div>
		<label>$tags_label</label>
		$tags_input
	</div>
	<div>
		<label>$icon_label</label>
		$icon_input
	</div>
	$current_icon_content
	<div>
		<label>$type_label</label>
		$type_input
	</div>
	<div>
		<label>$access_label</label>
		$access_input
		$required_fields_info
	</div>
HTML;
