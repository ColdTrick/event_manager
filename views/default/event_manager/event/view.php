<?php

$event = $vars["entity"];
$owner = $event->getOwnerEntity();
$event_details = "";

$owner_link = elgg_view('output/url', array(
	'href' => $owner->getURL(),
	'text' => $owner->name,
	'is_trusted' => true
));

$author_text = elgg_echo('byline', array($owner_link));
$date = elgg_view_friendly_time($event->time_created);

$subtitle = "$author_text $date";

// event details
if ($event->icontime) {
	$image = elgg_view('output/img', [
		'src' => $event->getIconURL(),
		'alt' => $event->title
	]);

	$event_details .= "<div class='float-alt pam elgg-border-plain'>";
	$event_details .= elgg_view("output/url", array(
		"href" => $event->getIconURL('master'),
		"text" => $image,
		"target" => "_blank",
		"class" => "elgg-lightbox-photo"
	));

	$event_details .= "</div>";
}
$event_details .= "<table class='event-manager-event-details'>";
if ($venue = $event->venue) {
	$event_details .= '<tr><td><label>' . elgg_echo('event_manager:edit:form:venue') . ':</label></td><td>' . $venue . '</td></tr>';
}
if ($location = $event->location) {
	$event_details .= '<tr><td><label>' . elgg_echo('event_manager:edit:form:location') . ':</label></td><td>';
	$event_details .= '<a href="' . elgg_get_site_url() . 'ajax/view/event_manager/event/maps/route?from=' . urlencode($location) . '" class="openRouteToEvent">' . $location . '</a>';
	$event_details .= '</td></tr>';
}

$event_details .= '<tr><td><label>' . elgg_echo('event_manager:edit:form:start') . ':</label></td><td>' . event_manager_format_date($event->start_day) . " " . date('H', $event->start_time) . ':' . date('i', $event->start_time) . '</td></tr>';

if ($event->end_ts) {
	$event_details .= '<tr><td><label>' . elgg_echo('event_manager:edit:form:end') . ':</label></td><td>' . event_manager_format_date($event->end_ts) . " " . date('H', $event->end_ts) . ':' . date('i', $event->end_ts) . '</td></tr>';
}

// optional end day
if ($organizer = $event->organizer) {
	$event_details .= '<tr><td><label>' . elgg_echo('event_manager:edit:form:organizer') . ':</label></td><td>' . $organizer . '</td></tr>';
}

if ($max_attendees = $event->max_attendees) {
	$event_details .= '<tr><td><label>' . elgg_echo('event_manager:edit:form:spots_left') . ':</label></td><td>';

	$spots_left = ($max_attendees - $event->countAttendees());
	if ($spots_left < 1) {
		$count_waitinglist = $event->countWaiters();
		if ($count_waitinglist > 0) {
			$event_details .= elgg_echo('event_manager:full') . ', ' . $count_waitinglist . ' ';
			if ($count_waitinglist == 1) {
				$event_details .= elgg_echo('event_manager:personwaitinglist');
			} else {
				$event_details .= elgg_echo('event_manager:peoplewaitinglist');
			}
		} else {
			$event_details .= elgg_echo('event_manager:full');
		}
	} else {
		$event_details .= $spots_left . " / " . $max_attendees;
	}

	$event_details .= '</td></tr>';
}

if ($description = $event->description) {
	$event_details .= '<tr><td><label>' . elgg_echo('description') . ':</label></td><td>' . elgg_view("output/longtext", array("value" => $description)) . '</td></tr>';
} elseif ($shortdescription = $event->shortdescription) {
	$event_details .= '<tr><td><label>' . elgg_echo('description') . ':</label></td><td>' . elgg_view("output/longtext", array("value" => $shortdescription)) . '</td></tr>';
}

if ($website = $event->website) {
	if (!preg_match('~^https?\://~i', $website)) {
		$website = "http://$website";
	}
	$event_details .= '<tr><td><label>' . elgg_echo('event_manager:edit:form:website') . ':</label></td><td>' . elgg_view("output/url", array("value" => $website)) . '</td></tr>';
}

if ($contact_details = $event->contact_details) {
	$event_details .= '<tr><td><label>' . elgg_echo('event_manager:edit:form:contact_details') . ':</label></td><td>' . elgg_view("output/text", array("value" => $contact_details)) . '</td></tr>';
}

if ($twitter_hash = $event->twitter_hash) {
	$event_details .= '<tr><td><label>' . elgg_echo('event_manager:edit:form:twitter_hash') . ':</label></td><td>' . elgg_view("output/url", array("value" => "http://twitter.com/search?q=" . urlencode($twitter_hash), "text" => elgg_view("output/text", array("value" => $twitter_hash)))) . '</td></tr>';
}

if ($fee = $event->fee) {
	$event_details .= '<tr><td><label>' . elgg_echo('event_manager:edit:form:fee') . ':</label></td><td>' . elgg_view("output/text", array("value" => $fee)) . '</td></tr>';
}

if ($region = $event->region) {
	$event_details .= '<tr><td><label>' . elgg_echo('event_manager:edit:form:region') . ':</label></td><td>' . $region . '</td></tr>';
}

if ($type = $event->event_type) {
	$event_details .= '<tr><td><label>' . elgg_echo('event_manager:edit:form:type') . ':</label></td><td>' . $type . '</td></tr>';
}

$files = $event->hasFiles();

if (!empty($files) || $event->canEdit()) {
	$user_path = "events/" . $event->getGUID() . "/files/";

	$event_details .= "<tr><td><label>" . elgg_echo("event_manager:edit:form:files") . ":</label></td><td>";
	$event_details .= "<ul>";
	if (!empty($files)) {
		foreach ($files as $file) {
			$file_link = elgg_view("output/url", array(
				"href" => "events/event/file/" . $event->getGUID() . "/" . $file->file,
				"text" => elgg_view_icon("download", "mrs") . $file->title
			));
			$event_details .= "<li>" . $file_link . "</li>";
		}
	}

	if ($event->canEdit()) {
		$add_link = elgg_view("output/url", array(
			"href" => "events/event/upload/" . $event->getGUID(),
			"text" => elgg_view_icon("round-plus", "mrs") . elgg_echo("event_manager:event:uploadfiles")
		));
		$event_details .= "<li>" . $add_link . "</li>";
	}

	$event_details .= "</ul>";
	$event_details .= "</td></tr>";
}

$event_details .= "</table>";

$body = elgg_view_module("main", "", $event_details);

$body .= elgg_view_module("main", "", elgg_view("event_manager/event/actions", $vars));

if ($event->show_attendees || $event->canEdit()) {
	$body .= elgg_view("event_manager/event/attendees", $vars);
}

if ($event->with_program) {
	$body .= elgg_view("event_manager/program/view", $vars);
}

if ($event->comments_on) {
	$body .= elgg_view_comments($event);
}

$entity_menu = elgg_view_menu("entity", array("entity" => $event, "sort_by" => "priority", "class" => "elgg-menu-hz", "handler" => "event"));

$params = array(
	'entity' => $event,
	'title' => false,
	'metadata' => $entity_menu,
	'subtitle' => $subtitle,
);
$params = $params + $vars;
$summary = elgg_view('object/elements/summary', $params);

echo elgg_view('object/elements/full', array(
	'summary' => $summary,
	'body' => $body,
));
