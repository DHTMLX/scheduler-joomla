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



jimport('joomla.application.component.controller');

class SchedulersController extends JController {

	function __construct() {
		parent::__construct();
		$this->registerTask( 'save' , 'save' );
		$this->registerTask( 'cancel' , 'cancel' );
		$config = JFactory::getConfig();
	}


	function save() {
		$model = $this->getModel('schedulers');
		$data_post = JRequest::get('post');

		$data = array(
			'name' => 'scheduler_xml',
			'value' => $data_post['scheduler_xml']
		);
		$model->store($data);

		$data = array(
			'name' => 'scheduler_xml_version',
			'value' => $data_post['scheduler_xml_version']
		);
		$model->store($data);

		$link = 'index.php?option=com_scheduler';
		$this->setRedirect($link);
	}

	function cancel() {
		$this->setRedirect('index.php');
	}

	function default_xml() {
		require_once(JPATH_ROOT.DS.'components'.DS.'com_scheduler'.DS.'config_init.php');
		$model = $this->getModel('schedulers');
		$data_post = JRequest::get('post');
		$data = array(
			'name' => 'scheduler_xml',
			'value' => $config->default_xml
		);
		$model->store($data);

		$data = array(
			'name' => 'scheduler_xml_version',
			'value' => $data_post['scheduler_xml_version']
		);
		$model->store($data);

		$link = 'index.php?option=com_scheduler';
		$this->setRedirect($link);
	}


	function loadxml() {
		require_once(JPATH_ROOT.DS.'components'.DS.'com_scheduler'.DS.'config_init.php');

		if (isset($_GET['config_xml'])) {
			// gets configuration xml
			header('Content-type: text/xml');
			echo $scheduler->getXML();
		} else if (isset($_GET['grid_events'])) {
			// get events in grid format
			$scheduler->getEventsRecGrid();
		} else if ((isset($_GET['google_import'])) || (isset($_GET['google_export']))) {
			// google import/export
			$email = urldecode($_GET['email']);
			$pass = urldecode($_GET['pass']);
			$cal = urldecode($_GET['cal']);
			if ($cal === "") $cal = $email;
			if (isset($_GET['google_import']))
				$res = $scheduler->gcalImport($email, $pass, $cal);
			else if (isset($_GET['google_export']))
				$res = $scheduler->gcalExport($email, $pass, $cal);
			echo $res;
		} else if (isset($_GET['skin'])) {
			// skin making
			$bg_color = $_GET['bg'];
			$event_color = $_GET['event'];
			$bg_color = urldecode($bg_color);
			$bg_color = substr($bg_color, 1);
			$event_color = urldecode($event_color);
			$event_color = substr($event_color, 1);
			$path = getcwd();
			chdir(JPATH_ROOT.DS.'components'.DS.'com_scheduler'.DS.'codebase'.DS.'skin_builder');
			require_once("./skin.php");
			chdir($path);
		}
		die();
	}

}