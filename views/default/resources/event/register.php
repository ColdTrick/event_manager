<?php

use Elgg\Exceptions\Http\BadRequestException;
use Elgg\Exceptions\HttpException;

$guid = (int) elgg_extract('guid', $vars);
elgg_entity_gatekeeper($guid, 'object', Event::SUBTYPE);

/* @var $entity \Event */
$event = get_entity($guid);

elgg_set_page_owner_guid($event->getContainerGUID());

if ((!$event->registration_needed && elgg_is_logged_in()) || (!elgg_is_logged_in() && !$event->register_nologin)) {
	$exception = new BadRequestException(elgg_echo('event_manager:registration:message:registrationnotneeded'));
	$exception->setRedirectUrl($event->getURL());
	throw $exception;
}

if (!elgg_is_logged_in()) {
	if (!$event->hasEventSpotsLeft() || !$event->hasSlotSpotsLeft()) {
		if ($event->waiting_list_enabled && $event->registration_needed && $event->openForRegistration()) {
			$exception = new HttpException();
			$exception->setRedirectUrl(elgg_generate_url('collection:object:event:waitinglist', ['guid' => $guid]));
			throw $exception;
		} else {
			$exception = new BadRequestException(elgg_echo('event_manager:event:rsvp:nospotsleft'));
			$exception->setRedirectUrl(REFERRER);
			throw $exception;
		}
	}
}

if (!$event->openForRegistration()) {
	$exception = new BadRequestException(elgg_echo('event_manager:event:rsvp:registration_ended'));
	$exception->setRedirectUrl($event->getURL());
	throw $exception;
}

$form_vars = ['id' => 'event_manager_event_register', 'name' => 'event_manager_event_register'];

$form = elgg_view_form('event_manager/event/register', $form_vars, ['entity' => $event]);

$title_text = elgg_echo('event_manager:registration:register:title');

elgg_push_entity_breadcrumbs($event);

$title = $title_text . " '{$event->getDisplayName()}'";

echo elgg_view_page($title, [
	'content' => $form,
	'filter' => false,
]);

elgg_clear_sticky_form('event_register');
