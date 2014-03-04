<?php

	// make sticky form
	elgg_make_sticky_form("event_unsubscribe");

	$guid = (int) get_input("guid");
	$email = get_input("email");
	
	$forward_url = REFERER;
	
	if (!empty($guid) && ($entity = get_entity($guid))) {
		if (!empty($email) && is_email_address($email)) {
			if (elgg_instanceof($entity, "object", Event::SUBTYPE)) {
				// try to find a registration
				$options = array(
					"type" => "object",
					"subtype" => EventRegistration::SUBTYPE,
					"owner_guid" => $entity->getGUID(),
					"limit" => 1,
					"metadata_name_value_pairs" => array(
						"name" => "email",
						"value" => $email,
						"case_sensitive" => false
					)
				);
				
				if ($registrations = elgg_get_entities_from_metadata($options)) {
					$registration = $registrations[0];
					
					// generate unsubscribe code
					$unsubscribe_code = event_manager_create_unsubscribe_code($registration, $entity);
					$unsubscribe_link = elgg_normalize_url("events/unsubscribe/confirm/" . $registration->getGUID() . "/" . $unsubscribe_code);
					
					// make a message with further instructions
					$subject = elgg_echo("event_manager:unsubscribe:confirm:subject", array($entity->title));
					$message = elgg_echo("event_manager:unsubscribe:confirm:message", array($registration->name, $entity->title, $entity->getURL(), $unsubscribe_link));
					
					// nice e-mail addresses
					$site = elgg_get_site_entity();
					if($site->email) {
						$from = $site->name . " <" . $site->email . ">";
					} else {
						$from = $site->name . " <noreply@" . get_site_domain($site->getGUID()) . ">";
					}
					
					$to = $registration->name . " <" . $registration->email . ">";
					
					if(elgg_send_email($from, $to, $subject, $message)){
						elgg_clear_sticky_form("event_unsubscribe");
						$forward_url = $entity->getURL();
						
						system_message(elgg_echo("event_manager:action:unsubscribe:success"));
					} else {
						register_error(elgg_echo("event_manager:action:unsubscribe:error:mail"));
					}
				} else {
					register_error(elgg_echo("event_manager:action:unsubscribe:error:no_registration"));
				}
			} else {
				register_error(elgg_echo("ClassException:ClassnameNotClass", array($guid, elgg_echo("item:object:" . Event::SUBTYPE))));
			}
		} else {
			register_error(elgg_echo("registration:notemail"));
		}
	} else {
		register_error(elgg_echo("InvalidParameterException:NoEntityFound"));
	}
	
	forward($forward_url);
	