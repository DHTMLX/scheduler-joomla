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



require_once(JPATH_ROOT.DS.'components'.DS.'com_scheduler'.DS.'config_init.php');
$groups = getGroups();
foreach ($groups as $k => $v)
	$groups[$k] = '{ id: "'.$k.'", label:"'.$v.'" }';
$groups = '['.implode(',', $groups).']';


// gets all user groups list
function getGroups() {
	$db = JFactory::getDBO();
	$db->setQuery(
		'SELECT a.id AS id, a.title AS title'.
		' FROM #__usergroups AS a' .
		' LEFT JOIN #__usergroups AS b ON a.lft > b.lft AND a.rgt < b.rgt' .
		' GROUP BY a.id ORDER BY a.lft ASC'
	);
	$options = $db->loadObjectList();
	$groups = array();
	if ($options) {
		foreach ($options as &$option)
			$groups[$option->id] = $option->title;
	} else {
		$groups = array(
			'registered' => 'Registered',
			'author' => 'Author',
			'editor' => 'Editor',
			'publisher' => 'Publisher',
			'manager' => 'Manager',
			'administrator' => 'Administrator',
			'superadministrator' => 'Super administrator'
		);
	}
	return $groups;
}

?>
<link rel='STYLESHEET' type='text/css' href='<?php

 echo JURI::root(); ?>components/com_scheduler/codebase/dhtmlx.css' />
<script src='<?php echo JURI::root(); ?>components/com_scheduler/codebase/dhtmlx.js' charset="utf-8"></script>
<script src="<?php echo JURI::root(); ?>components/com_scheduler/codebase/connector/connector.js" type="text/javascript" charset="utf-8"></script>
<script src='<?php echo JURI::root(); ?>components/com_scheduler/codebase/dhtmlxSchedulerConfigurator.js' charset="utf-8"></script>
<?php
 if ($config->locale !== 'en') { ?>
<script src='<?php echo JURI::root(); ?>components/com_scheduler/codebase/locale/locale_<?php echo $config->locale; ?>.js' charset="utf-8"></script>
<?php } ?>
<style>
ul.scheduler_problems {
	width: 47%;
	padding-top: 10px;
	padding-right: 10px;
	padding-bottom: 0px;
	padding-left: 14px;
	font-family: Tahoma;
	font-size: 12px;
	color: #555555;
	list-style-type: none;
}

ul.scheduler_problems li {
	background-color: #FFFBCC;
	border: 1px solid #E6DB55;
	padding-top: 10px;
	padding-left: 10px;
	padding-bottom: 10px;
}
</style>
<?php echo $scheduler->getProblems(); ?>
<script>
		var conf;
		window.onload = function() {
			conf = new dhtmlxSchedulerConfig({
				parent: 'schedulerConfigurator',
				hidden: 'scheduler_xml',
				groups: <?php echo $groups; ?>,
				url: '<?php echo JURI::root(); ?>components/com_scheduler/codebase/',
				url_load: '<?php echo JURI::root(); ?>/administrator/index.php?option=com_scheduler&view=scheduler&task=loadxml',
				wp_specific: false
			});
		}
</script>
<form action="index.php" method="post" name="adminForm">
	<div id="schedulerConfigurator" style="position: relative; width: 800px; height: 620px; float: left;"></div>
<input type="hidden" id="scheduler_xml_version" name="scheduler_xml_version" value="<?php echo $scheduler->getXmlVersion(); ?>" />
<input type="hidden" id="scheduler_xml" name="scheduler_xml" value='' />
<input type="hidden" name="option" value="com_scheduler" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="" />
</form>