<?
session_start();
$calendar = $_REQUEST['calendar'];
$cash = $_REQUEST['cash'];
if($_REQUEST['code_ticket']!=''){
	$code = ',code_ticket';
	$code_ticket = ','.$_REQUEST['code_ticket'];
}else{
	$code = '';
}

$date = date('Y-m-d');
$user = $_SESSION['UserId'];
$sql = "INSERT INTO payment(calendar,cash".$code.",date,users,discount) VALUES($calendar,$cash".$code_ticket.",'$date',$user,0)";

echo $sql;
?>