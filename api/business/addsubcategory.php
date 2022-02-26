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
  $business->subcatname = $data->subcatname;
  $business->picurl = $data->picurl;
  $business->catid = $data->catid;
  $business->status = $data->status;
  $business->langcode = $data->langcode;

  /*
  echo "\n uid:".$data->uid;
  echo "\n token:".$data->token;
  echo "\n busid:".$data->busid;
  

  foreach ($data->timings as $key) {
    echo "\n I'm here";   
    $business->weekday = $key->weekday;
    $business->starttime = $key->starttime;
    $business->endtime = $key->endtime;

    //$result = $neighbors->addfav();
  
    if(!$business->addbustimings()) {
      echo json_encode(
        array(
          'status' => 'false',
          'message' => 'Failed to add business timings.'
        )
      );
      exit;
    }
  
  }
 */
  if(!$business->addsubcategory()) {
    echo json_encode(
    array(
      'status' => 'false',
      'message' => 'Failed to add subcategory.'
    )
  );
  exit;
  }
  echo json_encode(
      array(
        'status' => 'true',
        'message' => 'subcategory added successfully'
      )
  );
  
  
