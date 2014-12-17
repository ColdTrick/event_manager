<?php
/**
 * Event
 *
 * @package EventManager
 *
 */
class Event extends ElggObject {
	const SUBTYPE = "event";

	/**
	 * initializes the default class attributes
	 *
	 * @return void
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();

		$this->attributes["subtype"] = self::SUBTYPE;
	}

	/**
	 * Returns URL to the entity
	 *
	 * @return string
	 *
	 * @see ElggEntity::getURL()
	 */
	public function getURL() {
		return elgg_get_site_url() . "events/event/view/" . $this->getGUID() . "/" . elgg_get_friendly_title($this->title);
	}

	/**
	 * Updates access of objects owned
	 *
	 * @param string $access_id new access id
	 *
	 * @return void
	 */
	public function setAccessToOwningObjects($access_id = null) {
		$this->setAccessToProgramEntities($access_id);
		$this->setAccessToRegistrationForm($access_id);
	}

	/**
	 * Updates access of Program Entities
	 *
	 * @param string $access_id new access id
	 *
	 * @return void
	 */
	public function setAccessToProgramEntities($access_id = null) {
		if ($access_id == null) {
			$access_id = $this->access_id;
		}

		$eventDays = $this->getEventDays();
		if (empty($eventDays)) {
			return;
		}

		foreach ($eventDays as $day) {
			$day->access_id = $access_id;
			$day->save();

			$eventSlots = $day->getEventSlots();
			if (empty($eventSlots)) {
				continue;
			}

			foreach ($eventSlots as $slot) {
				$slot->access_id = $access_id;
				$slot->save();
			}
		}
	}

	/**
	 * Updates access of Registration Form Entities
	 *
	 * @param string $access_id new access id
	 *
	 * @return void
	 */
	public function setAccessToRegistrationForm($access_id = null) {
		if ($access_id == null) {
			$access_id = $this->access_id;
		}

		$questions = $this->getRegistrationFormQuestions();
		if (empty($questions)) {
			return;
		}

		foreach ($questions as $question) {
			$question->access_id = $access_id;
			$question->save();
		}
	}

	/**
	 * Returns files for the event
	 *
	 * @return mixed|boolean
	 */
	public function hasFiles() {
		$files = json_decode($this->files);
		if (count($files) > 0) {
			return $files;
		}

		return false;
	}

	/**
	 * RSVP to the event
	 *
	 * @param string  $type          type of the rsvp
	 * @param number  $user_guid     guid of the user for whom the rsvp is changed
	 * @param boolean $reset_program does the program need a reset with this rsvp
	 * @param boolean $add_to_river  add an event to the river
	 *
	 * @return boolean
	 */
	public function rsvp($type = EVENT_MANAGER_RELATION_UNDO, $user_guid = 0, $reset_program = true, $add_to_river = true) {
		global $EVENT_MANAGER_UNDO_REGISTRATION;

		$user_guid = sanitise_int($user_guid, false);

		if (empty($user_guid)) {
			$user_guid = elgg_get_logged_in_user_guid();
		}

		if (empty($user_guid)) {
			return false;
		}

		// check if it is a user
		$user_entity = get_user($user_guid);

		$event_guid = $this->getGUID();

		// remove registrations
		if ($type == EVENT_MANAGER_RELATION_UNDO) {
			if (empty($user_entity)) {
				// make sure we can remove the registration object
				$EVENT_MANAGER_UNDO_REGISTRATION = true;
				$registration_object = get_entity($user_guid);
				$registration_object->delete();

				// undo overrides
				$EVENT_MANAGER_UNDO_REGISTRATION = false;
			} else {
				if ($reset_program) {
					if ($this->with_program) {
						$this->relateToAllSlots(false, $user_guid);
					}
					$this->clearRegistrations($user_guid);
				}
			}
		}

		// remove current relationships
		delete_data("DELETE FROM " . elgg_get_config("dbprefix") . "entity_relationships WHERE guid_one=$event_guid AND guid_two=$user_guid");

		// remove river events
		if ($user_entity) {
			$params = array(
				"subject_guid" => $user_guid,
				"object_guid" => $event_guid,
				"action_type" => "event_relationship"
			);
			elgg_delete_river($params);
		}

		// add the new relationship
		if ($type && ($type != EVENT_MANAGER_RELATION_UNDO) && (in_array($type, event_manager_event_get_relationship_options()))) {
			$result = $this->addRelationship($user_guid, $type);

			if ($result && $add_to_river) {
				if ($user_entity) {
					// add river events
					if (($type != "event_waitinglist") && ($type != "event_pending")) {
						elgg_create_river_item(array(
							'view' => 'river/event_relationship/create',
							'action_type' => 'event_relationship',
							'subject_guid' => $user_guid,
							'object_guid' => $event_guid,
						));
					}
				}
			}
		} else {

			if ($this->hasEventSpotsLeft() || $this->hasSlotSpotsLeft()) {
				$this->generateNewAttendee();
			}

			$result = true;
		}

		if ($this->notify_onsignup && ($type !== EVENT_MANAGER_RELATION_ATTENDING_PENDING)) {
			$this->notifyOnRsvp($type, $user_guid);
		}

		return $result;
	}

