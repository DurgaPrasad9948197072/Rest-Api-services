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
  $users->password = $data->password;

  $fname ="../../jsons/".$users->mobile."verifypwd.json";
  //echo $fname;
  $content = json_encode($data);

  $file = fopen($fname,'w+');
  fwrite($file, $content);
  fclose($file);

  //echo "\n mobile".$users->mobile;
  //echo "\n password".$users->password;
  $result = $users->verifypwd();
  
  //echo "<br> after verifyopp\n";
  
  // Get row count
  $num = $result->rowCount();
  
  //echo "<br> after verifyopp num ".$num;

  if($num > 0) {

    $result2 = $users->getbusinesses();
    $num2 = $result2->rowCount();
    //echo "\n business count".$num2;
    if($num2 > 0) {
      while($bus = $result2->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $busid = $bus['BusId'];
        //echo "\n busid".$busid;
        
      }
    }

    echo json_encode(
      array(
        'status' => 'true',
        'message' => 'Password verified',
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
        'device' => $users->device,
        'busid' => $busid
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
  