<?php
	// get the id parameter from the request
    $id = $_GET['id'];
    // set the Content-Type header to JSON, 
    // so that the client knows that we are returning JSON data
    header('Content-Type: application/json');




    
    $mng = new MongoDB\Driver\Manager("mongodb://localhost:27017");
    $filter = [ 'id' => $id ]; 
    $query = new MongoDB\Driver\Query($filter); 

/*
   Send the following fake JSON as the result
   {  "id": $id,
      "givenName": { "en": "A. Michael" },
      "familyName": { "en": "Spencer" },
      "affiliations": [ "UCLA", "White House" ]
   }
*/



    
    $laureates = $mng->executeQuery("nobel.laureates", $query);
    foreach ($laureates as $laureate) {
        unset($laureate->{'_id'});
        echo json_encode($laureate, 1);
    }
?>