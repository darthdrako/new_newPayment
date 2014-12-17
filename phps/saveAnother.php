<?
include('../../../libs/db.class.php');

session_start();

$db = new DB();

$calendar = $_REQUEST['calendar'];
$type = $_REQUEST['type'];


if($type=='bonus'){
	$bonus_number = $_REQUEST['ticket'];
	$effective = $_REQUEST['value'];
	$copago = $_REQUEST['covalue'];
	$sql = "INSERT INTO bono(calendar,effective,bonus_number,type,copago) VALUES($calendar,$effective,$bonus_number,'BONIFICACION',$copago)";
	$db->doSql($sql);

}elseif($type=='credit' || $type=='debit' || $type=='cheque'){
	if($type=='credit' || $type=='debit') $type=$type.'o';
	$bank = $_REQUEST['bank'];
	$holder = $_REQUEST['holder'];
	$number = $_REQUEST['number'];
	$value = $_REQUEST['value'];

	$sql = "INSERT INTO $type(bank,holder,number,value,observation,calendar,type,state) 
			VALUES('$bank','$holder',$number,$value,'$date',$calendar,'".strtoupper($type)."','activa'";


}elseif($type=='warranty'){
	$init_date = date('Y-m-d');
	$end_date = $_REQUEST['end_date'];
	$cash = $_REQUEST['cash'];
	$observation = $_REQUEST['observation'];
	$user = $_SESSION['UserId'];

	$sql = "INSERT INTO $warranty(init_date,end_date,calendar,cash,observation,users) 
			VALUES('$init_date','$end_date',$calendar,$cash,'$observation',$user";

}

echo $sql;
?>