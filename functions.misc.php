<?php

function add_permission($newp, $newf, $permit_array_file)	{
$stack = unserialize(file_get_contents($permit_array_file));
//debug echo "\n\r*****************************************\n\r";
//print_r($stack);

if(isset($stack[$newf]))	{	// if new file name is present in array
  //debug echo "\n\rtrue\n\r";
  if(in_array($newp,array($stack[$newf])))	{	// check to see name is part of array
    echo "\n\r".$newp." is already in array\n\r";
  }
  else 						{
    //debug echo "&&&&&&&&&&&&&&&\n\r";      
    //print_r($stack[$newf]);
    //print_r(explode(" ", $stack[$newf]));
    if(in_array($newp,explode(" ", $stack[$newf])))	{
      echo "\n\r".$newp." is already added for ".$newf."\n\r";
    }
    else 			{
      $stack[$newf] = ''.str_replace(array('{','}'),"",$stack[$newf]).' '.$newp.'';    
      echo "\n\rPermission for ".$newp." has been added to ".$newf."\n\r";
    }
    //debug echo "\n\r&&&&&&&&&&&&&&&\n\r"; 
  }
  $result = $stack;
}
else
{
  //debug echo "\n\rfalse\n\r";
  $fun = array($newf => $newp);
  print_r($fun);
  $stack[$newf] = $newp;
  $result = $stack;
} 
//debug echo "\n\r*****************************************\n\r";
//debug echo print_r($result);

file_put_contents($permit_array_file,serialize($result));
}

//  email to folder preference file
function add_pref($newp, $newf, $array_file)       {

$stack = array();
$stack = unserialize(file_get_contents($array_file));
$stack[$newp] = $newf;
echo "\n\r*** add_pref ***\n\r";
print_r($stack);
file_put_contents($array_file,serialize($stack));
echo"\n\r*** returned add_pref ".$newf." ***\n\r";
return $newf;
}

function check_pref($newp, $newf, $array_file)       {
$stack = array();
$stack = unserialize(file_get_contents($array_file));
echo "\n\r*** check_pref ***\n\r";
print_r($stack);
if(isset($stack[$newp]))	{
  $newf = $stack[$newp];
}
echo "\n\r*** returned check_pref".$newf."***\n\r";
return $newf;
}
?>
