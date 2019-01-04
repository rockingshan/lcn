<?php

function printpackage($in_pack='')
{
	require_once "connect.php";
$chn_var="";
$chn_array[]=array();
$i=0;
mysqli_select_db($con,'meghbela_lcn_db_kol') or die("No database");

$sql_t="SELECT * FROM channel_tb JOIN lcn_tb ON channel_tb.lcn=lcn_tb.lcn JOIN package_tb ON channel_tb.sid=package_tb.sid WHERE package_tb.$in_pack='YES'";
$sql_tr1=mysqli_query($con, $sql_t);
$sql_tr2=mysqli_query($con, $sql_t);
if (!$sql_tr1) { // add this check.
    die('Invalid query: ' . mysqli_error());
}
//$mdata1=mysqli_fetch_array($sql_tr);

 while($mdata = mysqli_fetch_array($sql_tr1)){
	$m_array[]=$mdata['genre'];
	
}
$m_array=array_unique($m_array);
while($mdata2 = mysqli_fetch_array($sql_tr2)){
	$m_array2[] = $mdata2;
}
	foreach ($m_array as $w_key => $w_value) {
	foreach($m_array2 as $w_key1 => $w_value2){
	if($w_value2['genre'] == $w_value){
		$chn_var=$chn_var.$w_value2['channel'].", ";
				
	}
	
	}  
	$chn_var=rtrim($chn_var,', ');
	$chn_array[$i]=array($w_value=>$chn_var);
	$chn_var="";
	$i++;
}
	return $chn_array;
}
?>