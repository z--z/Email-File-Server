<?php
require('functions.send.php');	//email sending function
require('function.attach.php');	//download attachments function
require('functions.misc.php');  //for setting folder permisions
//****** See config.php for more the settings ******
if(file_exists('my_config.php'))	{
  $configs = include('my_config.php');  //a seperate config file from the defualt
}	
else				{
  $configs = include('config.php');
}



function clean_email($long_email) 	{
  $clean_mail = trim(str_replace(array('>','<'),"",substr($long_email,strripos($long_email,"<"))));
  return $clean_mail;
}
function clean_domain($long_email)       {
  $clean_domain = trim(str_replace(array('>','@'),"",substr($long_email,strripos($long_email,"@"))));
  return $clean_domain;
}



$start_dir = getcwd();

$inbox = imap_open($configs['hostname_IMAP_login'],$configs['username'],$configs['password']) or die('Cannot connect to Gmail: ' . imap_last_error());

$list_imap_labels = imap_list($inbox,$configs['hostname_IMAP'], "*");
if(in_array($configs['hostname_IMAP'].$configs['folder_prefix'].$configs['default_folder'],$list_imap_labels,true))      {
}  else {
  imap_createmailbox($inbox,$configs['hostname_IMAP'].$configs['folder_prefix'].$configs['default_folder']);
}

$emails = imap_search($inbox,'ALL');
//debug $list = imap_getmailboxes($inbox, "{imap.gmail.com}", "*");
//print_r($list);
$n = imap_num_msg($inbox);

$output = "\r\n\r\n"."*******  ".date("y.m.d  h.i.s",time())."\t".$n."  ******\r\n\r\n";
$output_e = "";
echo $output;

if($emails) 	{
  foreach($emails as $email_number) 	{

    //fetchs data about the email
    $overview = imap_fetch_overview($inbox,$email_number,0);
 //   imap_mail_move($inbox,$email_number,"Replied");
    $subject[$email_number] = $overview[0]->subject;
    $from[$email_number] = $overview[0]->from;

    $working_email = clean_email($from[$email_number]);

  // ****** Start Selection of working folder selection
    if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.$configs['file_server_folder']))	{
    }
    Else	{
      mkdir(dirname(__FILE__).DIRECTORY_SEPARATOR.$configs['file_server_folder']);
    }

    $c_email = $working_email;


//      check_permission( ) ;
//      active_folder();

    $folder = $configs['folder_prefix'].$configs['default_folder'];  //if default folder is "", a new folder will not be created
    $folder = check_pref($c_email,$folder,$configs['pref_array']);

    $working_dir = dirname(__FILE__).DIRECTORY_SEPARATOR.$configs['file_server_folder'].DIRECTORY_SEPARATOR.$folder;
    if(file_exists($working_dir))       {
    }
    else                          {
      mkdir($working_dir);
    }
    $cabinet = glob($working_dir.DIRECTORY_SEPARATOR.'*');
    chdir($working_dir);
    $files = glob('*');
    chdir($start_dir);
  // ****** END of working folder Selection

    //outputs the email header information	
    $output_e.= "\r\n".$email_number."\t".$overview[0]->subject."\t\t\t\t".($overview[0]->seen ? 'read' : 'unread');
 
    // Looks at alternate commands
    if(substr(strtolower($subject[$email_number]),0,3) == "***")	{   // if string starts with "***"
      if(substr(strtolower($subject[$email_number]),3,3) == "add")    {   // if *** is followed with...
        $save_location = $working_dir;
	$structure = imap_fetchstructure($inbox,$email_number);
	$num_files_attached = otto_attach($structure,$inbox,$email_number,$save_location);
	echo "\r\n *** ".$num_files_attached." File(s) added to ".$save_location."*** \r\n";
      }
      elseif(substr(strtolower($subject[$email_number]),3,3) == "new")	{	//new folder function

	$new_folder = $configs['folder_prefix'].trim(str_replace(array(':'),"",substr($subject[$email_number],strripos($subject[$email_number],':'))));
	if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.$configs['file_server_folder'].DIRECTORY_SEPARATOR.$new_folder))	{
	  echo "***error*** ".$new_folder." already exists in the local file system\n\r";
	  // add in negative email response
	}
	else {
          mkdir(dirname(__FILE__).DIRECTORY_SEPARATOR.$configs['file_server_folder'].DIRECTORY_SEPARATOR.$new_folder);
          echo "\n\r".$new_folder." was created in the local file system\n\r";
        
	  $list_imap_labels = imap_list($inbox,$configs['hostname_IMAP'], "*");
  	  if(in_array($configs['hostname_IMAP'].$new_folder,$list_imap_labels,true))	{
	    echo "\n\r***error*** ".$configs['hostname_IMAP'].$new_folder." label already exists\n\r";
            print_r($list_imap_labels);
	    $folder = $configs['folder_prefix'].$configs['default_folder'];
	  }
	  else	{
	    imap_createmailbox($inbox,$configs['hostname_IMAP'].$new_folder);
            echo "\n\r".$new_folder." was created on the IMAP server\n\r";
	    $folder = $new_folder;
	  }
          add_permission($c_email,$new_folder,$configs['permit_array']);
	  add_pref($c_email,$new_folder,$configs['pref_array']);
	}
      }

      else {		// future functions
	echo "\r\n***Nothing done*** \r\n".substr(strtolower($subject[$email_number]),3,6)."\n\r";
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
	  $e_address = $working_email;
          $e_subject = "We've found multiple matchs for: ".$subject[$email_number];
	  $e_body = "<p><b>".$configs['from_name']."</b> is having trouble picking an file.  Use the following email links below to select a file.</p><p>Then hit <b>SEND</b></p>";
	  $output_body = $e_body;
	  foreach($ahhhh as $bah)	{
	    $e_body.= '<p><a href="mailto:'.$configs['email_address'].'?subject='.$bah.'" target="_top">'.$bah.'</a></p>';
	    $output_body.= "\n\r".$bah."\n\r";
	  }
	  $e_alt_body = strip_tags($e_body);
	  $e_attach = null;
	  echo $output_body."\n\r";
	}
	else 			{ 		//******** 1 matching entry ********
	  $e_address = $working_email;
	  $e_subject = "File attached: ".$files[key($ahhhh)];
	  $e_body = "<p>Success, </p><p>Using the search term <b>".$subject[$email_number]."</b> we have selected the attached file for you.";
	  $e_attach = $cabinet[key($ahhhh)];
	  $e_alt_body = strip_tags($e_body);
	  echo "\r\nThe ".$files[key($ahhhh)]." has been selected to send.\r\n";
	}
      }  // closes if($ahhhh) 
      else	{				//******** No matching entries ********
	$e_address = $working_email;
	$e_subject = "Not found:".$subject[$email_number];
	$e_body = "<p>Sorry,</p><p>a match for <b>".$subject[$email_number]."</b> could not be found.</p><p>Thank you for trying this service.</p>";
	$e_attach = null;
        $e_alt_body = strip_tags($e_body);
	echo $subject[$email_number]." was not found\r\n";
      }
      otto_send($configs,$e_address,$e_subject,$e_body,$e_alt_body,$e_attach);
    }

  imap_mail_move($inbox,$email_number,$folder);
  }  // closes foreach($emails as $email_number)
  echo $output_e."\n\r".$output."\n\r";
}  // closes if($emails)

echo "\r\n";
/* close the connection */

imap_expunge($inbox);
imap_close($inbox);

?>

