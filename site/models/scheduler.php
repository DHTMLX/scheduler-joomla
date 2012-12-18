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



jimport( 'joomla.application.component.model' );
require_once(JPATH_ROOT.DS.'components'.DS.'com_scheduler'.DS.'codebase'.DS.'dhtmlxSchedulerConfigurator.php');

class SchedulerModelScheduler extends JModel {

	function getScheduler() {
		$url = JURI::root().'components/com_scheduler/codebase/';
		$loader_url = JURI::root()."index.php?option=com_scheduler&view=scheduler&task=loadxml&scheduler_events=";

		$usergroups = $this->getUserGroups();
		require_once(JPATH_ROOT.DS.'components'.DS.'com_scheduler'.DS.'config_init.php');
		$scheduler = $scheduler->schedulerInit($usergroups, $locale, $url, $loader_url);
		return $scheduler;
	}

	protected function getUserGroups() {
		$user = JFactory::getUser();
		$gr = Array();
		if (isset($user->groups)) {
			// multiple user groups
			$groups = $user->groups;
			foreach ($groups as $k => $v)
				$gr[] = (string) $v;
		} else {
			
			switch($user->usertype) {
				case '':
					$usertype = null;
					break;
				case 'Super Administrator':
					$usertype = 'superadministrator';
					break;
				default:
					$usertype = strtolower($user->usertype);
			}
			if ($usertype)
				$gr[] = $usertype;
		}
		return $gr;
	}

}