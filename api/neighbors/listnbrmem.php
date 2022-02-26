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

  $neighbors->nid = $data->nid;
  $neighbors->uid = $data->uid;
  $neighbors->token = $data->token;

// Save JSON
  $fname ="../../jsons/".$neighbors->uid."listnbrmem.json";
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

  if($num > 0){
       //echo "<br> count :".$count;

    $result = $neighbors->listnbrmem();
  
  // Get row count
    $nbrcnt = $result->rowCount();
    //echo "<br> row count :".$nbrcnt;

  // Check if any categories
    if($nbrcnt > 0) {
        $neighbors_arr = array();
        $neighbors_arr['data'] = array();

        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
          extract($row);

          $neighbors_item = array(
            'uid' => $Id,
            'name' => $Name,
            'device' => $Device,
            'fcmToken' => $FcmToken
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
               'message' => 'No members found under this Neighborhoods'
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

  


