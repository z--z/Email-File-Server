<?php 
// Set you time zone or use Vancouver as default
date_default_timezone_set("America/Vancouver");  // This is used for logs (runlog.txt)

/* I was having trouble with variable scope are something like that,
*  it was a problem with send.php not being able to use them. 
*  I change how I introduced them, I used a method described by Hugo Leonardo;
*  http://stackoverflow.com/questions/14752470/best-way-to-create-config-file



	***Defualt GMAIL settings included***

*/

return array(
//Used for IMAP
  'hostname_IMAP' => '{imap.gmail.com}',
  'hostname_IMAP_login' => '{imap.gmail.com:993/imap/ssl}INBOX',  // Default gmail server settings
  'username' => '',			//****enter here**** 
  'email_address' => '',		//****enter here**** 
  'password' => '',			//****enter here****

//Used for SMTP
  'hostname_SMTP' => 'smtp.gmail.com',	// Default gmail SMTP server address
  'req_auth_SMTP' => true,			// Enable SMTP authentication
  'encrypt_SMTP' => 'ssl',			// Enable encryption, 'tsl' also accepted
  'port_SMTP' => 465,                    	// Default gmail port
  'from_name' => '',	//****enter here****	// Name to be displayed when messages are received 

//Other file
  'file_server_folder' => 'email_file_server',	//This file is where all stored files (and folders) are contained 
  'folder_prefix' => 'efs.',			//prefix used in imap labels and directory folders
  'pref_array' => 'preference_array',            // File that select the folder
  'permit_array' => 'permit_array',		// This is where all the folder permisions are stored
  'default_folder' => 'catchALL'		//This is the default folder within the 'file server folder';  "" is no folder
);

?>


