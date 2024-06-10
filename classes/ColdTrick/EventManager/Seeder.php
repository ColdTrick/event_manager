<?php

namespace ColdTrick\EventManager;

use Elgg\Database\Seeds\Seed;
use Elgg\Exceptions\Seeding\MaxAttemptsException;
use Elgg\Groups\Tool;
use Elgg\Values;

/**
 * Database seeder for Events
 */
class Seeder extends Seed {
	
	/**
	 * {@inheritdoc}
	 */
	public function seed() {
		$this->advance($this->getCount());
		
		$groups_supported = elgg()->group_tools->get('event_manager') instanceof Tool;
		
		while ($this->getCount() < $this->limit) {
			$container = null;
			if ($groups_supported && $this->faker()->boolean()) {
				$container = $this->getRandomGroup();
				$container->enableTool('event_manager');
			}
			
			try {
				/* @var $event \Event */
				$event = $this->createObject([
					'subtype' => \Event::SUBTYPE,
					'container_guid' => $container ? $container->guid : null,
					'shortdescription' => $this->faker()->words($this->faker()->numberBetween(4, 8), true),
					'comments_on' => (int) $this->faker()->boolean(75),
					'venue' => $this->faker()->streetName(),
					'location' => $this->faker()->address(),
					'contact_details' => $this->faker()->company(),
					'organizer' => $this->faker()->name(),
					'website' => $this->faker()->url(),
					'max_attendees' => $this->faker()->numberBetween(10, 80),
					'show_attendees' => (int) $this->faker()->boolean(80),
					'registration_completed' => $this->faker()->words($this->faker()->numberBetween(150, 500), true),
					'waiting_list_enabled' => (int) $this->faker()->boolean(),
					// no (yet) supported features
					'with_program' => 0,
					'registration_needed' => 0,
					'registration_ended' => 0,
					'notify_onsignup' => 0,
					'notify_onsignup_contact' => 0,
					'notify_onsignup_organizer' => 0,
				]);
			} catch (MaxAttemptsException $e) {
				// unable to create a blog with the given options
				continue;
			}
			
			if ($event->comments_on) {
				$this->createComments($event);
			}
			
			$this->createLikes($event);
			
			$this->setStartEndTime($event);
			$event->setLatLong($this->faker()->latitude(24.52, 49.38), $this->faker()->longitude(-66.95, -124.77)); // in the USA, can be in the ocean
			$this->addAttendees($event);
			$this->addOrganizers($event);
			$this->addContacts($event);
			
			elgg_create_river_item([
				'action_type' => 'create',
				'subject_guid' => $event->owner_guid,
				'object_guid' => $event->guid,
				'target_guid' => $event->container_guid,
				'posted' => $event->time_created,
			]);
			
			$event->save();
			
			$this->advance();
		}
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function unseed() {
		/* @var $events \ElggBatch */
		$events = elgg_get_entities([
			'type' => 'object',
			'subtype' => \Event::SUBTYPE,
			'metadata_name' => '__faker',
			'limit' => false,
			'batch' => true,
			'batch_inc_offset' => false,
		]);
		
		/* @var $event \Event */
		foreach ($events as $event) {
			if ($event->delete()) {
				$this->log("Deleted event {$event->guid}");
			} else {
				$this->log("Failed to delete event {$event->guid}");
				$events->reportFailure();
				continue;
			}
			
			$this->advance();
		}
	}
	
	/**
	 * {@inheritdoc}
	 */
	public static function getType(): string {
		return \Event::SUBTYPE;
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function getCountOptions(): array {
		return [
			'type' => 'object',
			'subtype' => \Event::SUBTYPE,
		];
	}
	
	/**
	 * Set the start and end time of the event
	 *
	 * @param \Event $event the event
	 *
	 * @return void
	 */
	protected function setStartEndTime(\Event $event): void {
		$since = $this->create_since;
		
		$this->setCreateSince($event->time_created);
		$start = $this->getRandomCreationTimestamp();
		$event->event_start = $start - ($start % 900); // round to a 15-min period
		
		$end = Values::normalizeTime($event->event_start);
		$end = $end->modify("+{$this->faker()->numberBetween(60, 480)} minutes"); // range of 1-8 hours
		
		$event->event_end = $end->getTimestamp() - ($end->getTimestamp() % 900); // round to a 15-min period
		
		$this->setCreateSince($since);
	}
	
	/**
	 * Add attendees to the event
	 *
	 * @param \Event $event the event
	 *
	 * @return void
	 */
	protected function addAttendees(\Event $event): void {
		$since = $this->create_since;
		$until = $this->create_until;
		
		$this->setCreateSince($event->time_created);
		$this->setCreateUntil($event->event_start);
		
		$num = $this->faker()->numberBetween(0, $event->max_attendees);
		$users = [];
		for ($i = 0; $i < $num; $i++) {
			$user = $this->getRandomUser($users);
			$users[] = $user->guid;
			
			$event->rsvp(EVENT_MANAGER_RELATION_ATTENDING, $user->guid, true, false, false);
			
			elgg_create_river_item([
				'view' => 'river/event_relationship/create',
				'action_type' => 'event_relationship',
				'subject_guid' => $user->guid,
				'object_guid' => $event->guid,
				'posted' => $this->getRandomCreationTimestamp(),
			]);
		}
		
		$this->setCreateSince($since);
		$this->setCreateUntil($until);
	}
	
	/**
	 * Add organizers to an event
	 *
	 * @param \Event $event the event
	 *
	 * @return void
	 */
	protected function addOrganizers(\Event $event): void {
		$exclude = (array) $event->contact_guids;
		$exclude[] = $event->owner_guid;
		
		$organizers = [];
		for ($i = 0; $i < $this->faker()->numberBetween(0, 5); $i++) {
			$user = $this->getRandomUser($exclude);
			$exclude[] = $user->guid;
			
			$organizers[] = $user->guid;
		}
		
		if (!empty($organizers)) {
			$event->organizer_guids = $organizers;
		}
	}
	
	/**
	 * Add contacts to an event
	 *
	 * @param \Event $event the event
	 *
	 * @return void
	 */
	protected function addContacts(\Event $event): void {
		$exclude = (array) $event->organizer_guids;
		$exclude[] = $event->owner_guid;
		
		$contacts = [];
		for ($i = 0; $i < $this->faker()->numberBetween(0, 5); $i++) {
			$user = $this->getRandomUser($exclude);
			$exclude[] = $user->guid;
			
			$contacts[] = $user->guid;
		}
		
		if (!empty($contacts)) {
			$event->contact_guids = $contacts;
		}
	}
}
