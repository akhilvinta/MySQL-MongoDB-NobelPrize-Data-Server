<?php

$id = intval($_GET['id']);

// set the Content-Type header to JSON,
// so that the client knows that we are returning JSON data
$db = new mysqli('localhost', 'cs143', '', 'class_db');
if ($db->connect_errno > 0) {
    ie('Unable to connect to database [' . $db->connect_error . ']');
}

$query = "SELECT * FROM Laureate WHERE id=$id";
$rs = $db->query($query);


while ($row = $rs->fetch_assoc()) {
    $id = $row['id'];
    $date = $row['birth_date'];
    $city = $row['birth_city'];
    $country = $row['birth_country'];
    // print "$id, $date, $city, $country<br>";
}

$query = "SELECT * FROM Person WHERE id=$id";
$rs = $db->query($query);
$num_results_person = $rs->num_rows;

while ($row = $rs->fetch_assoc()) {
    $person_name = $row['given_name'];
    $person_family_name = $row['family_name'];
    $person_gender = $row['gender'];
    // print "$person_name, $person_family_name, $person_gender<br>";
}

$query = "SELECT * FROM Organization WHERE id=$id";
$rs = $db->query($query);
$num_results_person = $rs->num_rows;

while ($row = $rs->fetch_assoc()) {
    $org_name = $row['org_name'];
    // print "$org_name<br>";
}

// echo "Prizes and Affiliations:<br>";
$query = "SELECT * FROM Awarded WHERE laureate_id=$id";
$rs = $db->query($query);
$num_results_person = $rs->num_rows;

$arr = array();

while ($row = $rs->fetch_assoc()) {
    $prize_id = $row['prize_id'];
    $affiliation_id = $row['affiliation_id'];
    // print "$prize_id, $affiliation_id<br>";
    $key_in_dict = array_key_exists($prize_id,$arr);
    if($key_in_dict){
      array_push($arr[$prize_id], $affiliation_id);
    }
    else{
      $arr[$prize_id] = array($affiliation_id);
    }
}

// print_r($arr);
// echo "<br>";

$allPrizes = [];

foreach ($arr as $prize => $affs) {
  $currentPrize = (object)[];

  // echo "prize = $prize, affiliation = $affs<br>";
  $query = "SELECT * FROM Prize WHERE id=$prize";
  $rs = $db->query($query);
  while ($row = $rs->fetch_assoc()) {
    $award_year = $row['award_year'];
    if(strlen(strval($award_year))>0){     
        $currentPrize->{"awardYear"} = $award_year;
    }

    $category = $row['category'];
    if(strlen(strval($category))>0){    
        $en = (object)[];
        $en->{"en"} = $category;
        $currentPrize->{"category"} = $en;
    }
    $sort_order = $row['sort_order'];
    if(strlen(strval($sort_order))>0){     
        $currentPrize->{"sortOrder"} = $sort_order;
    }
    // print "infooooo $award_year, $category, $sort_order<br>";
  }

  $all_affiliations = [];
  foreach ($affs as $aff) {

      if (strlen(strval($aff)) < 1){
        continue;
      }

      $cur_affiliation = (object)[];
      // echo "aff = $aff<br>";
      $query = "SELECT * FROM Affiliation WHERE id=$aff";
      $rs = $db->query($query);
      while ($row = $rs->fetch_assoc()) {
        $affiliation_name = $row['name'];
        $affiliation_city = $row['city'];
        $affiliation_country = $row['country'];
        // print "$affiliation_name, $affiliation_city, $affiliation_country<br>";
      }

      if(strlen(strval($affiliation_name))>0){
            $en=(object)[];
            $en->{"en"} = $affiliation_name;        
            $cur_affiliation->{"name"} = $en;         
      }
      if(strlen(strval($affiliation_city))>0){
            $en=(object)[];
            $en->{"en"} = $affiliation_city;        
            $cur_affiliation->{"city"} = $en;         
      }
      if(strlen(strval($affiliation_country))>0){
            $en=(object)[];
            $en->{"en"} = $affiliation_country;        
            $cur_affiliation->{"country"} = $en;         
      }

      // echo "printing current affiliation:";
      // print_r($cur_affiliation);
      // echo "<br>";
      
      array_push($all_affiliations, $cur_affiliation);
  }  
  if(count($all_affiliations) > 0){
    $currentPrize -> {"affiliations"} = $all_affiliations;
  }
  // echo "printing current prize with all affiliations: ";
  // print_r($currentPrize);
  // echo "<br>";
  array_push($allPrizes, $currentPrize);
}


$output = (object)[];
$output->{"id"} = strval($id);

//birth/founding data will exist whether a person or organization
$birth_info = (object)[];
  if(strlen(strval($date)) > 0){
    $birth_info->{"date"} = $date;
  }

$places = (object)[];
if(strlen(strval($city)) > 0){
  $en = (object)[];
  $en->{"en"} = $city;
  $places->{"city"} = $en;

}
if(strlen(strval($country)) > 0){
  $en = (object)[];
  $en->{"en"} = $country;
  $places->{"country"} = $en;
}

$birth_info->{"place"} = $places;

if(strlen(strval($person_name)) > 0){
  $en = (object)[];
  $en->{"en"} = $person_name;
  $output->{"givenName"} = $en;
  if(strlen(strval($person_family_name)) > 0){
    $en = (object)[];
    $en->{"en"} = $person_family_name;
    $output->{"familyName"} = $en;
  }
  if(strlen(strval($person_gender)) > 0){
    $output->{"gender"} = $person_gender;
  }
  
  $output->{"birth"} = $birth_info;

}

else{
  $en = (object)[];
  $en->{"en"} = $org_name;
  $output -> {"orgName"} = $en;
  $output->{"founded"} = $birth_info;
}

if(count(nobelPrizes) > 0){
  $output -> {"nobelPrizes"} = $allPrizes;
}

$output = (object) $output;
echo json_encode($output);


?>
