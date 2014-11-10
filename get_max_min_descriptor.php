<?php

header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

// Grab the posted data from the AJAX POST method ($.post)
$attrname = $_POST['attr'];
$tablename = $_POST['table'];
$schema = $_POST['cs_area'];
//$area = $_POST['area'];
//$area = strtolower($area);
 
$dbconn = pg_connect("host=localhost port=5432 dbname=besecure_data user=postgres password=postgres")
or die('Could not connect: ' . pg_last_error());
 
$sql_maxmin = 'SELECT max('.$attrname.') AS maximum, min('.$attrname.') AS minimum FROM '.$schema.'.'.$tablename;

$rs_maxmin = pg_query($dbconn, $sql_maxmin) or die('Query failed: ' . pg_last_error());
if (!$rs_maxmin) {
    echo "An SQL error occured while querying max value for the descriptor: \n";
    exit;
}
$row = pg_fetch_array($rs_maxmin);
print json_encode($row);

// Free resultset
pg_free_result($rs_maxmin);



// Closing connection
pg_close($dbconn);
?>