	/**
	 * Checks if the event has spots left
	 *
	 * @return boolean
	 */
	public function hasEventSpotsLeft() {
		$result = false;

		if ($this->max_attendees != '') {
			$attendees = $this->countAttendees();

			if (($this->max_attendees > $attendees)) {
				$result = true;
			}
		} else {
			$result = true;
		}

		return $result;
	}

	/**
	 * Checks if the event has slot spots left
	 *
	 * @return boolean
	 */
	public function hasSlotSpotsLeft() {
		$result = true;

		$slotsSpots = $this->countEventSlotSpots();

		if (($slotsSpots['total'] > 0) && ($slotsSpots['left'] < 1) && !$this->hasUnlimitedSpotSlots()) {
			$result = false;
		}

		return $result;
	}

	/**
	 * Checks if event is open for registration
	 *
	 * @return boolean
	 */
	public function openForRegistration() {
		$result = true;

		if ($this->registration_ended || (!empty($this->endregistration_day) && $this->endregistration_day < time())) {
			$result = false;
		}

		return $result;
	}

	/**
	 * Clears registrations for a given user
	 *
	 * @param string $user_guid guid of the user
	 *
	 * @return void
	 */
	public function clearRegistrations($user_guid = null) {
		if ($user_guid == null) {
			$user_guid = elgg_get_logged_in_user_guid();
		}

		if (empty($user_guid)) {
			return;
		}

		$questions = $this->getRegistrationFormQuestions();
		if (empty($questions)) {
			return;
		}

		foreach ($questions as $question) {
			$question->deleteAnswerFromUser($user_guid);
		}
	}

	/**
	 * Returns the registrations for a given user
	 *
	 * @param boolean $count     return count
	 * @param string  $user_guid guid of the user
	 *
	 * @return array|boolean
	 */
	public function getRegistrationsByUser($count = false, $user_guid = null) {
		if ($user_guid == null) {
			$user_guid = elgg_get_logged_in_user_guid();
		}

		$entities_options = array(
			"type" => "object",
			"subtype" => EventRegistration::SUBTYPE,
			"joins" => array("JOIN " . elgg_get_config("dbprefix") . "entity_relationships e_r ON e.guid = e_r.guid_two"),
			"wheres" => array("e_r.guid_one = " . $this->getGUID()),
			"owner_guids" => array($user_guid),
			"count" => $count
		);

		return elgg_get_entities($entities_options);
	}

