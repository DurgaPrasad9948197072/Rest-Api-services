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
  
  //echo "uid :".$business->uid."\n";
  //echo "busid :".$business->busid."\n";

 
  foreach ($data->payments as $key) {
        
    $business->pmtid = $key->pmtid;
    $business->phoneno = $key->phoneno;

    //echo "pmtid :".$business->pmtid."\n";
    //echo "phoneno :".$business->phoneno."\n";

    //$result = $neighbors->addfav();
  
    if(!$business->addbuspayments()) {
      echo json_encode(
        array(
          'status' => 'false',
          'message' => 'Failed to add payment types.'
        )
      );
    }
  
  }

  echo json_encode(
      array(
        'status' => 'true',
        'message' => 'Business payment types added successfully'
      )
  );
  
  
