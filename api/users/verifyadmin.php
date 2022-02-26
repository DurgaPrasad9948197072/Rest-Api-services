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

  $data = json_decode(file_get_contents("php://input"));

  $users->name = $data->name;
  $users->password = $data->password;

  $fname ="../../jsons/".$users->name."verifyadmin.json";
  //echo $fname;
  $content = json_encode($data);

  $file = fopen($fname,'w+');
  fwrite($file, $content);
  fclose($file);

  //echo "\n mobile".$users->mobile;
  //echo "\n password".$users->password;
  $result = $users->verifyadmin();
  
  //echo "<br> after verifyopp\n";
  
  // Get row count
  $num = $result->rowCount();
  
  //echo "<br> after verifyopp num ".$num;

  if($num > 0) {

    echo json_encode(
      array(
        'status' => 'true',
        'message' => 'Password verified',
        'adminid'      => $users->adminid,
        'name' => $users->name,
        'password' => $users->password,
		'token'   => $users->token
       
            )
    );
  } else {
    echo json_encode(
      array(
        'status' =>'false',
        'message' => 'Invalid Password'
      )
    );
  }
  
