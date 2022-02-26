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
  $users->password = $data->password;
 
  echo "\n name : ".$data->name;
  echo "\n password : ".$data->password;
  
  
  $fname ="../../jsons/".$users->name."adminregister.json";
  //echo $fname;
  $content = json_encode($data);

  $file = fopen($fname,'w+');
  fwrite($file, $content);
  fclose($file);


  //echo "<br> mobile no[".$users->mobile."]";

  
  //Generate Key;
  $users->phoneId = $users->crypt_password($users->password);

  //echo "<br> token no[".$users->phoneId."]";


  if($users->adminregister()) {
      
      echo json_encode(
        array(
          'status' => 'true',
          'message' => 'Registered successfully'
        )
      );
    } else {
      echo json_encode(
        array(
          'status' => 'false',
          'message' => 'Unable to Register'
        )
      );
    }