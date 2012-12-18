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


require_once("base_connector.php");

/*! DataItem class for Scheduler component
**/
class SchedulerDataItem extends DataItem{
	/*! return self as XML string
	*/
	function to_xml(){
		if ($this->skip) return "";
		
		$str="<event id='".$this->get_id()."' >";
		$str.="<start_date><![CDATA[".$this->data[$this->config->text[0]["name"]]."]]></start_date>";
		$str.="<end_date><![CDATA[".$this->data[$this->config->text[1]["name"]]."]]></end_date>";
		$str.="<text><![CDATA[".$this->data[$this->config->text[2]["name"]]."]]></text>";
		for ($i=3; $i<sizeof($this->config->text); $i++){
			$extra = $this->config->text[$i]["name"];
			$str.="<".$extra."><![CDATA[".$this->data[$extra]."]]></".$extra.">";
		}
		return $str."</event>";
	}
}


/*! Connector class for dhtmlxScheduler
**/
class SchedulerConnector extends Connector{
	
	protected $extra_output="";//!< extra info which need to be sent to client side
	private $options=array();//!< hash of OptionsConnector 
	
			
	/*! assign options collection to the column
		
		@param name 
			name of the column
		@param options
			array or connector object
	*/
	public function set_options($name,$options){
		if (is_array($options)){
			$str="";
			foreach($options as $k => $v)
				$str.="<item value='".$this->xmlentities($k)."' label='".$this->xmlentities($v)."' />";
			$options=$str;
		}
		$this->options[$name]=$options;
	}
	/*! generates xml description for options collections
		
		@param list 
			comma separated list of column names, for which options need to be generated
	*/
	protected function fill_collections(){
		foreach ($this->options as $k=>$v) { 
			$name = $k;
			$this->extra_output.="<coll_options for='{$name}'>";
			if (!is_string($this->options[$name]))
				$this->extra_output.=$this->options[$name]->render();
			else
				$this->extra_output.=$this->options[$name];
			$this->extra_output.="</coll_options>";
		}
	}
	
	/*! renders self as  xml, ending part
	*/
	protected function xml_end(){
		$this->fill_collections();
		return $this->extra_output."</data>";
	}
	
	
	/*! constructor
		
		Here initilization of all Masters occurs, execution timer initialized
		@param res 
			db connection resource
		@param type
			string , which hold type of database ( MySQL or Postgre ), optional, instead of short DB name, full name of DataWrapper-based class can be provided
		@param item_type
			name of class, which will be used for item rendering, optional, DataItem will be used by default
		@param data_type
			name of class which will be used for dataprocessor calls handling, optional, DataProcessor class will be used by default. 
	*/	
	public function __construct($res,$type=false,$item_type=false,$data_type=false){
		if (!$item_type) $item_type="SchedulerDataItem";
		if (!$data_type) $data_type="SchedulerDataProcessor";
		parent::__construct($res,$type,$item_type,$data_type);
	}

	//parse GET scoope, all operations with incoming request must be done here
	function parse_request(){
		parent::parse_request();
		if (count($this->config->text)){
			if (isset($_GET["to"]))
				$this->request->set_filter($this->config->text[0]["name"],$_GET["to"],"<");
			if (isset($_GET["from"]))
				$this->request->set_filter($this->config->text[1]["name"],$_GET["from"],">");
		}
	}
}

/*! DataProcessor class for Scheduler component
**/
class SchedulerDataProcessor extends DataProcessor{
	function name_data($data){
		if ($data=="start_date")
			return $this->config->text[0]["db_name"];
		if ($data=="id")
			return $this->config->id["db_name"];
		if ($data=="end_date")
			return $this->config->text[1]["db_name"];
		if ($data=="text")
			return $this->config->text[2]["db_name"];
			
		return $data;
	}
}

?>