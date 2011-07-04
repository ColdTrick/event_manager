<?php 

	$event = $vars["entity"];
	
	if($relationships = $event->getRelationships())
	{
		$i = 0;
		
		if($event->canEdit())
		{
			elgg_extend_view('profile/menu/actions', 'event_manager/profile/menu/actions');
		}
		// force correct order
		foreach(event_manager_event_get_relationship_options() as $rel)
		{
			if(array_key_exists($rel, $relationships))
			{
				if($rel == EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST && !$event->waiting_list_enabled)
				{
					continue;
				}
				
				$members = $relationships[$rel];

				$tab_titles .= "<li";
				if($i == 0){
					$tab_titles .= " class='selected'";	
				}
				$tab_titles .= "><a href='javascript:void(0);' rel='" . $event->getGUID() . "_relation_". $rel . "'>" . elgg_echo("event_manager:event:relationship:" . $rel) . " (" . count($members) . ")</a></li>";
				
				$tab_content .= "<div";
				if($i == 0){
					$tab_content .= " style='display:block;'";	
				}
				$tab_content .= " class='event_manager_event_view_attendees' id='" . $event->getGUID() . "_relation_". $rel . "'>"; 
				
				foreach($members as $member)
				{
					if(($user = get_entity($member)) instanceof ElggUser)
					{
						$tab_content .= elgg_view("profile/icon", array("entity" => $user, "size" => "small"));
					}
					else
					{
						
						$tab_content .= '<div class="usericon">';
						
						if($event->canEdit())
						{
							//http://dev174.coldtrick.com/pg/events/registration/view/?guid=7845&u_g=3194&k=b97d6f294bf074bfe6611d73273ecc5e
							$tab_content .= '<div class="avatar_menu_button" style="display: none;">
												<img width="15" height="15" border="0" src="/_graphics/spacer.gif">
											</div>
											<div class="sub_menu">
												<h3>Unregistered user</h3>
											
												<p class="user_menu_profile">
													<a href="'.EVENT_MANAGER_BASEURL.'/registration/view/?guid='.$event->getGUID().'&u_g='.$member.'">View registration</a>
													<a href="'.elgg_add_action_tokens_to_url($vars['url'] . 'action/event_manager/event/rsvp?guid='.$event->getGUID().'&user='.$member.'&type='.EVENT_MANAGER_RELATION_UNDO).'">Kick from event</a>
												</p>	
											</div>';
						}
						$tab_content .= '
										<a class="icon" href="#">
											<img border="0" title="Jeroen Dalsem" alt="Jeroen Dalsem" src="http://dev174.coldtrick.com/mod/profile/graphics/defaultsmall.gif">
										</a>
									</div>';
					}
				}
				
				$tab_content .= "</div>";
				$i++;
			}
		}
		
		if($tab_content)
		{
			$output = "<h3 class='settings'>" . elgg_echo('event_manager:event:attendees') . "</h3>";
			
			$output .= "<div id='event_manager_event_view_attendees'>";
			$output .= "<div id='elgg_horizontal_tabbed_nav'><ul>" . $tab_titles . "</ul></div>";
			$output .= "</div>";
			$output .= $tab_content;
			$output .= "<div class='clearfloat'></div>";
			
			echo $output;
			
			?>
			<script type='text/javascript'>
				$(document).ready(function(){
					$("#event_manager_event_view_attendees a").live("click", function(){
						$(".event_manager_event_view_attendees").hide();
						$("#event_manager_event_view_attendees li").removeClass("selected");
						var selected = $(this).attr("rel");
						$(this).parent().addClass("selected");
						$("#" + selected).show();
					});
				});
			</script>
			<?php
		} 
	}
	
?>