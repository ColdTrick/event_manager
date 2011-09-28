<!-- Event Manager --><?php 

global $datepicker;

if (empty($datepicker)) 
{
	echo <<< END
<script type="text/javascript" src="{$vars['url']}mod/event_manager/vendors/jquery.datepick.package-4.0.5/jquery.datepick.pack.js"></script>
<link rel="stylesheet" type="text/css" href="{$vars['url']}mod/event_manager/vendors/jquery.datepick.package-4.0.5/redmond.datepick.css">        
END;
	if(!empty($locale_js))
	{
		echo "<script type='text/javascript' src='" . $vars['url'] . "mod/event_manager/vendors/jquery.datepick.package-4.0.5/" . $locale_js . "'></script>";
	}
	
	$datepicker = 1;
} 
else 
{
	$datepicker++;
}

if(event_manager_has_maps_key())
{
	if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
	{
		$protocol = 'https';
	}
	else
	{
		$protocol = 'http';
	}
	
	?>
	
	<script src="<?php echo $protocol;?>://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=<?php echo get_plugin_setting('google_maps_key','event_manager');?>" type="text/javascript"></script>
	<?php 
}

global $fancybox_js_loaded;

if(empty($fancybox_js_loaded))
{
	$fancybox_js_loaded = true;
	?>
	<script type="text/javascript" src="<?php echo $vars["url"];?>mod/event_manager/vendors/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
	<?php 
}

?>
<script type="text/javascript">

/*
 * Adding location to event
 */
$(function ()
{		
	$('.event_manager_program_slot_delete').live('click', function()
	{
		if(confirm('<?php echo elgg_echo('deleteconfirm');?>'))
		{
			slotGuid = $(this).parent().attr("rel");
			if(slotGuid)
			{
				$slotElement = $("#" + slotGuid); 
				$slotElement.hide();
				$.getJSON('<?php echo EVENT_MANAGER_BASEURL; ?>/proc/slot/delete', {guid: slotGuid}, function(response)
				{
					if(response.valid)
					{
						$slotElement.remove();
					}
					else
					{
						$slotElement.show();
					}																					
				});
			}
		}
		return false;
		
	});

	$('.event_manager_program_day_delete').live('click', function(e)
	{
		if(confirm('<?php echo elgg_echo('deleteconfirm');?>'))
		{
			dayGuid = $(this).parent().attr("rel");
			if(dayGuid)
			{
				$dayElements = $("#day_" + dayGuid + ", #event_manager_event_view_program li.selected"); 
				$dayElements.hide();
				$.getJSON('<?php echo EVENT_MANAGER_BASEURL; ?>/proc/day/delete', {guid: dayGuid}, function(response)
				{
					if(response.valid)
					{
						// remove from DOM
						$dayElements.remove();
						if($("#event_manager_event_view_program li").length > 1){
							$("#event_manager_event_view_program li:first a").click();
						}						
					}
					else
					{
						// revert
						$dayElements.show();
					}																					
				});
			}
		}
		
		return false;
	});
	
	$('#event_manager_program_register').click(function()
	{
		$.getJSON('<?php echo EVENT_MANAGER_BASEURL; ?>/proc/program/register', {event: $('#eventguid').val(), guids: guids.toSource()}, function(response)
		{
			if(response.valid)
			{
				$('#register_status').html('<?php echo elgg_echo('event_manager:registration:program:success');?>');
			}
			else
			{
				$('#register_status').html('<?php echo elgg_echo('event_manager:registration:program:fail');?>');
			}
		});
	});

	$('.event_manager_questions_delete').live('click', function(e)
	{
		if(confirm('<?php echo elgg_echo('deleteconfirm');?>'))
		{
			questionGuid = $(this).attr("rel");
			if(questionGuid)
			{
				$questionElement = $(this); 
				$questionElement.parent().hide();
				$.getJSON('<?php echo EVENT_MANAGER_BASEURL; ?>/proc/question/delete', {guid: questionGuid}, function(response)
				{
					if(response.valid)
					{
						// remove from DOM
						$questionElement.parent().remove();				
					}
					else
					{
						// revert
						$questionElement.parent().show();
					}																					
				});
			}
		}
		
		return false;
	});
});
</script>
<!-- End Event Manager -->