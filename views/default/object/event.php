<?php 

	if($vars["full"]) {
        echo elgg_view("event_manager/event/view", $vars);
    } elseif(elgg_in_context("maps")) {    
        $event = $vars["entity"];
        
        $output = '<div class="gmaps_infowindow">';
        $output .= '<div class="gmaps_infowindow_text">';
        $output .= '<div class="event_manager_event_view_owner"><a href="' . $event->getURL() . '">' . $event->title . '</a> (' . date(EVENT_MANAGER_FORMAT_DATE_EVENTDAY, $event->start_day) . ')</div>';
        $output .= $event->getLocation(true) . '<br /><br />' . $event->shortdescription . '<br /><br />';
        $output .= elgg_view("event_manager/event/actions", $vars) . '</div>';
        if($event->icontime){
            $output .= '<div class="gmaps_infowindow_icon"><img src="' . $event->getIcon('medium') . '" /></div>';
        }
        $output .= '</div>';    

        echo $output;
    } else {
        $event = $vars["entity"];
        $owner = $event->getOwnerEntity();
        $container = $event->getContainerEntity();
        
        $owner_link = elgg_view('output/url', array(
			'href' => $owner->getURL(),
			'text' => $owner->name,
		));
		
		$author_text = elgg_echo('byline', array($owner_link));
    	if(($container instanceof ElggGroup) && (elgg_get_page_owner_guid() !== $container->getGUID())){
        	$author_text .= ' ' . elgg_echo('in') . ' <a href="' . elgg_get_site_url() . 'events/event/list/' . $container->getGUID() . '">' . $container->name . '</a>';
		}
		
		$date = elgg_view_friendly_time($event->time_created);
		
		$content = "";
		$subtitle = "";
		
        if(!elgg_in_context("widgets")){
        	$subtitle = "<p>$author_text $date</p>";
        	
	        if($location = $event->getLocation()){
	            $content .= '<div>' . elgg_echo('event_manager:edit:form:location') . ': ';
	            $content .= '<a href="' . elgg_get_site_url() . 'events/event/route?from=' . $location . '" class="openRouteToEvent">' . $location . '</a>';
	            $content .= '</div>'; 
	        }
	        
	        if($shortdescription = $event->shortdescription){
	        	$content .= "<div>" . $shortdescription . "</div>";
	        }
        }
        
        $content .= elgg_view("event_manager/event/actions", $vars);
        
        $icon = elgg_view_entity_icon($event, "date");
        
        $menu = elgg_view_menu('entity', array(
			'entity' => $vars['entity'],
			'handler' => 'event',
			'sort_by' => 'priority',
			'class' => 'elgg-menu-hz',
		));
		
		$params = array(
			'entity' => $event,
			'metadata' => $menu,
			'subtitle' => $subtitle,
			'tags' => false,
			'content' => $content,
		);
		$params = $params + $vars;
		
		$list_body = elgg_view('object/elements/summary', $params);
	
		echo elgg_view_image_block($icon, $list_body);
    }