	/**
	 * Returns registration data
	 *
	 * @param string  $guid guid of the user or registration object
	 * @param boolean $view adds a title to the output
	 *
	 * @return boolean|string
	 */
	public function getRegistrationData($guid = null, $view = false) {
		if ($guid == null) {
			$guid = elgg_get_logged_in_user_guid();
		}

		$entity = get_entity($guid);
		if (empty($entity)) {
			return false;
		}

		$questions = $this->getRegistrationFormQuestions();
		if (empty($questions)) {
			return false;
		}

		$registration_table = "";
		if ($view) {
			$registration_table .= "<h3>Information</h3>";
		}

		$registration_table .= "<table>";

		if (($guid != elgg_get_logged_in_user_guid()) && !($entity instanceof ElggUser)) {
			$registration_table .= "<tr><td><label>" . elgg_echo("user:name:label") . "</label></td><td>: " . $entity->name . "</td></tr>";
			$registration_table .= "<tr><td><label>" . elgg_echo("email") . "</label></td><td>: " . $entity->email . "</td></tr>";
		}

		foreach ($questions as $question) {
			$answer = $question->getAnswerFromUser($guid);

			$registration_table .= "<tr><td><label>" . $question->title . "</label></td><td>: " . $answer->value . "</td></tr>";
		}

		$registration_table .= "</table>";

		return elgg_view_module("main", "", $registration_table);
	}

	/**
	 * Checks if the event has a registration form
	 *
	 * @return boolean
	 */
	public function hasRegistrationForm() {
		if (!elgg_is_logged_in()) {
			return true;
		}

		if ($this->getRegistrationFormQuestions(true)) {
			return true;
		}

		if ($this->with_program && $this->getEventDays()) {
			return true;
		}

		return false;
	}

	/**
	 * Returns the program data for a user
	 *
	 * @param string $user_guid     guid of the entity
	 * @param string $participate   show the participation
	 * @param string $register_type type of the registration
	 *
	 * @return boolean|string
	 */
	public function getProgramData($user_guid = null, $participate = false, $register_type = "register") {
		if ($user_guid == null) {
			$user_guid = elgg_get_logged_in_user_guid();
		}

		if (!$this->getEventDays()) {
			return false;
		}

		$result = "";

		if (!$participate) {
			elgg_push_context("programmailview");

			$result .= elgg_view("event_manager/program/view", array("entity" => $this, "member" => $user_guid));

			elgg_pop_context();
		} else {
			$result .= elgg_view("event_manager/program/edit", array("entity" => $this, "register_type" => $register_type, "member" => $user_guid));
		}

		$result = elgg_view_module("main", "", $result);

		return $result;
	}

