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
class Credit_Card {

   public $account_number;
   public $expire_month;
   public $expire_year;
   public $card_verification_value;
   public $card_type;
   public $name_on_card;
   public $swipe_data;
   public $card_reader;
   public $last_4_account_number;
   public $masked_account_number;
	

   //Constructor for credit card, please note the optional parameters at the end
   function __construct($account_number=NULL, $expire_month=NULL, $expire_year=NULL, $card_verification_value=NULL, $card_type=NULL, $name_on_card= "",$swipe_data="none", $card_reader="no")
   {
	   
	   $this->account_number = $account_number;
	   $this->expire_month = $expire_month;
	   $this->expire_year = $expire_year;
	   $card_verification_value = $card_verification_value;
	   $this->card_type = $card_type;
	   $this->name_on_card = $name_on_card;
	   $this->swipe_data = $swipe_data;
	   $this->card_reader = $card_reader;
   }
   
   function hydrate($data) {
	$this->name_on_card = $data->name_on_card;
	$this->expire_month = $data->expire_month;
	$this->expire_year = $data->expire_year;
	$this->card_type = $data->card_type;
	//$this->card_data = $data->card_data;
	$this->card_reader = $data->card_reader;
   }
   
};


class Card_Generator {
	
	private $card_types = array(
		//"2" => "mast",
		3 => "amex",
		4 => "visa",
		5 => "mast");
	
	//This function creates and returns an object of class Credit_Card with the following characteristics
	//Card Types: random Visa, Amex, or Mastercard
	//Card Expiration: random month between 2021 and 2025
	//The card account number is always the type concatenated with 15 '1's.
	//The CVV is always '1111'
	//The name on the card, the card magnetic strip data, and the card reader data are all set to null
	function get_random_paymethod() {
		

		
		$card_type_index = rand(min(array_keys($this->card_types)), max(array_keys($this->card_types))); 
		$card_account_number = $card_type_index.'111111111111111';
		if($card_type_index == 3) {
			$card_account_number = $card_type_index.'79039034727652';
		} else if ($card_type_index == 5) {
			$card_account_number = $card_type_index.'454545454545454';
		}
		$card_exp_month = rand(1, 12);
		$card_exp_year = rand(2021, 2025);
		$card_verification_value = '111';
		$card_type = $this->card_types[$card_type_index];
		return $new_paymethod = new Credit_Card($card_account_number, $card_exp_month, $card_exp_year, $card_verification_value, $card_type);
		

	}
	
	function test() {
		$random_cc = $this->get_random_paymethod();
		var_dump($random_cc);
	}

}
//$cg = new Card_Generator();
//$cg->test();

?>