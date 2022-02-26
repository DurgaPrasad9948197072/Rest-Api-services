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
  $neighbors->nid = $data->nid;

  $neighbors->token=$data->token;
  $neighbors->uid=$data->uid;


  $fname ="../../jsons/".$users->uid."nbrprofile.json";
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


$neighbors->read_single();

//Total followers for this user
$nbrfollowers = $neighbors->getnbrfollowers();

$numfollowers = $nbrfollowers->rowCount();

$mem_arr = array();
$mem_arr['members'] = array();
$nbr_arr['data'] = array();

$totbizfoll = 0;

//
//array_push($user_arr['data'], $user_item);

if($numfollowers > 0) {

    while($row = $nbrfollowers->fetch(PDO::FETCH_ASSOC)) {
      extract($row);

      $following->fromusrid = $neighbors->uid;
      $following->uid = $Id;

      $following->isFollowingUser();
  
        // Id,Name,StatusMsg,Status,Invisible,PicUrl,Activity 
      if($Id == $data->uid){
        $Name = 'You';
      }
      
      $mem_item = array(
            'MemId' => $Id,
            'Name' => $Name,
            'StatusMsg' => $StatusMsg,
            'Status'  => $Status,
            'Invisible' => $Invisible,
            'PicUrl' => $PicUrl,
            'Activity' => $Activity,
            'follow' => $following->cnt
      );

      $totbizfoll = $totbizfoll+$bussfolcnt;

      // Push to "data"
      array_push($mem_arr['members'], $mem_item);
    }   
}


$nbr_item = array(
  'nid' => $neighbors->nid,
  'title' => $neighbors->title,
  'pincode' => $neighbors->pincode,
  'state' => $neighbors->state,
  'district' => $neighbors->district,
  'totfollowers' => $numfollowers
  #'totolbussfollowers' => $numbussfollowers
);
array_push($nbr_arr['data'], $nbr_item);
$result = array_merge($nbr_arr,$mem_arr);
//array_push($user_arr['data'],$buss_item);

echo json_encode($result);

