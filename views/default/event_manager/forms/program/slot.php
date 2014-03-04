<?php

	$day_guid = $vars["day_guid"];
	$slot_guid = $vars["slot_guid"];
	
	if ($day_guid && ($entity = get_entity($day_guid))) {
		// assume new slot mode
		if (!($entity instanceof EventDay)) {
			unset($entity);
		}
		
		$start_time_hours = '';
		$start_time_minutes = '';
		
		$end_time_hours = '';
		$end_time_minutes = '';
	} elseif ($slot_guid && ($entity = get_entity($slot_guid))) {
		// assume slot edit mode
		if (!($entity instanceof EventSlot))	{
			unset($entity);
		}
	}
	
	
	if ($entity && $entity->canEdit()) {
	
		if ($entity instanceof EventSlot) {
			// assume slot edit mode
			$guid = $entity->getGUID();
			$title = $entity->title;
			$start_time = $entity->start_time;	
			$end_time = $entity->end_time;	
			$location = $entity->location;
			$max_attendees = $entity->max_attendees;
			$description = $entity->description;			
			$slot_set = $entity->slot_set;
			
			$start_time_hours = date('H', $entity->start_time);
			$start_time_minutes = date('i', $entity->start_time);	
		
			$end_time_hours = date('H', $entity->end_time);
			$end_time_minutes = date('i', $entity->end_time);			
			
			
			if ($related_days = $entity->getEntitiesFromRelationship('event_day_slot_relation', false, 1)) {
				$parent_guid = $related_days[0]->getGUID();
			}
		} else {
			// entity is a day
			$parent_guid = $entity->getGUID();
		}
		
		if(!isset($slot_set)){
			$slot_set = 0;
		}
		
		$form_body .= elgg_view('input/hidden', array('name' => 'guid', 'value' => $guid));
		$form_body .= elgg_view('input/hidden', array('name' => 'parent_guid', 'value' => $parent_guid));
		
		$form_body .= "<table><tr>";
		
		$form_body .= "<td><label>" . elgg_echo("title") . " *</label></td>";
		$form_body .= "<td>" . elgg_view('input/text', array('name' => 'title', 'value' => $title)) . "</td>";
		
		$form_body .= "</tr><tr>";
		
		$form_body .= "<td><label>" . elgg_echo("event_manager:edit:form:start_time") . " *</label></td>";
		$form_body .= "<td>";
		$form_body .= event_manager_get_form_pulldown_hours('start_time_hours', $start_time_hours);
		$form_body .= event_manager_get_form_pulldown_minutes('start_time_minutes', $start_time_minutes);
		$form_body .= "</td>";
		
		$form_body .= "</tr><tr>";
		
		$form_body .= "<td><label>" . elgg_echo("event_manager:edit:form:end_time") . " *</label></td>";
		$form_body .= "<td>";
		$form_body .= event_manager_get_form_pulldown_hours('end_time_hours', $end_time_hours);
		$form_body .= event_manager_get_form_pulldown_minutes('end_time_minutes', $end_time_minutes);
		$form_body .= "</td>";
		
		$form_body .= "</tr><tr>";
						
		$form_body .= "<td><label>" . elgg_echo("event_manager:edit:form:location") . "</label></td>";
		$form_body .= "<td>" . elgg_view('input/text', array('name' => 'location', 'value' => $location)) . "</td>";
		
		$form_body .= "</tr><tr>";
		
		$form_body .= "<td><label>" . elgg_echo("event_manager:edit:form:max_attendees") . "</label></td>";
		$form_body .= "<td>" . elgg_view('input/text', array('name' => 'max_attendees', 'value' => $max_attendees)) . "</td>";

		$form_body .= "</tr><tr>";
		
		$form_body .= "<td><label>" . elgg_echo("description") . "</label></td>";
		$form_body .= "<td>" .  elgg_view('input/plaintext', array('name' => 'description', 'value' => $description)) . "</td>";

		$form_body .= "</tr><tr>";
		
		$form_body .= "<td><label>" . elgg_echo("event_manager:edit:form:slot_set") . "</label></td>";
		$form_body .= "<td>"; 
		
		$form_body .= elgg_view("input/radio", array("name" => "slot_set", "options" => array(elgg_echo("event_manager:edit:form:slot_set:empty") => 0), "value" => $slot_set));
		
		// unique set names for this event
		$metadata = elgg_get_metadata(array(
					"type" => "object",
					"subtype" => EventSlot::SUBTYPE,
					"container_guids" => array($entity->container_guid),
					"metadata_names" => array("slot_set"),
					"limit" => false
				));
		
		$metadata_values = metadata_array_to_values($metadata);
		
		if(!empty($metadata_values)){
			$metadata_values = array_unique($metadata_values);
			foreach($metadata_values as $value){
				$form_body .= elgg_view("input/radio", array("name" => "slot_set", "options" => array($value => $value), "value" => $slot_set));
			}
		}
		
		// optionally add a new set
		$form_body .= elgg_view("input/text", array("id" => "event-manager-new-slot-set-name"));
		$form_body .= elgg_view("input/button", array("id" => "event-manager-new-slot-set-name-button", "value" => elgg_echo("event_manager:edit:form:slot_set:add"), "class" => "elgg-button-action"));
		
		$form_body .= "<div class='elgg-subtext'>" . elgg_echo("event_manager:edit:form:slot_set:description") . "</div>";
		$form_body .= "</td>";
		
		$form_body .= "</tr></table>";
		
		$form_body .= elgg_view('input/submit', array('value' => elgg_echo('submit')));
		
		$form = elgg_view('input/form', array(	'id' 	=> 'event_manager_form_program_slot', 
											'name' 	=> 'event_manager_form_program_slot', 
											'action' 		=> 'javascript:event_manager_program_add_slot($(\'#event_manager_form_program_slot\'))',
											'body' 			=> $form_body));
		
		echo elgg_view_module("info", elgg_echo("event_manager:form:program:slot"), $form, array("id" => "event-manager-program-slot-lightbox"));
	} else {
		echo elgg_echo("error");
	}