<?php

 header('Access-Control-Allow-Origin: *');
 header('Content-Type: application/json');
 header('Access-Control-Allow-Methods: POST');
 header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/Users.php';
  include_once '../../models/Neighbors.php';
  include_once '../../models/Following.php';
  include_once '../../models/Groups.php';
  include_once '../../models/Business.php';
  include_once '../../models/Chats.php';

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate model objects
  $users = new Users($db);
  $neighbors = new Neighbors($db);
  $following = new Following($db);
  $business = new Business($db);
  $groups = new Groups($db);
  $chats = new Chats($db);
  
  $error = 0;
  // Get post
  $data = json_decode(file_get_contents("php://input"));
  
  
  $users->token = $data->token;
  $users->uid   = $data->uid;
  $business->lat = $data->latitude;
  $business->lon = $data->longitude;

  $groups->uid   = $data->uid;
  $following->uid = $data->uid;
  $neighbors->token=$data->token;
  $neighbors->uid=$data->uid;


  $following->touserid = $data->uid;
  

  $fname ="../../jsons/".$users->uid."home.json";
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

// $users->read_single();

// Get businesses followed by this user.
$result1 = $neighbors->latestnbrdiscussion();
// Get row count
$nbrdiscnt = $result1->rowCount();

$nbrdis_arr = array();
$nbrdis_arr['nbrlatest'] = array();

if($nbrdiscnt > 0) {

    while($row = $result1->fetch(PDO::FETCH_ASSOC)) {
      extract($row);

      $neighbors->nid = $nei_id;

      $nbrs = $neighbors->getneighborhood();

      $nbrdis_item = array(
        'nid' => $neighbors->nid,
        'name' => $neighbors->name,
        'longitude' => $neighbors->longitude,
        'latitude'  => $neighbors->latitude
      );
      // Push to "nbrlatest"
      array_push($nbrdis_arr['nbrlatest'], $nbrdis_item);
    }   
}




$result2 = $following->getfollwedbuss();
  
// Get row count
$busscnt = $result2->rowCount();

$buss_arr = array();
$buss_arr['businesses'] = array();
$user_arr['data'] = array(); 

if($busscnt > 0) {
     
    while($row = $result2->fetch(PDO::FETCH_ASSOC)) {
      extract($row);
    
      $buss_item = array(
        'bussid' => $BusId,
        'name' => $Name,
        'bussdesc' => $BusDesc,
        'buzzpic'  => $PicUrl,
        'phoneid' => $PhoneId
      );
      // Push to "data"
      array_push($buss_arr['businesses'], $buss_item);
       
    }   
} else {

  $result3 = $business->latlon();
  $num = $result3->rowCount();

  if($num > 0) {

        while($row = $result3->fetch(PDO::FETCH_ASSOC)) {
          extract($row);

          $distance = round($row['distance'],2)." km";

          $buss_item = array(
            'busid'   => $row['BusId'],
            'name'    => $row['Name'],
            'busdesc' => $row['BusDesc'],
            'ownerid' => $row['UsrId'],
            'address' => $row['Address'],
            'buzzpic'  => $row['PicUrl'],
            'phoneid' => $row['PhoneId'],
            'distance' => $distance
          );

          // Push to "data"
          array_push($buss_arr['businesses'], $buss_item);
        }

  }



}

$merge1 = array_merge($nbrdis_arr,$buss_arr);

// Get Neighborhoods followed by this user.

$listresult = $neighbors->listfavnbr();
  
// Get row count
$nbrcnt = $listresult->rowCount();
 
$nbr_arr = array();
$nbr_arr['neighborhoods'] = array();

if($nbrcnt > 0) {

    while($row = $listresult->fetch(PDO::FETCH_ASSOC)) {
      extract($row);
    
      $nbr_item = array(
        'nid' => $id,
        'name' => $title,
        'longitude' => $Lon,
        'latitude'  => $Lat
      );
      // Push to "data"
      array_push($nbr_arr['neighborhoods'], $nbr_item);
       
    }   
}
//Mergr Businesses and Neighborhoods arrays.
$merge2 = array_merge($merge1,$nbr_arr);


