<?php

header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
 
/**
 * PostGIS to GeoJSON
 * Query a PostGIS table or view and return the results in GeoJSON format, suitable for use in OpenLayers, Leaflet, etc.
 * 
 * @param 		string		$geotable		The PostGIS layer name *REQUIRED*
 * @param 		string		$geomfield		The PostGIS geometry field *REQUIRED*
 * @param 		string		$srid			The SRID of the returned GeoJSON *OPTIONAL (If omitted, EPSG: 4326 will be used)*
 * @param 		string 		$fields 		Fields to be returned *OPTIONAL (If omitted, all fields will be returned)* NOTE- Uppercase field names should be wrapped in double quotes
 * @param 		string		$parameters		SQL WHERE clause parameters *OPTIONAL*
 * @param 		string		$orderby		SQL ORDER BY constraint *OPTIONAL*
 * @param 		string		$sort			SQL ORDER BY sort order (ASC or DESC) *OPTIONAL*
 * @param 		string		$limit			Limit number of results returned *OPTIONAL*
 * @param 		string		$offset			Offset used in conjunction with limit *OPTIONAL*
 * @return 		string					resulting geojson string
 */
function escapeJsonString($value) { # list from www.json.org: (\b backspace, \f formfeed)
  $escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
  $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
  $result = str_replace($escapers, $replacements, $value);
  return $result;
}

// Grab the posted data from the AJAX POST method ($.post)
$attrname = $_POST['attr'];
$tablename = $_POST['table'];
//$area = $_POST['area'];
//$area = strtolower($area);
 
$dbconn = pg_connect("host=localhost port=5432 dbname=besecure_data user=postgres password=postgres")
or die('Could not connect: ' . pg_last_error());
 
# Build SQL SELECT statement and return the geometry as a GeoJSON element in EPSG: 4326
//$sql = "SELECT " . pg_escape_string($fields) . ", st_asgeojson(transform(" . pg_escape_string($geomfield) . ",$srid)) AS geojson FROM " . pg_escape_string($geotable);
$sql = 'SELECT st_asgeojson(st_transform(A.geom,4326)) AS geojson, A.name, B.'.$attrname.' AS descriptor FROM northern_ireland.osni_ward93 AS A, northern_ireland.'.$tablename.' AS B WHERE lower(A.name) = lower(B.ward)';
 
# Try query or error
$rs = pg_query($dbconn, $sql) or die('Query failed: ' . pg_last_error());
if (!$rs) {
    echo "An SQL error occured.\n";
    exit;
}
 
# Build GeoJSON
$output    = '';
$rowOutput = '';
 
while ($row = pg_fetch_assoc($rs)) {
    $rowOutput = (strlen($rowOutput) > 0 ? ',' : '') . '{"type": "Feature", "geometry": ' . $row['geojson'] . ', "properties": {';
    $props = '';
    $id    = '';
    foreach ($row as $key => $val) {
        if ($key != "geojson") {
            $props .= (strlen($props) > 0 ? ',' : '') . '"' . $key . '":"' . escapeJsonString($val) . '"';
        }
        if ($key == "id") {
            $id .= ',"id":"' . escapeJsonString($val) . '"';
        }
    }
    
    $rowOutput .= $props . '}';
    $rowOutput .= $id;
    $rowOutput .= '}';
    $output .= $rowOutput;
}
 
$output = '{ "type": "FeatureCollection", "features": [ ' . $output . ' ]}';

$myfile = fopen("choropleth_results.geojson", "w");
fwrite($myfile, $output);
fclose($myfile);

print -1;

// Free resultset
pg_free_result($rs);

// Closing connection
pg_close($dbconn);
?>