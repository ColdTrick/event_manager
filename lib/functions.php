<?php 


	function event_manager_event_get_relationship_options()
	{
		$result = array(
				EVENT_MANAGER_RELATION_ATTENDING,
				EVENT_MANAGER_RELATION_INTERESTED,
				EVENT_MANAGER_RELATION_PRESENTING,
				EVENT_MANAGER_RELATION_EXHIBITING,
				EVENT_MANAGER_RELATION_ORGANIZING,
				EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST,			
			);
			
		return $result;
	}

	function event_manager_icon_sizes()
	{
		$result = array(
				'tiny',
				'small',
				'medium',
				'large',
				'master'				
			);
			
		return $result;
	}
	
	function event_manager_get_registration_fiedtypes()
	{
		$result = array(
				'Textfield' => 'text',
				'Textarea' => 'plaintext',
				'Dropdown' => 'pulldown',
				'Radiobutton' => 'radio'
			);
			
		return $result;
	}
	
	function event_manager_search_events($options = array())
	{
		global $CONFIG;
		
		$defaults = array(	'past_events' 		=> false,
							'count' 			=> false,
							'offset' 			=> 0,
							'limit'				=> EVENT_MANAGER_SEARCH_LIST_LIMIT,
							'container_guid'	=> null,
							'query'				=> false,
							'meattending'		=> false,
							'owning'			=> false,
							'friendsattending' 	=> false,
							'region'			=> null,
							'event_type'		=> false,
							'past_events'		=> false,
		);
		
		$options = array_merge($defaults, $options);
		
		$fields = array('title', 'description');	
		
		$entities_options = array(
			'type' 			=> 'object',
			'subtype' 		=> 'event',
			'offset' 		=> $options['offset'],
			'limit' 		=> $options['limit'],
			'container_guid'=> $options['container_guid'],
			'joins' 		=> array(
								"JOIN {$CONFIG->dbprefix}objects_entity oe ON e.guid = oe.guid",
		
								"JOIN {$CONFIG->dbprefix}metadata n_table ON e.guid = n_table.entity_guid",
								"JOIN {$CONFIG->dbprefix}metadata d_table ON e.guid = d_table.entity_guid",
		
								"JOIN {$CONFIG->dbprefix}metastrings msn ON n_table.name_id = msn.id",
								"JOIN {$CONFIG->dbprefix}metastrings msv ON n_table.value_id = msv.id",
		
								"JOIN {$CONFIG->dbprefix}metastrings msnd ON d_table.name_id = msnd.id",
								"JOIN {$CONFIG->dbprefix}metastrings msvd ON d_table.value_id = msvd.id"),
			'order_by' 		=> 'msvd.string ASC'
		);
		
		if($options['query'])
		{		
			$fields_object 	= array('title', 'description');	
			$fields 		= array('string');
			
			$meta_fields 	= array("'shortdescription'", "'organizer'", "'venue'", "'location'");
			$meta_fields_in = implode(',', $meta_fields);
		
			$where			= event_manager_search_get_where_sql('oe', $fields_object, $options, false);
			$search_where 	= event_manager_search_get_where_sql('msv', $fields, $options, false);
			
			$meta_where = "((msn.string IN ($meta_fields_in)) AND $search_where)";
			
			$entities_options['wheres'][] = '('.$where.' OR '.$meta_where.')';
		}
				
		$entities_options['wheres'][] = "(msnd.string LIKE 'start_day')";
					
		if(!empty($options['start_day']))
		{
			$entities_options['wheres'][] = "(".$options['start_day']." <= msvd.string)";
		}
		
		if(!empty($options['end_day']))
		{
			$entities_options['wheres'][] = "(msvd.string <= ".$options['end_day'].")";
		}			
		
		if($options['meattending'])
		{
			$entities_options['joins'][] = "JOIN {$CONFIG->dbprefix}entity_relationships e_r ON e.guid = e_r.guid_one";
			
			$entities_options['wheres'][] = "e_r.guid_two = " . get_loggedin_userid();
			$entities_options['wheres'][] = "e_r.relationship = '" . EVENT_MANAGER_RELATION_ATTENDING . "'";
		}
		
		if($options['owning'])
		{
			$entities_options['owner_guids'] = array(get_loggedin_userid()); 			
		}
		
		if($options['friendsattending'])
		{
			$friends = get_loggedin_user()->getFriends();
				
			$friends_guids = array();
			foreach($friends as $user)
			{
				$friends_guids[] = $user->getGUID();
			}
			$entities_options['joins'][] = "JOIN {$CONFIG->dbprefix}entity_relationships e_ra ON e.guid = e_ra.guid_one"; 
			$entities_options['wheres'][] = "(e_ra.guid_two IN (" . implode(", ", $friends_guids) . "))";
		}
		
		if($options['region'])
		{
			$entities_options['joins'][] = "JOIN {$CONFIG->dbprefix}metastrings msnr ON n_table.name_id = msnr.id";
			$entities_options['joins'][] = "JOIN {$CONFIG->dbprefix}metastrings msvr ON n_table.value_id = msvr.id";
			
			$entities_options['wheres'][] = "((msnr.string LIKE 'region') AND (msvr.string LIKE '".$options['region']."'))";
		}
		
		if($options['event_type'])
		{
			$entities_options['joins'][] = "JOIN {$CONFIG->dbprefix}metastrings msnt ON n_table.name_id = msnr.id";
			$entities_options['joins'][] = "JOIN {$CONFIG->dbprefix}metastrings msvt ON n_table.value_id = msvr.id";
			
			$entities_options['wheres'][] = "((msnt.string LIKE 'event_type') AND (msvt.string LIKE '".$options['event_type']."'))";
		}
	
		if(!$options['past_events'])
		{
			$time = time();
			$ts = mktime(0, 0, 1, date('m', $time), date('d', $time), date('y', $time));
			
			$entities_options['wheres'][] = "(msvd.string >= ".$ts.")";
		}
		
		$entities = elgg_get_entities($entities_options);
		
		$entities_options['count'] = true;
		$count_entities = elgg_get_entities($entities_options);
		
		$result = array(
			"entities" => $entities,
			"count" => $count_entities
			);
			
		return $result;
	}
	
	function event_manager_get_eventregistrationform_fields($event_guid, $count = false)
	{
		global $CONFIG;

		$entities_options = array(
			'type' => 'object',
			'subtype' => 'eventregistrationquestion',
			'joins' => array(
							"JOIN {$CONFIG->dbprefix}metadata n_table on e.guid = n_table.entity_guid",
							"JOIN {$CONFIG->dbprefix}metastrings msn on n_table.name_id = msn.id",
							"JOIN {$CONFIG->dbprefix}metastrings msv on n_table.value_id = msv.id",
							
							"JOIN {$CONFIG->dbprefix}entity_relationships r on r.guid_one = e.guid"),
			'wheres' => array(
							"r.guid_two = " . $event_guid,
							"r.relationship = 'event_registrationquestion_relation'",
							"(msn.string IN ('order'))"),
			'order_by' => 'msv.string ASC',
			'count' => $count
		);
		
		
		if($entities = elgg_get_entities($entities_options))
		{
			return $entities;
		}
		else
		{
			return false;
		}
	}

	/*
	 * 
	 function event_manager_get_events($filter = "all", $offset = 0, $past_events = false)
	{
		global $CONFIG;
		
		$time = time();
		$ts = mktime(0, 0, 1, date('m', $time), date('d', $time), date('y', $time));
				
		$entities_options = array(
			'type' 		=> 'object',
			'subtype' 	=> 'event',
			'full_view' => false,
			'offset' 	=> $offset,
			'limit' 	=> EVENT_MANAGER_SEARCH_LIST_LIMIT,
			'joins' 	=> array(
							"JOIN {$CONFIG->dbprefix}metadata n_table ON e.guid = n_table.entity_guid",
							"JOIN {$CONFIG->dbprefix}metastrings msn ON n_table.name_id = msn.id",
							"JOIN {$CONFIG->dbprefix}metastrings msv ON n_table.value_id = msv.id"),
			'wheres' 	=> array(
							"(msn.string IN ('start_day')) AND (msv.string >= '".$ts."')"),
			'order_by' 	=> "msv.string ASC"
		);
		
		switch($filter){
			case "owned":
				$entities_options['owner_guids'] = array(get_loggedin_userid()); 
				$entities = elgg_get_entities($entities_options);
				
				$entities_options['count'] = true;
				$count_entities = elgg_get_entities($entities_options);
			break;
			case "friends":
				$friends = get_loggedin_user()->getFriends();
				
				$friends_guids = array();
				foreach($friends as $user)
				{
					$friends_guids[] = $user->getGUID();
				}
				$entities_options['joins'][] = "JOIN {$CONFIG->dbprefix}entity_relationships e_r ON e.guid = e_r.guid_one"; 
				$entities_options['wheres'][] = "e_r.guid_two IN (" . implode(", ", $friends_guids) . ")";
				
				$entities = elgg_get_entities($entities_options);
				
				$entities_options['count'] = true;
				$count_entities = elgg_get_entities($entities_options);
			break;
			case "attending":
				
				$entities_options['joins'][] = "JOIN {$CONFIG->dbprefix}entity_relationships e_r ON e.guid = e_r.guid_one"; 
				$entities_options['wheres'][] = "e_r.guid_two = " . get_loggedin_userid();
				$entities_options['wheres'][] = "e_r.relationship = '" . EVENT_MANAGER_RELATION_ATTENDING . "'";
				 
				$entities = elgg_get_entities($entities_options);
				
				$entities_options['count'] = true;
				$count_entities = elgg_get_entities($entities_options);
			break;
			case "all":
			case "onthemap":
			case "default":
				$entities = elgg_get_entities($entities_options);
				
				$entities_options['count'] = true;
				$count_entities = elgg_get_entities($entities_options);
			break;
		}
		
		$result = array(
			"entities" => $entities,
			"count" => $count_entities
			);
			
		return $result;
	}*/
	
	function get_entities_from_viewport($lat, $long, $radius, $type = "", $subtype = "", $limit = 20)
	{
		global $CONFIG;
		
		if ($subtype === false || $subtype === null || $subtype === 0)
		{
			return false;
		}
		
		$lat = (real)$lat;
		$long = (real)$long;
		$radius = (real)$radius;
		
		$order_by = sanitise_string($order_by);
		$limit = (int)$limit;
		$offset = (int)$offset;
		$site_guid = (int) $site_guid;
		if ($site_guid == 0) 
		{
			$site_guid = $CONFIG->site_guid;
		}
		
		$where = array();
		 
		if (is_array($type)) 
		{
			$tempwhere = "";
			if (sizeof($type)) 
			{
				foreach($type as $typekey => $subtypearray) 
				{
					foreach($subtypearray as $subtypeval) 
					{
						$typekey = sanitise_string($typekey);
						if (!empty($subtypeval)) 
						{
							$subtypeval = (int) get_subtype_id($typekey, $subtypeval);
						} 
						else 
						{
							$subtypeval = 0;
						}
						if (!empty($tempwhere)) $tempwhere .= " or ";
						$tempwhere .= "(e.type = '{$typekey}' AND e.subtype = {$subtypeval})";
					}
				}
			}
			if (!empty($tempwhere)) 
			{
				$where[] = "({$tempwhere})";
			}
		} 
		else 
		{
			$type = sanitise_string($type);
			$subtype = get_subtype_id($type, $subtype);
			
			if ($type != "") 
			{
				$where[] = "e.type='$type'";
			}
			
			if ($subtype!=="") 
			{
				$where[] = "e.subtype=$subtype";
			}
		}
		
		if ($owner_guid != "")
		{
			if (!is_array($owner_guid)) 
			{
				$owner_array = array($owner_guid);
				$owner_guid = (int) $owner_guid;
				$where[] = "e.owner_guid = '$owner_guid'";
			} 
			else if (sizeof($owner_guid) > 0) 
			{
				$owner_array = array_map('sanitise_int', $owner_guid);
				// Cast every element to the owner_guid array to int
				$owner_guid = implode(",",$owner_guid); //
				$where[] = "e.owner_guid in ({$owner_guid})" ; //
			}
			if (is_null($container_guid)) 
			{
				$container_guid = $owner_array;
			}
		}
		
		if ($site_guid > 0) 
		{
			$where[] = "e.site_guid = {$site_guid}";
		}
		
		if (!is_null($container_guid)) 
		{
			if (is_array($container_guid)) 
			{
				foreach($container_guid as $key => $val) $container_guid[$key] = (int) $val;
				$where[] = "e.container_guid in (" . implode(",",$container_guid) . ")";
			} 
			else 
			{
				$container_guid = (int) $container_guid;
				$where[] = "e.container_guid = {$container_guid}";
			}
		}
		
		// Add the calendar stuff
		$loc_join = "	JOIN {$CONFIG->dbprefix}metadata loc_start on e.guid=loc_start.entity_guid
						JOIN {$CONFIG->dbprefix}metastrings loc_start_name on loc_start.name_id=loc_start_name.id
						JOIN {$CONFIG->dbprefix}metastrings loc_start_value on loc_start.value_id=loc_start_value.id
						
						JOIN {$CONFIG->dbprefix}metadata loc_end on e.guid=loc_end.entity_guid
						JOIN {$CONFIG->dbprefix}metastrings loc_end_name on loc_end.name_id=loc_end_name.id
						JOIN {$CONFIG->dbprefix}metastrings loc_end_value on loc_end.value_id=loc_end_value.id";
		
		$lat_min = $lat - $radius;
		$lat_max = $lat + $radius;
		$long_min = $long - $radius;
		$long_max = $long + $radius;
		
		$where[] = "loc_start_name.string='geo:lat'";
		$where[] = "loc_start_value.string>=$lat_min";
		$where[] = "loc_start_value.string<=$lat_max";
		$where[] = "loc_end_name.string='geo:long'";
		$where[] = "loc_end_value.string >= $long_min";
		$where[] = "loc_end_value.string <= $long_max";
		
		if (!$count) 
		{
			$query = "SELECT e.* from {$CONFIG->dbprefix}entities e $loc_join where ";
		} 
		else 
		{
			$query = "SELECT count(e.guid) as total from {$CONFIG->dbprefix}entities e $loc_join where ";
		}
		foreach ($where as $w) 
		{
			$query .= " $w and ";
		}
		
		$query .= get_access_sql_suffix('e'); // Add access controls
		
		if (!$count) 
		{
			//$query .= " order by n.calendar_start $order_by";
			// Add order and limit
			if ($limit) 
			{
				$query .= " limit $offset, $limit";
			}
			$dt = get_data($query, "entity_row_to_elggstar");
			return $dt;
		} 
		else 
		{
			$total = get_data_row($query);
			return $total->total;
		}
	}
	
	function event_manager_export_attendees($event, $file = false)
	{
		if($file)
		{
			$EOL = "\r\n";
		}
		else
		{
			$EOL = PHP_EOL;
		}
		
		$headerString .= '"'.elgg_echo('name').'","'.elgg_echo('email').'","'.elgg_echo('username').'"';
		
		if($event->registration_needed)
		{
			if($registration_form = $event->getRegistrationFormQuestions())
			{
				foreach($registration_form as $question)
				{
					$headerString .= ',"'.$question->title.'"';
				}
			}
		}
		
		if($event->with_program)
		{
			if($eventDays = $event->getEventDays())
			{
				foreach($eventDays as $eventDay)
				{
					$date = date(EVENT_MANAGER_FORMAT_DATE_EVENTDAY, $eventDay->date);
					if($eventSlots = $eventDay->getEventSlots())
					{
						foreach($eventSlots as $eventSlot)
						{
							$start_time = $eventSlot->start_time;
							$end_time = $eventSlot->end_time;
							
							$start_time_hour = date('H', $start_time);
							$start_time_minutes = date('i', $start_time);
							
							$end_time_hour = date('H', $end_time);
							$end_time_minutes = date('i', $end_time);
							
							$headerString .= ',"Event activity: \''.$eventSlot->title.'\' '.$date. ' ('.$start_time_hour.':'.$start_time_minutes.' - '.$end_time_hour.':'.$end_time_minutes.')"';
						}
					}
				}
			}
		}
		
		if($attendees = $event->getEntitiesFromRelationship(EVENT_MANAGER_RELATION_ATTENDING))
		{
			foreach($attendees as $attendee)
			{
				$answerString = '';
				
				$dataString .= '"'.$attendee->name.'","'.$attendee->email.'","'.$attendee->username.'"';
			
				if($event->registration_needed)
				{
					if($registration_form = $event->getRegistrationFormQuestions())
					{
						foreach($registration_form as $question)
						{
							$answer = $question->getAnswerFromUser($attendee->getGUID());
							
							$answerString .= '"'.addslashes($answer->value).'",';
						}
					}	
					$dataString .= ','.substr($answerString, 0, (strlen($answerString) -1));
				}
				
				if($event->with_program)
				{
					if($eventDays = $event->getEventDays())
					{
						foreach($eventDays as $eventDay)
						{
							if($eventSlots = $eventDay->getEventSlots())
							{
								foreach($eventSlots as $eventSlot)
								{
									if(check_entity_relationship($attendee->getGUID(), EVENT_MANAGER_RELATION_SLOT_REGISTRATION, $eventSlot->getGUID()))
									{
										$dataString .= ',"V"';
									}
									else
									{
										$dataString .= ',""';
									}
								}
							}
						}
					}
				}
				
				$dataString .= $EOL;
			}
		}
		
		$headerString .= $EOL;
		
		return $headerString.$dataString;
	}
	
	function event_manager_get_migratable_events()
	{
		global $CONFIG;
		
		$entities_options = array(
							'type' 			=> 'object',
							'subtype' 		=> 'event_calendar',
							'limit'			=> false,
						);		
							
						
						
		$migratable_events = array();
		if($entities = elgg_get_entities($entities_options))
		{
			foreach($entities as $event)
			{
				if(!$event->migrated)
				{
					$migratable_events[] = $event;
				}
			}
		}
		
		return $result = array('entities' => $migratable_events, 'count' => count($migratable_events));
	}
	
	function sanitize_filename($string, $force_lowercase = true, $anal = false) 
	{
	    $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
	                   "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
	                   "—", "–", ",", "<", ">", "/", "?");
	    $clean = trim(str_replace($strip, "", strip_tags($string)));
	    $clean = preg_replace('/\s+/', "-", $clean);
	    $clean = ($anal) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean ;
	    return ($force_lowercase) ?
	        (function_exists('mb_strtolower')) ?
	            mb_strtolower($clean, 'UTF-8') :
	            strtolower($clean) :
	        $clean;
	}
	
	function event_manager_search_get_where_sql($table, $fields, $params, $use_fulltext = true) 
	{
		global $CONFIG;
		$query = $params['query'];
		
		// add the table prefix to the fields
		foreach ($fields as $i => $field) 
		{
			if ($table) 
			{
				$fields[$i] = "$table.$field";
			}
		}
		
		$where = '';
		
		// if query is shorter than the min for fts words
		// it's likely a single acronym or similar
		// switch to literal mode
		if (elgg_strlen($query) < $CONFIG->search_info['min_chars']) 
		{
			$likes = array();
			$query = sanitise_string($query);
			foreach ($fields as $field) 
			{
				$likes[] = "$field LIKE '%$query%'";
			}
			$likes_str = implode(' OR ', $likes);
			$where = "($likes_str)";
		} 
		else 
		{
			// if we're not using full text, rewrite the query for bool mode.
			// exploiting a feature(ish) of bool mode where +-word is the same as -word
			if (!$use_fulltext) 
			{
				$query = '+' . str_replace(' ', ' +', $query);
			}
			
			// if using advanced, boolean operators, or paired "s, switch into boolean mode
			$booleans_used = preg_match("/([\-\+~])([\w]+)/i", $query);
			$quotes_used = (elgg_substr_count($query, '"') >= 2); 
			
			if (!$use_fulltext || $booleans_used || $quotes_used) 
			{
				$options = 'IN BOOLEAN MODE';
			} 
			else 
			{
				// natural language mode is default and this keyword isn't supported in < 5.1
				//$options = 'IN NATURAL LANGUAGE MODE';
				$options = '';
			}
			
			$query = sanitise_string($query);
			
			$fields_str = implode(',', $fields);
			$where = "(MATCH ($fields_str) AGAINST ('$query*' $options))";
		}
		
		return $where;
	}
	
	/* Used for maps */
	function getRealIpAddr()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP']))
		{
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
	
	function IPtoLatLng($ip)
	{
	    $latlngValue=array();
	    $dom = new DOMDocument();
	    $ipcheck = ip2long($ip);
	 
	 
	    if($ipcheck == -1 || $ipcheck === false)
	    {
	        return false;
	    }
	    else
	    {
	        $uri = "http://api.hostip.info/?ip=$ip&position=true";
	    }
	    $dom->load($uri);
	    
	    $name = $dom->getElementsByTagNameNS('http://www.opengis.net/gml','name')->item(1)->nodeValue;
	    
	    $coordinates = $dom->getElementsByTagNameNS('http://www.opengis.net/gml','coordinates')->item(0)->nodeValue;
	    
	    $countryName = $dom->getElementsByTagName('countryName')->item(0)->nodeValue;
	    
	    $temp = explode(",",$coordinates);
	    
	    $latlngValue['LNG'] = $temp[0];
	    $latlngValue['LAT'] = $temp[1];
	    $latlngValue['NAME'] = $name;
	    $latlngValue['COUNTRY'] = $countryName;
	    
	    return $latlngValue;
	 
	}
	
	function trim_array_values(&$value) 
	{ 
	    $value = trim($value); 
	}
	
	function event_manager_event_region_options()
	{
		$result = false;
		
		$region_settings = trim(get_plugin_setting('region_list', 'event_manager'));
		
		if(!empty($region_settings))
		{
			$region_options = array('-');
			$region_list = explode(',', $region_settings);
			$region_options = array_merge($region_options, $region_list);
			array_walk($region_options, 'trim_array_values');
			
			$result = $region_options;
			
		}
		return $result;
	}
	
	function event_manager_event_type_options()
	{
		$result = false;
		
		$type_settings = trim(get_plugin_setting('type_list', 'event_manager'));
		
		if(!empty($type_settings))
		{
			$type_options = array('-');
			$type_list = explode(',', $type_settings);
			$type_options = array_merge($type_options, $type_list);
			array_walk($type_options, 'trim_array_values');
			
			$result = $type_options;
			
			
		}
		return $result;
	}
	
	function event_manager_has_maps_key()
	{
		static $has_maps_key;
		if(isset($has_maps_key))
		{
			return $has_maps_key;
		}
		
		$return = false;
		
		if(get_plugin_setting('google_maps_key','event_manager') != '')
		{
			$return = true;
		}
		
		$has_maps_key = $return;
		
		return $return;
	}
	
	function event_manager_get_form_pulldown_hours($internalname = '', $value = '', $h = 24)
	{
		$time_hours_options = range(0, $h);
		
		array_walk($time_hours_options, 'event_manager_time_pad');
		
		return elgg_view('input/pulldown', array('internalname' => $internalname, 'value' => $value, 'options' => $time_hours_options));
	}
	
	function event_manager_get_form_pulldown_minutes($internalname = '', $value = '')
	{
		$time_minutes_options = range(0, 59, 5);
		
		array_walk($time_minutes_options, 'event_manager_time_pad');
		
		return elgg_view('input/pulldown', array('internalname' => $internalname, 'value' => $value, 'options' => $time_minutes_options));
	}
	
	function event_manager_time_pad(&$value) 
	{ 
	    $value = str_pad($value, 2, "0", STR_PAD_LEFT);; 
	}
	
	function get_curl_content($link)
	{
		$result = false;
		
		$ch = curl_init();
		$timeout = 5;
		
		curl_setopt ($ch, CURLOPT_URL, $link);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		
		curl_setopt($ch, CURLOPT_COOKIE, 'Elgg='.$_COOKIE['Elgg']); 
		
		$content = curl_exec($ch);
		
		curl_close($ch);
		
		if($content)
		{
			$result = $content;
		}
		
		return $result;
	}
	
	function event_manager_check_sitetakeover_event()
	{
		global $CONFIG;
		
		static $site_take_over;
		if(isset($site_take_over)){
			return $site_take_over;
		}
		
		$result = false;
		
		$entities_options = array(
			'type' 			=> 'object',
			'subtype' 		=> 'event',
			'joins' 		=> array(
								"JOIN {$CONFIG->dbprefix}objects_entity oe ON e.guid 			= oe.guid",
		
								"JOIN {$CONFIG->dbprefix}metadata n_table ON e.guid 			= n_table.entity_guid",
		
								"JOIN {$CONFIG->dbprefix}metastrings msn ON n_table.name_id 	= msn.id",
								"JOIN {$CONFIG->dbprefix}metastrings msv ON n_table.value_id 	= msv.id"),
			'wheres' 		=>  '(msn.string LIKE "site_takeover") AND (msv.string LIKE "1")'
		);
		
		if($entities = elgg_get_entities($entities_options))
		{
			$result['entities'] = $entities;
			
			$entities_options['count'] = true;
			$result['count'] = elgg_get_entities($entities_options);
		}
		
		$site_take_over = $result;
		
		return $result;
	}