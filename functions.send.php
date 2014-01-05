<?php
include('/usr/share/php/PHPMailer/class.phpmailer.php');

function otto_send(&$configs,$otto_email,$otto_subject,$otto_body,$otto_alt_body,$otto_attach = Null)
{
$mail = new PHPMailer();

$mail->isSMTP();
$mail->Host = $configs['hostname_SMTP'];
$mail->SMTPAuth = $configs['req_auth_SMTP'];
$mail->SMTPSecure = $configs['encrypt_SMTP'];
$mail->Port = $configs['port_SMTP'];


$mail->Username = $configs['username'];
$mail->Password = $configs['password'];
$mail->From = $configs['email_address'];
$mail->FromName = $configs['from_name'];

$mail->isHTML(true);

if($otto_attach) {
        $mail->addAttachment($otto_attach); // The attachment will only activate if one is passed
}

$mail->addAddress($otto_email);
$mail->Subject = $otto_subject;
$mail->Body = $otto_body;
$mail->AltBody = $otto_alt_body;

if(!$mail->send()) {
   echo 'Message could not be sent.';
   echo 'Mailer Error: ' . $mail->ErrorInfo;
   exit;
}

echo "\r\nA message to ".$otto_email." has been sent\r\n";

}
?>
