<?php
gatekeeper();

elgg_require_js('event_manager/edit_questions');

$title_text = elgg_echo('event_manager:editregistration:title');

$guid = (int) elgg_extract('guid', $vars);

elgg_entity_gatekeeper($guid, 'object', Event::SUBTYPE);

$event = get_entity($guid);

if (!$event->canEdit()) {
	register_error(elgg_echo('actionunauthorized'));
	forward($event->getURL());
}

elgg_push_breadcrumb($event->title, $event->getURL());

// Have to do this for private events
$ia = elgg_set_ignore_access(true);

$output = '<ul id="event_manager_registrationform_fields">';

$registration_form = $event->getRegistrationFormQuestions();
if ($registration_form) {
	foreach ($registration_form as $question) {
		$output .= elgg_view('event_manager/registration/question', ['entity' => $question]);
	}
}

$output .= '</ul><br />';

elgg_load_js('lightbox');
elgg_load_css('lightbox');

$output .= elgg_view('output/url', [
	'href' => 'javascript:void(0);',
	'data-colorbox-opts' => json_encode([
		'href' => elgg_normalize_url('events/registrationform/question?event_guid=' . $guid)
	]),
	'class' => 'elgg-button elgg-button-action elgg-lightbox',
	'text' => elgg_echo('event_manager:editregistration:addfield'),
]);

$body = elgg_view_layout('content', [
	'filter' => '',
	'content' => $output,
	'title' => $title_text,
]);

elgg_set_ignore_access($ia);

echo elgg_view_page($title_text, $body);
