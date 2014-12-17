<?
include('../../../libs/db.class.php');

session_start();

$db = new DB();

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
$payment_type = $_REQUEST['payment_type'];

//ACTUALIZAR GARANTÍA EN CASO QUE CORRESPONDA

$sql = "INSERT INTO payment(calendar,cash".$code.",date,users,discount,payment_type) VALUES($calendar,$cash".$code_ticket.",'$date',$user,0,'$payment_type')";
$db->doSql($sql);
$sql = "UPDATE calendar SET exam_state='pagado' WHERE id=$calendar";
$db->doSql($sql);

$exams = $_REQUEST['exams'];
$agreements = $_REQUEST['agreements'];
$agreements_value = $_REQUEST['agreements_value'];

$exams = explode('-', $exams);
$agreements = explode('-', $agreements);
$agreements_value = explode('-', $agreements_value);

for($i=0;$i<count($exams);$i++){
	$sql2 = "UPDATE calendar_exam SET agreement=".$agreements[$i].", agreement_value=".$agreements_value[$i]." WHERE id=".$exams[$i];
	$db->doSql($sql2);
}

//echo $sql.'<br/>';
echo $sql2.'<br/>';

?>