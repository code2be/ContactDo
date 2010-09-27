<?php

/*
#		ContactDo 1.0 BETA, Advanced contact-us class
#      Copyright (C)  Ahmed Hosny < http://AhmedHosny.com >
#												
#    This program is free software: you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation, either version 3 of the License, or
#    (at your option) any later version.						
#													
#    This program is distributed in the hope that it will be useful,	
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.					
#
#    You should have received a copy of the GNU General Public License	
#    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class contact {

	private $to_email;
	private $subject_prefix;
	private $fields;
	private $html_header;
	private $html_form;
	private $html_done;
	private $html_error;

	private $recaptcha_private = ""; 	// Specify your reCaptcha Private Key here ..


	public function __construct($to_email, $subject_prefix, $fields, $html_header, $html_form, $html_done, $html_error)
	{
		list($this->to_email, $this->subject_prefix, $this->fields, $this->html_header, $this->html_form, $this->html_done, $this->html_error) = func_get_args();		
		
		print $this->html_header;
		
		$act = $_GET[act];
		if(!empty($act))
			call_user_func_array(array($this, $act), array($_GET[opt]));
		else
			print $this->html_form;
	}

	private function validate_input(Array &$input, Array &$fail_reasons=NULL)
	{
		$error = 0;

		foreach($this->fields as $field => $options)
		{

			if(in_array("req", $options) && empty($input[$field]))	{ $fail_reasons[$field][]="Not filled !"; $error=1; }

			if(in_array("num", $options) && !is_numeric($input[$field])) { $fail_reasons[$field][]="Not numeric !"; $error=1; }

			if(in_array("email", $options) && !preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z]{2,5}$/i", $input[$field]))	{$fail_reasons[$field][]="Not a valid email !"; $error=1; }

			if(in_array("url", $options) && !preg_match("/^https?\:\/\/[a-zA-Z-0-9-]+\.[a-zA-Z]{2,5}\/(.*)$/i", $input[$field]))	{$fail_reasons[$field][]="Not a valid URL !"; $error=1; }

			if(key_exists("max_length", $options) && (strlen($input[$field]) > $options['max_length']))	{$fail_reasons[$field][]="Max. Length is {$options['max_length']} !"; $error=1; }

			if(key_exists("max_length", $options) && (strlen($input[$field]) < $options['min_length']))	{$fail_reasons[$field][]="Min. Length is {$options['min_length']} !"; $error=1; }

		}

		if($error === 1) return false; else return true;

	}

	private function send()
	{

		if(!$this->recaptcha_verify($_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']))	die("reCaptcha not valid !");

		foreach($this->fields as $field => $options)
		{
			$input[$field] = $_POST[$field];
		}

//var_dump($input);

		$validation_result = $this->validate_input($input, $fail_reasons);

//var_dump($validation_result);


		if($validation_result === true)
		{
			if(mail($this->to_email, $this->subject_prefix . $input['subject'], $input['msg']))
				print $this->html_done;
			else
				print $this->html_error;
		}
		else
		{
			foreach($fail_reasons as $field => $reasons)
			{
				print "<hr>For field: <b>$field</b> ..<br><ul>";
			
				foreach($reasons as $reason)
				{
					print "<li>$reason</li>";
				}

				print "</ul>";
			}
		}

	}
	

function getRemoteIP ()
  {

    if (strlen($_SERVER["HTTP_X_FORWARDED_FOR"]) > 0) 
		$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	else
		$ip = $_SERVER["REMOTE_ADDR"];

    return $ip;

  }


    function recaptcha_verify($challenge, $response)
    {

        $remote_ip = $this->getRemoteIP();
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://www.google.com/recaptcha/api/verify");
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "privatekey={$this->recaptcha_private}&rempteip=$remote_ip&challenge=$challenge&response=$response");

        $google_response = explode("\n", curl_exec($ch));
        
        curl_close($ch);

        switch($google_response[0])
        {
            case "true":
                return TRUE;
                break;
            default:
                return FALSE;
        }
    }

}
