<?php 

	$event = $vars["entity"];
	$register_type = $vars["register_type"];
	
	if(!empty($event) && ($event instanceof Event))
	{
		if($event->with_program)
		{
			if($eventDays = $event->getEventDays())
			{
				foreach($eventDays as $key => $day)
				{
					if($key == 0 )
					{
						// select the first
						$selected = true;
						$tabtitles .= "<li class='selected'>";
					} 
					else 
					{
						$selected = false;
						$tabtitles .= "<li>";
					}
					$tabtitles .= "<a href='javascript:void(0);' rel='day_" . $day->getGUID() . "'>" . date(EVENT_MANAGER_FORMAT_DATE_EVENTDAY, $day->date) . "</a></li>";
					
					$tabcontent .= elgg_view("event_manager/program/elements/day", array("entity" => $day, "selected" => $selected, 'participate' => true, 'register_type' => $register_type));
				}
			}
			
			echo "<h3 class='settings'>" . elgg_echo('event_manager:event:progam') . "</h3>";
			
			echo '<div id="event_manager_event_view_program">';
			echo '<div id="elgg_horizontal_tabbed_nav"><ul>';
			
			echo $tabtitles;
			
			echo '</ul></div>';
			
			echo '</div>';
			echo elgg_view('input/hidden', array('internalid' => 'event_manager_program_guids', 'internalname' => 'program_guids'));
			
			echo $tabcontent;
			
			?>
				<script type='text/javascript'>
					$(document).ready(function()
					{
						$("#event_manager_event_view_program a").live("click", function()
						{
							$(".event_manager_program_day").hide();
							$("#event_manager_event_view_program li").removeClass("selected");
							var selected = $(this).attr("rel");
							$(this).parent().addClass("selected");
							$("#" + selected).show();
						});
					});
				</script>
			<?php 
		}
	}	