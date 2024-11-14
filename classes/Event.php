<?php

use Elgg\Database\QueryBuilder;
use Elgg\Database\Clauses\JoinClause;
use Elgg\Database\Delete;
use Elgg\Database\Select;

/**
 * Event
 *
 * @property bool   $comments_on               comments enabled
 * @property string $contact_details           contact details
 * @property int[]  $contact_guids             additional contact persons
 * @property int    $endregistration_day       date until which registration is allowed
 * @property string $event_type                admin controlled event type
 * @property int    $event_start               start date
 * @property int    $event_end                 end date
 * @property string $fee                       fee for the event
 * @property string $fee_details               information about the fee
 * @property bool   $notify_onsignup           event owner receives notification on new registration
 * @property bool   $notify_onsignup_organizer event organizers receive notification on new registration
 * @property bool   $notify_onsignup_contact   event contacts receive notification on new registration
 * @property string $organizer                 organizer
 * @property int[]  $organizer_guids           additional organizers
 * @property string $region                    admin controlled event region
 * @property string $registration_completed    text to show after registration is completed
 * @property bool   $registration_ended        is registration closed
 * @property bool   $registration_needed       is registration needed
 * @property bool   $register_nologin          registration is enabled for non site users
 * @property string $shortdescription          short event description
 * @property bool   $show_attendees            show attendees to users
 * @property string $venue                     venue of the event
 * @property bool   $waiting_list_enabled      is a waitling list enabled
 * @property string $website                   event website
 * @property bool   $with_program              has a program
 */
class Event extends \ElggObject {
	
	const SUBTYPE = 'event';

	/**
	 * {@inheritdoc}
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();

		$this->attributes['subtype'] = self::SUBTYPE;
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function update(): bool {
		if (!parent::update()) {
			return false;
		}
		
		$fillup = false;
		if ($this->with_program && $this->hasSlotSpotsLeft()) {
			$fillup = true;
		} elseif (!$this->with_program && $this->hasEventSpotsLeft()) {
			$fillup = true;
		}
		
		if ($fillup) {
			while ($this->generateNewAttendee()) {
				continue;
			}
		}
		
		return true;
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function prepareObject(\Elgg\Export\Entity $object) {
		$object = parent::prepareObject($object);
		
		$object->starttime = date('c', $this->getStartTimestamp());
		$object->endtime = date('c', $this->getEndTimestamp());
		$object->location = $this->location;
		$object->region = $this->region;
		$object->event_type = $this->event_type;
		$object->short_description = $this->short_description;
		$object->venue = $this->venue;
		$object->contact_details = $this->contact_details;
		$object->website = $this->website;
		$object->organizer = $this->organizer;
		$object->{'geo:lat'} = $this->getLatitude();
		$object->{'geo:long'} = $this->getLongitude();
		
		return $object;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function canComment(int $user_guid = 0): bool {
		if (!$this->comments_on) {
			return false;
		}
		
		return parent::canComment($user_guid);
	}

	/**
	 * Returns excerpt based on shortdescription and falls back to long description
	 *
	 * @param int $limit (optional) limited amount of characters
	 *
	 * @return string
	 */
	public function getExcerpt(int $limit = 250): string {
		$result = $this->shortdescription ?: $this->description;
		
		return elgg_get_excerpt((string) $result, $limit);
	}
	
	/**
	 * Returns the timestamp for the start of the event
	 *
	 * @return null|int the timestamp
	 */
	public function getStartTimestamp(): ?int {
		return $this->event_start;
	}
	
	/**
	 * Returns the timestamp for the end of the event
	 *
	 * @return null|int the timestamp
	 */
	public function getEndTimestamp(): ?int {
		return $this->event_end;
	}
	
	/**
	 * Returns the startdate and time for the event formatted as ISO-8601
	 *
	 * @param string $format provide a format for the date
	 *
	 * @see https://en.wikipedia.org/wiki/ISO_8601
	 *
	 * @return string a formatted date string
	 */
	public function getStartDate(string $format = 'c'): string {
		return gmdate($format, $this->getStartTimestamp());
	}
	
