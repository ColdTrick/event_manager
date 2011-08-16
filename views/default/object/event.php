<?php 

	$context = get_context();
    if($vars["full"])
    {
        echo elgg_view("event_manager/event/view", $vars);
    } 
    elseif($context == "maps") 
    {    
        $event = $vars["entity"];
        
        $output = '<div class="gmaps_infowindow">';
        $output .= '<div class="gmaps_infowindow_text">';
        $output .= '<div class="event_manager_event_view_owner"><a href="'.$event->getURL().'">'.$event->title.'</a> ('.date(EVENT_MANAGER_FORMAT_DATE_EVENTDAY, $event->start_day).')</div>';
        $output .= $event->getLocation(true).'<br /><br />'.$event->shortdescription.'<br /><br />';
        $output .= elgg_view("event_manager/event/actions", $vars).'</div>';
        if($event->icontime){
            $output .= '<div class="gmaps_infowindow_icon"><img src="'.$event->getIcon('medium').'" /></div>';
        }
        $output .= '</div>';    

        echo $output;
    }
    else 
    {
        $event = $vars["entity"];
        $owner = $event->getOwnerEntity();
        $container = $event->getContainerEntity();
        $output = '';

        if($context != 'widget')
        {
            $output .= '<div class="event_manager_event_list_actions">';
            $output .= '<div class="event_manager_event_list_owner">';
            $output .= elgg_echo('event_manager:event:view:createdby').' <a href="'.$owner->getURL().'">'.$owner->name.'</a>';
            if(($container instanceof ElggGroup) && (page_owner() !== $container->getGUID())){
                $output .= ' ' . elgg_echo('in').' <a href="' . EVENT_MANAGER_BASEURL . '/event/list/'.$container->username.'">'.$container->name.'</a>';
            } 
            $output .= '</div>';
            
            $output .= '</div>';
        }
        
        $output .= '<div>';
        
        $output .= '<div><a href="'.$event->getURL().'">'.$event->title.'</a></div>';
        $output .= '<div>'.elgg_echo('event_manager:event:view:date').': '.date(EVENT_MANAGER_FORMAT_DATE_EVENTDAY,$event->start_day).'</div>';

        if($location = $event->getLocation()){
            $output .= '<div>'.elgg_echo('event_manager:edit:form:location') . ': ';
            $output .= ((event_manager_has_maps_key())?'<a href="'.EVENT_MANAGER_BASEURL.'/event/route?from='.$location.'" class="openRouteToEvent">'.$location.'</a>':$location);
            $output .= '</div>'; 
        }

        $output .= '</div>';
        
        $output .= elgg_view("event_manager/event/actions", $vars);
        
        $icon = '';
        $icon .= "<div class='event_manager_event_list_icon'>";
            $icon .= "<div class='event_manager_event_list_icon_month'>" . strtoupper(date("M",$event->start_day)) . "</div>";
            $icon .= "<div class='event_manager_event_list_icon_day'>" . date("d",$event->start_day) . "</div>";
        $icon .= "</div>";
        
        echo elgg_view_listing($icon, $output);
    }
