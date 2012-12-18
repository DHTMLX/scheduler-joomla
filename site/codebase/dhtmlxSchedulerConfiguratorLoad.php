<?php

/*------------------------------------------------------------------------
# com_scheduler
# ------------------------------------------------------------------------
# author DHTMLX LTD
# copyright Copyright (C) 2012 DHTMLX LTD. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://dhtmlx.com/
# Technical Support: Forum - http://forum.dhtmlx.com/viewforum.php?f=16
-------------------------------------------------------------------------*/

/**
 This file is part of dhtmlxScheduler for Joomla.

    dhtmlxScheduler for Joomla is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    dhtmlxScheduler for Joomla is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with dhtmlxScheduler for Joomla.  If not, see <http://www.gnu.org/licenses/>.
**/
// deny direct access
defined( '_JEXEC' ) or die( 'Restricted access' );



$start_time = microtime();
if (!defined('E_DEPRECATED'))
	define('E_DEPRECATED', 8192);

$err = error_reporting();
if ($err & E_DEPRECATED) {
	$err = $err ^ E_DEPRECATED;
	error_reporting($err);
}
require('../../../../wp-config.php');
define('WP_USE_THEMES', true);

require_once(WP_PLUGIN_DIR.'/event-calendar-scheduler/codebase/dhtmlxSchedulerConfigurator.php');
require_once(WP_PLUGIN_DIR.'/event-calendar-scheduler/codebase/dhtmlxSchedulerHelpers.php');

$scheduler_usertypes = Array(0 => 'subscriber', 1 => 'contributor', 2 => 'author', 3 => 'editor', 4 => 'editor', 5 => 'editor', 6 => 'editor', 7 => 'editor', 8 => 'administrator', 9 => 'administrator', 10 => 'administrator');
get_currentuserinfo();
$scheduler_userid = $current_user->id;

if (isset($current_user->roles[0])) {
	$scheduler_usertype = $current_user->roles[0];
} else {
	$scheduler_usertype = '-1';
}
$scheduler_usertype = Array($scheduler_usertype);


$db = new DHXDBConfig();
$db->connection = $wpdb->dbh;
$db->prefix = $wpdb->prefix;
$db->base_prefix = isset($wpdb->base_prefix) ? $wpdb->base_prefix : $wpdb->prefix;
$db->events = 'events_rec';
$db->options = 'options';
$db->options_name = 'option_name';
$db->options_value = 'option_value';
$db->users = 'users';
$db->users_id = 'ID';
$db->users_login = 'user_login';

$scheduler_cfg = new SchedulerConfig('scheduler_config_xml', $db, $scheduler_userid, false);
if (isset($_GET['config_xml'])) {
	if (scheduler_is_admin()) {
		header('Content-type: text/xml');
		echo $scheduler_cfg->getXML();
	}
} else if (isset($_GET['grid_events'])) {
	if (scheduler_is_admin()) {
		$scheduler_cfg->getEventsRecGrid();
	}
} else if (isset($_GET['scheduler_events'])) {
	$scheduler_cfg->getEventsRec($scheduler_usertype, false);
} else if ((isset($_GET['google_import'])) || (isset($_GET['google_export']))) {
	if (scheduler_is_admin()) {
		$email = urldecode($_GET['email']);
		$pass = urldecode($_GET['pass']);
		$cal = urldecode($_GET['cal']);
		if ($cal === "") $cal = $email;
		if (isset($_GET['google_import']))
			$res = $scheduler_cfg->gcalImport($email, $pass, $cal);
		else if (isset($_GET['google_export']))
			$res = $scheduler_cfg->gcalExport($email, $pass, $cal);
		echo $res;
	}
} else if (isset($_GET['skin'])) {
	if (scheduler_is_admin()) {
		// skin making
		$bg_color = $_GET['bg'];
		$event_color = $_GET['event'];
		$bg_color = urldecode($bg_color);
		$bg_color = substr($bg_color, 1);
		$event_color = urldecode($event_color);
		$event_color = substr($event_color, 1);
		chdir('./skin_builder');
		require_once("./skin.php");
		chdir('..');
		die();
	}
}

function scheduler_is_admin() {
	get_currentuserinfo();
	return is_super_admin($current_user->id);
}

?>