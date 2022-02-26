<?php

  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/Users.php';
  include_once '../../models/Neighbors.php';

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();
  // Instantiate blog Users object
  $users = new Users($db);
  $neighbors = new Neighbors($db);

  // Get post
  $data = json_decode(file_get_contents("php://input"));
  /*
  $users->token = $data->token;
  $users->uid   = $data->uid;
  

  $neighbors->token=$data->token;
  $neighbors->uid=$data->uid;
  
  $fname ="../../jsons/".$users->uid."getlanguages.json";
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
*/

$result = $users->getlanguages();

$lngcnt = $result->rowCount();
$languages_arr['data'] = array();
// Check for languages list
if($lngcnt > 0) {

    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        //echo "lngcnt :".$lngcnt."\n";
        $languages_item = array(
        'lid' => $Id,
        'name' => $Name,
        'language' => $Code
        );
        //echo "Id :".$Id."\n";
        //echo "Name :".$Name."\n";
        //echo "Code :".$Code."\n";

        // Push to "data"
        array_push($languages_arr['data'], $languages_item);
    }

    // Turn to JSON & output
    echo json_encode($languages_arr);

} else {
    // No Users
    echo json_encode(
        array(
            'status' => 'false',
            'message' => 'No languages supported'
            )
    );
}
//print_r(json_encode($languages_arr));
