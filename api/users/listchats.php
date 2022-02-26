<?php

  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/Users.php';
  include_once '../../models/Neighbors.php';
  include_once '../../models/Chats.php';

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();
  
  $users = new Users($db);
  $neighbors = new Neighbors($db);
  $chats = new Chats($db);

  // Get post
  $data = json_decode(file_get_contents("php://input"));
  
  $users->token = $data->token;
  $users->uid   = $data->uid;
  

  $neighbors->token=$data->token;
  $neighbors->uid=$data->uid;
  
  $fname ="../../jsons/".$users->uid."listchats.json";
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
echo json_encode($cht_arr);
