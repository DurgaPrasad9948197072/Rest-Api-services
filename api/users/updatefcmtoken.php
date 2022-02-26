<?php

  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/Users.php';
  include_once '../../models/Neighbors.php';

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();
  // Instantiate blog Users object
  $users = new Users($db);
  $neighbors = new Neighbors($db);

  // Get post
  $data = json_decode(file_get_contents("php://input"));
  
  $users->token = $data->token;
  $users->uid   = $data->uid;
  $users->fcmtoken  = $data->fcmToken;
  $users->device = $data->device;

  $neighbors->token=$data->token;
  $neighbors->uid=$data->uid;
  
  $fname ="../../jsons/".$users->uid."updatefcmtoken.json";
  //echo $fname;
  $content = json_encode($data);

  $file = fopen($fname,'w+');
  fwrite($file, $content);
  fclose($file);


  // check if id in json
  if(!$users->uid){
    echo json_encode(
        array(
          'status' => 'false',
          'message' => 'User identification missing.'
        )
    );
    $error = 1;
  } else if(!$users->token){  // Check if token exists
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

 $result = $neighbors->valtoken();

 $num = $result->rowCount();
 // echo "\n Number of rows:".$num;

if($num == 0){
 echo json_encode(
        array(
          'status' => 'false',
          'message' => 'Invalid User.'
        )
  );
  exit;
}


if($users->updatefcmtoken()) {
    echo json_encode(
      array(
        'status' => 'true',
        'message' => 'User updated successfully'
      )
    );
  } else {
    echo json_encode(
      array(
        'status' => 'false',
        'message' => 'Unable to update user details'
      )
    );
  }