	/**
	 * Returns the startdate and time for the event formatted as ISO-8601
	 *
	 * @param string $format provide a format for the date
	 *
	 * @see https://en.wikipedia.org/wiki/ISO_8601
	 *
	 * @return string a formatted date string
	 */
	public function getEndDate(string $format = 'c'): string {
		return gmdate($format, $this->getEndTimestamp());
	}
	
	/**
	 * Returns if the event is spanning multiple days
	 *
	 * @return bool is it a multiday event
	 */
	public function isMultiDayEvent(): bool {
		$start = $this->getStartDate('d-m-Y');
		$end = $this->getEndDate('d-m-Y');
		
		return $start !== $end;
	}

	/**
	 * Correctly sets the max attendees
	 *
	 * @param int $max the max attendees
	 *
	 * @return void
	 */
	public function setMaxAttendees(int $max): void {
		if ($max < 1) {
			$max = null;
		}
		
		$this->max_attendees = $max;
	}

	/**
	 * Returns if event has files
	 *
	 * @return bool
	 */
	public function hasFiles(): bool {
		return !empty($this->getFiles());
	}
	
	/**
	 * Returns an array of files
	 *
	 * @return array
	 */
	public function getFiles(): array {
		if (empty($this->files)) {
			return [];
		}
		
		return (array) json_decode($this->files) ?: [];
	}

	/**
	 * RSVP to the event
	 *
	 * @param string  $type           type of the rsvp
	 * @param int     $user_guid      guid of the user for whom the rsvp is changed
	 * @param boolean $reset_program  does the program need a reset with this rsvp
	 * @param boolean $add_to_river   add an event to the river
	 * @param boolean $notify_on_rsvp control if a (potential)notification is send
	 *
	 * @return boolean
	 */
	public function rsvp(string $type = EVENT_MANAGER_RELATION_UNDO, int $user_guid = 0, bool $reset_program = true, bool $add_to_river = true, bool $notify_on_rsvp = true): bool {
		
		$user_guid = $user_guid ?: elgg_get_logged_in_user_guid();
		if (empty($user_guid)) {
			return false;
		}

		// check if it is a user
		$user_entity = get_user($user_guid);

		$event_guid = $this->guid;

		// remove registrations
		if ($type == EVENT_MANAGER_RELATION_UNDO) {
			$this->undoRegistration($user_guid, $reset_program);
		}

		// remove current relationships
		$qb = Delete::fromTable('entity_relationships');
		$qb->where($qb->compare('guid_one', '=', $event_guid, ELGG_VALUE_INTEGER))
			->andWhere($qb->compare('guid_two', '=', $user_guid, ELGG_VALUE_INTEGER));
		
		$qb->execute();
		
		// remove river events
		if ($user_entity) {
			elgg_delete_river([
				'subject_guid' => $user_guid,
				'object_guid' => $event_guid,
				'action_type' => 'event_relationship',
			]);
		}

		// add the new relationship
		if ($type && ($type != EVENT_MANAGER_RELATION_UNDO) && (in_array($type, event_manager_event_get_relationship_options()))) {
			$result = $this->addRelationship($user_guid, $type);

			if ($result && $add_to_river && $user_entity) {
				// add river events
				if (($type !== 'event_waitinglist') && ($type !== 'event_pending')) {
					elgg_create_river_item([
						'view' => 'river/event_relationship/create',
						'action_type' => 'event_relationship',
						'subject_guid' => $user_guid,
						'object_guid' => $event_guid,
					]);
				}
			}
		} else {
			if ($this->hasEventSpotsLeft() || $this->hasSlotSpotsLeft()) {
				$this->generateNewAttendee();
			}

			$result = true;
		}

		if ($notify_on_rsvp) {
			$this->notifyOnRsvp($type, $user_guid);
		}
		
		return $result;
	}
	
