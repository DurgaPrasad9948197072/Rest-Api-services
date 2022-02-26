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

  $users->mobile = $data->mobile;
  $users->otp = $data->otp;

  $fname ="../../jsons/".$users->mobile."verifyotp.json";
  //echo $fname;
  $content = json_encode($data);

  $file = fopen($fname,'w+');
  fwrite($file, $content);
  fclose($file);


  $result = $users->verifyotp();
  
  //echo "<br> after verifyopp\n";
  
  // Get row count
  $num = $result->rowCount();
  
  //echo "<br> after verifyopp num ".$num;

  if($num > 0) {
    echo json_encode(
      array(
        'status' => 'true',
        'message' => 'OTP verified',
        'uid'      => $users->uid,
        'token'   => $users->phoneid,
        'user'   => $users->status,
        'name' => $users->name,
        'mobile' => $users->mobile,
        'refcode' => $users->refcode,
        'refcount' => $users->refcount,
        'referredby' => $users->referredby,
        'doj' => $users->doj,
        'dou' => $users->activity,
        'statusmsg' => $users->statusmsg,
        'invisible' => $users->invisible, #0 visible, 1 invisible
        'picurl' => $users->picUrl,
        'fcmToken' => $users->fcmtoken,
        'device' => $users->device
     )
    );
  } else {
    echo json_encode(
      array(
        'status' =>'false',
        'message' => 'Invalid OTP'
      )
    );
  }
  