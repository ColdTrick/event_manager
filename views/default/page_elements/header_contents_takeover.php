<?php
/**
 * Elgg header contents
 * This file holds the header output that a user will see
 *
 * @package Elgg
 * @subpackage Core
 **/

?>
<style type="text/css">

#page_wrapper
{
	width: 750px !important;
}

#event_manager_layout_header_title
{
	position: absolute;
	left: 0px;
}

#event_manager_layout_header_date
{
	float: right;
}

</style>

<div id="page_container">
<div id="page_wrapper">

<div id="layout_header">
	<a href="<?php echo $vars['url']; ?>"></a>
	<?php 
	$event = elgg_get_page_owner_entity();
	?>
	
	<div id="event_manager_layout_header_title"><h1><a href="<?php echo $event->getURL();?>"><?php echo $event->title;?></a></h1></div>
	<div id="event_manager_layout_header_date"><h1><?php echo date(EVENT_MANAGER_FORMAT_DATE_EVENTDAY, $event->start_day);?></h1></div>
	
	<div id="event_manager_sitetakeover_menu">
		<ul>
			<li><a href="<?php echo $vars['url']; ?>pg/event/view">View</a></li>
			<?php
			if($event->with_program)
			{ 
				?>
				<li><a href="<?php echo $vars['url']; ?>pg/event/program">Program</a></li>
				<?php
			}
			  
			if($event->hasFiles())
			{
				?>
				<li><a href="<?php echo $vars['url']; ?>pg/event/files">Files</a></li>
				<?php
			} 
			?>
			<li><a href="<?php echo $vars['url']; ?>pg/event/attendees">Attendees</a></li>
			<?php 
			if($event->getLocation())
			{
				?>
				<li><a href="<?php echo $vars['url']; ?>pg/event/googlemaps">Google maps</a></li>
				<?php 
			}
			
			if(!elgg_is_logged_in())
			{
				?>
				<li><a href="<?php echo $vars['url']; ?>pg/event/login">Login</a></li>
				<?php 
			}
			else
			{
				?>
				<li><a href="<?php echo $vars['url']; ?>action/logout">Logout</a></li>
				<?php 
			}
			?>
		</ul>
	</div>
	
	<div class='clearfloat'></div>
</div><!-- /#layout_header -->
