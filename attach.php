<?PHP
// Adapted from Chali's post at http://stackoverflow.com/questions/2649579/downloading-attachments-to-directory-with-imap-in-php-randomly-works
// **********************need to add in check if file exists and handling...currently overwrites

function otto_attach(&$structure,$inbox,$email_number,$save_location)  {

     $attachments = array();
       if(isset($structure->parts) && count($structure->parts)) {
         for($i = 0; $i < count($structure->parts); $i++) {
           $attachments[$i] = array(
              'is_attachment' => false,
              'filename' => '',
              'name' => '',
              'attachment' => '');

           if($structure->parts[$i]->ifdparameters) {
             foreach($structure->parts[$i]->dparameters as $object) {
               if(strtolower($object->attribute) == 'filename') {
                 $attachments[$i]['is_attachment'] = true;
                 $attachments[$i]['filename'] = $object->value;
               }
             }
           }

           if($structure->parts[$i]->ifparameters) {
             foreach($structure->parts[$i]->parameters as $object) {
               if(strtolower($object->attribute) == 'name') {
                 $attachments[$i]['is_attachment'] = true;
                 $attachments[$i]['name'] = $object->value;
               }
             }
           }

           if($attachments[$i]['is_attachment']) {
             $attachments[$i]['attachment'] = imap_fetchbody($inbox, $email_number, $i+1);
             if($structure->parts[$i]->encoding == 3) { // 3 = BASE64
               $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
             }
             elseif($structure->parts[$i]->encoding == 4) { // 4 = QUOTED-PRINTABLE
               $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
             }
           }             
         } // for($i = 0; $i < count($structure->parts); $i++)
       } // if(isset($structure->parts) && count($structure->parts))

    if(count($attachments)!=0){
	$namer = "";
        foreach($attachments as $at){
            if($at['is_attachment']==1){
                file_put_contents($save_location.DIRECTORY_SEPARATOR.$at['filename'],$at['attachment']);
		$namer.= "  ".$at['filename']."  ";
            }
        }
    //This is for debuging
    return (count($attachments)-1).$namer;
    }
}
?>
