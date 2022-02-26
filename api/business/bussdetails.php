<?php

 header('Access-Control-Allow-Origin: *');
 header('Content-Type: application/json');
 header('Access-Control-Allow-Methods: POST');
 header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/Following.php';
  include_once '../../models/Business.php';

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

 
  $following = new Following($db);
  $business = new Business($db);
  
  $error = 0;
  // Get post
  $data = json_decode(file_get_contents("php://input"));
  
  $business->token = $data->token;
  $business->uid   = $data->uid;
  $business->busid = $data->busid;

  //echo "\n uid :".$data->uid;
  //echo "\n token :".$data->token;
  //echo "\n busid :".$data->busid;
  $following->touserid = $data->uid;
  

  $fname ="../../jsons/".$business->uid."bussdetails.json";
  //echo $fname;
  $content = json_encode($data);

  $file = fopen($fname,'w+');
  fwrite($file, $content);
  fclose($file);

  

  // check if id in json
  if(!$business->uid){
    echo json_encode(
        array(
          'status' => 'false',
          'message' => 'User identification missing.'
        )
    );
    $error = 1;
  } else if(!$business->token){  // Check if token exists
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



 $result = $business->valtoken();

 $num = $result->rowCount();
 //echo "\n Number of rows:".$num;



if($num == 0){
 echo json_encode(
        array(
          'status' => 'false',
          'message' => 'Invalid User.'
        )
  );
  exit;
}




$time_arr = array();
$time_arr['timings'] = array();
$pymts_arr['payments'] = array();
$products_arr['productlist'] = array();
$buss_arr['data'] = array();

// echo "\n I'm here 1";
$totbizfoll = 0;

$following->busid = $data->busid;


$bussfollowers = $following->getbusfollowers();
$bussfolcnt = $bussfollowers->rowCount();
//Get business timings
$result = $business->gettimings();
$timecnt = $result->rowCount();
// echo "\n I'm here 2";
if($timecnt > 0) {

    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
      extract($row);

      $time_item = array(
        'bussid'        => $row['BusId'],
        'weekday' 		=> $row['WeekDay'],
        'starttime' 	=> $row['StartTime'],
        'endtime'  		=> $row['EndTime'],
        'busstimingsid' => $row['BussTimingsId']
      );
      // Push to "data"
      array_push($time_arr['timings'], $time_item);
    }   
}

//Get accepted payments - TBD
//echo "\n I'm here 3";
$result2 = $business->getpayments();
$pymtcnt = $result2->rowCount();

if($pymtcnt > 0) {

    while($row = $result2->fetch(PDO::FETCH_ASSOC)) {
      extract($row);

      $pymt_item = array(	
        'bussid'        => $row['BusId'],
        'paymenttype' 	=> $row['Name'],
        'logourl' 		=> $row['LogoUrl'],
        'pmtphone'  	=> $row['PmtPhone']
      );
      // Push to "data"
      array_push($pymts_arr['payments'], $pymt_item);
    }   
}

//Get Products list  -TBD
// echo "\n I'm here 4";
$result3 = $business->getproducts();
$prdcnt = $result3->rowCount();

if($prdcnt > 0) {

    while($row = $result3->fetch(PDO::FETCH_ASSOC)) {
      extract($row);

      $products_item = array(	
        'bussid'      => $row['BusId'],
        'prodid' 	    => $row['ProdId'],
        'prodname' 		=> $row['ProductName'],
        'picurl'  	=> $row['CoverImage'],
        'originalprice' => $row['OriginalPrice'],
        'SellPrice' => $row['SellPrice']
      );
      // Push to "data"
      array_push($products_arr['products'], $products_item);
    }   
}
// echo "\n I'm here 5";
$business->fromusrid = $data->uid;
//Get business followers count
$business->isFollowingBuss();
//Get business details
$business->businessdetails();
//Get home delivery details
$business->bussdelvry();

$buss_item = array(
    'busid'   => $business->busid,
    'name'    => $business->name,
    'busdesc' => $business->busdesc,
    'ownerid' => $business->usrid,
    'address' => $business->address,
    'picurl'  => $business->picurl,
    'phoneid' => $business->phoneid,
    'totalbussfollowers' => $bussfolcnt,
    'homedelivery'	=> $business->delvryid,
    'follow' => $business->cnt

);
//echo "\n I'm here 6";
array_push($buss_arr['data'], $buss_item);
$merge1 = array_merge($buss_arr,$time_arr);
$merge2 = array_merge($merge1,$pymts_arr);
$result = array_merge($merge2,$products_arr);

//array_push($user_arr['data'],$buss_item);

echo json_encode($result);