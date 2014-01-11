<?php
$array_file = $argv[1];

if(isset($array_file)) {
  $stack = array();
  $stack = unserialize(file_get_contents($array_file));
  echo "\n\r ********* OUTPUT FOR :".$array_file."*********\n\r";
  print_r($stack);
  echo "\n\r ********* OUTPUT END :".$array_file."*********\n\r";
}
else {
  echo "\n\rCall the array file you would like to see  ex// php lesser.php preference_array\n\r";
}

?>

