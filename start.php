<?php

ini_set('display_errors', 'on');
error_reporting(E_ALL);

	global $CONFIG;
	
	define("EVENT_MANAGER_BASEURL", 		$CONFIG->wwwroot."pg/events");
	
	define("EVENT_MANAGER_EVENT_CLASSNAME", 					"Event");
	define("EVENT_MANAGER_EVENTDAY_CLASSNAME", 					"EventDay");
	define("EVENT_MANAGER_EVENTSLOT_CLASSNAME", 				"EventSlot");
	define("EVENT_MANAGER_REGISTRATION_CLASSNAME", 				"EventRegistration");
	define("EVENT_MANAGER_EVENTREGISTRATION_CLASSNAME", 		"EventRegistrationForm");
	define("EVENT_MANAGER_EVENTREGISTRATIONQUESTION_CLASSNAME", "EventRegistrationQuestion");
	
	define("EVENT_MANAGER_FORMAT_DATE_EVENTDAY", 	"d-m-y");
	define("EVENT_MANAGER_FORMAT_DATE_EVENTDAY_JS", "dd-mm-yy");
	define("EVENT_MANAGER_FORMAT_DATE_EVENTDAYTIME","H:i");
	
	define("EVENT_MANAGER_SEARCH_LIST_LIMIT", 		10);
	define("EVENT_MANAGER_SEARCH_LIST_MAPS_LIMIT", 	100);
	
	define("EVENT_MANAGER_RELATION_ATTENDING", 				"event_attending");
	define("EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST", 	"event_waitinglist");
	define("EVENT_MANAGER_RELATION_EXHIBITING",				"event_exhibiting");
	define("EVENT_MANAGER_RELATION_ORGANIZING",				"event_organizing");
	define("EVENT_MANAGER_RELATION_PRESENTING", 			"event_presenting");
	define("EVENT_MANAGER_RELATION_INTERESTED", 			"event_interested");
	define("EVENT_MANAGER_RELATION_UNDO", 					"event_undo");

	define("EVENT_MANAGER_RELATION_REGISTRATION_QUESTION", 			"event_registration_questions");
	define("EVENT_MANAGER_RELATION_USER_REGISTERED", 				"event_user_registered");
	define("EVENT_MANAGER_RELATION_SLOT_REGISTRATION", 				"event_slot_registration");
	define("EVENT_MANAGER_RELATION_SLOT_REGISTRATION_WAITINGLIST", 	"event_slot_registration_waitinglist");
		
	include_once(dirname(__FILE__)."/lib/functions.php");
	include_once(dirname(__FILE__)."/lib/run_once.php"); 
	include_once(dirname(__FILE__)."/lib/events.php");
	include_once(dirname(__FILE__)."/lib/hooks.php");
	
	include_once(dirname(__FILE__)."/classes/Event.php");
	include_once(dirname(__FILE__)."/classes/EventDay.php");
	include_once(dirname(__FILE__)."/classes/EventSlot.php");
	include_once(dirname(__FILE__)."/classes/EventRegistrationForm.php");
	include_once(dirname(__FILE__)."/classes/EventRegistrationQuestion.php");
	include_once(dirname(__FILE__)."/classes/EventRegistration.php");

	function event_manager_init()
	{
		global $CONFIG;
		
		// Register subtype
		run_function_once('event_manager_run_once_subtypes');
		
		// Register entity_type for search
		register_entity_type('object', Event::SUBTYPE);
		
		elgg_extend_view("css", "event_manager/css");
		elgg_extend_view("css", "fancybox/css");
		elgg_extend_view("js/initialise_elgg", "event_manager/js");
		elgg_extend_view("metatags", "event_manager/metatags");
		
		add_widget_type("events", elgg_echo("event_manager:widgets:events:title"), elgg_echo("event_manager:widgets:events:description"), "index,dashboard,profile,groups");
		
		$sitetakeover = event_manager_check_sitetakeover_event();
		if($sitetakeover['count']>0)
		{
			define('EVENT_MANAGER_SITETAKEOVER', true);
						
			register_page_handler("event", "event_manager_event_page_handler");
			if($sitetakeover['entities'][0]->canEdit())
			{
				register_page_handler("events", "event_manager_page_handler");
			}
			
			if(in_array(get_context(), array('main', 'event')))
			{
				set_page_owner($sitetakeover['entities'][0]->getGUID());
			}
			
			register_plugin_hook('index', 'system', 'event_manager_sitetakeover_hook', 10);
		}
		else
		{
			register_page_handler("events", "event_manager_page_handler");
		}
		
		if(!is_plugin_enabled('event_calendar'))
		{
			register_page_handler('event_calendar', 'event_manager_event_calendar_page_handler');
		}
		
		add_menu(elgg_echo("event_manager:menu:title"), EVENT_MANAGER_BASEURL);
		
		register_plugin_hook('entity:icon:url', 'object', 'event_manager_eventicon_hook');
		
		add_group_tool_option('event_manager',elgg_echo('groups:enableevents'),true);
		elgg_extend_view('groups/right_column', 'event_manager/groupprofile_events');
		
		if(!isloggedin() && is_plugin_enabled('captcha'))
		{			
			register_plugin_hook("action", 'event_manager/event/register', "event_manager_register_postdata_hook", 300);
			register_plugin_hook("action", 'event_manager/event/register', "captcha_verify_action_hook");
		}
	}
	
	function event_manager_event_page_handler($page)
	{
		$sitetakeover = event_manager_check_sitetakeover_event();
		
		$include = "/pages/sitetakeover/view.php";
		
		if(!empty($page))
		{
			$include = "/pages/sitetakeover/".$page[0].".php";
		}
		
		set_input('guid', $sitetakeover['entities'][0]->getGUID());
		
		if(file_exists(dirname(__FILE__).$include))
		{
			if($page[0] == 'googlemaps')
			{
				elgg_extend_view("metatags", "event_manager/googlemapsjs");
			}
			include(dirname(__FILE__).$include);
		}
		else
		{
			include(dirname(__FILE__)."/pages/sitetakeover/view.php");
		}
	}
	
	function event_manager_event_calendar_page_handler($page)
	{
		register_error(elgg_echo('changebookmark'));
		forward('pg/events');
	}

	function event_manager_page_handler($page)
	{
		if(in_array($page[1], array('list', 'view', 'new', 'edit', '')))
		{
			if(event_manager_has_maps_key())
			{
				elgg_extend_view("metatags", "event_manager/googlemapsjs");
			}
		}
	
		$include = "/pages/event/list.php";
		if(!empty($page))
		{
			switch($page[0])
			{
				case "proc":
					if(file_exists(dirname(__FILE__)."/procedures/".$page[1]."/".$page[2].".php"))
					{
						$include = "/procedures/".$page[1]."/".$page[2].".php";
						
					} 
					else 
					{
						echo json_encode(array('valid' => 0));
						exit;
					}
					break;
				case "event":
					switch($page[1])
					{
						case 'register':
							if(!empty($page[3]))
							{
								set_input("relation", $page[3]);	
							}
							break;
						case 'file':
							if(!empty($page[3]))
							{
								set_input("time", $page[3]);
								set_input("file", $page[4]);
							}
							break;
						case 'list':
							set_input("username", $page[2]);
							break;
						case 'new':
							$page[1] = "edit";
							set_input("username", $page[2]);
					}
				default:
					if(!empty($page[2]))
					{
						set_input("guid", $page[2]);
					}
					
					if(file_exists(dirname(__FILE__)."/pages/".$page[0]."/".$page[1].".php"))
					{
						$include = "/pages/".$page[0]."/".$page[1].".php";
					}
					else
					{
						forward(EVENT_MANAGER_BASEURL);
					}			
					break;
			}			
		}
		
		include(dirname(__FILE__).$include);
	}

	function event_manager_pagesetup()
	{
		global $CONFIG;
		
		$sitetakeover = event_manager_check_sitetakeover_event();
		if($sitetakeover['count']>0)
		{
			if(!isadminloggedin() && !in_array(get_context(), array('main', 'event')))
			{
				forward();
			}
		}
		
		$context = get_context();
		if(in_array($context, array("events", "groups")))
		{
			$page_owner = page_owner_entity();
			$user = get_loggedin_user();
			
			if($page_owner instanceof ElggGroup)
			{
				// group				
				if($context == 'events')
				{
					add_submenu_item(elgg_echo("event_manager:menu:events"), EVENT_MANAGER_BASEURL.'/event/list');
				}
				
				if($page_owner->event_manager_enable != "no")
				{
					$who_create_group_events = get_plugin_setting('who_create_group_events', 'event_manager'); // group_admin, members
					if(!empty($who_create_group_events))
					{
						add_submenu_item(elgg_echo("event_manager:menu:group_events"), EVENT_MANAGER_BASEURL.'/event/list/'. $page_owner->username);
	
						if($context == "events" && ((($who_create_group_events == "group_admin") && $page_owner->canEdit()) || (($who_create_group_events == "members") && $page_owner->isMember($user))))
						{
							add_submenu_item(elgg_echo("event_manager:menu:new_event"), EVENT_MANAGER_BASEURL.'/event/new/' . $page_owner->username);  	
						} 
					}
				}
			} 
			elseif($context == 'events') 
			{
				// site
				add_submenu_item(elgg_echo("event_manager:menu:events"), EVENT_MANAGER_BASEURL.'/event/list');
				
				if($user)
				{
					$who_create_site_events = get_plugin_setting('who_create_site_events', 'event_manager');
					if($who_create_site_events != 'admin_only' || isadminloggedin())
					{
						add_submenu_item(elgg_echo("event_manager:menu:new_event"), EVENT_MANAGER_BASEURL.'/event/new');
					}
				}	
			}
		}
	}

	// register default elgg events
	register_elgg_event_handler("init", "system", "event_manager_init");
	register_elgg_event_handler("pagesetup", "system", "event_manager_pagesetup");
	
	register_action("event_manager/event/edit",				false,dirname(__FILE__)."/actions/event/edit.php");
	register_action("event_manager/event/delete",			false,dirname(__FILE__)."/actions/event/delete.php");
	register_action("event_manager/event/rsvp",				false,dirname(__FILE__)."/actions/event/rsvp.php");
	register_action("event_manager/event/register",			true, dirname(__FILE__)."/actions/event/register.php");
	register_action("event_manager/event/upload",			false,dirname(__FILE__)."/actions/event/upload.php");
	register_action("event_manager/event/deletefile",		false,dirname(__FILE__)."/actions/event/deletefile.php");
	register_action("event_manager/event/search",			false,dirname(__FILE__)."/actions/event/search.php");
	register_action("event_manager/attendees/export",		false,dirname(__FILE__)."/actions/attendees/export.php");
	register_action("event_manager/slot/edit",				false,dirname(__FILE__)."/actions/slot/edit.php");
	register_action("event_manager/questions/edit",			false,dirname(__FILE__)."/actions/registrationform/edit.php");
	register_action("event_manager/registration/edit",		false,dirname(__FILE__)."/actions/registration/edit.php");
	register_action("event_manager/registration/approve",	false,dirname(__FILE__)."/actions/registration/approve.php");
	register_action("event_manager/registration/pdf",		false,dirname(__FILE__)."/actions/registration/pdf.php");
	register_action("event_manager/migrate/calender",		false,dirname(__FILE__)."/actions/migrate/calender.php", true);
	register_action("event_manager/event/sitetakeover",		false,dirname(__FILE__)."/actions/event/sitetakeover.php", true);