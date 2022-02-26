<?php
  class Users {
    // DB Stuff
    private $conn;
    private $table = 'Users';

    // Properties
    public $Id;
    public $Name;
    public $PhoneId;
    public $Mobile;
    public $Password;
    public $Refcode;
    public $RefCount;
    public $ReferredBy;
    public $Doj;

    // Constructor with DB
    public function __construct($db) {
      $this->conn = $db;
    }
 // Get categories
    public function getbusinesses() {
      // Create query
      $query = 'SELECT
        BusId,
        Name,
        BusDesc,
        UsrId,
        Lat,
        Lon,
        Address,
        PhoneId,
        PicUrl,
	BusTypeId,
	Status
      FROM
        Business 
      WHERE UsrId =:uid or PhoneId =:phoneid';

      // Prepare statement
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':uid', $this->uid);
      $stmt->bindParam(':phoneid', $this->phoneid);
      // echo "profile id from getbusinesses :".$this->uid."\n";
      // echo "phoneid id from getbusinesses :".$this->phoneid."\n";
      // Execute query
      $stmt->execute();

      return $stmt;
    }

    // Businesses user following.
    //select BusId,Name from Business where BusId in (select BusId from Following where FromUsrId=57 and BusId!=0);
    public function followedbusinesses() {
      // Create query
      $query = 'SELECT
        BusId,
        Name,
        BusDesc,
        UsrId,
        Lat,
        Lon,
        Address,
        PhoneId,
        PicUrl
      FROM
        Business 
      WHERE BusId in (select BusId from Following where FromUsrId=:uid and BusId !=0)';

      // Prepare statement
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':uid', $this->uid);
      // echo "profile id from followedbusinesses :".$this->uid."\n";
      // Execute query
      $stmt->execute();

      return $stmt;
    }


    // Get categories
    public function read() {
      // Create query
      $query = 'SELECT
        Id,
        Name,
        PhoneId,
        Mobile,
        Password,
        Refcode,
        RefCount,
        ReferredBy,
        Doj,
        Status,
        Otp
      FROM
        ' . $this->table . '
      ORDER BY
        Doj DESC';

      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Execute query
      $stmt->execute();

      return $stmt;
    }
	  public function getstafflist() {
      // Create query
      $query = 'SELECT t1.Id,t1.Name,t1.Mobile,t1.Doj
				FROM Users t1
				LEFT JOIN Business t2 ON t2.UsrId = t1.Id
				WHERE t2.UsrId IS NULL ORDER BY `Name` ASC';

      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Execute query
      $stmt->execute();

      return $stmt;
    }
	  public function getboys() {
      // Create query
      $query = 'SELECT 
		COUNT(DbId) AS Delivery
		FROM `DeliveryBoys` WHERE Accept=1';

      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Execute query
      $stmt->execute();

      return $stmt;
    }
	
  public function getproof(){
    // Create query
    $query = 'SELECT 
			UsrId, 
			Address,
			Balance,
			UPI,
	DrivingLicense,
	IdentityCard,
	Dob,
	BankAccountNumber,
	BankName,
	AccountName,
	IfscCode,
	Doc,
	Accept,
	Delcode
	FROM 
	DeliveryBoys
	WHERE UsrId =:usrid';

      //Prepare statement
      $stmt = $this->conn->prepare($query);

      // Bind ID
      $stmt->bindParam(':usrid', $this->usrid);
	
	  $stmt->execute();
		
      return $stmt;
  }
 public function getdeliveryboy() {
      // Create query
      $query = 'SELECT t1.Id,t1.Name,t1.Mobile,t1.Doj
				FROM Users t1
				LEFT JOIN DeliveryBoys t2 ON t2.UsrId = t1.Id
				WHERE t2.UsrId IS NOT NULL AND Accept=1 ORDER BY `Name` ASC';

      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Execute query
      $stmt->execute();

      return $stmt;
    }
	
	
	public function getdelydoc() {
      // Create query
      $query = 'SELECT t2.UsrId,t1.Name,t1.Mobile,t1.Doj,t2.Doc,t2.Accept
				FROM Users t1
				LEFT JOIN DeliveryBoys t2 ON t2.UsrId = t1.Id
				WHERE t2.Status=0 OR t2.Status=1 ORDER BY `Name` ASC';

      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Execute query
      $stmt->execute();

      return $stmt;
    }


    // Get Single Users
  public function read_single(){
    // Create query
    $query = 'SELECT
        Id,
        Name,
        PhoneId,
        Mobile,
        Password,
        Refcode,
        RefCount,
        ReferredBy,
        Doj,
        Status,
        StatusMsg,
        Invisible,
        Activity,
        Otp,
        PicUrl,
        Profession,
        Dob,
        Gender,
        Language,
        Device,
        FcmToken
       FROM
          ' . $this->table . '
      WHERE id = ?
      LIMIT 0,1';

      //Prepare statement
      $stmt = $this->conn->prepare($query);

      // Bind ID
      $stmt->bindParam(1, $this->uid);

      // Execute query
      $stmt->execute();

      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      // set properties
      $this->uid = $row['Id'];
      $this->name = $row['Name'];
      $this->phoneId = $row['PhoneId'];
      $this->mobile = $row['Mobile'];
      //$this->password = $row['Password'];
      $this->refcode = $row['Refcode'];
      $this->refCount = $row['RefCount'];
      $this->referredBy = $row['ReferredBy'];
      $this->doj = $row['Doj'];
      $this->status = $row['Status'];
      //$this->otp = $row['Otp'];
      $this->statusmsg = $row['StatusMsg'];
      $this->invisible = $row['Invisible'];
      $this->activity = $row['Activity'];
      $this->picurl = $row['PicUrl'];

      $this->profession = $row['Profession'];
      $this->dob  = $row['Dob'];
      $this->gender  = $row['Gender'];
      $this->language  = $row['Language'];
      $this->device  = $row['Device'];
      $this->fcmtoken  = $row['FcmToken'];

  }
 


  // Create Users
  public function create() {
    // Create Query
    $query = 'INSERT INTO ' .
      $this->table . '
    SET
      Mobile = :mobile,
      Otp    = :otp';

  // Prepare Statement
  $stmt = $this->conn->prepare($query);

  // Clean data
  $this->mobile = htmlspecialchars(strip_tags($this->mobile));
  $this->otp = htmlspecialchars(strip_tags($this->otp));

  // Bind data
  $stmt-> bindParam(':mobile', $this->mobile);
  $stmt-> bindParam(':otp', $this->otp);

  // Execute query
  if($stmt->execute()) {
    return true;
  }

  // Print error if something goes wrong
  printf("Error: $s.\n", $stmt->error);

  return false;
  }

  // Update Users
  public function updateprofile() {
    // Create Query
    $query = 'UPDATE ' .
      $this->table . '
    SET
      Name = :name,
      StatusMsg = :statusmsg,
      Invisible = :invisible,
      Language = :language
      WHERE
      id = :uid';

  // Prepare Statement
  $stmt = $this->conn->prepare($query);

  // Clean data
  $this->name = htmlspecialchars(strip_tags($this->name));
  $this->uid = htmlspecialchars(strip_tags($this->uid));
  $this->statusmsg = htmlspecialchars(strip_tags($this->statusmsg));
  $this->invisible = htmlspecialchars(strip_tags($this->invisible));
  $this->language = htmlspecialchars(strip_tags($this->language));

  //echo "\n <br> id :".$this->uid;
  //echo "\n <br> name :".$this->name;
  //echo "\n <br> statusmsg :".$this->statusmsg;
  //echo "\n <br> invisible :".$this->invisible;

  // Bind data
  $stmt-> bindParam(':name', $this->name);
  $stmt-> bindParam(':statusmsg', $this->statusmsg);

  $stmt-> bindParam(':uid', $this->uid);
  $stmt-> bindParam(':invisible', $this->invisible);
  $stmt-> bindParam(':language', $this->language);

  // Execute query
  if($stmt->execute()) {
    return true;
  }

  
  // Print error if something goes wrong
  printf("Error: $s.\n", $stmt->error);

  return false;
  }

  // Updated User details

  public function updateuser() {
    // Create Query
    $query = 'UPDATE ' .
      $this->table . '
    SET
      Name = :name,
      Language = :language,
      Gender = :gender,
      Profession = :profession,
      Dob = :dob
      WHERE
      id = :uid';

  // Prepare Statement
  $stmt = $this->conn->prepare($query);

  // Clean data
  $this->name = htmlspecialchars(strip_tags($this->name));
  $this->uid = htmlspecialchars(strip_tags($this->uid));
  $this->language = htmlspecialchars(strip_tags($this->language));
  $this->gender = htmlspecialchars(strip_tags($this->gender));
  $this->profession = htmlspecialchars(strip_tags($this->profession));
  $this->dob = htmlspecialchars(strip_tags($this->dob));

  //echo "\n <br> id :".$this->uid;
  //echo "\n <br> name :".$this->name;
  //echo "\n <br> gender :".$this->gender;
  //echo "\n <br> dob :".$this->dob;

  // Bind data
  $stmt-> bindParam(':uid', $this->uid);
  $stmt-> bindParam(':name', $this->name);
  $stmt-> bindParam(':language', $this->language);
  $stmt-> bindParam(':gender', $this->gender);
  $stmt-> bindParam(':profession', $this->profession);
  $stmt-> bindParam(':dob', $this->dob);

  

  // Execute query
  if($stmt->execute()) {
    return true;
  }

  
  // Print error if something goes wrong
  printf("Error: $s.\n", $stmt->error);

  return false;
  }
  public function updatebususer(){

     // Create Query
     $query = 'UPDATE ' .
     $this->table . '
        SET
          Name = :name,
          Language = :language,
          Gender = :gender,
          StatusMsg = :statusmsg,
          Dob = :dob
          WHERE
          id = :uid';

      // Prepare Statement
      $stmt = $this->conn->prepare($query);

      // Clean data
      $this->name = htmlspecialchars(strip_tags($this->name));
      $this->uid = htmlspecialchars(strip_tags($this->uid));
      $this->language = htmlspecialchars(strip_tags($this->language));
      $this->gender = htmlspecialchars(strip_tags($this->gender));
      $this->statusmsg = htmlspecialchars(strip_tags($this->statusmsg));
      $this->dob = htmlspecialchars(strip_tags($this->dob));

      //echo "\n <br> id :".$this->uid;
      //echo "\n <br> name :".$this->name;
      //echo "\n <br> gender :".$this->gender;
      //echo "\n <br> dob :".$this->dob;

      // Bind data
      $stmt-> bindParam(':uid', $this->uid);
      $stmt-> bindParam(':name', $this->name);
      $stmt-> bindParam(':language', $this->language);
      $stmt-> bindParam(':gender', $this->gender);
      $stmt-> bindParam(':statusmsg', $this->statusmsg);
      $stmt-> bindParam(':dob', $this->dob);

      

      // Execute query
      if($stmt->execute()) {
        return true;
      }

      
      // Print error if something goes wrong
      printf("Error: $s.\n", $stmt->error);

      return false;
      
  }
  // Updated User fcm  details

  public function updatefcmtoken() {
    // Create Query
    $query = 'UPDATE ' .
      $this->table . '
    SET
      fcmToken = :fcmtoken,
      Device = :device
      WHERE
      id = :uid';

  // Prepare Statement
  $stmt = $this->conn->prepare($query);

  // Clean data
  $this->fcmtoken = htmlspecialchars(strip_tags($this->fcmtoken));
  $this->uid = htmlspecialchars(strip_tags($this->uid));
  $this->device = htmlspecialchars(strip_tags($this->device));
  

  //echo "\n <br> id :".$this->uid;
  //echo "\n <br> name :".$this->name;
  //echo "\n <br> gender :".$this->gender;
  //echo "\n <br> dob :".$this->dob;

  // Bind data
  $stmt-> bindParam(':uid', $this->uid);
  $stmt-> bindParam(':fcmtoken', $this->fcmtoken);
  $stmt-> bindParam(':device', $this->device);

  

  // Execute query
  if($stmt->execute()) {
    return true;
  }

  
  // Print error if something goes wrong
  printf("Error: $s.\n", $stmt->error);

  return false;
  }

  // Delete Users
  public function delete() {
    // Create query
    $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';

    // Prepare Statement
    $stmt = $this->conn->prepare($query);

    // clean data
    $this->id = htmlspecialchars(strip_tags($this->id));

    // Bind Data
    $stmt-> bindParam(':id', $this->id);

    // Execute query
    if($stmt->execute()) {
      return true;
    }

    // Print error if something goes wrong
    printf("Error: $s.\n", $stmt->error);

    return false;
    }

  // Send OTP
  public function sendotp() {
    
    include_once '../../textlocal.class.php';
    include_once '../../credential.php';

    /*
    $textlocal = new Textlocal(false, false, API_KEY);

    $numbers = array($this->mobile);

    $sender = 'TXTLCL';
    $otp = mt_rand(10000, 99999);
    $message = $otp." is your SECRET OTP to access CLOZBII";

    try {
        $result = $textlocal->sendSms($numbers, $message, $sender);
        
    } catch (Exception $e) {
       // printf("Error: $s.\n" . $e->getMessage());
      return false;
    }
    */
    $otp = "44444";

    
    // Create query
    $query = 'INSERT INTO ' .
      $this->table . '
    (PhoneId,Mobile,Otp,Name) 
    VALUES (:phoneid,:mobile,:otp,:name)
    ON DUPLICATE KEY UPDATE Otp = :otp2,PhoneId = :phoneid,Status=2';

    // Prepare Statement
    $stmt = $this->conn->prepare($query);

    // clean data
    $this->mobile = htmlspecialchars(strip_tags($this->mobile));
    $this->otp = $otp;
    $this->name = htmlspecialchars(strip_tags($this->name));

    // Bind Data
    $stmt-> bindParam(':phoneid', $this->phoneId);
    $stmt-> bindParam(':mobile', $this->mobile);
    $stmt-> bindParam(':otp', $otp);
    $stmt-> bindParam(':otp2', $otp);
    $stmt-> bindParam(':name', $this->name);

    // Execute query
    if($stmt->execute()) {
      return true;
    }

    // Print error if something goes wrong
    printf("Error: $s.\n", $stmt->error);

    return false;
  }
