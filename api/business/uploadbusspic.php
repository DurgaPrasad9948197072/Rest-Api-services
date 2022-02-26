<?php 

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With');

include_once '../../config/Database.php';
include_once '../../models/Business.php';


// Instantiate DB & connect
$database = new Database();
$db = $database->connect();
// Instantiate blog business object
$business = new Business($db);



$response = array();
$upload_dir = 'uploads/busspic/';
$server_dir ='/Applications/MAMP/htdocs/RestAPI/';
$server_url = 'http://localhost:8888/RestAPI/';

//$server_dir = '/var/www/clozbii.in/html/';
//$server_url = 'https://www.clozbii.in/';


if($_FILES['avatar'])
{
    $avatar_name = $_FILES["avatar"]["name"];
    $avatar_tmp_name = $_FILES["avatar"]["tmp_name"];
    $error = $_FILES["avatar"]["error"];

    
    $business->uid = $_POST['uid'];
    $business->token = $_POST['token'];
    $business->busid = $_POST['busid'];
 

    if($error > 0){
        $response = array(
            "status" => "error",
            "error" => true,
            "message" => "Error uploading the file!"
        );
    }else{

        if($_FILES['avatar']['size'] / 1024 > 5120) { 
            $response = array(
                "status" => "false",
                "message" => "Image should be maximun 5MB in size!"
            );

        }
        $size = $_FILES['avatar']['size'] / 1024;

        if($_FILES['avatar']['type'] == 'image/jpeg' || 
               $_FILES['avatar']['type'] == 'image/pjpeg' || 
               $_FILES['avatar']['type'] == 'image/png' ||
               $_FILES['avatar']['type'] == 'image/gif'){

                $random_name = rand(1000,1000000)."-".$avatar_name;
                $random_name = strtolower($random_name);
                $random_name = preg_replace('/\s+/', '-', $random_name);

                $directoryName = $server_dir.$upload_dir.$business->busid;
                
                //The name of the directory that we need to create.
                //$directoryName = 'images';
                 
                //Check if the directory already exists.
                if(!is_dir($directoryName)){
                    //Directory does not exist, so lets create it.
                    mkdir($directoryName, 0755);
                    // echo "directory created\n";
                }

                $upload_name = $server_dir.$upload_dir.$business->busid."/".$random_name;


                $upload_name = preg_replace('/\s+/', '-', $upload_name);
                

                $width      = 110;
                $height     = 110;
                $quality    = 75;

                //$image_name = $_FILES['uploadImg']['name'];

                $success = compressImage($avatar_tmp_name, $upload_name, $width, $height, $quality);
                if($success) {
                                       
                    $business->picurl = $server_url.$upload_dir.$business->busid."/".$random_name;

                    //echo "picurl ".$business->picurl."\n";
                    
                    $business->updatebusspic();

                    $response = array(
                        "status" => "true",
                        "message" => "File uploaded successfully",
                        "url" => $server_url.$upload_dir.$business->busid."/".$random_name
                    );
                }
          } else {
            $response = array(
                "status" => "false",
                "message" => "Sorry, only JPG, JPEG, PNG, & GIF files are allowed to upload!"
            );
          }

    }

}else{
    $response = array(
        "status" => "error",
        "error" => true,
        "message" => "No file was sent!"
    );
}

echo json_encode($response);

function compressImage($source_file, $target_file, $nwidth, $nheight, $quality) {
  //Return an array consisting of image type, height, widh and mime type.
 
  $image_info = getimagesize($source_file);
  if(!($nwidth > 0)) $nwidth = $image_info[0];
  if(!($nheight > 0)) $nheight = $image_info[1];
  
  if(!empty($image_info)) {
    switch($image_info['mime']) {
      case 'image/jpeg' :
        if($quality == '' || $quality < 0 || $quality > 100) $quality = 75; //Default quality
        // Create a new image from the file or the url.
        $image = imagecreatefromjpeg($source_file);
        //$thumb = imagecreatetruecolor($nwidth, $nheight);
        //Resize the $thumb image
        //imagecopyresized($thumb, $image, 0, 0, 0, 0, $nwidth, $nheight, $image_info[0], $image_info[1]);
        // Output image to the browser or file.
        return imagejpeg($image, $target_file, $quality); 
        
        break;
      
      case 'image/png' :
        if($quality == '' || $quality < 0 || $quality > 9) $quality = 6; //Default quality
        // Create a new image from the file or the url.
        $image = imagecreatefrompng($source_file);
        //$thumb = imagecreatetruecolor($nwidth, $nheight);
        //Resize the $thumb image
        //imagecopyresized($thumb, $image, 0, 0, 0, 0, $nwidth, $nheight, $image_info[0], $image_info[1]);
        // Output image to the browser or file.
        return imagepng($image, $target_file, $quality);
        break;
        
      case 'image/gif' :
        if($quality == '' || $quality < 0 || $quality > 100) $quality = 75; //Default quality
        // Create a new image from the file or the url.
        $image = imagecreatefromgif($source_file);
        //$thumb = imagecreatetruecolor($nwidth, $nheight);
        //Resize the $thumb image
        //imagecopyresized($thumb, $image, 0, 0, 0, 0, $nwidth, $nheight, $image_info[0], $image_info[1]);
        // Output image to the browser or file.
        return imagegif($image, $target_file, $quality); //$success = true;
        break;
        
      default:
        echo "<h4>Not supported file type!</h4>";
        break;
    }
  }
}
?>