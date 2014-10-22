<?php
 header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
 
// Grab the posted data from the AJAX POST method ($.post)
$tablename = $_POST['table'];
 
$dbconn = pg_connect("host=localhost port=5432 dbname=besecure_data user=postgres password=postgres")
or die('Could not connect: ' . pg_last_error());

$query = 'SELECT column_name 
FROM information_schema.columns 
WHERE table_name = \''.$tablename.'\' 
ORDER BY ordinal_position';	
$result = pg_query($query) or die('Query failed: ' . pg_last_error());
$out=pg_fetch_all($result);

// encode the array to JSON format so it is usable in javascript
print json_encode($out);


// Free resultset
pg_free_result($result);

// Closing connection
pg_close($dbconn);
 
?>