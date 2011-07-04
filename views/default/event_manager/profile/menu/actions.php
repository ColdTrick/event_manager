<?php 

$user = $vars['entity'];
$eventGuid = get_input('guid');

if($user->getGUID() != get_entity($eventGuid)->owner_guid)
{
	echo '<p class="user_menu_kickfromevent"><a href="'.elgg_add_action_tokens_to_url($vars['url'] . 'action/event_manager/event/rsvp?guid='.$eventGuid.'&user='.$user->getGUID().'&type='.EVENT_MANAGER_RELATION_UNDO).'">'.elgg_echo("event_manager:event:relationship:kick").'</a></p>';
}