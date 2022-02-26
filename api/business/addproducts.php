<?php
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/Business.php';
  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate blog post object
  $business = new Business($db);

  // Get raw posted data
  $data = json_decode(file_get_contents("php://input"));
  $business->uid = $data->uid;
  $business->token = $data->token;
  $business->busId = $data->busId;
  $business->coverimage = $data->coverimage;
  $business->productname = $data->productname;
  $business->originalprice = $data->originalprice;
  $business->sellprice = $data->sellprice;
  $business->categoryid = $data->categoryid;   
  $business->weight = $data->weight;
  $business->tax = $data->tax;
  $business->description = $data->description;
  

  /*
  echo "uid :".$business->uid."\n";
  echo "token :".$business->token."\n";
  echo "busname :".$business->busname."\n";
  echo "bustypeid :".$business->bustypeid."\n";
  echo "contactno :".$business->phoneid."\n";
  echo "about :".$business->about."\n";
  echo "url :".$business->url."\n";
  echo "searchtags :".$business->searchtags."\n";
  echo "picurl :".$business->picurl."\n";
  echo "address :".$business->address."\n";
  */  

  // Save JSON
  $fname ="../../jsons/".$business->busId."addproducts.json";
  //echo $fname;
  $content = json_encode($data);

  $file = fopen($fname,'w+');
  fwrite($file, $content);
  fclose($file);
  
  // Create Group
  if(!$business->addproducts()){
    echo json_encode(
        array(
          'status' => 'false',
          'message' => 'Unable to add product.'
        )
    );
  }


  echo json_encode(
      array(
        'status' => 'true',
        'message' => 'Business added successfully',
        'busid' => $business->busid
      )
  );
  
  
