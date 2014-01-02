<?php
//****** See config.php for more the settings ******
require('send.php');	//email sending function
require('attach.php');	//download attachments function
$configs = include('config.php');

function clean_email($long_email) 	{
  $clean_mail = trim(str_replace(array('>','<'),"",substr($long_email,strripos($long_email,"<"))));
  return $clean_mail;
}
function clean_domain($long_email)       {
  $clean_domain = trim(str_replace(array('>','<'),"",substr($long_email,strripos($long_email,"@"))));
  return $clean_domain;
}

// Looks at current files in MSDS directory
$start_dir = getcwd();
$working_dir = dirname(__FILE__).DIRECTORY_SEPARATOR.'MSDS';
$cabinet = glob(dirname(__FILE__).DIRECTORY_SEPARATOR.'MSDS'.DIRECTORY_SEPARATOR.'*');
chdir(dirname(__FILE__).DIRECTORY_SEPARATOR.'MSDS');
$files = glob('*');
chdir($start_dir);

$inbox = imap_open($configs['hostname_IMAP'],$configs['username'],$configs['password']) or die('Cannot connect to Gmail: ' . imap_last_error());

$emails = imap_search($inbox,'ALL');
$n = imap_num_msg($inbox);

$output = "\r\n\r\n"."*******  ".date("y.m.d  h.i.s",time())."\t".$n."  ******";
echo $output;

if($emails) 	{
  foreach($emails as $email_number) 	{

    //fetchs data about the email
    $overview = imap_fetch_overview($inbox,$email_number,0);
    imap_mail_move($inbox,$email_number,"Replied");
    $subject[$email_number] = $overview[0]->subject;
    $from[$email_number] = $overview[0]->from;
			
    //outputs the email header information	
    $output.= "\r\n".$email_number."\t".$overview[0]->subject."\t\t\t\t".($overview[0]->seen ? 'read' : 'unread');
 
    // Looks at alternate commands
    if(substr(strtolower($subject[$email_number]),0,3) == "***")	{   // if string starts with "***"
      if(substr(strtolower($subject[$email_number]),3,6) == "add")    {   // if *** is followed with...
        $save_location = $working_dir;
	$structure = imap_fetchstructure($inbox,$email_number);
	$num_files_attached = otto_attach($structure,$inbox,$email_number,$save_location);
	echo "\r\n *** ".$num_files_attached." File(s) added *** \r\n";
      }
      else {		// future functions
	echo "\r\n***Nothing done***\r\n";
      }
    }
    else	{  //  ****** This is where most of the key work is done! ******
 
      // Compares the list of files to the search term in the subject
      $add_delim = "/".strtolower(trim($subject[$email_number]))."/";   //Paul, thanks for your stackoverflow post.  I know I'm still just a hack... 
      $ahhhh = preg_grep ($add_delim,$files);
      //debug  print_r ($ahhhh);

      // Starts to look at the emails
      if($ahhhh) 	{
	if(count($ahhhh)>1)	{		//******** Multiple matching entries ********
	  $e_address = clean_email($from[$email_number]);
          $e_subject = "We've found multiple matchs for: ".$subject[$email_number];
	  $e_body = "<p><b>".$configs['from_name']."</b> is having trouble picking an file.  Use the following email links below to select a file.</p><p>Then hit <b>SEND</b></p>";
	  $output_body = $e_body;
	  foreach($ahhhh as $bah)	{
	    $e_body.= '<p><a href="mailto:'.$configs['email_address'].'?subject='.$bah.'" target="_top">'.$bah.'</a></p>';
	    $output_body.= "\n\r".$bah."\n\r";
	  }
	  $e_alt_body = strip_tags($e_body);
	  $e_attach = null;
	  echo $output_body."\r\n";
	}
	else 			{ 		//******** 1 matching entry ********
	  $e_address = clean_email($from[$email_number]);
	  $e_subject = "File attached: ".$files[key($ahhhh)];
	  $e_body = "<p>Success, </p><p>Using the search term <b>".$subject[$email_number]."</b> we have selected the attached file for you.";
	  $e_attach = $cabinet[key($ahhhh)];
	  $e_alt_body = strip_tags($e_body);
	  echo "\r\nThe ".$files[key($ahhhh)]." has been selected to send.\r\n";
	}
      }  // closes if($ahhhh) 
      else	{				//******** No matching entries ********
	$e_address = clean_email($from[$email_number]);
	$e_subject = "Not found:".$subject[$email_number];
	$e_body = "<p>Sorry,</p><p>a match for <b>".$subject[$email_number]."</b> could not be found.</p><p>Thank you for trying this service.</p>";
	$e_attach = null;
        $e_alt_body = strip_tags($e_body);
	echo $subject[$email_number]." was not found\r\n";
      }
      otto_send($configs,$e_address,$e_subject,$e_body,$e_alt_body,$e_attach);
    }
  }  // closes foreach($emails as $email_number)
  echo $output."\r\n";
}  // closes if($emails)

echo "\r\n";
/* close the connection */

imap_expunge($inbox);
imap_close($inbox);

?>