// Registration thru store site.
  public function registerstoreuser() {
    
    // Create query
    $query = 'INSERT INTO ' .
      $this->table . '
    (PhoneId,Mobile,Password,Name) 
    VALUES (:phoneid,:mobile,:password2,:name)
    ON DUPLICATE KEY UPDATE Password = :password,Status=2';

    //echo "\n query".$query;
    // Prepare Statement
    $stmt = $this->conn->prepare($query);

    // clean data
    $this->mobile = htmlspecialchars(strip_tags($this->mobile));
    $this->password = htmlspecialchars(strip_tags($this->password));
    $this->name = htmlspecialchars(strip_tags($this->name));

    // Bind Data
    $stmt-> bindParam(':phoneid', $this->phoneId);
    $stmt-> bindParam(':mobile', $this->mobile);
    $stmt-> bindParam(':password', $this->password);
    $stmt-> bindParam(':password2', $this->password);
    $stmt-> bindParam(':name', $this->name);

    // Execute query
    if($stmt->execute()) {
      return true;
    }

    // Print error if something goes wrong
    printf("Error: $s.\n", $stmt->error);

    return false;
  }
// Registration thru store site.
  public function adminregister() {
    
    // Create query
    $query = 'INSERT INTO Admin (Token, Name, password)
    VALUES (:phoneid,:name,:password)';

    //echo "\n query".$query;
    // Prepare Statement
    $stmt = $this->conn->prepare($query);

    // clean data
   
    $this->name = htmlspecialchars(strip_tags($this->name));
    $this->password = htmlspecialchars(strip_tags($this->password));

    // Bind Data
    $stmt-> bindParam(':phoneid', $this->phoneid);
    $stmt-> bindParam(':name', $this->name);
    $stmt-> bindParam(':password', $this->password);
    // Execute query
    if($stmt->execute()) {
      return true;
    }

    // Print error if something goes wrong
    printf("Error: $s.\n", $stmt->error);

    return false;
  }