	/**
	 * Notifies an user of the RSVP
	 *
	 * @param string $type type of the RSVP
	 * @param string $to   guid of the user
	 *
	 * @return void
	 */
	public function notifyOnRsvp($type, $to = null) {

		$ia = elgg_set_ignore_access(true);

		if ($to == null) {
			$to = elgg_get_logged_in_user_guid();
		}

		$to_entity = get_entity($to);
		if (empty($to_entity)) {
			elgg_set_ignore_access($ia);
			return;
		}

		// can we make nice links in the emails
		$html_email_handler_enabled = elgg_is_active_plugin("html_email_handler");

		// do we have a registration link
		$registrationLink = "";
		$unsubscribeLink = "";

		if ($type == EVENT_MANAGER_RELATION_ATTENDING) {
			if ($this->registration_needed) {
				$link = elgg_get_site_url() . 'events/registration/view/?guid=' . $this->getGUID() . '&u_g=' . $to . '&k=' . md5($this->time_created . get_site_secret() . $to);

				$registrationLink = PHP_EOL . PHP_EOL;
				$registrationLink .= elgg_echo('event_manager:event:registration:notification:program:linktext');
				$registrationLink .= PHP_EOL . PHP_EOL;
				if ($html_email_handler_enabled) {
					$registrationLink .= elgg_view("output/url", array("text" => $link, "href" => $link));
				} else {
					$registrationLink .= $link;
				}
			}

			if ($this->register_nologin) {
				$link = elgg_get_site_url() . "events/unsubscribe/" . $this->getGUID() . "/" . elgg_get_friendly_title($this->title) . "?e=" . $to_entity->email;

				$unsubscribeLink = PHP_EOL . PHP_EOL;
				$unsubscribeLink .= elgg_echo('event_manager:event:registration:notification:unsubscribe:linktext');
				$unsubscribeLink .= PHP_EOL . PHP_EOL;
				if ($html_email_handler_enabled) {
					$unsubscribeLink .= elgg_view("output/url", array("text" => $link, "href" => $link));
				} else {
					$unsubscribeLink .= $link;
				}
			}
		}

		// make the event title for in the e-mail
		if ($html_email_handler_enabled) {
			$event_title_link = elgg_view("output/url", array("text" => $this->title, "href" => $this->getURL()));
		} else {
			$event_title_link = $this->title;
		}

		// notify the owner of the event
		$owner_subject = elgg_echo('event_manager:event:registration:notification:owner:subject');

		$owner_message = elgg_echo('event_manager:event:registration:notification:owner:text:' . $type, array(
			$this->getOwnerEntity()->name,
			$to_entity->name,
			$event_title_link
		));

		$owner_message .= $registrationLink;

		notify_user($this->getOwnerGUID(), $this->getGUID(), $owner_subject, $owner_message);

		// notify the attending user
		$user_subject = elgg_echo('event_manager:event:registration:notification:user:subject');

		$user_message = elgg_echo('event_manager:event:registration:notification:user:text:' . $type, array(
			$to_entity->name,
			$event_title_link
		));

		$user_message .= $registrationLink;
		$user_message .= $unsubscribeLink;

		if ($to_entity instanceof ElggUser) {
			// use notification system for real users
			notify_user($to, $this->getGUID(), $user_subject, $user_message);
		} else {
			// send e-mail for non users
			$to_email = $to_entity->name . "<" . $to_entity->email . ">";

			$site = elgg_get_site_entity($this->site_guid);
			if ($site->email) {
				if ($site->name) {
					$site_from = $site->name . " <" . $site->email . ">";
				} else {
					$site_from = $site->email;
				}
			} else {
				// no site email, so make one up
				if ($site->name) {
					$site_from = $site->name . " <noreply@" . get_site_domain($site->getGUID()) . ">";
				} else {
					$site_from = "noreply@" . get_site_domain($site->getGUID());
				}
			}

			elgg_send_email($site_from, $to_email, $user_subject, $user_message);
		}

		elgg_set_ignore_access($ia);
	}

	/**
	 * Relates a user to all the slots
	 *
	 * @param boolean $relate add or remove relationship
	 * @param string  $guid   guid of the entity
	 *
	 * @return void
	 */
	public function relateToAllSlots($relate = true, $guid = null) {
		if ($guid == null) {
			$guid = elgg_get_logged_in_user_guid();
		}

		if (empty($guid)) {
			return;
		}

		$entity = get_entity($guid);
		if (empty($entity)) {
			return;
		}

		$days = $this->getEventDays();
		if (empty($days)) {
			return;
		}

		foreach ($days as $day) {
			$slots = $day->getEventSlots();
			if (empty($slots)) {
				continue;
			}

			foreach ($slots as $slot) {
				if ($relate) {
					$entity->addRelationship($slot->getGUID(), EVENT_MANAGER_RELATION_SLOT_REGISTRATION);
				} else {
					delete_data("DELETE FROM " . elgg_get_config("dbprefix") . "entity_relationships WHERE guid_one='" . $guid . "' AND guid_two='" . $slot->getGUID() . "'");
				}
			}
		}
	}

	/**
	 * Counts the event slot spots
	 *
	 * @return array
	 */
	public function countEventSlotSpots() {
		$spots = array();

		$eventDays = $this->getEventDays();
		if (empty($eventDays)) {
			return array();
		}

		foreach ($eventDays as $eventDay) {
			$eventSlots = $eventDay->getEventSlots();
			if (empty($eventSlots)) {
				continue;
			}

			foreach ($eventSlots as $eventSlot) {
				$spots['total'] = ($spots['total'] + $eventSlot->max_attendees);
				$spots['left'] = ($spots['left'] + ($eventSlot->max_attendees - $eventSlot->countRegistrations()));
			}
		}

		return $spots;
	}

