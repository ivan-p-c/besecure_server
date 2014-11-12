<?php
 header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
 
// Grab the posted data from the AJAX POST method ($.post)
$category = $_POST['category'];
$schema = $_POST['cs_area'];
$geo_level = $_POST['geo_level'];
 
$dbconn = pg_connect("host=localhost port=5432 dbname=besecure_data user=postgres password=postgres")
or die('Could not connect: ' . pg_last_error());

$pre_query = 'SELECT id FROM metadata.categories WHERE name = \''.$category.'\'';	
$pre_result = pg_query($pre_query) or die('Query failed: ' . pg_last_error());
$pre_out=pg_fetch_assoc($pre_result);

$geo_level_query = 'SELECT id FROM metadata.geography_levels WHERE name = \''.$geo_level.'\'';
$geo_level_result = pg_query($geo_level_query) or die('Query failed: ' . pg_last_error());
$geo_level_id = pg_fetch_assoc($geo_level_result);
	
// Performing SQL query
$query = 'SELECT tablename,name_shown FROM '.$schema.'.tables_list_'.$schema.' WHERE category_id = \''.$pre_out['id'].'\' AND geography_level_id = \''.$geo_level_id['id'].'\' ORDER BY name_shown ASC';
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