// Verify OTP
  public function verifyotp() {
    
    // Create query
    $query = 'SELECT
        Id,
        Name,
        PhoneId,
        Mobile,
        Password,
        Refcode,
        RefCount,
        ReferredBy,
        Doj,
        Status,
        StatusMsg,
        Invisible,
        Activity,
        Otp,
        PicUrl,
        FcmToken,
        Device
       FROM
          ' . $this->table . '
       WHERE Mobile = :mobile
       AND Otp = :otp';

      //Prepare statement
      $stmt = $this->conn->prepare($query);

      // Bind ID
      $stmt->bindParam(':mobile', $this->mobile);
      $stmt->bindParam(':otp', $this->otp);

      // Execute query
      $stmt->execute();

      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      // set properties
      $this->uid = $row['Id'];
      $this->name = $row['Name'];
      $this->phoneid = $row['PhoneId'];
      $this->mobile = $row['Mobile'];
      //$this->password = $row['Password'];
      $this->refcode = $row['Refcode'];
      $this->refcount = $row['RefCount'];
      $this->referredby = $row['ReferredBy'];
      $this->doj = $row['Doj'];
      $this->status = $row['Status'];
      //$this->otp = $row['Otp'];
      $this->statusmsg = $row['StatusMsg'];
      $this->invisible = $row['Invisible'];
      $this->activity = $row['Activity'];
      $this->picurl = $row['PicUrl'];
      $this->device = $row['Device'];
      $this->fcmtoken = $row['FcmToken'];

      return $stmt;
  }

  public function verifypwd() {
    $query = 'SELECT
       t1.Id,
        t1.Name,
        t1.PhoneId,
        t1.Mobile,
        t1.Password,
        t1.Refcode,
        t1.RefCount,
        t1.ReferredBy,
        t1.Doj,
        t1.Status,
        t1.StatusMsg,
        t1.Invisible,
        t1.Activity,
        t1.Otp,
        t1.PicUrl,
        t1.FcmToken,
        t1.Device,
        t2.Accept,
		t2.Reason
       FROM
          Users t1
          LEFT JOIN DeliveryBoys t2 ON t2.UsrId = t1.Id
       WHERE t1.Mobile = :mobile
       AND  t1.Password = :password';
       
       //echo "\n ".$query;
      //Prepare statement
      $stmt = $this->conn->prepare($query);

      // Bind ID
      $stmt->bindParam(':mobile', $this->mobile);
      $stmt->bindParam(':password', $this->password);

      //echo "\n mobile".$this->mobile;
      //echo "\n password".$this->password;
      // Execute query
      $stmt->execute();

      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      // set properties
      $this->uid = $row['Id'];
      $this->name = $row['Name'];
      $this->phoneid = $row['PhoneId'];
      $this->mobile = $row['Mobile'];
      //$this->password = $row['Password'];
      $this->refcode = $row['Refcode'];
      $this->refcount = $row['RefCount'];
      $this->referredby = $row['ReferredBy'];
      $this->doj = $row['Doj'];
      $this->status = $row['Status'];
      //$this->otp = $row['Otp'];
      $this->statusmsg = $row['StatusMsg'];
      $this->invisible = $row['Invisible'];
      $this->activity = $row['Activity'];
      $this->picurl = $row['PicUrl'];
      $this->device = $row['Device'];
      $this->fcmtoken = $row['FcmToken'];
      $this->accept = $row['Accept'];
      $this->reason = $row['Reason'];

      return $stmt;
  }
  /*  This function makes a new key from a plaintext password. An
  *   encrypted password + salt is returned */
  ////////////////
 public function verifyadmin() {
    $query = 'SELECT 
	AdminId,
	Token,
	Name,
	password
       FROM
         Admin
       WHERE Name = :name
       AND password = :password';
       
       //echo "\n ".$query;
      //Prepare statement
      $stmt = $this->conn->prepare($query);

      // Bind ID
      $stmt->bindParam(':name', $this->name);
      $stmt->bindParam(':password', $this->password);

      //echo "\n mobile".$this->mobile;
      //echo "\n password".$this->password;
      // Execute query
      $stmt->execute();

      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      // set properties
      $this->adminid = $row['AdminId'];
      $this->name = $row['Name'];
      $this->password = $row['password'];
      $this->token = $row['Token'];
     
      return $stmt;
  }
  ///////////
  public function crypt_password($plain_pass){
      //create a semi random salt 
      mt_srand ((double) microtime() * 1000000);
      for($i=0;$i<10;$i++){
       $tstring   .= mt_rand();
      }

      $salt = substr(md5($tstring),0, 2);

      $passtring = $salt . $plain_pass;

      $encrypted = md5($passtring);

      return($encrypted . ":" . $salt);
  } // function crypt_password($plain_pass)

  
  public function updateprofilepic() {
    // Create Query
    $query = 'UPDATE ' .
      $this->table . '
    SET
      PicUrl = :picurl
      WHERE
      Id = :uid';

    // Prepare Statement
    $stmt = $this->conn->prepare($query);

    // Clean data
    $this->picurl = htmlspecialchars(strip_tags($this->picurl));
    $this->uid = htmlspecialchars(strip_tags($this->uid));
    
    // Bind data
    $stmt-> bindParam(':uid', $this->uid);
    $stmt-> bindParam(':picurl', $this->picurl);

    // Execute query
    if($stmt->execute()) {
      return true;
    }
   }
   
