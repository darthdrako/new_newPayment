<?
header('Content-Type: application/json');
session_start();
$calendar = $_POST['calendar'];
$dbconn = pg_connect("host=192.168.0.210 port=5432 dbname=es14b_hrt2 user=postgres password=justgoon") or die('NO HAY CONEXION: ' . pg_last_error());
$query = "SELECT (patient.name || ' ' || patient.lastname) AS paciente, date_c AS date, hour_c AS hour FROM calendar LEFT JOIN patient ON patient.id=calendar.patient WHERE calendar.id=$calendar";
$result = pg_query($query) or die("SQL Error 1: " . pg_last_error());
$row = pg_fetch_array($result, null, PGSQL_ASSOC);
$detail = array(
    'paciente' => $row['paciente'],
    'date' => $row['date'],
    'hour' => $row['hour'],
    'pay' => 5000 //Abono ya realizado, ficticio de momento
);
echo json_encode($detail);
?>