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
  $business->busname = $data->busname;
  $business->bustypeid = $data->bustypeid;
  $business->phoneid = $data->contactno;
  $business->url = $data->url;   
  $business->about = $data->about;
  $business->searchtags = $data->searchtags;
  $business->picurl = $data->picurl;
  $business->lat = $data->latitude;
  $business->lon = $data->longitude;
  $business->address = $data->address;

  /*
  echo "uid :".$business->uid."\n";
  echo "token :".$business->token."\n";
  echo "busname :".$business->busname."\n";
  echo "bustypeid :".$business->bustypeid."\n";
  echo "contactno :".$business->phoneid."\n";
  echo "about :".$business->about."\n";
  echo "url :".$business->url."\n";
  echo "searchtags :".$business->searchtags."\n";
  echo "picurl :".$business->picurl."\n";
  echo "address :".$business->address."\n";
  */  

  // Save JSON
  $fname ="../../jsons/".$business->uid."addbusiness.json";
  //echo $fname;
  $content = json_encode($data);

  $file = fopen($fname,'w+');
  fwrite($file, $content);
  fclose($file);
  
  // Create Group
  if(!$business->addbusiness()){
    echo json_encode(
        array(
          'status' => 'false',
          'message' => 'Unable to add business.'
        )
    );
  }


  echo json_encode(
      array(
        'status' => 'true',
        'message' => 'Business added successfully',
        'busid' => $business->busid
      )
  );
  
  
