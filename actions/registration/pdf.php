<?php
		
	if (!function_exists('DOMPDF_autoload')) {
		require_once(dirname(dirname(dirname(__FILE__)))."/vendors/dompdf/dompdf_config.inc.php");
	}
	
	$key = get_input('k');
	$guid = get_input("guid");
	$user_guid = get_input('u_g', elgg_get_logged_in_user_guid());

	if ($guid && ($entity = get_entity($guid))) {
		if ($entity instanceof Event) {
			$event = $entity;
		}
	}

	if (!empty($key)) {
		$tempKey = md5($event->time_created . get_site_secret() . $user_guid);
		
		if ($event && ($tempKey == $key) && get_entity($user_guid)) {
			$html = elgg_view_title(elgg_echo('event_manager:registration:yourregistration'));
			
			$html .= elgg_view('event_manager/event/pdf', array('entity' => $event));

			$old_ia = elgg_set_ignore_access(true);
			
			$html .= $event->getRegistrationData($user_guid, true);

			if ($event->with_program) {
				$html .= $event->getProgramDataForPdf($user_guid);
			}
			
			elgg_set_ignore_access($old_ia);
			
			$dompdf = new DOMPDF();
			$dompdf->set_paper('A4');
			$dompdf->load_html($html);
			$dompdf->render();
			$dompdf->stream("registration.pdf");
			
			exit;
		} else {
			forward("events");
		}
	} else {
		forward("events");
	}