<?php
 header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
 
  // Grab the posted data from the AJAX POST method ($.post)
  //$username = $_POST['username'];
 
  $dbconn = pg_connect("host=cdcgeoserver.cloudapp.net port=5432 dbname=besecure_data user=postgres password=postgrescau5ew4y")
    or die('Could not connect: ' . pg_last_error());

// Performing SQL query
$query = 'SELECT DISTINCT name FROM metadata.categories WHERE name NOT IN (\'locations\',\'geographies\')';
$result = pg_query($query) or die('Query failed: ' . pg_last_error());

//Fetch all the result in an array
$out=pg_fetch_all($result);
// encode the array to JSON format so it is usable in javascript
print json_encode($out);


// Free resultset
pg_free_result($result);

// Closing connection
pg_close($dbconn);
 
?>