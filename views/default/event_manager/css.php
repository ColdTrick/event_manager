<?php 
	$graphics_folder = $vars["url"] . "mod/event_manager/_graphics/";
?>

/* Event global */
.event_manager_back{
	float: right;
	font-size: 12px;
	padding: 0 10px 0 0 ;
}

.event_manager_required {
	color: #AAAAAA;
}

/* Event Edit */
#event_manager_event_edit label{
	font-weight: normal;
	font-size: 100%;
}

#event_manager_event_edit > table {
	width: 100%;
}

#event_manager_event_edit > table td {
	padding: 0 0 1px 0;
}

#event_manager_event_edit .event_manager_event_edit_label {
	max-width: 250px;
}

/* Event listing  */
#event_manager_event_search_advanced_enable {
	float: right;
	font-size: 12px;
	font-weight: normal;
}

.event_manager_event_list_search_input
{
	width: 300px;
}
.event_manager_event_list_owner, .event_manager_event_view_owner
{
	color: #808080;
	font-size: 11px;
	border-bottom: 1px solid #CCCCCC;
	padding: 0 0 2px;
}

.event_manager_event_list_actions {
	float: right;
	text-align: right;
}

.event_manager_event_list_icon {
	text-align: center;
	width: 40px;
	border-right: 1px solid #CECECE;
	border-bottom: 1px solid #CECECE;
}

.event_manager_event_list_icon_day {
	font-size: 15px;
    font-weight: bold;
    line-height: 23px;
    background: #FFFFFF;
    border-bottom: 1px solid #4690D6;
    border-left: 1px solid #4690D6;
    border-right: 1px solid #4690D6;
    
}
.event_manager_event_list_icon_month {
	background: #4690D6;
	color: #FFFFFF;
	font-size: 11px;
    line-height: 11px;
    padding: 2px;
    border: 1px solid #4690D6;
}

.event_manager_event_list_count 
{
	color: #666666;
    font-weight: bold;
    margin: 0 0 5px 4px;
}

#event_manager_event_listing div.pagination {
	margin: 0;
}
#event_manager_event_listing div.search_listing {
	border: 2px solid #CCCCCC;
	margin: 0 0 5px;
}

#event_manager_event_listing div.search_listing:hover {
	background: #DEDEDE;
}

#event_manager_result_refreshing {
	display: none;
	float: right;
	color: #AAAAAA;
}

#event_manager_event_list_search_more
{
	width: auto; 
	line-height: 40px;
	height: 40px;
	text-align: center;
	-webkit-border-radius: 8px;
	-moz-border-radius: 8px;
	border: 2px solid #CCCCCC; 
	cursor: pointer;
}

#event_manager_event_map {
	display: none;
}

/* Event Search */
#event_manager_search_form h3{
	margin: 0;
}

#event_manager_search_form label {
	font-weight: normal;
	font-size: 100%;
}

#event_manager_event_search_advanced_container
{
	display: none;
}

/* Event view */

.event_manager_event_view_image {
	float: right;
	padding: 10px;
	background: #FFFFFF;
	border: 1px solid #CCCCCC;
}

.event_manager_event_view_attendees
{
	display: none;
}

.event_manager_event_files {
	list-style: none;
	padding: 0;
}

.event_manager_event_files li{
	background: url("<?php echo $graphics_folder; ?>file_icon.png") no-repeat left center; 
	padding-left: 20px;
}

.event_manager_event_view_attendees .usericon, .event_manager_event_view_waitinglist .usericon {
	width: 40px;
	float: left;
	margin: 0 5px 0 0;
}

/* Event tool links */

.event_manager_event_actions_drop_down {
	position: relative;
	background: url(<?php echo $graphics_folder; ?>arrows_down.png) right center no-repeat;
	padding-right: 15px;
	cursor: pointer;
	z-index: 1;
}

.event_manager_event_actions_drop_down ul {	
    background: #FFFFFF;
    display: none;
    left: -1px;
    margin: -2px 0 0;
    padding: 5px 2px 5px 0;
    position: absolute;
    white-space: nowrap;
    border-color: #CCCCCC;
    border-style: solid;
	z-index: 1;
    border-width: 1px 1px 1px 1px;
    list-style-type: none;
}

.event_manager_event_actions_drop_down:hover ul {
	display: block;
	position: absolute;
	z-index: 10;
    left: -1px;
    top: 18px;
}

