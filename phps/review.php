<?
include('../../../libs/db.class.php');

session_start();

$db = new DB();

$calendar = $_REQUEST['calendar'];

$payment = $db->doSql("SELECT COUNT(*) AS pay FROM payment WHERE calendar=$calendar");

if($payment['pay']==0){
	$warranty = $db->doSql("SELECT COUNT(*) AS pay FROM warranty WHERE calendar=$calendar");
	$value = $warranty['pay'];
}else{
	$value=$payment['pay'];
}

echo $value;

?>