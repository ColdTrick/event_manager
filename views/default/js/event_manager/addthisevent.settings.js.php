<?php
$get_setting = function ($service) {
	return elgg_get_plugin_setting("show_service_{$service}", 'event_manager') ? 'true' : 'false';
};
?>

window.addeventasync = function(){
	addeventatc.settings({
	    license   : "<?php echo elgg_get_plugin_setting('add_event_license', 'event_manager');?>",
	    mouse     : false,
	    css       : false,
	    google     : {show:<?php echo $get_setting('google');?>, text:"Google <em>(online)</em>"},
        yahoo      : {show:<?php echo $get_setting('yahoo');?>, text:"Yahoo <em>(online)</em>"},
        office365  : {show:<?php echo $get_setting('office365');?>, text:"Office 365 <em>(online)</em>"},
        outlookcom : {show:<?php echo $get_setting('outlookcom');?>, text:"Outlook.com <em>(online)</em>"},
        outlook    : {show:<?php echo $get_setting('outlook');?>, text:"Outlook"},
        appleical  : {show:<?php echo $get_setting('appleical');?>, text:"iCal Calendar"},
        
        dropdown   : {order:"google,yahoo,office365,outlookcom,outlook,appleical"}
	});
};
