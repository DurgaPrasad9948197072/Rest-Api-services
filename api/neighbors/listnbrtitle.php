<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/Neighbors.php';

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate Users object
  $neighbors = new Neighbors($db);

  $data = json_decode(file_get_contents("php://input"));

  $neighbors->title = $data->title;
  $neighbors->uid     = $data->uid;
  $neighbors->token   = $data->token;
 
 // Save JSON
  $fname ="../../jsons/".$neighbors->uid."listnbrtitle.json";
  //echo $fname;
  $content = json_encode($data);

  $file = fopen($fname,'w+');
  fwrite($file, $content);
  fclose($file);

 // check if id in json
  if(!$neighbors->uid){
    echo json_encode(
        array(
          'status' => 'false',
          'message' => 'User identification missing.'
        )
    );
    $error = 1;
  } else if(!$neighbors->token){  // Check if token exists
    echo json_encode(
        array(
          'status' => 'false',
          'message' => 'Auth token missing.'
        )
    );
    $error = 1;
  }

  if($error > 0)
    exit;

  // Validate token against Users table.
  $result = $neighbors->valtoken();

  $num = $result->rowCount();

  if($num > 0) {

  // Users read query
      $result = $neighbors->listnbrtitle();
      
      // Get row count
      $num = $result->rowCount();

      // Check if any categories
      if($num > 0) {
            // Cat array
            $neighbors_arr = array();
            $neighbors_arr['data'] = array();

            while($row = $result->fetch(PDO::FETCH_ASSOC)) {
              extract($row);
              if(!$division)
                $division='';
              if((!$taluk) || ($taluk=='NA'))
                $taluk='';
              $neighbors_item = array(
                'id' => $id,
                'title' => $title,
                'district' => $district,
                'state' => $state,
                'pincode' => $pincode,
                'division' => $division,
                'taluk' => $taluk,
                'longitude' => $Lon,
                'latitude' => $Lat
              );

              // Push to "data"
              array_push($neighbors_arr['data'], $neighbors_item);
            }

            // Turn to JSON & output
            echo json_encode($neighbors_arr);

      } else {
            // No Users
              echo json_encode(
              array(
                'status' => 'false',
                'message' => 'Neighborhoods not Found for this pincode'
              )
            );
      }
    } else {
    echo json_encode(
        array(
          'status' => 'false',
          'message' => 'Invalid Auth token.'
        )
    );

  }