	/**
	 * Checks if the event has unlimited spot slots
	 *
	 * @return boolean
	 */
	public function hasUnlimitedSpotSlots() {
		$result = false;

		$eventDays = $this->getEventDays();
		if (empty($eventDays)) {
			return $result;
		}

		foreach ($eventDays as $eventDay) {
			$eventSlots = $eventDay->getEventSlots();
			if (empty($eventSlots)) {
				continue;
			}

			foreach ($eventSlots as $eventSlot) {
				if (empty($eventSlot->max_attendees)) {
					$result = true;
					break;
				}
			}
		}

		return $result;
	}

	/**
	 * Returns the location
	 *
	 * @param boolean $type should it be formatted
	 *
	 * @return string
	 */
	public function getEventLocation($type = false) {
		$location = $this->location;
		if ($type) {
			$location = str_replace(',', '<br />', $this->location);
		}

		return $location;
	}

	/**
	 * Returns the relationships between a user and the event
	 *
	 * @param string $user_guid guid of the user
	 *
	 * @return boolean|string
	 */
	public function getRelationshipByUser($user_guid = null) {
		$result = false;

		$user_guid = (int) $user_guid;
		if (empty($user_guid)) {
			$user_guid = elgg_get_logged_in_user_guid();
		}

		$event_guid = $this->getGUID();

		$row = get_data_row("SELECT * FROM " . elgg_get_config("dbprefix") . "entity_relationships WHERE guid_one=$event_guid AND guid_two=$user_guid");
		if ($row) {
			$result = $row->relationship;
		}

		return $result;
	}

	/**
	 * Returns all relationships with their count
	 *
	 * @param string $count return count or array
	 *
	 * @return boolean|array
	 */
	public function getRelationships($count = false) {
		$event_guid = $this->getGUID();

		if ($count) {
			$query = "SELECT relationship, count(*) as count FROM " . elgg_get_config("dbprefix") . "entity_relationships WHERE guid_one=$event_guid GROUP BY relationship ORDER BY relationship ASC";
		} else {
			$query = "SELECT * FROM " . elgg_get_config("dbprefix") . "entity_relationships WHERE guid_one=$event_guid ORDER BY relationship ASC";
		}

		$all_relations = get_data($query);
		if (empty($all_relations)) {
			return false;
		}

		$result = array("total" => 0);
		foreach ($all_relations as $row) {
			$relationship = $row->relationship;

			if ($count) {
				$result[$relationship] = $row->count;
				$result["total"] += $row->count;
			} else {
				if (!array_key_exists($relationship, $result)) {
					$result[$relationship] = array();
				}
				$result[$relationship][] = $row->guid_two;
			}
		}

		return $result;
	}

	/**
	 * Returns the registration form questions
	 *
	 * @param boolean $count return the count or the entities
	 *
	 * @return array|boolean
	 */
	public function getRegistrationFormQuestions($count = false) {
		$entities_options = array(
			"type" => "object",
			"subtype" => "eventregistrationquestion",
			"joins" => array(
				"JOIN " . elgg_get_config("dbprefix") . "metadata n_table_r on e.guid = n_table_r.entity_guid",
				"JOIN " . elgg_get_config("dbprefix") . "entity_relationships r on r.guid_one = e.guid"
			),
			"wheres" => array("r.guid_two = " . $this->getGUID(), "r.relationship = 'event_registrationquestion_relation'"),
			"order_by_metadata" => array("name" => "order", "direction" => "ASC", "as" => "integer"),
			"count" => $count,
			"limit" => false
		);

		return elgg_get_entities_from_metadata($entities_options);
	}

	/**
	 * Returns if a user is on the attendees list
	 *
	 * @param string $user_guid guid of the user
	 *
	 * @return ElggRelationship|boolean
	 */
	public function isAttending($user_guid = null) {
		if (empty($user_guid)) {
			$user_guid = elgg_get_logged_in_user_guid();
		}

		return check_entity_relationship($this->getGUID(), EVENT_MANAGER_RELATION_ATTENDING, $user_guid);
	}