// Populate Groups info  getusrgroups

$grpslistresult = $groups->latestgrpdiscussion();
  
// Get row count
$grpscnt = $grpslistresult->rowCount();
 
$grp_arr = array();
$grp_arr['groups'] = array();
//echo "count of groups:".$grpscnt."\n";
if($grpscnt > 0) {

    while($row = $grpslistresult->fetch(PDO::FETCH_ASSOC)) {
      extract($row);

      $groups->groupid = $group_id;

      //echo "GroupId now is:".$groups->groupid ."\n";
      $grps = $groups->getgroupinfo();

      $grp_item = array(
        'groupid' => $groups->groupid,
        'name' => $groups->name,
        'description' => $groups->description,
        'state' => $groups->state,
        'picurl' => $groups->picurl
      );
      // Push to "data"
      array_push($grp_arr['groups'], $grp_item);
       
    }   
}
//Mergr Businesses and Neighborhoods arrays.
$merge3 = array_merge($merge2,$grp_arr);



// Populate Chats info
$cht_arr = array();
$cht_arr['chats'] = array();

$chats->uid = $data->uid;
$chtslistresult = $chats->latestchats();
//echo "after  latestchats:\n";

// Get row count
$chtscnt = $chtslistresult->rowCount();
//echo "count of chats:".$chtscnt."\n";

if($chtscnt > 0) {

    while($row = $chtslistresult->fetch(PDO::FETCH_ASSOC)) {
      extract($row);

      $chats->senderid = $row['sender_id'];
      $chats->chatid = $row['id'];
      $chats->thread = $row['thread'];
      $chats->recieverid = $row['reciever_id'];

      $sender_arr = array();
      $sender_arr['senderdata'] = array();

      $users->uid = $row['sender_id'];
      $users->read_single();
      
      $sender_item = array(
        'senderid' => $chats->senderid,
        'name' => $users->name,
        'picurl' => $users->picurl,
        'device' => $users->device,
        'fcmtoken'  => $users->fcmtoken
      );
      
      $recev_arr = array();
      $recev_arr['recieverdata'] = array();

      $users->uid = $row['reciever_id'];
      $users->read_single();

      $recev_item = array(
        'recieverid' => $chats->recieverid,
        'name' => $users->name,
        'picurl' => $users->picurl,
        'device' => $users->device,
        'fcmtoken'  => $users->fcmtoken
      );
     /*  
      $cht_item = array(
        "senderdata" => $sender_item,
        "recieverdata" => $recev_item,
        'thread' => $chats->thread,
        'chatid' => $chats->chatid
      );
      */
      //Get latest chat message
     
      $chats->getlatestmessage(); 

      $cht_item = array(
        "senderdata" => $sender_item,
        "recieverdata" => $recev_item,
        'thread' => $chats->thread,
        'message' => $chats->message,
        'chatid' => $chats->chatid
      );

      // Push to "data"
      array_push($cht_arr['chats'], $cht_item);
       
    }   
}
//Mergr Businesses and Neighborhoods arrays.
$merge4 = array_merge($merge3,$cht_arr);

// Populate Announcements.

$users->uid = $data->uid;
$users->read_single();

$user_item = array(
  'uid' => $users->uid,
  'name' => $users->name,
  'userpic' => $users->picurl,
  'statusmsg' => $users->statusmsg,
  'invisible' => $users->invisible,
  'persfollowers' => $numfollowers,
  'totalbussfollowers' => $totbizfoll
  #'totolbussfollowers' => $numbussfollowers
);
array_push($user_arr['data'], $user_item);
$result = array_merge($user_arr,$merge4);
//array_push($user_arr['data'],$buss_item);

echo json_encode($result);

