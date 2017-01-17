<?php

$event = elgg_extract('entity', $vars);
if (!($event instanceof Event)) {
	return;
}

if (!$event->openForRegistration()) {
	return;
}

$full_view = elgg_extract('full_view', $vars);

if (elgg_is_logged_in()) {
	$event_relationship_options = event_manager_event_get_relationship_options();
	
	$user_relation = $event->getRelationshipByUser();
	if ($user_relation) {
		if (!in_array($user_relation, $event_relationship_options)) {
			$event_relationship_options[] = $user_relation;
		}
	}
	
	$rsvp_options = [];
	
	if (in_array($user_relation, $event_relationship_options)) {
		$event_relationship_options = [$user_relation];
	}
	
	foreach ($event_relationship_options as $rel) {
		if (($rel == EVENT_MANAGER_RELATION_ATTENDING) || ($rel == EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST) || $event->$rel) {
			
			if ($rel == EVENT_MANAGER_RELATION_ATTENDING) {
				if (!$event->hasEventSpotsLeft() && !$event->waiting_list_enabled) {
					continue;
				}
			}
			
			if ($rel == $user_relation) {
				$icon = elgg_view_icon('checkmark', 'float-alt');
				$link = elgg_echo("event_manager:event:relationship:{$rel}:undo");
				
				$rsvp_options[] = [
					'attributes' => ['class' => 'selected'],
					'icon' => $icon,
					'text' => $link,
					'link_attributes' => [
						'is_action' => true,
						'href' => 'action/event_manager/event/rsvp?guid=' . $event->getGUID() . '&type=' . EVENT_MANAGER_RELATION_UNDO,
						'confirm' => true,
						'text' => $link,
					],
				];
			} else {
				if ($rel != EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST) {
					$icon = elgg_view_icon('checkmark-hover', 'float-alt');
					$link = [
						'is_action' => true,
						'href' => 'action/event_manager/event/rsvp?guid=' . $event->getGUID() . '&type=' . $rel,
						'text' => elgg_echo('event_manager:event:relationship:' . $rel),
					];
					
					$rsvp_options[] = [
						'icon' => $icon,
						'link_attributes' => $link,
					];
				}
			}
		}
	}
} else {
	if ($event->register_nologin) {
		$rsvp_options[] = [
			'link_attributes' => [
				'href' => '/events/event/register/' . $event->getGUID(),
			],
			'text' => elgg_echo('event_manager:event:register:register_link'),
		];
	} else {
		if ($full_view) {
			$rsvp_options[] = [
				'text' => elgg_echo('event_manager:event:register:log_in_first'),
				'textonly' => true,
			];
		}
	}
}

if (empty($rsvp_options)) {
	return;
}

$button_text = elgg_echo('event_manager:event:rsvp');

if ($full_view) {
	echo '<div class="clearfix">';
	echo '<div class="elgg-col elgg-col-1of5"><label>' . $button_text . ':</label></div>';
	echo '<div class="elgg-col elgg-col-4of5">';
	
	foreach ($rsvp_options as $option) {
		$attributes = (array) elgg_extract('link_attributes', $option, []);
		$attributes['class'] = ['elgg-button', 'mrs'];
		$text = elgg_extract('text', $option);
		$textonly = elgg_extract('textonly', $option, false);
		if ($textonly) {
			echo $text;
			continue;
		}
		
		if ($text) {
			$attributes['class'][] = 'elgg-button-submit';
			$attributes['text'] = $text;
		} else {
			$attributes['class'][] = 'elgg-button-action';
		}
		echo elgg_view('output/url', $attributes);
	}
	
	$registration = elgg_view('event_manager/event/registration', $vars);
	if ($registration) {
		echo '<div>' . $registration . '</div>';
	}
	
	echo '</div></div>';
} else {
	if (elgg_is_logged_in()) {
		if (count($rsvp_options) > 1) {
			if ($user_relation) {
				$button_text = "<b>$button_text</b>";
			}
			
			$button_text .= elgg_view_icon('caret-square-o-down', ['class' => 'mls']);
			
			echo elgg_format_element('span', ['class' => 'event_manager_event_actions link'], $button_text);
		
			$list_items = '';
			foreach ($rsvp_options as $option) {
				$text = elgg_view('output/url', elgg_extract('link_attributes', $option));
				$list_items .= elgg_format_element('li', elgg_extract('attributes', $option, []), elgg_extract('icon', $option) . $text);
			}
			echo elgg_format_element('ul', ['class' => 'event_manager_event_actions_drop_down'], $list_items);
		} else {
			foreach ($rsvp_options as $option) {
				$attributes = (array) elgg_extract('link_attributes', $option, []);
				$attributes['class'] = ['elgg-button', 'mrs'];
				$text = elgg_extract('text', $option);
				$textonly = elgg_extract('textonly', $option, false);
				if ($textonly) {
					echo $text;
					continue;
				}
				
				if ($text) {
					$attributes['class'][] = 'elgg-button-submit';
					$attributes['text'] = $text;
				} else {
					$attributes['class'][] = 'elgg-button-action';
				}
				echo elgg_view('output/url', $attributes);
			}
		}
	} else {
		foreach ($rsvp_options as $option) {
			$attributes = (array) elgg_extract('link_attributes', $option, []);
			$attributes['class'] = ['elgg-button'];
			$text = elgg_extract('text', $option);
			if ($text) {
				$attributes['class'][] = 'elgg-button-submit';
				$attributes['text'] = $text;
			} else {
				$attributes['class'][] = 'elgg-button-action';
			}
			echo elgg_view('output/url', $attributes);
		}
	}
}