public function getvehicletype() {
    //echo "profile id from getlanguages :".$this->uid."\n";
    // Create query
    $query = 'SELECT
        delid,
        Name,
        Delcode,
        Status
       FROM
          VehicleType
       WHERE Status = 1';

      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Execute query
      $stmt->execute();

      return $stmt;
  }
   
   public function getlanguages() {
    //echo "profile id from getlanguages :".$this->uid."\n";
    // Create query
    $query = 'SELECT
        Id,
        Name,
        Code,
        Status
       FROM
          Languages
       WHERE Status = 1';

      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Execute query
      $stmt->execute();

      return $stmt;
  }
  
   public function updatefiles() {
    // Create Query
    $query = 'UPDATE
	
	DeliveryBoys
	
    SET
	
     
        IdentityCard = :identitycard
      WHERE
      UsrId=:uid';

    // Prepare Statement
    $stmt = $this->conn->prepare($query);

    // Clean data
	$this->uid = htmlspecialchars(strip_tags($this->uid));
 
    $this->identitycard = htmlspecialchars(strip_tags($this->identitycard));
    
    
    // Bind data
    $stmt-> bindParam(':uid', $this->uid);
  
    $stmt-> bindParam(':identitycard', $this->identitycard);

    // Execute query
    if($stmt->execute()) {
      return true;
    }
   }
    
       public function getname() {
    //echo "profile id from getlanguages :".$this->uid."\n";
    // Create query
    $query = 'SELECT `Id`, `Name`,PicUrl FROM `Users` WHERE Id=:uid';

      // Prepare statement
      $stmt = $this->conn->prepare($query);
	  
	  
		$stmt-> bindParam(':uid', $this->uid);
      // Execute query
      $stmt->execute();
	   $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // set properties
        $this->uid = $row['Id'];
        $this->nameu = $row['Name'];
        $this->picurl2 = $row['PicUrl'];
      

      return $stmt;
  }
  public function adddocment(){
      // Create Query
      //echo "Inside addcategory \n";

      $query = 'INSERT INTO DeliveryBoys ( UsrId, Address, UPI,  Dob, BankAccountNumber, BankName, AccountName, IfscCode, Status, Accept,Delcode)
      VALUES (:usrid,:address,:upi,:dob,:bankaccountnumber,:bankname,:accountname,:ifsccode,:status,:accept, :delcode)';
   
      // Prepare Statement
      $stmt = $this->conn->prepare($query);

      // Clean data
      $this->usrid = htmlspecialchars(strip_tags($this->usrid));
      $this->address = htmlspecialchars(strip_tags($this->address));
      $this->upi = htmlspecialchars(strip_tags($this->upi));
    //  $this->drivinglicense = htmlspecialchars(strip_tags($this->drivinglicense));
     // $this->identitycard = htmlspecialchars(strip_tags($this->identitycard));
      $this->dob = htmlspecialchars(strip_tags($this->dob));
      $this->bankaccountnumber = htmlspecialchars(strip_tags($this->bankaccountnumber));
      $this->bankname = htmlspecialchars(strip_tags($this->bankname));
      $this->accountname = htmlspecialchars(strip_tags($this->accountname));
      $this->ifsccode = htmlspecialchars(strip_tags($this->ifsccode));
      $this->status = '0';
      $this->accept = '0';
	  $this->delcode = htmlspecialchars(strip_tags($this->delcode));
     
     
      
      // Bind data
      $stmt-> bindParam(':usrid', $this->usrid);
      $stmt-> bindParam(':address', $this->address);
      $stmt-> bindParam(':upi', $this->upi);
   //   $stmt-> bindParam(':drivinglicense', $this->drivinglicense);
    //  $stmt-> bindParam(':identitycard', $this->identitycard);
      $stmt-> bindParam(':dob', $this->dob);
      $stmt-> bindParam(':bankaccountnumber', $this->bankaccountnumber);
      $stmt-> bindParam(':bankname', $this->bankname);
      $stmt-> bindParam(':accountname', $this->accountname);
      $stmt-> bindParam(':ifsccode', $this->ifsccode);
      $stmt-> bindParam(':status', $this->status);
      $stmt-> bindParam(':accept', $this->accept);
      $stmt-> bindParam(':delcode', $this->delcode);
     
      //echo "\n address:".$this->address;
      //echo "\n phoneid:".$this->phoneid;


      if($stmt->execute()) {
        return true;
      } else {

        printf("Error: $s.\n", $stmt->error);
        return false;
      }
  

    }
	 public function resubmitdocment(){
      // Create Query
      //echo "Inside addcategory \n";

      $query = 'UPDATE  DeliveryBoys 
					SET
					UsrId = :usrid,
					Address = :address,
					UPI = :upi,
					Dob = :dob,
					BankAccountNumber = :bankaccountnumber,
					BankName = :bankname,
					AccountName = :accountname,
					IfscCode = :ifsccode,
					Status = :status,
					Accept = :accept,
					Delcode = :delcode
					WHERE
					 UsrId = :usrid';
   
      // Prepare Statement
      $stmt = $this->conn->prepare($query);

      // Clean data
      $this->usrid = htmlspecialchars(strip_tags($this->usrid));
      $this->address = htmlspecialchars(strip_tags($this->address));
      $this->upi = htmlspecialchars(strip_tags($this->upi));
    //  $this->drivinglicense = htmlspecialchars(strip_tags($this->drivinglicense));
     // $this->identitycard = htmlspecialchars(strip_tags($this->identitycard));
      $this->dob = htmlspecialchars(strip_tags($this->dob));
      $this->bankaccountnumber = htmlspecialchars(strip_tags($this->bankaccountnumber));
      $this->bankname = htmlspecialchars(strip_tags($this->bankname));
      $this->accountname = htmlspecialchars(strip_tags($this->accountname));
      $this->ifsccode = htmlspecialchars(strip_tags($this->ifsccode));
      $this->status = '0';
      $this->accept = htmlspecialchars(strip_tags($this->accept));
	  $this->delcode = htmlspecialchars(strip_tags($this->delcode));
     
     
      
      // Bind data
      $stmt-> bindParam(':usrid', $this->usrid);
      $stmt-> bindParam(':address', $this->address);
      $stmt-> bindParam(':upi', $this->upi);
   //   $stmt-> bindParam(':drivinglicense', $this->drivinglicense);
    //  $stmt-> bindParam(':identitycard', $this->identitycard);
      $stmt-> bindParam(':dob', $this->dob);
      $stmt-> bindParam(':bankaccountnumber', $this->bankaccountnumber);
      $stmt-> bindParam(':bankname', $this->bankname);
      $stmt-> bindParam(':accountname', $this->accountname);
      $stmt-> bindParam(':ifsccode', $this->ifsccode);
      $stmt-> bindParam(':status', $this->status);
      $stmt-> bindParam(':accept', $this->accept);
      $stmt-> bindParam(':delcode', $this->delcode);
     
      //echo "\n address:".$this->address;
      //echo "\n phoneid:".$this->phoneid;


      if($stmt->execute()) {
        return true;
      } else {

        printf("Error: $s.\n", $stmt->error);
        return false;
      }
  

    }
public function delvrystatus() {
      // Create Query
      $query = 'UPDATE 
        DeliveryBoys
      SET
        Status = :status
        WHERE
        UsrId = :uid';

      // Prepare Statement
      $stmt = $this->conn->prepare($query);

      // Clean data
      $this->status = htmlspecialchars(strip_tags($this->status));
      $this->uid = htmlspecialchars(strip_tags($this->uid));
      
      // Bind data
      $stmt-> bindParam(':uid', $this->uid);
      $stmt-> bindParam(':status', $this->status);

      // Execute query
      if($stmt->execute()) {
        return true;
      }
    }
     public function getidcard() {
    //echo "profile id from getlanguages :".$this->uid."\n";
    // Create query
    $query = 'SELECT Name,PicUrl,Dob,Mobile FROM `Users` WHERE Id=:uid';

      // Prepare statement
      $stmt = $this->conn->prepare($query);
	  
	  
		$stmt-> bindParam(':uid', $this->uid);
      // Execute query
      $stmt->execute();
	   $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // set properties
        
        $this->delname = $row['Name'];
        $this->idpic = $row['PicUrl'];
        $this->dobb = $row['Dob'];
        $this->mobi = $row['Mobile'];
      

      return $stmt;
  }
  	 
}
