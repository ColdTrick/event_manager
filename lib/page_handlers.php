<?php
/**
 * Page handlers for Event Manager
 */

/**
 * Page handler
 *
 * @param array $page page elements
 *
 * @return boolean
 */
function event_manager_page_handler($page) {
	elgg_push_breadcrumb(elgg_echo("event_manager:menu:events"), "/events");

	$base_dir = elgg_get_plugins_path() . "event_manager";
	
	$include = "/pages/event/list.php";
	if (!empty($page)) {
		switch ($page[0]) {
			case "unsubscribe":
				if (isset($page[1])) {
					if ($page[1] == "confirm") {
						if (isset($page[2])) {
							set_input("guid", $page[2]);
						}

						if (isset($page[3])) {
							set_input("code", $page[3]);
						}

						$include = "/pages/event/unsubscribe_confirm.php";
					} else {
						set_input("guid", $page[1]);

						$include = "/pages/event/unsubscribe.php";
					}
				}
				break;
			case "registration":
				if (isset($page[1])) {
					switch ($page[1]) {
						case "confirm":
							if (isset($page[2])) {
								set_input("event_guid", $page[2]);
							}

							$include = "/pages/registration/confirm.php";
							break(2);
						case "completed":
							if (isset($page[2])) {
								set_input("event_guid", $page[2]);
							}

							if (isset($page[3])) {
								set_input("object_guid", $page[3]);
							}

							$include = "/pages/registration/completed.php";
							break(2);
					}
				}
			case "event":
				switch ($page[1]) {
					case "register":
						if (!empty($page[3])) {
							set_input("relation", $page[3]);
						}
						break;
					case "file":
						if (!empty($page[3])) {
							set_input("file", $page[3]);
						}
						break;
					case "list":
						set_input("owner_guid", $page[2]);
						break;
					case "new":
						$page[1] = "edit";
						set_input("owner_guid", $page[2]);
				}
			default:
				if (!empty($page[2]) && ($page[1] !== "new")) {
					set_input("guid", $page[2]);
				}

				if (file_exists($base_dir . "/pages/" . $page[0] . "/" . $page[1] . ".php")) {
					$include = "/pages/" . $page[0] . "/" . $page[1] . ".php";
				} else {
					forward("/events");
				}
				break;
		}
	}

	include($base_dir . $include);

	return true;
}
