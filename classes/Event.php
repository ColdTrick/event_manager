<?php 

	class Event extends ElggObject 
	{
		const SUBTYPE = "event";
		
		protected function initialise_attributes() 
		{
			global $CONFIG;
			parent::initialise_attributes();
			
			$this->attributes["subtype"] = self::SUBTYPE;
		}
		
		public function getURL()
		{
			global $CONFIG;
			
			$sitetakeover = event_manager_check_sitetakeover_event();
			if($sitetakeover['count']>0)
			{
				return EVENT_MANAGER_BASEURL."/pg/event/view";
			}
			else
			{
				return EVENT_MANAGER_BASEURL."/event/view/" . $this->getGUID() . "/" . elgg_get_friendly_title($this->title);
			}
		}
		
		public function setAccessToOwningObjects($access_id = null)
		{
			$this->setAccessToProgramEntities($access_id);
			$this->setAccessToRegistrationForm($access_id);
		}
		
		public function setAccessToProgramEntities($access_id = null)
		{
			if($access_id == null)
			{
				$access_id = $this->access_id;
			}
			
			if($eventDays = $this->getEventDays())
			{
				foreach($eventDays as $day)
				{
					$day->access_id = $access_id;
					$day->save();
					
					if($eventSlots = $day->getEventSlots())
					{
						foreach($eventSlots as $slot)
						{
							$slot->access_id = $access_id;
							$slot->save();
						}
					}
				}
			}
		}
		
		public function setAccessToRegistrationForm($access_id = null)
		{
			if($access_id == null)
			{
				$access_id = $this->access_id;
			}
			
			if($questions = $this->getRegistrationFormQuestions())
			{
				foreach($questions as $question)
				{
					$question->access_id = $access_id;
					$question->save();
				}
			}
		}
		
		public function hasFiles()
		{
			$files = json_decode($this->files);
			if(count($files) > 0)
			{
				return $files;
			}
			return false;
		}
		
		public function rsvp($type = EVENT_MANAGER_RELATION_UNDO, $user_guid = null, $reset_program = true)
		{
			global $CONFIG;
			$result = false;
			
			if($user_guid == null)
			{
				$user_guid = get_loggedin_userid();
			}
			
			if(!empty($user_guid))
			{
				$event_guid = $this->getGUID();
				
				// remove registrations
				if($type == EVENT_MANAGER_RELATION_UNDO)
				{
					if(!(($user = get_entity($user_guid)) instanceof ElggUser))
					{
						$user->delete();
					}
					else
					{
						if($reset_program)
						{
							if($this->with_program)
							{
								$this->relateToAllSlots(false, $user_guid);
							}
							$this->clearRegistrations($user_guid);
						}
						
						// check if currently attending
						if(check_entity_relationship($this->getGUID(), EVENT_MANAGER_RELATION_ATTENDING, $user_guid))
						{
							if(!$this->hasEventSpotsLeft() || !$this->hasSlotSpotsLeft())
							{
								if($this->getWaitingUsers())
								{
									$this->generateNewAttendee();
								}
							}
						}
					}
				}
				
				// remove current relationships
				delete_data("DELETE FROM {$CONFIG->dbprefix}entity_relationships WHERE guid_one=$event_guid AND guid_two=$user_guid");
				
				// remove river events
				if(get_entity($user_guid) instanceof ElggUser)
				{
					if($items = get_river_items($user_guid, $event_guid, "", "", "", "event_relationship", 9999)){
						foreach($items as $item){
							if($item->view == "river/event_relationship/create"){
								remove_from_river_by_id($item->id);
							}
						}
					}
				}
				
				// add the new relationship
				if($type && ($type != EVENT_MANAGER_RELATION_UNDO) && (in_array($type, event_manager_event_get_relationship_options())))
				{
					if($result = $this->addRelationship($user_guid, $type))
					{
						if(get_entity($user_guid) instanceof ElggUser)
						{
							// add river events
							add_to_river('river/event_relationship/create', 'event_relationship', $user_guid, $event_guid);
						}
					}
				}
				else
				{
					$result = true;
				}
				
				if($this->notify_onsignup)
				{
					$this->notifyOnRsvp($type, $user_guid);
				}
			}
			
			return $result;
		}
		
		public function hasEventSpotsLeft()
		{
			$result = false;
			
			if($this->max_attendees != '')
			{
				$attendees = $this->countEntitiesFromRelationship(EVENT_MANAGER_RELATION_ATTENDING);
				
				if(($this->max_attendees > $attendees))
				{
					$result = true;
				}
			}
			else
			{
				$result = true;
			}
			
			return $result;
		}
		
		public function hasSlotSpotsLeft()
		{
			$result = true;
			
			$slotsSpots = $this->countEventSlotSpots();

			if(($slotsSpots['total'] > 0) && ($slotsSpots['left'] < 1) && !$this->hasUnlimitedSpotSlots())
			{
				$result = false;
			}
			
			return $result;
		}
		
		public function openForRegistration()
		{
			$result = true;
			
			if($this->registration_ended || ($this->endregistration_day != 0 && $this->endregistration_day < time()))
			{
				$result = false;
			}
			elseif(!($registration = $this->generateRegistrationForm()))
			{
				$result = false;
			}
			elseif(!$this->with_program || !($questions = $this->getRegistrationFormQuestions()))
			{
				$result = false;
			}
			return $result;
		}
		
		public function clearRegistrations($user_guid = null)
		{
			if($user_guid == null)
			{
				$user_guid = get_loggedin_userid();
			}			
			
			if($questions = $this->getRegistrationFormQuestions())
			{
				foreach($questions as $question)
				{
					$question->deleteAnswerFromUser($user_guid);
				}
			}
		}
		
		public function getRegistrationsByUser($count = false, $user_guid = null)
		{
			global $CONFIG;
			if($user_guid == null)
			{
				$user_guid = get_loggedin_userid();
			}
			
			$entities_options = array(
				'type' => 'object',
				'subtype' => 'eventregistration',
				'joins' => array("JOIN {$CONFIG->dbprefix}entity_relationships e_r ON e.guid = e_r.guid_two"),
				'wheres' => array("e_r.guid_one = " . $this->getGUID()),
				'owner_guids' => array($user_guid),
				'count' => $count
			);
			
			return elgg_get_entities($entities_options);
		}
		
		public function _getAllRegistrations($filter)
		{
			global $CONFIG;
			
			if($filter == 'waiting')
			{
				$approved = 0;
			}
			else
			{
				$approved = 1;
			}
			
			$entities_options = array(
				'type' => 'object',
				'subtype' => 'eventregistration',
				'full_view' => false,
				'offset' => $offset,
				'joins' => array(	"JOIN {$CONFIG->dbprefix}entity_relationships e_r ON e.guid = e_r.guid_two",

									//Wachtrij check dingetje
									//"JOIN {$CONFIG->dbprefix}metadata n_table on e.guid = n_table.entity_guid",
									//"JOIN {$CONFIG->dbprefix}metastrings msn on n_table.name_id = msn.id",
									//"JOIN {$CONFIG->dbprefix}metastrings msv on n_table.value_id = msv.id"
									),
				'wheres' => array(	"e_r.guid_one = " . $this->getGUID(),
									"e_r.relationship = '" . EVENT_MANAGER_RELATION_USER_REGISTERED . "'",

									//Wachtrij check dingetje
									//"(msn.string IN ('approved'))",
									//"msv.string = $approved"
								)								
			);
			
			$return['entities'] = elgg_get_entities($entities_options);
			
			$entities_options['count'] = true;
			$return['count'] = elgg_get_entities($entities_options);
			
			return $return;
		}

		public function _getRegistrationQuestions()
		{
			$entities = $this->getEntitiesFromRelationship(EVENT_MANAGER_RELATION_REGISTRATION_QUESTION);
			
			return $entities[0];
		}
		
		public function getRegistrationData($user_guid = null, $view = false)
		{
			$result = false;
			
			if($user_guid == null)
			{
				$user_guid = get_loggedin_userid();
			}
			
			if($view)
			{
				$registration_table .= '<h3 class="settings">Information</h3>';
			}

			$registration_table .= '<table>';

			if(($user_guid != get_loggedin_userid()) && !(($user = get_entity($user_guid)) instanceof ElggUser))
			{
				$registration_table .= '<tr><td><label>'.elgg_echo('user:name:label').'</label></td><td>: '.$user->name.'</td></tr>';
				$registration_table .= '<tr><td><label>'.elgg_echo('email').'</label></td><td>: '.$user->email.'</td></tr>';
			}
			
			if($registration_form = $this->getRegistrationFormQuestions())
			{
				foreach($registration_form as $question)
				{				
					$answer = $question->getAnswerFromUser($user_guid);
					
					$registration_table .= '<tr><td><label>'.$question->title.'</label></td><td>: '.$answer->value.'</td></tr>';
				}
				$registration_table .= '</table>';
			
				$result = elgg_view('page_elements/contentwrapper', array('body' => $registration_table));
			}
			
			return $result;
		}
		
		public function generateRegistrationForm($register_type = 'register')
		{
			$form = false;
			
			if($registration_form = $this->getRegistrationFormQuestions())
			{				
				if($register_type == 'waitinglist')
				{
					$form_body .= '<p>'. elgg_echo('event_manager:event:rsvp:waiting_list:message') .'</p><br />';
				}
				
				$form_body .= '<ul>';
				
					if(!isloggedin())
					{
						$form_body .= '<li><label>'.elgg_echo('user:name:label').' *</label><br /><input type="text" name="question_name" value="'.$_SESSION['registerevent_values']['question_name'].'" class="input-text"></li>';
						$form_body .= '<li><label>'.elgg_echo('email').' *</label><br /><input type="text" name="question_email" value="'.$_SESSION['registerevent_values']['question_email'].'" class="input-text"></li>';
					}
	
					foreach($registration_form as $question)
					{
						$sessionValue = $_SESSION['registerevent_values']['question_'.$question->getGUID()];					
	
						if(isloggedin())
						{
							$answer = $question->getAnswerFromUser();
						}
	
						$value = (($sessionValue != '')?$sessionValue:$answer->value);
	
						$form_body .= elgg_view('event_manager/registration/question', array('entity' => $question, 'register' => true, 'value' => $value));
					}
	
					if(!isloggedin())
					{
						$form_body .= elgg_view('input/captcha');
					}
				
				$form_body .= '</ul>';

				$form_body = elgg_view('page_elements/contentwrapper', array('body' => $form_body));
			}

			if($this->with_program)
			{
				$form_body .= $this->getProgramData(get_loggedin_userid(), true, $register_type);
			}
			
			if($form_body)
			{
				$form_body .= elgg_view('input/hidden', array('internalname' => 'event_guid', 'value' => $this->getGUID()));
				$form_body .= elgg_view('input/hidden', array('internalname' => 'relation', 'value' => 'event_attending'));
				
				$form_body .= elgg_view('input/hidden', array('internalname' => 'register_type', 'value' => $register_type));
				
				$form_body .= elgg_view('input/button', array('type' => 'button', 'internalid' => 'event_manager_event_register_submit', 'value' => elgg_echo('register')));
				
				$form_body = elgg_view('page_elements/contentwrapper', array('body' => $form_body));
								
				$form = elgg_view('input/form', array(	'internalid' 	=> 'event_manager_event_register', 
														'internalname' 	=> 'event_manager_event_register', 
														'action' 		=> $vars['url'].'/action/event_manager/event/register', 
														'body' 			=> $form_body));
			}
			
			return $form;
		}

		public function getProgramData($user_guid = null, $participate = false, $register_type = 'register')
		{
			$result = false;
			
			if($user_guid == null)
			{
				$user_guid = get_loggedin_userid();
			}
			
			if($eventDays = $this->getEventDays())
			{
				if(!$participate)
				{
					$currentContext = get_context();
					set_context('programmailview');
					
					$result .= elgg_view('event_manager/program/view', array('entity' => $this));
										
					set_context($currentContext);
				}
				else
				{
					$result .= elgg_view('event_manager/program/edit', array('entity' => $this, 'register_type' => $register_type));			
				}
				
				$result = elgg_view('page_elements/contentwrapper', array('body' => $result));
			}
			
			return $result;
		}

		public function getProgramDataForPdf($user_guid = null, $register_type = 'register')
		{
			$result = false;
			
			if($user_guid == null)
			{
				$user_guid = get_loggedin_userid();
			}
			
			if($eventDays = $this->getEventDays())
			{
				$currentContext = get_context();
				set_context('programmailview');
				
				$result .= elgg_view('event_manager/program/pdf', array('entity' => $this));
									
				set_context($currentContext);
				
				$result = elgg_view('page_elements/contentwrapper', array('body' => $result));
			}
			
			return $result;
		}
		
		public function notifyOnRsvp($type, $to = null)
		{
			elgg_set_ignore_access(true);
			
			if($to == null)
			{
				$to = get_loggedin_userid();
			}
			
			if($type == EVENT_MANAGER_RELATION_ATTENDING)
			{
				if($this->registration_needed)
				{
					$registrationLink 	= PHP_EOL . PHP_EOL. elgg_echo('event_manager:event:registration:notification:program:linktext').PHP_EOL . PHP_EOL.'<br /><a href="'.EVENT_MANAGER_BASEURL.'/registration/view/?guid='.$this->getGUID().'&u_g='.$to.'&k='.md5($this->time_created.get_site_secret().$to).'">'.EVENT_MANAGER_BASEURL.'/registration/view/?guid='.$this->getGUID().'&u_g='.$to.'&k='.md5($this->time_created.get_site_secret().$to).'</a>';
				}
			}
			
			if(is_plugin_enabled('html_email_handler'))
			{
				$owner_message = sprintf(elgg_echo('event_manager:event:registration:notification:owner:text:html:'.$type), 
								get_entity($this->owner_guid)->name, 
								get_entity($to)->name, 
								$this->getURL(), 
								$this->title).
								$registrationLink;
			}
			else
			{
				$owner_message = sprintf(elgg_echo('event_manager:event:registration:notification:owner:text:'.$type), 
								get_entity($this->owner_guid)->name, 
								get_entity($to)->name, 
								$this->title).
								$registrationLink;
			}
			notify_user($this->owner_guid,
						$this->getGUID(), 
						elgg_echo('event_manager:event:registration:notification:owner:subject'), 
						$owner_message
						);
						
			if(($user = get_entity($to)) instanceof ElggUser)
			{
				if(is_plugin_enabled('html_email_handler'))
				{
					$message = sprintf(elgg_echo('event_manager:event:registration:notification:user:text:html:'.$type), 
									get_entity($to)->name,  
									$this->getURL(), 
									$this->title).
									$registrationLink; 
				}
				else
				{
					$message = sprintf(elgg_echo('event_manager:event:registration:notification:user:text:'.$type), 
									get_entity($to)->name,  
									$this->title).
									$registrationLink; 
				}
				notify_user($to, 
							$this->getGUID(), 
							elgg_echo('event_manager:event:registration:notification:user:subject'),
							$message
							);
			}
			else
			{				
				$headers .= "Reply-To: ". get_entity($to)->email . "\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
				
				mail(	get_entity($to)->email, 
						elgg_echo('event_manager:event:registration:notification:user:subject'), 
						sprintf(elgg_echo('event_manager:event:registration:notification:user:text:'.$type), 
							get_entity($to)->name,  
							$this->getURL(), 
							$this->title).
							$registrationLink,
						$headers);
			}
			
			elgg_set_ignore_access(false);			
		}
		
		public function relateToAllSlots($relate = true, $user = null)
		{
			global $CONFIG;
			if($user == null)
			{
				$user = get_loggedin_userid();
			}
			
			if($this->getEventDays())
			{
				foreach($this->getEventDays() as $eventDay)
				{
					foreach($eventDay->getEventSlots() as $eventSlot)
					{
						if($relate)
						{
							$user->addRelationship($eventSlot->getGUID(), EVENT_MANAGER_RELATION_SLOT_REGISTRATION);
						}
						else
						{
							delete_data("DELETE FROM {$CONFIG->dbprefix}entity_relationships WHERE guid_one='".$user."' AND guid_two='".$eventSlot->getGUID()."'");
						}
					}
				}
			}
		}
		
		public function countEventSlotSpots()
		{
			$spots = array();
			
			if($eventDays = $this->getEventDays())
			{
				foreach($eventDays as $eventDay)
				{
					if($eventSlots = $eventDay->getEventSlots())
					{
						foreach($eventSlots as $eventSlot)
						{
							$spots['total'] = ($spots['total'] + $eventSlot->max_attendees);
							$spots['left'] = ($spots['left'] + ($eventSlot->max_attendees - $eventSlot->countRegistrations()));
						}
					}
				}
			}
			return $spots;
		}

		public function hasUnlimitedSpotSlots()
		{
			if($eventDays = $this->getEventDays())
			{
				foreach($eventDays as $eventDay)
				{
					if($eventSlots = $eventDay->getEventSlots())
					{
						foreach($eventSlots as $eventSlot)
						{
							if($eventSlot->max_attendees == '' || $eventSlot->max_attendees == 0)
							{
								return true;
							}
						}
					}
				}
			}
		}
		
		public function getLocation($type = false)
		{
			$location = $this->location;
			if($type)
			{
				$location = str_replace(',', '<br />',$this->location);
			}
			
			return $location;
		}
		
		public function getRelationshipByUser($user_guid = null)
		{
			global $CONFIG;
			
			$user_guid = (int)$user_guid;
			if(empty($user_guid))
			{
				$user_guid = get_loggedin_userid();
			}
			
			$event_guid = $this->getGUID();
			
			$row = get_data_row("SELECT * FROM {$CONFIG->dbprefix}entity_relationships WHERE guid_one=$event_guid AND guid_two=$user_guid");
			return $row->relationship;
		}

		public function getRelationships($count = false)
		{
			global $CONFIG;
			
			$result = false;
			
			$event_guid = $this->getGUID();
			
			if($count){
				$query = "SELECT relationship, count(*) as count FROM {$CONFIG->dbprefix}entity_relationships WHERE guid_one=$event_guid GROUP BY relationship ORDER BY relationship ASC";
			} else {
				$query = "SELECT * FROM {$CONFIG->dbprefix}entity_relationships WHERE guid_one=$event_guid ORDER BY relationship ASC";	
			}
			
			$all_relations = get_data($query);
			
			if(!empty($all_relations)){
				$result = array();
				foreach($all_relations as $row){
					$relationship = $row->relationship;
					
					if($count){
						$result[$relationship] = $row->count;
						$result["total"] += $row->count;	
					} else {
						if(!array_key_exists($relationship, $result)){
							$result[$relationship] = array();
						}
						$result[$relationship][] = $row->guid_two;
					}
				}
			}
			
			return $result;
		}
		
		public function getRegistrationFormQuestions($count = false)
		{
			$result = false;
			
			if($entities = event_manager_get_eventregistrationform_fields($this->getGUID(), $count))
			{
				$result = $entities;
			}
			
			return $result;
		}
		
		public function isAttending($user_guid = null)
		{
			$result = false;
			
			if(empty($user_guid))
			{
				$user_guid = get_loggedin_userid();
			} 
			
			$result = check_entity_relationship($this->getGUID(), EVENT_MANAGER_RELATION_ATTENDING, $user_guid);
			
			return $result;			
		}
		
		public function isWaiting($user_guid = null)
		{
			$result = false;
			
			if(empty($user_guid))
			{
				$user_guid = get_loggedin_userid();
			} 
			
			$result = check_entity_relationship($this->getGUID(), EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST, $user_guid);
			
			return $result;			
		}
		
		public function getWaitingUsers()
		{		
			global $CONFIG;
			
			$result = false;
				
			$query = "SELECT * FROM {$CONFIG->dbprefix}entity_relationships WHERE guid_one= '".$this->getGUID(). "' AND relationship = '".EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST."' ORDER BY time_created ASC";
				
			if($waiting_users = get_data($query))
			{
				$result = array();
				foreach($waiting_users as $user)
				{
					$result[] = get_entity($user->guid_two);
				}
			}
			
			return $result;
		}
		
		public function getFirstWaitingUser()
		{		
			global $CONFIG;
			
			$result = false;
				
			$query = "SELECT * FROM {$CONFIG->dbprefix}entity_relationships WHERE guid_one= '".$this->getGUID(). "' AND relationship = '".EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST."' ORDER BY time_created ASC LIMIT 1";	
			if($waiting_users = get_data($query))
			{
				foreach($waiting_users as $user)
				{
					$result = get_entity($user->guid_two);
				}
			}
			
			return $result;
		}
		
		public function generateNewAttendee()
		{
			$result = false;
			
			if($waiting_user = $this->getFirstWaitingUser())
			{
				foreach($this->getRegisteredSlotsByUser($waiting_user->getGUID()) as $slot)
				{
					if($slot->hasSpotsLeft())
					{
						$rsvp = true;
						$waiting_user->removeRelationship($slot->getGUID(), EVENT_MANAGER_RELATION_SLOT_REGISTRATION_WAITINGLIST);
						
						$waiting_user->addRelationship($slot->getGUID(), EVENT_MANAGER_RELATION_SLOT_REGISTRATION);
					}
				}
				
				if($rsvp)
				{
					$this->rsvp(EVENT_MANAGER_RELATION_ATTENDING, $waiting_user->getGUID(), false);
					
					notify_user(get_loggedin_userid(), 
								$this->getGUID(), 
								elgg_echo('event_manager:event:registration:notification:user:subject'),
								sprintf(elgg_echo('event_manager:event:registration:notification:user:text:event_spotfree'), 
									$waiting_user->name,  
									$this->getURL(), 
									$this->title)
								);
				}
				$result = true;
			}
			
			return $result;
		}
		
		public function getRegisteredSlotsByUser($user_guid)
		{
			global $CONFIG;
			
			$slots = array();
			
			$data = get_data("	SELECT slot.guid FROM {$CONFIG->dbprefix}entities AS slot
								INNER JOIN {$CONFIG->dbprefix}entities AS event ON event.guid = slot.owner_guid
								INNER JOIN {$CONFIG->dbprefix}entity_relationships AS slot_user_relation ON slot.guid = slot_user_relation.guid_two
								INNER JOIN {$CONFIG->dbprefix}users_entity AS user ON user.guid = slot_user_relation.guid_one
								WHERE 	user.guid=$user_guid AND 
										slot_user_relation.relationship='".EVENT_MANAGER_RELATION_SLOT_REGISTRATION_WAITINGLIST."'
							");
			
			foreach($data as $slot)
			{
				$slots[] = get_entity($slot->guid);
			}
			
			return $slots;
		}
		
		public function getIcon($size = "medium", $icontime = 0)
		{
			if (!in_array($size, array('small','medium','large','tiny','master','topbar')))
			{
				$size = 'medium';
			}
			
			if ($icontime = $this->icontime)
			{
				$icontime = $icontime;
			}
			else
			{
				$icontime = "default";
			}
			
			return get_entity_icon_url($this, $size);
		}
		
		public function getEventDays($order = 'ASC')
		{
			global $CONFIG;
			
			$entities_options = array(
				'type' => 'object',
				'subtype' => 'eventday',
				'relationship_guid' => $this->getGUID(),
				'relationship' => 'event_day_relation',
				'inverse_relationship' => true,
				'full_view' => false,
				'joins' => array(
					"JOIN {$CONFIG->dbprefix}metadata n_table on e.guid = n_table.entity_guid",
					"JOIN {$CONFIG->dbprefix}metastrings msn on n_table.name_id = msn.id",
					"JOIN {$CONFIG->dbprefix}metastrings msv on n_table.value_id = msv.id"),
				'wheres' => array("(msn.string IN ('date'))"),
				'order_by' => "msv.string {$order}",
				'limit' => false,
					
			);
		 
			return elgg_get_entities_from_relationship($entities_options);
		}
	
		
		public function isUserRegistered($userid = null, $count = true)
		{
			global $CONFIG;
			if($userid == null)
			{
				$userid = get_loggedin_userid();
			}
			
			$entities_options = array(
				'type' => 'object',
				'subtype' => 'eventregistration',
				'joins' => array("JOIN {$CONFIG->dbprefix}entity_relationships e_r ON e.guid = e_r.guid_two"),
				'wheres' => array("e_r.guid_one = " . $this->getGUID()),
				'count' => $count,
				'owner_guids' => array($userid)
			);
			
			$entityCount = elgg_get_entities_from_relationship($entities_options);
			
			if($count)
			{
				if($entityCount > 0)
				{
					return true;
				}
				return false;
			}
			else
			{
				return $entityCount[0];
			}
		}
		
		public function countAttendees()
		{
			elgg_set_ignore_access(true);
			
			$entities = $this->countEntitiesFromRelationship(EVENT_MANAGER_RELATION_ATTENDING);			
			
			elgg_set_ignore_access(false);
			
			return $entities;
		}
		
		public function exportAttendees()
		{
			$entities = $this->getEntitiesFromRelationship(EVENT_MANAGER_RELATION_ATTENDING);
			return $entities;
		}
	}

?>