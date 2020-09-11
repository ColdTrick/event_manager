<?php
/**
 * Map listing of events used by resource files
 *
 * @uses $vars['options']    Additional options for elgg_list_entities()
 * @uses $vars['resource']   The calling resource
 * @uses $vars['page_owner'] Page owner during the call
 */

use Elgg\EntityNotFoundException;

$maps_provider = elgg_get_plugin_setting('maps_provider', 'event_manager');
if ($maps_provider === 'none') {
	throw new EntityNotFoundException();
}

$page_owner = elgg_extract('page_owner', $vars);

$body = elgg_format_element('div', [
	'id' => 'event_manager_onthemap_canvas',
	'data-resource' => elgg_extract('resource', $vars),
	'data-guid' => ($page_owner instanceof ElggEntity) ? $page_owner->guid : null,
]);

echo elgg_format_element('div', ['id' => 'event_manager_event_map'], $body);

echo elgg_format_element('script', [], 'require(["elgg", "event_manager/maps/' . $maps_provider . '/onthemap"], function(elgg) {
	elgg.trigger_hook("tab:onthemap", "event_manager");
});');
