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
  $business->busid = $data->busid;
  $business->homedel = $data->homedel; /* 0 for no, 1 for yes */
  
  //echo "uid :".$business->uid."\n";
  //echo "token :".$business->token."\n";
  //echo "busid :".$business->busid."\n";
  //echo "homedel :".$business->homedel."\n";

 

  // Create Group
  if(!$business->addbusdelivery()){
    echo json_encode(
        array(
          'status' => 'false',
          'message' => 'Unable to add business.'
        )
    );
  }

  //echo "Before exit \n";

  echo json_encode(
      array(
        'status' => 'true',
        'message' => 'Business delivery added successfully'
      )
  );
  
  
