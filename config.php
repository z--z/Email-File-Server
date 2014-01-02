<?php 
// Set you time zone or use Vancouver as default
date_default_timezone_set("America/Vancouver");  // This is used for logs (runlog.txt)

/* I was having trouble with variable scope are something like that,
*  it was a problem with send.php not being able to use them. 
*  I change how I introduced them, I used a method described by Hugo Leonardo;
*  http://stackoverflow.com/questions/14752470/best-way-to-create-config-file
*
*  This file is set up as if you were to use a gmail account
*
*/

return array(
//Used for IMAP
  'hostname_IMAP' => '{imap.gmail.com:993/imap/ssl}INBOX',  // Default gmail server settings
  'username' => '',	 	//***Enter data her***
  'email_address' => '',	//***Enter data her***
  'password' => '',		//***Enter data her***

//Used for SMTP
  'hostname_SMTP' => 'smtp.gmail.com',	// Default gmail SMTP server address
  'req_auth_SMTP' => true,			// Enable SMTP authentication
  'encrypt_SMTP' => 'ssl',			// Enable encryption, 'tsl' also accepted
  'port_SMTP' => 465,                    	// Default gmail port
  'from_name' => ''	//***Enter data her***  // Name to be displayed when messages are received 
);

?>

