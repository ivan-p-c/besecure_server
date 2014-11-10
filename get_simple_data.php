<?php
 header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
 
// Grab the posted data from the AJAX POST method ($.post)
$attrname = $_POST['attr'];
$tablename = $_POST['table'];
$area = $_POST['area'];
$area = strtolower($area);
$schema = $_POST['cs_area'];
 
$dbconn = pg_connect("host=localhost port=5432 dbname=besecure_data user=postgres password=postgres")
or die('Could not connect: ' . pg_last_error());

$query = 'SELECT '.$attrname.' FROM '.$schema.'.'.$tablename.' WHERE lower(ward) = lower(\''.$area.'\')';	
$result = pg_query($query) or die('Query failed: ' . pg_last_error());
$out=pg_fetch_assoc($result);

// encode the array to JSON format so it is usable in javascript
print $out[$attrname];

// Free resultset
pg_free_result($result);

// Closing connection
pg_close($dbconn);
 
?>