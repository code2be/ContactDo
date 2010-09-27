<?php

// Example on {ContactDo 1.0 BETA Class} Usage
// By: Ahmed Hosny < http://www.AhmedHosny.com >

include "contactdo.class.php";

$to_email = "test@example.org";	// Mail to send the messages to.

$subject_prefix = "MYSITE: ";	// Subject Prefix for the messages.

## Form fields
// req => Required field.
// min_length => Minimum Length.
// max_length => Maximum Length.
// (( email )) OR (( num )) OR (( url )) Data types for the validation process.
$fields = Array(
"fullname" => Array("min_length" => 5),
"email" => Array("email", "req"),
"msg" => Array("req", "min_length" => 15, "max_length" => 500),
"subject" => Array("req", "min_length" => 4, "max_length" => 50)
);


## Simple header
$html_header = <<<HTML

<b><center>ScalWeb.Com</center></b><hr>

HTML;


## Form HTML ( Change reCaptcha public key to your own )
$html_form = <<<HTML

<b>Contact Us</b>
<form method="POST" action="?act=send">
Full Name: <input type="text" name="fullname"><br>
Email: <input type="text" name="email"><br>
Subject: <input type="text" name="subject"><br>
Message:<br><textarea name="msg" cols="50" rows="5"></textarea><br>

<script type="text/javascript" src="http://www.google.com/recaptcha/api/challenge?k=6LfaIr0SAAAAAESO7wus474o_eiJ7EJjqSA4zf0U ">
    </script>
    <noscript>
       <iframe src="http://www.google.com/recaptcha/api/noscript?k=6LfaIr0SAAAAAESO7wus474o_eiJ7EJjqSA4zf0U "
           height="300" width="500" frameborder="0"></iframe><br>
       <textarea name="recaptcha_challenge_field" rows="3" cols="40">
       </textarea>
       <input type="hidden" name="recaptcha_response_field" value="manual_challenge">
    </noscript>

<input type="submit" value="send">
</form>

HTML;


## Success HTML
$html_done = <<<HTML

Message Sent !

HTML;


##  Fail HTML
$html_error = <<<HTML

Error, try again !.

HTML;

## Don't modify under this line ..

$contact = new contact($to_email, $subject_prefix, $fields, $html_header, $html_form, $html_done, $html_error);

?>


