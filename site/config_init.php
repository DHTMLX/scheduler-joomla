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



$basedir = dirname(__FILE__);
require_once($basedir.'/codebase/dhtmlxSchedulerConfigurator.php');
require_once($basedir.'/codebase/dhtmlxSchedulerHelpers.php');

$default_xml = file_get_contents($basedir.'/default.xml');
$default_xml = str_replace("\r", "", $default_xml);
$default_xml = str_replace("\n", "", $default_xml);
$default_xml = str_replace("\t", "", $default_xml);

$j_cfg = new JConfig;
$res=mysql_connect($j_cfg->host, $j_cfg->user, $j_cfg->password);
mysql_select_db($j_cfg->db);

$document =  JFactory::getDocument();
$locale = substr($document->language,0 ,2);

$config = new DHXDBConfig();
$config->connection = $res;
$config->prefix = $j_cfg->dbprefix;
$config->base_prefix = $j_cfg->dbprefix;
$config->events = 'events_rec';
$config->options = 'scheduler_options';
$config->options_name = 'name';
$config->options_value = 'value';
$config->users = 'users';
$config->users_id = 'id';
$config->users_login = 'username';
$config->locale = $locale;
$config->default_xml = $default_xml;

$curuser = JFactory::getUser();
//$usertype = $this->getUser();
$userid = $curuser->id;

$scheduler = new SchedulerConfig('scheduler_config_xml', $config, $userid, false);

?>