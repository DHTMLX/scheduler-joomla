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


if (!isset($bg_color) || !isset($event_color))
	die('Access denied');
define('BG_COLOR', $bg_color);
define('EVENT_COLOR', $event_color);
$start_time = microtime();
require_once('codebase.php');

$output = "./custom/";
$css_dump = 'custom.css';
$images_dump = 'images.dmp';
$imagecolors = array(
	'blue_tab.png' => $bg_color,
	'blue_tab_wide.png' => $bg_color,
	'buttons.gif' => $bg_color,
	'databg.png' => $bg_color,
	'databg_now.png' => $bg_color,
	'event-bg.png' => $event_color,
	'left-separator.png' => $bg_color,
	'left-time-bg.png' => $bg_color,
	'lightbox.png' => $bg_color,
	'move.png' => $event_color,
	'multi-days-bg.png' => $bg_color,
	'second-top-days-bg.png' => $bg_color,
	'top-days-bg.png' => $bg_color,
	'top-separator.gif' => $bg_color,
	'white_tab.png' => $bg_color,
	'white_tab_wide.png' => $bg_color
);
$s_diff = array(
	'move.png' => -10
);

/* generating images */
$dumps = file_get_contents($images_dump);
$dumps = fromString($dumps);

if (!file_exists('./custom/imgs/'))
	mkdir('./custom/imgs/', 0777);

foreach ($dumps as $name => $dump) {
	$color = (isset($imagecolors[$name])) ? $imagecolors[$name] : $bg_color;
	$s = (isset($s_diff[$name])) ? $s_diff[$name] : 0;
	$im = image_apply($dump, $color, $s);
	imagepng($im, $output.'imgs/'.$name);
}


/* generating css */
$css = file_get_contents($css_dump);
$css = preg_replace_callback("/\((bg|event),(#[0-9A-F]{6}),(\d{1,3})%\)/i", "replace", $css);
$css = preg_replace_callback("/url\([\"']?([^'\"]*)[\"']?\)/U", "prevent_cache", $css);
file_put_contents($output.'dhtmlxscheduler_custom.css', $css);


function replace($matches) {
	global $bg_color, $event_color;
	$base = ($matches[1] === 'event') ? EVENT_COLOR : BG_COLOR;
	$base = new Color($base);
	$add = new Color($matches[2]);
	$opacity = (int) $matches[3];
	$base->add($add, $opacity);
	return $base->toString();
}


function prevent_cache($matches) {
	$postfix = rand(0, 100000);
	return 'url('.$matches[1].'?nocache='.$postfix.')';
}

echo "Well done in ".(microtime() - $start_time)." seconds";




function lighter($color, $opacity) {
	$c = new Color($color);
	$c->add(new Color("FFFFFF"), $opacity);
	return $c->toString(true);
}

?>