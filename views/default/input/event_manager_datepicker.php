<?php
/**
 * Elgg text input
 * Displays a text input field
 *
 * @package Elgg
 * @subpackage Core


 *
 * @uses $vars['value'] The current value, if any
 * @uses $vars['js'] Any Javascript to enter into the input tag
 * @uses $vars['internalname'] The name of the input field
 * @uses $vars['disabled'] If true then control is read-only
 * @uses $vars['class'] Class override
 */

?>

<script type="text/javascript">
	$(function(){
		$('#<?php echo $vars['internalid'];?>').datepick({
			yearRange: '-1:+10',
			dateFormat: '<?php echo EVENT_MANAGER_FORMAT_DATE_EVENTDAY_JS;?>', 
		    showTrigger: '<img src="<?php echo $vars['url']; ?>mod/event_manager/vendors/jquery.datepick.package-4.0.5/calendar.gif" alt="Popup" class="trigger">'
		});
	});

</script>
<input type="text" id="<?php echo $vars['internalid']; ?>" name="<?php echo $vars['internalname'];?>" value="<?php echo $vars['value']; ?>" style="width:200px"/>