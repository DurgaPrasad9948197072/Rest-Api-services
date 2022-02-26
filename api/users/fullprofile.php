<?php

 header('Access-Control-Allow-Origin: *');
 header('Content-Type: application/json');
 header('Access-Control-Allow-Methods: POST');
 header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/Users.php';
  include_once '../../models/Neighbors.php';
  include_once '../../models/Following.php';

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();
  // Instantiate blog Users object
  $users = new Users($db);
  $neighbors = new Neighbors($db);
  $following = new Following($db);
  
  $error = 0;
  // Get post
  $data = json_decode(file_get_contents("php://input"));
  
  $users->token = $data->token;
  $users->uid   = $data->uid;

  $neighbors->token=$data->token;
  $neighbors->uid=$data->uid;

  $following->touserid = $data->uid;
  

  $fname ="../../jsons/".$users->uid."fullprofile.json";
  //echo $fname;
  $content = json_encode($data);

  $file = fopen($fname,'w+');
  
  fwrite($file, $content);
  fclose($file);

  

  // check if id in json
  if(!$users->uid){
    echo json_encode(
        array(
          'status' => 'false',
          'message' => 'User identification missing.'
        )
    );
    $error = 1;
  } else if(!$users->token){  // Check if token exists
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



 $result = $neighbors->valtoken();

 $num = $result->rowCount();
 // echo "\n Number of rows:".$num;



if($num == 0){
 echo json_encode(
        array(
          'status' => 'false',
          'message' => 'Invalid User.'
        )
  );
  exit;
}


$users->read_single();

//Total followers for this user
$userfollowers = $following->getuserfollowers();
$numfollowers = $userfollowers->rowCount();

// Get businesses registered under this user.

$result = $users->getbusinesses();
  
// Get row count
$busscnt = $result->rowCount();
$buss_arr = array();
$buss_arr['businesses'] = array();
$user_arr['data'] = array();
$totbizfoll = 0;


//array_push($user_arr['data'], $user_item);

if($busscnt > 0) {

    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
      extract($row);
      
      $following->bussid = $BusId;
      

      $bussfollowers = $following->getbusfollowers();
      $bussfolcnt = $bussfollowers->rowCount();

      $buss_item = array(
        'bussid' => $BusId,
        'name' => $Name,
        'bussdesc' => $BusDesc,
        'buzzpic'  => $PicUrl,
        'bussfollowers' => $bussfolcnt
        
      );

      $totbizfoll = $totbizfoll+$bussfolcnt;

      // Push to "data"
      array_push($buss_arr['businesses'], $buss_item);
    }   
}
    $user_item = array(
      'id' => $users->uid,
      'name' => $users->name,
      'userpic' => $users->picurl,
      'statusmsg' => $users->statusmsg,
      'invisible' => $users->invisible,
      'persfollowers' => $numfollowers,
      'totalbussfollowers' => $totbizfoll,
      'language' => $users->language,
      'devtoken' => $users->devtoken,
      'device' => $users->device

      #'totolbussfollowers' => $numbussfollowers
    );
    array_push($user_arr['data'], $user_item);
    $result = array_merge($user_arr,$buss_arr);
    //array_push($user_arr['data'],$buss_item);

echo json_encode($result);

