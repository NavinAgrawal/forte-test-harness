<?php
class Name_Generator{
	
	private $first_name_array = array();
	private $last_name_array = array();
	
	function __construct($name_file_location) {
	$first_name_array = array();
	$last_name_array = array();
	$this->fill_names($name_file_location);
	}

	function fill_names($name_file_location) {
		$handle = fopen($name_file_location, "r");
		if ($handle) {
			while (($line = fgets($handle)) !== false) {
				$token = strtok($line, " ");
				$this->first_name_array[] = $token;
				$this->last_name_array[] =  strtok(" ");
			}
			//var_dump($first_name);
			//echo "<br />";
			//var_dump($last_name);
		fclose($handle);
		}
		else {
			echo "Error opening names file";
			exit();
		}
	}
	
	function test() {
		print_r($this->get_random_name()); 
	}

	function get_random_name() {
		$random_keys = array_rand($this->first_name_array, 2);
		return array($this->first_name_array[$random_keys[0]],$this->last_name_array[$random_keys[1]]);
	}
}

//$ng = new Name_Generator("names.txt");
//$ng->test();
?>