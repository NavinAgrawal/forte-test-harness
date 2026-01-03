<?php


//This class represents a credit card class object based on the Forte REST v3 API
//Parameters:
//Account Number - The account number on the credit card
//Expiration Month - The expiration month of the credit card 
//Expiration Year - The expiration year of the credit card 
//Card Verification Value - The CVV of the credit card 
//Card Types - The credit card company associated with the card 
//Name on card - The name of the account owner as displayed on the card, (optional) default value = null 
//Magnetic Strip Card Data (optional) default value = null 
//Card Reader (optional) default value = null 
//Please note that currently NO Validation is done by the constructor and attributes are public
//Todo: Add Validation/Getters/Setters
class ach_account {

   public $routing_number;
   public $account_number;
   public $account_type;
   public $account_holder;
   public $last_4_account_number;
   public $masked_account_number;
	

   //Constructor for credit card, please note the optional parameters at the end
   function __construct($routing_number=NULL, $account_number=NULL, $account_type=NULL, $account_holder= "")
   {
	   
	   $this->routing_number = $routing_number;
	   $this->account_number = $account_number;
	   $this->account_type = $account_type;
	   $this->account_holder = $account_holder;
   }
   
   function hydrate($data) {
	$this->account_holder = $data->account_holder;
	$this->routing_number = $data->routing_number;
	$this->account_number = $data->account_number;
	$this->account_type = $data->account_type;
   }
   
};


class ACH_Generator {
	
	private $routing_numbers = array(
		[0] => "111000012",
		[1] => "021000021",
		[2] => "011401533"),
		[3] => "091000019");
	
	//This function creates and returns an object of class Credit_Card with the following characteristics
	//Card Types: random Visa, Amex, or Mastercard
	//Card Expiration: random month between 2021 and 2025
	//The card account number is always the type concatenated with 15 '1's.
	//The CVV is always '1111'
	//The name on the card, the card magnetic strip data, and the card reader data are all set to null
	function get_random_paymethod() {
		

		
		$routing_numbers_index = rand(min(array_keys($this->routing_numbers)), max(array_keys($this->routing_numbers))); 
		$ach_routing_number = $routing_numbers_index;
		if($routing_numbers_index == 3) {
			$ach_routing_number = $routing_numbers_index.'79039034727652';
		} else if ($routing_numbers_index == 5) {
			$ach_routing_number = $routing_numbers_index.'454545454545454';
		}
		$card_exp_month = rand(1, 12);
		$card_exp_year = rand(2021, 2025);
		$card_verification_value = '111';
		$card_type = $this->card_types[$routing_numbers_index];
		return $new_paymethod = new ach_account($ach_routing_number, $card_exp_month, $card_exp_year, $card_verification_value, $card_type);
		

	}
	
	function test() {
		$random_cc = $this->get_random_paymethod();
		var_dump($random_cc);
	}

}
//$cg = new Card_Generator();
//$cg->test();

?>