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
  $business->busid = $data->busid;
  $business->token = $data->token;
  $business->busname = $data->busname;
  $business->about = $data->about;
  $business->address = $data->address;

  /*
  echo "uid :".$business->uid."\n";
  echo "token :".$business->token."\n";
  echo "busname :".$business->busname."\n";
  echo "busid :".$business->busid."\n";
  echo "about :".$business->about."\n";
  echo "address :".$business->address."\n";
  */  

  // Save JSON
  $fname ="../../jsons/".$business->uid."updatebusiness.json";
  //echo $fname;
  $content = json_encode($data);

  $file = fopen($fname,'w+');
  fwrite($file, $content);
  fclose($file);
  
  // Create Group
  if(!$business->updatebusiness()){
    echo json_encode(
        array(
          'status' => 'false',
          'message' => 'Unable to update business.'
        )
    );
  }


  echo json_encode(
      array(
        'status' => 'true',
        'message' => 'Business updated successfully'
      )
  );
  
  
