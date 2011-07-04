<?php 

	class EventSlot extends ElggObject 
	{
		const SUBTYPE = "eventslot";
		
		protected function initialise_attributes() 
		{
			global $CONFIG;
			parent::initialise_attributes();
			
			$this->attributes["subtype"] = self::SUBTYPE;
		}
		
		public function countRegistrations()
		{
			elgg_set_ignore_access(true);
			
			$result = $this->countEntitiesFromRelationship(EVENT_MANAGER_RELATION_SLOT_REGISTRATION, true);
			
			elgg_set_ignore_access(false);
			
			return $result;
		}
		
		public function hasSpotsLeft()
		{
			$result = false;
			
			if(empty($this->max_attendees) || (($this->max_attendees - $this->countRegistrations()) > 0))
			{
				$result = true;
			}
			return $result;
		}
		
		public function getWaitingUsers($count = false)
		{
			elgg_set_ignore_access(true);
			if($count)
			{
				$result = $this->countEntitiesFromRelationship(EVENT_MANAGER_RELATION_SLOT_REGISTRATION_WAITINGLIST, true);
			}
			else
			{
				$result = $this->getEntitiesFromRelationship(EVENT_MANAGER_RELATION_SLOT_REGISTRATION_WAITINGLIST, true);
			}
			
			elgg_set_ignore_access(false);
			
			return $result;
		}
		
		public function getEvent()
		{
			global $CONFIG;
			
			$data = get_data_row("	SELECT event.guid FROM {$CONFIG->dbprefix}entities AS event
									INNER JOIN {$CONFIG->dbprefix}entities AS slot ON slot.owner_guid = event.guid
									INNER JOIN {$CONFIG->dbprefix}entity_subtypes AS sub ON event.subtype = sub.id
									WHERE slot.guid = '".$this->getGUID()."' AND sub.subtype = 'event'
									LIMIT 1");
			
			$event = get_entity($data->guid);
			
			return $event;
		}
		
		public function isUserWaiting($user_guid = null)
		{
			$result = false;
			if($user_guid == null)
			{
				$user_guid = get_loggedin_userid();
			}
			
			if(check_entity_relationship($user_guid, EVENT_MANAGER_RELATION_SLOT_REGISTRATION_WAITINGLIST, $this->getGUID()))
			{
				$result = true;
			}
			
			return $result;
		}
	}