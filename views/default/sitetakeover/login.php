<?php

	$login_url = $vars['url'];
	if ((isset($vars["config"]->https_login)) && ($vars["config"]->https_login)) {
		$login_url = str_replace("http", "https", $vars['url']);
	}
	
	$form_body = "<p class='loginbox'>";
	$form_body .= "<label>" . elgg_echo('username') . "</label><br />";
	$form_body .= elgg_view('input/text', array('internalname' => 'username'));
	$form_body .= "<br />";
	$form_body .= "<label>" . elgg_echo('password') . "</label><br />";
	$form_body .= elgg_view('input/password', array('internalname' => 'password'));
	$form_body .= "<br />";
	
	$form_body .= elgg_view('login/extend');
	
	$form_body .= elgg_view('input/submit', array('value' => elgg_echo('login')));
	$form_body .= "<div id='persistent_login'>";
	$form_body .= "<label><input type='checkbox' name='persistent' value='true' />" . elgg_echo('user:persistent') . "</label>";
	$form_body .= "</div>";
	$form_body .= "</p>";
	
	$form_body .= "<p class='loginbox'>";
	if(!isset($vars["config"]->disable_registration) || !($vars["config"]->disable_registration)){
		$form_body .= "<a href='" . $vars['url'] . "pg/register/'>" . elgg_echo('register') . "</a> | ";
	}
	$form_body .= "<a href='" . $vars['url'] . "account/forgotten_password.php'>" . elgg_echo('user:password:lost') . "</a></p>";
	$form_body .= elgg_view('input/hidden', array('internalname' => 'returntoreferer', "value" => "true"));
	
	echo elgg_view('input/form', array('body' => $form_body, 'action' => $login_url . "action/login"));