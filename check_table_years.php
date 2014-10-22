<?php
 header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
 
// Grab the posted data from the AJAX POST method ($.post)
$tablename = $_POST['table'];
 
$dbconn = pg_connect("host=localhost port=5432 dbname=besecure_data user=postgres password=postgres")
or die('Could not connect: ' . pg_last_error());

$query = 'SELECT per_year FROM northern_ireland.tables_list_northern_ireland WHERE tablename = \''.$tablename.'\'';	
$result = pg_query($query) or die('Query failed: ' . pg_last_error());
$out=pg_fetch_assoc($result);
	
print json_encode($out);


// Free resultset
pg_free_result($result);

// Closing connection
pg_close($dbconn);
 
?>