	/**
	 * Returns if a user is on the waitinglist
	 *
	 * @param string $user_guid guid of the user
	 *
	 * @return ElggRelationship|boolean
	 */
	public function isWaiting($user_guid = null) {
		if (empty($user_guid)) {
			$user_guid = elgg_get_logged_in_user_guid();
		}

		return check_entity_relationship($this->getGUID(), EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST, $user_guid);
	}

	/**
	 * Returns the waiting users
	 *
	 * @return array|boolean
	 */
	public function getWaitingUsers() {
		$result = false;

		$query = "SELECT * FROM " . elgg_get_config("dbprefix") . "entity_relationships WHERE guid_one= '" . $this->getGUID() . "' AND relationship = '" . EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST . "' ORDER BY time_created ASC";

		if ($waiting_users = get_data($query)) {
			$result = array();
			foreach ($waiting_users as $user) {
				$result[] = get_entity($user->guid_two);
			}
		}

		return $result;
	}

	/**
	 * Returns the first waiting entity
	 *
	 * @return boolean|entity
	 */
	public function getFirstWaitingUser() {
		$result = false;

		$query = "SELECT * FROM " . elgg_get_config("dbprefix") . "entity_relationships WHERE guid_one= '" . $this->getGUID() . "' AND relationship = '" . EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST . "' ORDER BY time_created ASC LIMIT 1";

		$waiting_users = get_data($query);
		if (!empty($waiting_users)) {
			$result = get_entity($waiting_users[0]->guid_two);
		}

		return $result;
	}

	/**
	 * Generates a new attendee for this event
	 *
	 * @return boolean
	 */
	public function generateNewAttendee() {
		$waiting_user = $this->getFirstWaitingUser();
		if (empty($waiting_user)) {
			return false;
		}

		$result = false;
		$rsvp = false;

		if ($this->with_program) {
			$waiting_for_slots = $this->getRegisteredSlotsForEntity($waiting_user->getGUID(), EVENT_MANAGER_RELATION_SLOT_REGISTRATION_WAITINGLIST);
			if ($waiting_for_slots) {
				foreach ($waiting_for_slots as $slot) {
					if ($slot->hasSpotsLeft()) {
						$rsvp = true;

						$waiting_user->removeRelationship($slot->getGUID(), EVENT_MANAGER_RELATION_SLOT_REGISTRATION_WAITINGLIST);
						$waiting_user->addRelationship($slot->getGUID(), EVENT_MANAGER_RELATION_SLOT_REGISTRATION);
					}
				}
			} elseif ($this->hasEventSpotsLeft()) {
				// not waiting for slots and event has room
				$rsvp = true;
			}
		} elseif ($this->hasEventSpotsLeft()) {
			$rsvp = true;
		}

		if ($rsvp) {
			$this->rsvp(EVENT_MANAGER_RELATION_ATTENDING, $waiting_user->getGUID(), false);

			notify_user(elgg_get_logged_in_user_guid(),
						$this->getGUID(),
						elgg_echo("event_manager:event:registration:notification:user:subject"),
						elgg_echo("event_manager:event:registration:notification:user:text:event_spotfree", array(
							$waiting_user->name,
							$this->getURL(),
							$this->title
						)));

			$result = true;
		}

		return $result;
	}

