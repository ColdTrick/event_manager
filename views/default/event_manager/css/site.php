<?php
	$graphics_folder = elgg_get_site_url() . "mod/event_manager/_graphics/";
?>

/* Event global */
.event_manager_required {
	color: #AAAAAA;
}

.event-manager-hide-owner-block .elgg-sidebar .elgg-owner-block {
	display: none;
}

/* Event Edit */
#event_manager_event_edit > fieldset > table {
	width: 100%;
}

#event_manager_event_edit .event_manager_event_edit_label {
	max-width: 250px;
}

.event_manager_event_edit_date {
	width: 100px;
	height: 20px;
	padding: 0 5px;
}

#event_manager_event_edit > fieldset > table td {
	padding: 2px 0px;
}

/* Event listing  */
#event_manager_event_search_advanced_enable {
	float: right;
	font-size: 12px;
	font-weight: normal;
}

.event_manager_event_list_search_input {
	width: 300px;
}
.event_manager_event_list_owner, 
.event_manager_event_view_owner {
	color: #808080;
	font-size: 11px;
	border-bottom: 1px solid #CCCCCC;
	padding: 0 0 2px;
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

#event_manager_event_listing div.pagination {
	margin: 0;
}

#event_manager_event_listing .elgg-list {
	border-top: none;
}

#event_manager_event_list_search_more {
	border: 1px solid #CCCCCC;
    cursor: pointer;
    line-height: 31px;
    padding: 5px;
    text-align: center;
    width: auto;
    
    -webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
}

#event_manager_event_list_search_more:hover {
	border-color: #4690D6;
}

/* Event Search */
#event_manager_search_form label {
	font-weight: normal;
	font-size: 100%;
}

#past_events {
	display: inline-block;
}

#event_manager_event_search_advanced_container .elgg-input-dropdown,
#event_manager_event_search_advanced_container .elgg-input-date {
	width: 100px;
}

/* Event view */

.event-manager-event-view-attendees .elgg-head h3 a {
	font-size: 1em;
}

.event-manager-event-view-search-attendees .elgg-icon {
	vertical-align: top;
}

.event-manager-event-view-search-attendees .elgg-input-text {
	height: 16px;
	padding: 0 5px;
	width: 150px;
}

.event-manager-event-details label {
	white-space: nowrap;
	padding-right: 20px;
	font-size: 100%;
}

.event-manager-event-details .elgg-output {
	margin: 0px;
}

/* Event tool links */

.event_manager_event_actions {
	background: url(<?php echo $graphics_folder; ?>arrows_down.png) right center no-repeat;
	padding-right: 15px;
	cursor: pointer;
}

.event_manager_event_actions_drop_down {
    background: #FFFFFF;
    padding: 5px 2px 5px 0;
    position: absolute;
    white-space: nowrap;
    border-color: #CCCCCC;
    border-style: solid;
	z-index: 10;
    border-width: 1px 1px 1px 1px;
    list-style-type: none;
    display: none;
}

.event_manager_event_actions_drop_down li {
	padding: 0 18px 0 10px;
	line-height: 18px;
}
.event_manager_event_actions_drop_down li:hover {
	padding-right: 2px;
}

/* Event Program Edit */
#event_manager_program_edit {
	float: right;
	font-size: 12px;
	font-weight: normal;
}

/* Event Program Day */

#event-manager-program-day-lightbox {
	width: 300px;
	height: 230px;
	margin-bottom: 0px;
}

#event-manager-program-day-lightbox .datepick-popup {
	z-index: 20000;
}

/* Event Program Slot */
#event-manager-program-slot-lightbox {
	width: 600px;
	height: 500px;
	overflow: auto;
}

#event_manager_form_program_slot label {
	white-space: nowrap;
	padding-right: 10px;
}

#event-manager-new-slot-set-name {
	width: 150px;
	margin-right: 10px;
}

.event-manager-program-slot-set {
	padding: 2px 4px;
	color: white;
	margin-left: 5px;
	
	border-radius: 4px;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
}

.event_manager_program_slot_time {
	padding: 0px 5px;
	white-space: nowrap;
	font-weight: bold;
}

.event_manager_program_slot_attending {
	width: 20px;
}

.event_manager_program_slot_description{
	word-wrap: break-word;
	width: 500px;
}

/* google maps */
.gmaps_infowindow,
.gmaps_infowindow_text div.event_manager_event_view_owner {
	font-size: 11px;
}

.gmaps_infowindow_text {
	float: left;
	width: 250px;
}

.gmaps_infowindow_icon {
	float: right;
	height: 100px;
	width: 100px;
	padding: 10px;
	border: 1px solid #CCCCCC;
}

#event_manager_onthemap_canvas {
	height: 650px;
	margin-bottom: 20px;
}

#event_manager_onthemap_legend {
	padding: 5px 0px;
	text-align: center;
	height: 32px;
}

#event_manager_onthemap_legend img {
	vertical-align: middle;
}

#event_manager_registrationform_lightbox {
	width: 400px;
	height: 200px;
}

#event_manager_registration_form_fields ul,
#event_manager_registrationform_fields {
	margin: 0px;
	padding: 0px;
	list-style: none;
}

#event_manager_registration_form_fields ul li,
#event_manager_registrationform_fields li {
	margin-bottom: 10px;
}

#event_manager_registrationform_fields li {
	background: #FFFFFF;
}

.addthisevent {
	display: none;
}

.addthisevent_dropdown {
	display: none;
	right: 0px;
	left: inherit !important;
	position: absolute;
	padding: 10px 0;
	background: white;
	border: 1px solid #CCC;
}

.addthisevent_dropdown span {
	display: block;
	white-space: nowrap;
	color: #4690D6;
	padding: 5px 15px;
}

.addthisevent_dropdown span:hover {
	color: white;
	background: #4690D6;
	cursor: pointer;
}
