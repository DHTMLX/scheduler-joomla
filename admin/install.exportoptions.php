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



$db = JFactory::getDBO();

$default_xml = JPATH_ROOT.DS.'components'.DS.'com_scheduler'.DS.'default.xml';
$xml = file_get_contents($default_xml);
$xml = str_replace("\r", "", $xml);
$xml = str_replace("\n", "", $xml);
$xml = str_replace("\t", "", $xml);

$query = "SHOW TABLES LIKE '".$db->getPrefix()."events_rec'";
$db->setQuery($query);
$events_rec_exists = $db->loadResult($query);
if (!$events_rec_exists) {
	$query = "CREATE TABLE IF NOT EXISTS `#__events_rec` (
		`event_id` int(11) NOT NULL AUTO_INCREMENT,
		`start_date` datetime NOT NULL,
		`end_date` datetime NOT NULL,
		`text` varchar(255) NOT NULL,
		`rec_type` varchar(64) NOT NULL,
		`event_pid` int(11) NOT NULL,
		`event_length` int(11) NOT NULL,
		`user` int(11) NOT NULL,
		`lat` FLOAT(10,6) NOT NULL,
		`lng` FLOAT(10,6) NOT NULL,
		PRIMARY KEY (`event_id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;";
	$db->setQuery($query);
	$db->query();

	$query = "INSERT IGNORE INTO `#__events_rec`
		(`event_id`, `start_date`, `end_date`, `text`, `event_pid`, `event_length`) VALUES
		(1, NOW(), DATE_ADD(NOW(), INTERVAL 5 MINUTE), 'The Scheduler Calendar was installed!', 0, 0);";
	$db->setQuery($query);
	$db->query();
} else {
	$query = "ALTER TABLE `#__events_rec` ADD COLUMN `lat` FLOAT(10,6) NULL";
	$db->setQuery($query);
	$db->query();

	$query = "ALTER TABLE `#__event_rec` ADD COLUMN `lng` FLOAT(10,6) NULL";
	$db->setQuery($query);
	$db->query();

	$query = "SELECT value FROM #__scheduler_options WHERE `name`='scheduler_xml'";
	$db->setQuery($query);
	$xml = $db->loadResult($query);
	$xml = str_replace("&ltesc;", "<", $xml);
	$xml = str_replace("&gtesc;", ">", $xml);
	$xml = preg_replace_callback("/<access_([^>]+)View_j>.*(true|false).*(true|false).*(true|false).*<\/access_([^>]+)Edit_j>/U", "replace_roles", $xml);
	$xml = addslashes($xml);
	$query = "UPDATE #__scheduler_options SET value='{$xml}' WHERE name='scheduler_xml'";
	$db->setQuery($query);
	$db->query();
	
	// increment xml version
	$query = "SELECT value FROM #__scheduler_options WHERE `name`='scheduler_xml_version'";
	$db->setQuery($query);
	$version = (int) $db->loadResult($query);
	$query = "UPDATE #__scheduler_options SET value='".($version + 1)."' WHERE name='scheduler_xml_version'";
	$db->setQuery($query);
	$db->query();
}


function replace_roles($matches) {
	$group = ($matches[1] === 'guest') ? '-1' : $matches[1];
	$result = "<group id=\"{$group}\">";
	$result .= "<view>{$matches[2]}</view>";
	$result .= "<add>{$matches[3]}</add>";
	$result .= "<edit>{$matches[4]}</edit>";
	$result .= "</group>";
	return $result;
}


// creates options table if not exists
$query = "SELECT * FROM #__scheduler_options WHERE `name`='scheduler_php'";
$db->setQuery($query);
$config_exists = $db->loadResult($query);

if (!$config_exists) {
	$query = "CREATE TABLE IF NOT EXISTS `#__scheduler_options` (
		`id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) NOT NULL,
		`value` text NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0";
	$db->setQuery($query);
	$db->query();

	$query = "INSERT INTO `#__scheduler_options` (`id`, `name`, `value`) VALUES
		(null, 'scheduler_xml', '".$xml."'),
		(null, 'scheduler_php', ''),
		(null, 'scheduler_php_version', '0'),
		(null, 'scheduler_xml_version', '1'),
		(null, 'sidebar_num', '5'),
		(null, 'scheduler_stable_config', '".$xml."');";
	$db->setQuery($query);
	$db->query();
}

?>