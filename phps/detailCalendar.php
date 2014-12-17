<?
header('Content-Type: application/json');
session_start();
$calendar = $_POST['calendar'];
$dbconn = pg_connect("host=localhost port=5432 dbname=bioris user=postgres password=justgoon") or die('NO HAY CONEXION: ' . pg_last_error());
$query = "SELECT exam.name, exam.id  FROM calendar_exam LEFT JOIN exam ON exam.id=calendar_exam.exam WHERE calendar=$calendar";
$result = pg_query($query) or die("SQL Error 1: " . pg_last_error());
while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)) {
    $exams[] = array(
        'id' => $row['id'],
        'name' => $row['name']
    );
}



$query = "SELECT (patient.name || ' ' || patient.lastname) AS paciente, date_c AS date, hour_c AS hour FROM calendar LEFT JOIN patient ON patient.id=calendar.patient WHERE calendar.id=$calendar";
$result = pg_query($query) or die("SQL Error 1: " . pg_last_error());
$row = pg_fetch_array($result, null, PGSQL_ASSOC);
$detail = array(
    'paciente' => $row['paciente'],
    'date' => $row['date'],
    'hour' => $row['hour'],
    'pay' => 5000, //Abono ya realizado, ficticio de momento
    'exams' => $exams
);
echo json_encode($detail);
?>