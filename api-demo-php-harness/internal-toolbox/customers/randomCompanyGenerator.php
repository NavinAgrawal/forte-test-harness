<?php
class Company_Generator {
	
	private $company_name_array;

	function __construct($company_file_location) {
		
		$company_name_array = array();
		$this->fill_companies($company_file_location);
	}

	function fill_companies($company_file_location) {
		$handle = fopen($company_file_location, "r");
		if ($handle) {
			while (($line = fgets($handle)) !== false) {
				$this->company_name_array[] = $line;
			}
			//var_dump($company_name_array);
			//echo "<br />";
		fclose($handle);
		}
		else {
			echo "Error opening names file";
			exit();
		}
	}

	function get_random_company() {
		$random_key = array_rand($this->company_name_array);
		return $this->company_name_array[$random_key];
	}
	
	function test() {		
		echo( "Company: ");
		print_r($this->get_random_company());
	}
}

//$cg = new company_generator("companies.txt");
//$cg->test();

?>