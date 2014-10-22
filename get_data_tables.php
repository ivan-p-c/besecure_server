<?php
 header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
 
// Grab the posted data from the AJAX POST method ($.post)
$category = $_POST['category'];
 
$dbconn = pg_connect("host=localhost port=5432 dbname=besecure_data user=postgres password=postgres")
or die('Could not connect: ' . pg_last_error());

$pre_query = 'SELECT id FROM metadata.categories WHERE name = \''.$category.'\'';	
$pre_result = pg_query($pre_query) or die('Query failed: ' . pg_last_error());
$pre_out=pg_fetch_assoc($pre_result);
	
// Performing SQL query
$query = 'SELECT tablename,name_shown FROM northern_ireland.tables_list_northern_ireland WHERE category_id = \''.$pre_out['id'].'\' ORDER BY name_shown ASC';
$result = pg_query($query) or die('Query failed: ' . pg_last_error());

//Fetch all the result in an array
$out=pg_fetch_all($result);
// encode the array to JSON format so it is usable in javascript
print json_encode($out);


// Free resultset
pg_free_result($pre_result);
pg_free_result($result);

// Closing connection
pg_close($dbconn);
 
?>