.event_manager_event_actions_drop_down:hover
{
	z-index: 10;
	
	zoom: 1; /* IE hack */
}

.event_manager_event_actions_drop_down li {
	padding: 0 18px 0 10px;
	line-height: 18px;	
}

/* Event Program Edit */
#event_manager_program_edit {
	float: right;
	font-size: 12px;
	font-weight: normal;
}

/* Event Program Day */
.event_manager_program_day_details{
	margin: 0 0 2px 21px;
	padding: 0 0 2px 0;
	border-bottom: 1px solid #CCCCCC;
}

.event_manager_program_day > table {
	margin-bottom: 5px;
}

.event_manager_program_day {
	display: none;
}

/* Event Program Slot */
.event_manager_program_slot_time {
	padding: 0px 5px;
	white-space: nowrap;
}

.event_manager_program_slot_attending{
	width: 20px;
}

.event_manager_program_slot_attending_user { 
	width: 16px;
	height: 16px ;  
	background: url(<?php echo $graphics_folder; ?>vink.png) right top no-repeat;
} 

.event_manager_program_slot_time,
.event_manager_program_slot_title {
	font-weight: bold;
}

.event_manager_program_slot_subtitle {
	color: #AAAAAA;
}

.event_manager_program_slot_description{
	word-wrap: break-word;
	width: 500px;
}

.event_manager_program_slot_add {
	padding-left: 21px;
}

/* Select relation links */
.event_manager_event_select_relationship_selected {
	font-weight: bold;
}

.event_manager_event_select_relationship li:hover {
	background: url(<?php echo $graphics_folder; ?>vink.png) right top no-repeat;
}

.event_manager_event_select_relationship li.selected {
	background: url(<?php echo $graphics_folder; ?>vink.png) right bottom no-repeat;
}

/* Registration */
.event_manager_registration_icon
{
	width: 40px;
	height: 40px;
}
.event_manager_registration_info
{
	float: left;
}
.event_manager_registration_links
{
	float: right;
}

/* Editable fields */
.changeSlotFieldText, .changeSlotFieldLongText, .changeSlotFieldInteger
{
	margin-left: 20px;
	font-weight: normal;
	display: block;
	height: 16px;
	background-color: #ffffff;
	width: 350px;
	word-wrap: break-word;
}

.changeSlotFieldText, .changeDayFieldText, .changeSlotFieldLongText, .changeSlotFieldInteger
{
	cursor: url(<?php echo $graphics_folder; ?>icons/edit_cursor2.png), auto ;
}

.slotFieldTextarea
{
	width: 320px;
	height: 200px;
}

/* river events */
.river_object_event_event_relationship 
{
	background: url("<?php echo $graphics_folder; ?>icons/river_icon_event.gif") no-repeat scroll left -1px transparent;
}

/* google maps */
.gmaps_infowindow, .gmaps_infowindow_text div.event_manager_event_view_owner
{
	font-size: 11px;
}

.gmaps_infowindow_text
{
	float: left; 
	width: 250px;
}

.gmaps_infowindow_icon
{
	float: right; 
	height: 100px; 
	width: 100px; 
	padding: 10px; 
	border: 1px solid #CCCCCC;
}

#event_manager_onthemap_legend {
	padding: 5px 0px;
	text-align: center;
}

#event_manager_onthemap_legend img {
	vertical-align: middle;
}

#event_manager_onthemap_sidebar ul
{
	list-style-type: none;
	padding: 0;
	margin: 0;
}

.event_manager_event_file_delete
{
	background: url("http://dev174.coldtrick.com/_graphics/icon_customise_remove.png") no-repeat scroll left top transparent;
	display: inline-block;
	height: 15px;
	width: 14px;
}

.event_manager_event_file_delete:hover
{
	background-position: 0 -16px;
}

.user_menu_kickfromevent
{
	margin: 0px;
}

#event_manager_event_register ul, #event_manager_registrationform_fields
{
	margin: 0px;
	padding: 0px;
	list-style: none;
}

#event_manager_event_register ul li, #event_manager_registrationform_fields li
{
	margin-bottom: 10px;
}

#event_manager_registrationform_fields li span.move
{
	cursor: move;
	width: 13px;
	height: 13px;
	background-image: url("<?php echo $graphics_folder; ?>icons/move_icon.gif");
	display: inline-block;
}

.event_manager_registrationform_field_placeholder
{ 
	background-color: #DEDEDE;
	height: 51px;
}