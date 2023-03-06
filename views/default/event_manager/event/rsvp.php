<?php

$event = elgg_extract('entity', $vars);
if (!$event instanceof \Event) {
	return;
}

if (elgg_in_context('widgets')) {
	return;
}

if (!elgg_extract('show_rsvp', $vars, true)) {
	return;
}

$menu_vars = $vars;
$menu_vars['class'] = ['elgg-menu-hz'];
$menu = elgg_view_menu('event:rsvp', $menu_vars);
if (empty($menu)) {
	return;
}

echo $menu;

if (elgg_extract('full_view', $vars)) {
	$registration = elgg_view('event_manager/event/registration', $vars);
	if ($registration) {
		echo '<div>' . $registration . '</div>';
	}
}
