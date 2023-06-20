<?php
// Load the global data from the file or database
$global_data = json_decode(file_get_contents("cluster_output.json"));

// Define the global variable that contains the data
global $cluster;
$cluster = $global_data;

$global_data2 = json_decode(file_get_contents("TRY.json"));

global $cluster2;
$cluster2 = $global_data2;
?>