	/**
	 * Undo a registration for a given user
	 *
	 * @param int  $user_guid     the user to undo for
	 * @param bool $reset_program reset the event program
	 *
	 * @return void
	 */
	protected function undoRegistration(int $user_guid, bool $reset_program): void {
		global $EVENT_MANAGER_UNDO_REGISTRATION;
		
		$user_entity = get_user($user_guid);
		
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

	/**
	 * Checks if the event has spots left
	 *
	 * @return boolean
	 */
	public function hasEventSpotsLeft(): bool {
		if ($this->max_attendees != '') {
			$attendees = $this->countAttendees();

			if ($this->max_attendees > $attendees) {
				return true;
			}
		} else {
			return true;
		}

		return false;
	}

	/**
	 * Checks if the event has slot spots left
	 *
	 * @return boolean
	 */
	public function hasSlotSpotsLeft(): bool {
		$slotsSpots = $this->countEventSlotSpots();
		
		if ((elgg_extract('total', $slotsSpots) > 0) && (elgg_extract('left', $slotsSpots) < 1) && !$this->hasUnlimitedSpotSlots()) {
			return false;
		}

		return true;
	}

	/**
	 * Checks if event is open for registration
	 *
	 * @return boolean
	 */
	public function openForRegistration(): bool {
		if ($this->registration_ended || (!empty($this->endregistration_day) && $this->endregistration_day < time())) {
			return false;
		}

		return true;
	}

	/**
	 * Clears registrations for a given user
	 *
	 * @param int $user_guid guid of the user
	 *
	 * @return void
	 */
	public function clearRegistrations(int $user_guid = 0): void {
		$user_guid = $user_guid ?: elgg_get_logged_in_user_guid();
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
	 * Checks if the event has a registration form
	 *
	 * @return boolean
	 */
	public function hasRegistrationForm(): bool {
		if (!elgg_is_logged_in()) {
			return true;
		}

		if ($this->getRegistrationFormQuestions(true)) {
			return true;
		}

		if ($this->with_program && $this->hasEventDays()) {
			return true;
		}

		return false;
	}
	
	/**
	 * Generates a day and a slot if there is none
	 *
	 * @return void
	 */
	public function generateInitialProgramData(): void {
	
		if (empty($this->with_program)) {
			return;
		}
		
		if ($this->hasEventDays()) {
			return;
		}
		
		$day = new \ColdTrick\EventManager\Event\Day();
		$day->title = elgg_echo('event_manager:event:initial:day:title');
		$day->container_guid = $this->guid;
		$day->owner_guid = $this->guid;
		$day->access_id = $this->access_id;
		$day->save();
		$day->date = $this->getStartTimestamp();
		$day->addRelationship($this->guid, 'event_day_relation');
	
		$slot = new \ColdTrick\EventManager\Event\Slot();
		$slot->title = elgg_echo('event_manager:event:initial:slot:title');
		$slot->description = elgg_echo('event_manager:event:initial:slot:description');
		$slot->container_guid = $this->container_guid;
		$slot->owner_guid = $this->owner_guid;
		$slot->access_id = $this->access_id;
		$slot->save();
	
		$slot->location = $this->location;
		$slot->start_time = mktime('08', '00', 1, 0, 0, 0);
		$slot->end_time = mktime('09', '00', 1, 0, 0, 0);
		$slot->addRelationship($day->guid, 'event_day_slot_relation');
	}

	/**
	 * Returns the program data for a user
	 *
	 * @param int    $user_guid     guid of the entity
	 * @param bool   $participate   show the participation
	 * @param string $register_type type of the registration
	 *
	 * @return string
	 */
	public function getProgramData(int $user_guid = 0, bool $participate = false, string $register_type = 'register'): string {
		$user_guid = $user_guid ?: elgg_get_logged_in_user_guid();
		
		if (!$this->hasEventDays()) {
			return '';
		}
		
		if (!$participate) {
			$result = elgg_view('event_manager/program/view', [
				'entity' => $this,
				'member' => $user_guid,
				'show_owner_actions' => false,
			]);
		} else {
			$result = elgg_view('event_manager/program/edit', [
				'entity' => $this,
				'register_type' => $register_type,
				'member' => $user_guid,
			]);
		}

		return elgg_view_module('main', '', $result);
	}

	/**
	 * Notifies a user of the RSVP
	 *
	 * @param string $type type of the RSVP
	 * @param int    $to   guid of the user
	 *
	 * @return void
	 */
	protected function notifyOnRsvp(string $type, int $to = 0): void {

		if ($type === EVENT_MANAGER_RELATION_ATTENDING_PENDING) {
			return;
		}
		
		elgg_call(ELGG_IGNORE_ACCESS, function() use ($type, $to) {
			$to = $to ?: elgg_get_logged_in_user_guid();
			
			$to_entity = get_entity($to);
			if (empty($to_entity)) {
				return;
			}
	
			// can we make nice links in the emails
			$html_email_enabled = elgg_get_config('email_html_part');
	
			// do we have a registration link
			$registrationLink = '';
			$unsubscribeLink = '';
			$addevent = '';
	
			if ($type == EVENT_MANAGER_RELATION_ATTENDING) {
				if ($this->registration_needed) {
					$link = elgg_generate_url('view:object:eventregistration', [
						'guid' => $this->guid,
						'u_g' => $to,
						'k' => elgg_build_hmac([$this->time_created, $to])->getToken(),
					]);
					$registrationLink = PHP_EOL . PHP_EOL;
					$registrationLink .= elgg_echo('event_manager:event:registration:notification:program:linktext');
					$registrationLink .= PHP_EOL . PHP_EOL;
					$registrationLink .= ($html_email_enabled) ? elgg_view_url($link, $link) : $link;
				}
	
				if ($this->register_nologin && ($to_entity instanceof \EventRegistration)) {
					$link = elgg_generate_url('default:object:event:unsubscribe:request', [
						'guid' => $this->guid,
						'e' => $to_entity->email,
					]);
	
					$unsubscribeLink = PHP_EOL . PHP_EOL;
					$unsubscribeLink .= elgg_echo('event_manager:event:registration:notification:unsubscribe:linktext');
					$unsubscribeLink .= PHP_EOL . PHP_EOL;
					$unsubscribeLink .= ($html_email_enabled) ? elgg_view_url($link, $link) : $link;
				}
				
				if ($html_email_enabled) {
					// add addthisevent banners in footer
					$addevent = elgg_view('event_manager/addthisevent/email', ['entity' => $this]);
				}
			}
	
			// make the event title for in the e-mail
			$event_title_link = ($html_email_enabled) ? elgg_view_entity_url($this) : $this->getDisplayName();
			
			// notify the owner of the event
			$this->notifyOwnerOnRSVP($type, $to_entity, $event_title_link, $registrationLink);
	
			// notify the attending user
			$user_subject = elgg_echo('event_manager:event:registration:notification:user:subject', [$this->getDisplayName()]);
			if ($type === EVENT_MANAGER_RELATION_UNDO) {
				$user_subject = elgg_echo("event_manager:event:registration:notification:user:subject:{$type}", [$this->getDisplayName()]);
			}
	
			$user_message = elgg_echo('event_manager:event:registration:notification:user:text:' . $type, [$event_title_link]);
			
			if ($type == EVENT_MANAGER_RELATION_ATTENDING) {
				$completed_text = elgg_strip_tags((string) $this->registration_completed, '<a>');
				if (!empty($completed_text)) {
					$completed_text = str_ireplace('[NAME]', $to_entity->getDisplayName(), $completed_text);
					$completed_text = str_ireplace('[EVENT]', $this->getDisplayName(), $completed_text);
					
					$user_message .= PHP_EOL . PHP_EOL . $completed_text;
				}
			}
			
			$user_message .= $registrationLink . $addevent . $unsubscribeLink;
			
			$attachment = [];
			if ($type == EVENT_MANAGER_RELATION_ATTENDING) {
				// prepare attachment url
				$description = '';
				if (!empty($this->location)) {
					// add venue to description
					$description .= $this->venue . PHP_EOL;
				}
				
				// removing HTML and shorter because of URL length limitations
				$description .= $this->getExcerpt(500) . PHP_EOL . PHP_EOL;
				$description .= $this->getURL();
				
				$attachment_url = elgg_http_add_url_query_elements('https://www.addevent.com/dir/', [
					'client' => elgg_get_plugin_setting('add_event_license', 'event_manager'),
					'service' => 'stream',
					
					'start' => $this->getStartDate('d/m/Y H:i:00'),
					'end' => $this->getEndDate('d/m/Y H:i:00'),
					'title' => html_entity_decode($this->getDisplayName()),
					'description' => $description,
					'location' => $this->location ?: $this->venue,
					'date_format' => 'DD/MM/YYYY',
				]);
				
				$attachment_contents = file_get_contents($attachment_url);
				if (!empty($attachment_contents)) {
					$attachment['filename'] = 'event.ics';
					$attachment['type'] = 'text/calendar';
					$attachment['content'] = $attachment_contents;
				}
			}
			
			if ($to_entity instanceof ElggUser) {
				// use notification system for real users
				$summary = elgg_echo('event_manager:event:registration:notification:user:summary:' . $type, [$this->getDisplayName()]);
				
				// set params for site notifications
				$params = [
					'summary' => $summary,
					'object' => $this,
					'action' => 'rsvp',
				];
				
				if (!empty($attachment)) {
					$params['attachments'] = [$attachment];
				}
				
				notify_user($to, $this->getOwnerGUID(), $user_subject, $user_message, $params);
			} else {
				// send e-mail for non users
				$options = [
					'to' => $to_entity,
					'subject' => $user_subject,
					'body' => $user_message,
				];
				
				if (!empty($attachment)) {
					$options['attachments'] = [$attachment];
				}
				
				elgg_send_email(\Elgg\Email::factory($options));
			}
		});
	}
		
	/**
	 * Notifies an owner of the event
	 *
	 * @param string     $type              type of the RSVP
	 * @param ElggEntity $rsvp_entity       registering entity
	 * @param string     $event_title_link  title of the event
	 * @param string     $registration_link registration link of the event
	 *
	 * @return void
	 */
	protected function notifyOwnerOnRSVP(string $type, \ElggEntity $rsvp_entity, string $event_title_link, string $registration_link = ''): void {
		
		if (!$this->notify_onsignup) {
			return;
		}
		
		// set params for site notifications
		$params = [
			'summary' => elgg_echo('event_manager:event:registration:notification:owner:summary:' . $type, [
				$rsvp_entity->getDisplayName(),
				$this->getDisplayName(),
			]),
			'object' => $this,
			'action' => 'rsvp_owner',
		];
		
		$owner_subject = elgg_echo('event_manager:event:registration:notification:owner:subject', [$this->getDisplayName()]);
		if ($type === EVENT_MANAGER_RELATION_UNDO) {
			$owner_subject = elgg_echo("event_manager:event:registration:notification:owner:subject:{$type}", [$this->getDisplayName()]);
		}

		$recipients = [
			$this->owner_guid => $this->getOwnerEntity(),
		];
		
		if ($this->notify_onsignup_contact) {
			foreach ($this->getContacts() as $recipient) {
				$recipients[$recipient->guid] = $recipient;
			}
		}
		
		if ($this->notify_onsignup_organizer) {
			foreach ($this->getOrganizers() as $recipient) {
				$recipients[$recipient->guid] = $recipient;
			}
		}
		
		foreach ($recipients as $user) {
			$owner_message = elgg_echo('event_manager:event:registration:notification:owner:text:' . $type, [
				$rsvp_entity->getDisplayName(),
				$event_title_link,
			]) . $registration_link;
			
			notify_user($user->guid, $rsvp_entity->guid, $owner_subject, $owner_message, $params);
		}
	}

	/**
	 * Relates a user to all the slots
	 *
	 * @param boolean $relate add or remove relationship
	 * @param int     $guid   guid of the entity
	 *
	 * @return void
	 */
	public function relateToAllSlots(bool $relate = true, int $guid = 0): void {
		$guid = $guid ?: elgg_get_logged_in_user_guid();
		
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
					$entity->addRelationship($slot->guid, EVENT_MANAGER_RELATION_SLOT_REGISTRATION);
				} else {
					$qb = Delete::fromTable('entity_relationships');
					$qb->where($qb->compare('guid_one', '=', $guid, ELGG_VALUE_INTEGER))
						->andWhere($qb->compare('guid_two', '=', $slot->guid, ELGG_VALUE_INTEGER));
					
					$qb->execute();
				}
			}
		}
	}

	/**
	 * Counts the event slot spots
	 *
	 * @return array
	 */
	protected function countEventSlotSpots(): array {
		$eventDays = $this->getEventDays();
		if (empty($eventDays)) {
			return [];
		}

		$spots = [
			'total' => 0,
			'left' => 0,
		];
		
		foreach ($eventDays as $eventDay) {
			$eventSlots = $eventDay->getEventSlots();
			if (empty($eventSlots)) {
				continue;
			}

			foreach ($eventSlots as $eventSlot) {
				$max_attendees = (int) $eventSlot->max_attendees;
				
				$spots['total'] = ($spots['total'] + $max_attendees);
				$spots['left'] = ($spots['left'] + ($max_attendees - $eventSlot->countRegistrations()));
			}
		}

		return $spots;
	}

	/**
	 * Checks if the event has unlimited spot slots
	 *
	 * @return boolean
	 */
	protected function hasUnlimitedSpotSlots(): bool {
		$eventDays = $this->getEventDays();
		if (empty($eventDays)) {
			return false;
		}

		foreach ($eventDays as $eventDay) {
			$eventSlots = $eventDay->getEventSlots();
			if (empty($eventSlots)) {
				continue;
			}

			foreach ($eventSlots as $eventSlot) {
				if (empty($eventSlot->max_attendees)) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Returns the relationships between a user and the event
	 *
	 * @param int $user_guid guid of the user
	 *
	 * @return null|string
	 */
	public function getRelationshipByUser(int $user_guid = 0): ?string {
		
		$user_guid = $user_guid ?: elgg_get_logged_in_user_guid();
		
		$qb = Select::fromTable('entity_relationships');
		$qb->select('relationship');
		$qb->where($qb->compare('guid_one', '=', $this->guid, ELGG_VALUE_INTEGER))
			->andWhere($qb->compare('guid_two', '=', $user_guid, ELGG_VALUE_INTEGER));

		$row = elgg()->db->getDataRow($qb);
		
		return $row ? $row->relationship : null;
	}

	/**
	 * Returns all relationships with their count
	 *
	 * @param bool   $count return count or array
	 * @param string $order order of timecreated sorting
	 *
	 * @return array
	 */
	public function getRelationships(bool $count = false, string $order = 'ASC'): array {
		$event_guid = $this->guid;

		$qb = Select::fromTable('entity_relationships');
		$qb->where($qb->compare('guid_one', '=', $event_guid, ELGG_VALUE_INTEGER));
		$qb->orderBy('relationship', 'ASC');
		
		if ($count) {
			$qb->select('relationship');
			$qb->addSelect('count(*) AS count');
			$qb->groupBy('relationship');
		} else {
			if (!in_array($order, ['ASC', 'DESC'])) {
				$order = 'ASC';
			}
			
			$qb->select('*');
			$qb->addOrderBy('time_created', $order);
		}

		$all_relations = elgg()->db->getData($qb);
		if (empty($all_relations)) {
			return [];
		}

		$result = [
			'total' => 0,
		];
		foreach ($all_relations as $row) {
			$relationship = $row->relationship;

			if ($count) {
				$result[$relationship] = $row->count;
				$result['total'] += $row->count;
			} else {
				if (!array_key_exists($relationship, $result)) {
					$result[$relationship] = [];
				}
				
				$result[$relationship][] = $row->guid_two;
			}
		}

		return $result;
	}
	
	/**
	 * Returns the supported relationships for this event (primarily used for presentations purpose)
	 *
	 * @return string[]
	 */
	public function getSupportedRelationships(): array {
		$relationships = [
			EVENT_MANAGER_RELATION_ATTENDING,
		];
		
		if (elgg_get_plugin_setting('rsvp_interested', 'event_manager') !== 'no') {
			$relationships[] = EVENT_MANAGER_RELATION_INTERESTED;
		}
		
		if ($this->canEdit()) {
			if ($this->waiting_list_enabled) {
				$relationships[] = EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST;
			}
			
			if ($this->register_nologin) {
				$relationships[] = EVENT_MANAGER_RELATION_ATTENDING_PENDING;
			}
		}
		
		$result = [];
		foreach ($relationships as $rel) {
			$result[$rel] = elgg_echo("event_manager:event:relationship:{$rel}:label");
		}
		
		return $result;
	}

	/**
	 * Returns the registration form questions
	 *
	 * @param boolean $count return the count or the entities
	 *
	 * @return \ElggEntity[]|int
	 */
	public function getRegistrationFormQuestions(bool $count = false): int|array {

		$event_guid = $this->guid;
		
		$on_object = function (QueryBuilder $qb, $joined_alias, $main_alias) {
			return $qb->compare("{$joined_alias}.entity_guid", '=', "{$main_alias}.guid");
		};
		$on_relationship = function (QueryBuilder $qb, $joined_alias, $main_alias) {
			return $qb->compare("{$joined_alias}.guid_one", '=', "{$main_alias}.guid");
		};
		
		return elgg_get_entities([
			'type' => 'object',
			'subtype' => 'eventregistrationquestion',
			'joins' => [
				new JoinClause('metadata', 'n_table_r', $on_object),
				new JoinClause('entity_relationships', 'r', $on_relationship),
			],
			'wheres' => [
				function (QueryBuilder $qb, $main_alias) use ($event_guid) {
					$wheres = [];
					$wheres[] = $qb->compare('r.guid_two', '=', $event_guid, ELGG_VALUE_INTEGER);
					$wheres[] = $qb->compare('r.relationship', '=', 'event_registrationquestion_relation', ELGG_VALUE_STRING);
					
					return $qb->merge($wheres, 'AND');
				},
			],
			'sort_by' => [
				'property' => 'order',
				'direction' => 'ASC',
				'signed' => true,
			],
			'count' => $count,
			'limit' => false,
		]);
	}

	/**
	 * Returns the first waiting entity
	 *
	 * @return null|\ElggEntity
	 */
	protected function getFirstWaitingUser(): ?\ElggEntity {
		$qb = Select::fromTable('entity_relationships');
		$qb->select('guid_two');
		$qb->where($qb->compare('guid_one', '=', $this->guid, ELGG_VALUE_INTEGER));
		$qb->andWhere($qb->compare('relationship', '=', EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST, ELGG_VALUE_STRING));
		$qb->orderBy('time_created', 'ASC');
		$qb->setMaxResults(1);

		$waiting_user = elgg()->db->getDataRow($qb);
		
		return $waiting_user ? get_entity($waiting_user->guid_two) : null;
	}

	/**
	 * Generates a new attendee for this event
	 *
	 * @return boolean
	 */
	public function generateNewAttendee(): bool {
		$waiting_user = $this->getFirstWaitingUser();
		if (empty($waiting_user)) {
			return false;
		}

		$rsvp = false;
		if ($this->with_program) {
			$waiting_for_slots = $this->getRegisteredSlotsForEntity($waiting_user->guid, EVENT_MANAGER_RELATION_SLOT_REGISTRATION_WAITINGLIST);
			if (!empty($waiting_for_slots)) {
				foreach ($waiting_for_slots as $slot) {
					if (!$slot->hasSpotsLeft()) {
						continue;
					}
					
					$rsvp = true;

					$waiting_user->removeRelationship($slot->guid, EVENT_MANAGER_RELATION_SLOT_REGISTRATION_WAITINGLIST);
					$waiting_user->addRelationship($slot->guid, EVENT_MANAGER_RELATION_SLOT_REGISTRATION);
				}
			} elseif ($this->hasEventSpotsLeft()) {
				// not waiting for slots and event has room
				$rsvp = true;
			}
		} elseif ($this->hasEventSpotsLeft()) {
			$rsvp = true;
		}

		if (!$rsvp) {
			return false;
		}
		
		$this->rsvp(EVENT_MANAGER_RELATION_ATTENDING, $waiting_user->guid, false, false, false);
		
		$current_language = elgg_get_current_language();
		elgg()->translator->setCurrentLanguage($waiting_user->getLanguage());

		$notification_body = elgg_echo('event_manager:event:registration:notification:user:text:event_spotfree', [
			$this->getDisplayName(),
			$this->getURL(),
		]);
		
		$completed_text = elgg_strip_tags((string) $this->registration_completed, '<a>');
		if (!empty($completed_text)) {
			$completed_text = str_ireplace('[NAME]', $waiting_user->getDisplayName(), $completed_text);
			$completed_text = str_ireplace('[EVENT]', $this->getDisplayName(), $completed_text);
			
			$notification_body .= PHP_EOL . PHP_EOL . $completed_text;
		}
		
		if (elgg_get_config('email_html_part')) {
			// add addthisevent banners in footer
			$notification_body .= elgg_view('event_manager/addthisevent/email', ['entity' => $this]);
		}
		
		notify_user($waiting_user->guid, $this->owner_guid, elgg_echo('event_manager:event:registration:notification:user:subject'), $notification_body);
		
		elgg()->translator->setCurrentLanguage($current_language);

		return true;
	}

	/**
	 * Return the registered slots for an entity
	 *
	 * @param int    $guid              guid of the entity
	 * @param string $slot_relationship relationship
	 *
	 * @return array
	 */
	public function getRegisteredSlotsForEntity(int $guid, string $slot_relationship): array {
		$slots = [];

		$qb = Select::fromTable('entities', 'slot');
		$qb->select('slot.guid');
		$qb->joinEntitiesTable('slot', 'owner_guid', 'inner', 'event');
		$qb->joinRelationshipTable('slot', 'guid', $slot_relationship, false, 'inner', 'slot_user_relation');
		$qb->joinEntitiesTable('slot_user_relation', 'guid_one', 'inner', 'entity');
		$qb->where($qb->compare('entity.guid', '=', $guid, ELGG_VALUE_INTEGER));

		$data = elgg()->db->getData($qb);
		
		foreach ($data as $slot) {
			$slots[] = get_entity($slot->guid);
		}

		return $slots;
	}

	/**
	 * Returns the days of this event
	 *
	 * @param string $order the order in which to return the days
	 * @param bool   $count (optional) return the count of the days
	 *
	 * @return int|\ColdTrick\EventManager\Event\Day[]
	 */
	public function getEventDays(string $order = 'ASC', bool $count = false): int|array {
		return elgg_get_entities([
			'type' => 'object',
			'subtype' => \ColdTrick\EventManager\Event\Day::SUBTYPE,
			'relationship_guid' => $this->guid,
			'relationship' => 'event_day_relation',
			'inverse_relationship' => true,
			'sort_by' => [
				'property' => 'date',
				'direction' => $order,
			],
			'limit' => false,
			'count' => $count,
		]);
	}
	
	/**
	 * Check if the event has days
	 *
	 * @return bool
	 */
	public function hasEventDays(): bool {
		return !empty($this->getEventDays('ASC', true));
	}

	/**
	 * Counts the attendees
	 *
	 * @return int
	 */
	public function countAttendees(): int {
		return elgg_call(ELGG_IGNORE_ACCESS, function() {
			return elgg_count_entities([
				'relationship' => EVENT_MANAGER_RELATION_ATTENDING,
				'relationship_guid' => $this->guid,
				'inverse_relationship' => false,
			]);
		});
	}

	/**
	 * Counts the waiters
	 *
	 * @return int
	 */
	public function countWaiters(): int {
		return elgg_call(ELGG_IGNORE_ACCESS, function() {
			return elgg_count_entities([
				'relationship' => EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST,
				'relationship_guid' => $this->guid,
				'inverse_relationship' => false,
			]);
		});
	}
	
	/**
	 * Returns an array of entities based on the contact_guids metadata
	 *
	 * @param array $options additional options for elgg_get_entities()
	 *
	 * @return \ElggEntity[]|int
	 */
	public function getContacts(array $options = []): int|array {
		if (empty($this->contact_guids)) {
			return [];
		}
		
		$options['guids'] = $this->contact_guids;
		$options['type'] = 'user';
		$options['subtype'] = 'user';
		$options['limit'] = false;
		
		return elgg_get_entities($options);
	}
	
	/**
	 * Returns an array of entities based on the organizer_guids metadata
	 *
	 * @param array $options additional options for elgg_get_entities()
	 *
	 * @return \ElggEntity[]|int
	 */
	public function getOrganizers(array $options = []): int|array {
		if (empty($this->organizer_guids)) {
			return [];
		}
		
		$options['guids'] = $this->organizer_guids;
		$options['type'] = 'user';
		$options['subtype'] = 'user';
		$options['limit'] = false;
		
		return elgg_get_entities($options);
	}
}
