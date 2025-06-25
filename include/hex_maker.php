<?php

function hex_convert($dec_in=''){
$var_hex=dechex($dec_in);
$j=strlen($var_hex);
for ($i=0; $i <4-$j ; $i++) {
	$var_hex="0".$var_hex;
}
$var_hex_proper=$var_hex[0].$var_hex[1]." ".$var_hex[2].$var_hex[3];
$var_hex_proper=strtoupper($var_hex_proper);
return $var_hex_proper;
}
?>








