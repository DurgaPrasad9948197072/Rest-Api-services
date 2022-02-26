<?php 
 // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/Groups.php';
  include_once '../../models/Following.php';

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate blog post object
  $groups = new Groups($db);
  $following = new Following($db);

  // Get raw posted data
  $data = json_decode(file_get_contents("php://input"));

  $groups->uid = $data->uid;
  $groups->token = $data->token;
  $groups->groupid = $data->groupid;
 
  $following->uid = $data->uid;

  // Users read query
  $result = $groups->getgroupinfo();

  // Cat array
  $grp_arr = array();
  $grp_arr['data'] = array(); 

  $groups->isadmin();

  $EditPermit = 'false';

  if($groups->admin == 0){
    $EditPermit = 'true';
  }

  $grp_item = array(
    'groupid' => $groups->groupid,
    'name' => $groups->name,
    'description' => $groups->description,
    'status' => $groups->status,
    'state'  => $groups->state,
    'picurl' => $groups->picurl,
    'editpermit' => $EditPermit,
    'doc'    => $groups->doc
  );

  // Push to "data"
  array_push($grp_arr['data'], $grp_item);

  $following->uid = $data->uid;

  $result1 = $groups->getgroupmembers();
  // Get row count
  $memcnt = $result1->rowCount();

  $grpmem_arr = array();
  $grpmem_arr['members'] = array();

  if($memcnt > 0) {

      while($row = $result1->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        
        $following->fromusrid = $following->uid;
        $following->uid = $Id;

        $following->isFollowingUser();
        $grpmem_item = array(
          //Id,Name,StatusMsg,Status,Invisible,PicUrl,Activity,Admin
          'id' => $Id,
          'name' => $Name,
          'statusmsg' => $StatusMsg,
          'admin' => $Admin,
          'picurl' => $PicUrl,
          'follow' => $following->cnt
        );
        // Push to "nbrlatest"
        array_push($grpmem_arr['members'], $grpmem_item);
      }   
  }

  // Merge arrays
  $merge1 = array_merge($grp_arr,$grpmem_arr);

  // Turn to JSON & output
  echo json_encode($merge1);

