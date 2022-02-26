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

  $neighbors->pincode = $data->pincode;
  //$neighbors->lat = $data->latitude;
  //$neighbors->lon = $data->longitude;

  // Users read query
  $result = $neighbors->read();
  
  // Get row count
  $num = $result->rowCount();

  // Check if any categories
  if($num > 0) {
        // Cat array
        $neighbors_arr = array();
        $neighbors_arr['data'] = array();

        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
          extract($row);

          $neighbors_item = array(
            'id' => $id,
            'title' => $title
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
            'status'  => 'false',
            'message' => 'Neighborhoods not Found'
          )
        );
  }

