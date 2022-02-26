<?php

  header('Access-Control-Allow-Origin: *'); 
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/Users.php';
 

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();
  // Instantiate blog Users object
  $users = new Users($db);

  // Get post
  $data = json_decode(file_get_contents("php://input"));
  $users->name = $data->name;
  $users->mobile = $data->mobile;
  
  $fname ="../../jsons/".$users->mobile."sendotp.json";
  //echo $fname;
  $content = json_encode($data);

  $file = fopen($fname,'w+');
  fwrite($file, $content);
  fclose($file);


  //echo "<br> mobile no[".$users->mobile."]";

  
  //Generate Key;
  $users->phoneId = $users->crypt_password($users->mobile);

  //echo "<br> token no[".$users->phoneId."]";


  if($users->sendotp()) {
      echo json_encode(
        array(
          'status' => 'true',
          'message' => 'OTP sent successfully'
        )
      );
    } else {
      echo json_encode(
        array(
          'status' => 'false',
          'message' => 'Unable to send OTP'
        )
      );
    }