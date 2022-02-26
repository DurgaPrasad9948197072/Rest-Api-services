<?php 
 // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/Business.php';
  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate blog post object
  $business = new Business($db);

  // Get raw posted data
  $data = json_decode(file_get_contents("php://input"));

  $business->uid = $data->uid;
  $business->token = $data->token;
  
  // Users read query
  $result = $business->busstypes();
  
  // Get row count
  $num = $result->rowCount();
  
  
  // Check if any categories
  if($num > 0) {
        // Cat array
        $bus_arr = array();
        $bus_arr['data'] = array();

        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
          extract($row);
          $bus_item = array(
            'bustypeid' => $BusTypeId,
            'name1' => $Name1,
            'name2' => $Name2,
            'status' => $Status,
            'doc'  => $Doc,
            'dou'  => $Dou,
            'picurl' => $IconUrl
          );

          // Push to "data"
          array_push($bus_arr['data'], $bus_item);
        }

        // Turn to JSON & output
        echo json_encode($bus_arr);

  } else {
        // No Users
          echo json_encode(
          array(
            'status' => 'false',
            'message' => 'Business types missing.'
          )
        );
  }