	/**
	 * Return the registered slots for an entity
	 *
	 * @param string $guid              guid of the entity
	 * @param string $slot_relationship relationship
	 *
	 * @return array
	 */
	public function getRegisteredSlotsForEntity($guid, $slot_relationship) {
		$slots = array();

		$dbprefix = elgg_get_config("dbprefix");

		$data = get_data("SELECT slot.guid FROM " . $dbprefix . "entities AS slot
			INNER JOIN " . $dbprefix . "entities AS event ON event.guid = slot.owner_guid
			INNER JOIN " . $dbprefix . "entity_relationships AS slot_user_relation ON slot.guid = slot_user_relation.guid_two
			INNER JOIN " . $dbprefix . "entities AS entity ON entity.guid = slot_user_relation.guid_one
			WHERE entity.guid = $guid AND slot_user_relation.relationship='" . $slot_relationship . "'
		");

		foreach ($data as $slot) {
			$slots[] = get_entity($slot->guid);
		}

		return $slots;
	}

	/**
	 * Return the events icon url
	 *
	 * @param string $size size of the icon
	 *
	 * @return void|string
	 *
	 * @see ElggEntity::getIconURL()
	 */
	public function getIconURL($size = "medium") {
		$size = strtolower($size);

		$iconsizes = (array) elgg_get_config("icon_sizes");
		if (!array_key_exists($size, $iconsizes)) {
			$size = "medium";
		}

		$icontime = $this->icontime;
		if ($icontime) {
			return elgg_normalize_url("mod/event_manager/pages/event/thumbnail.php?icontime=" . $icontime . "&guid=" . $this->getOwnerGUID() . "&event_guid=" . $this->getGUID() . "&size=" . $size);
		}
	}

	/**
	 * Returns the days of this event
	 *
	 * @param string $order order
	 *
	 * @return boolean|array
	 */
	public function getEventDays($order = "ASC") {
		$entities_options = array(
			"type" => "object",
			"subtype" => EventDay::SUBTYPE,
			"relationship_guid" => $this->getGUID(),
			"relationship" => "event_day_relation",
			"inverse_relationship" => true,
			"order_by_metadata" => array(
				"name" => "date",
				"direction" => $order
			),
			"limit" => false
		);

		return elgg_get_entities_from_relationship($entities_options);
	}

	/**
	 * Checks if a user is registered
	 *
	 * @param string $userid guid of the owning entity
	 * @param string $count  check based on count
	 *
	 * @return boolean|ElggEntity
	 */
	public function isUserRegistered($userid = null, $count = true) {
		if ($userid == null) {
			$userid = elgg_get_logged_in_user_guid();
		}

		$entities_options = array(
			"type" => "object",
			"subtype" => EventRegistration::SUBTYPE,
			"joins" => array("JOIN " . elgg_get_config("dbprefix") . "entity_relationships e_r ON e.guid = e_r.guid_two"),
			"wheres" => array("e_r.guid_one = " . $this->getGUID()),
			"count" => $count,
			"owner_guids" => array($userid)
		);

		$entities = elgg_get_entities_from_relationship($entities_options);

		if ($count) {
			if ($entities > 0) {
				return true;
			}
			return false;
		} else {
			return $entities[0];
		}
	}

	/**
	 * Counts the attendees
	 *
	 * @return boolean|int
	 */
	public function countAttendees() {
		$old_ia = elgg_set_ignore_access(true);

		$entities = elgg_get_entities_from_relationship(array(
			"relationship" => EVENT_MANAGER_RELATION_ATTENDING,
			"relationship_guid" => $this->getGUID(),
			"inverse_relationship" => false,
			"count" => true,
			"site_guids" => false
		));

		elgg_set_ignore_access($old_ia);

		return $entities;
	}

	/**
	 * Counts the waiters
	 *
	 * @return boolean|int
	 */
	public function countWaiters() {
		$old_ia = elgg_set_ignore_access(true);

		$entities = elgg_get_entities_from_relationship(array(
			"relationship" => EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST,
			"relationship_guid" => $this->getGUID(),
			"inverse_relationship" => false,
			"count" => true,
			"site_guids" => false
		));

		elgg_set_ignore_access($old_ia);

		return $entities;
	}

	/**
	 * Returns the attendees based on a relationship
	 *
	 * @param string $rel relationship
	 *
	 * @return boolean|array
	 */
	public function exportAttendees($rel = EVENT_MANAGER_RELATION_ATTENDING) {

		$old_ia = elgg_set_ignore_access(true);

		$entities = elgg_get_entities_from_relationship(array(
			"relationship" => $rel,
			"relationship_guid" => $this->getGUID(),
			"inverse_relationship" => false,
			"site_guids" => false,
			"limit" => false
		));

		elgg_set_ignore_access($old_ia);

		return $entities;
	}
}
