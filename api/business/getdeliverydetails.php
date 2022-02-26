<?php

 header('Access-Control-Allow-Origin: *');
 header('Content-Type: application/json');
 header('Access-Control-Allow-Methods: POST');
 header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/Following.php';
  include_once '../../models/Business.php';

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

 
  //$following = new Following($db);
  $business = new Business($db);
  
  $error = 0;
  // Get post
  $data = json_decode(file_get_contents("php://input"));
  
  $business->token = $data->token;
  $business->uid   = $data->uid;
  $business->busid = $data->busid;

  //echo "\n uid :".$data->uid;
  //echo "\n token :".$data->token;
  //echo "\n busid :".$data->busid;
  //$following->touserid = $data->uid;
  

  $fname ="../../jsons/".$business->uid."getdeliverydetails.json";
  //echo $fname;
  $content = json_encode($data);

  $file = fopen($fname,'w+');
  fwrite($file, $content);
  fclose($file);

  

  // check if id in json
  if(!$business->uid){
    echo json_encode(
        array(
          'status' => 'false',
          'message' => 'User identification missing.'
        )
    );
    $error = 1;
  } else if(!$business->token){  // Check if token exists
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



 $result = $business->valtoken();

 $num = $result->rowCount();
 //echo "\n Number of rows:".$num;



if($num == 0){
 echo json_encode(
        array(
          'status' => 'false',
          'message' => 'Home delivery not set for this business.'
        )
  );
  exit;
}

$business->bussdelvry();

echo json_encode(
    array(
        'status' => 'true',
        'delvryid'    => $business->delvryid
   )
  );
