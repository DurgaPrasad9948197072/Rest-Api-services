<?php

  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With');

  include_once '../../config/Database.php';
  //include_once '../../models/Users.php';
  include_once '../../models/Business.php';

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();
  // Instantiate blog Users object
  //$users = new Users($db);
  $business = new Business($db);

  // Get post
  $data = json_decode(file_get_contents("php://input"));
  

$result = $business->getpaymenttypes();

$lngcnt = $result->rowCount();
$pmts_arr['data'] = array();
// Check for languages list
if($lngcnt > 0) {

    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        //echo "lngcnt :".$lngcnt."\n";
        $pmts_item = array(
        'pmtid' => $PmtId,
        'name' => $Name,
        'logourl' => $LogoUrl
        );
        //echo "Id :".$Id."\n";
        //echo "Name :".$Name."\n";
        //echo "Code :".$Code."\n";

        // Push to "data"
        array_push($pmts_arr['data'], $pmts_item);
    }

    // Turn to JSON & output
    echo json_encode($pmts_arr);

} else {
    // No Users
    echo json_encode(
        array(
            'status' => 'false',
            'message' => 'No supported Payment Types available'
            )
    );
}
//print_r(json_encode($languages_arr));
