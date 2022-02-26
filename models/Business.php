<?php
  class Business {
    // DB Stuff
    private $conn;
    private $table = 'Business';

    // Properties
    public $busid;
    public $name;
    public $busname;
    public $busdesc;
    public $usrid;
    public $address;
    public $lat;
    public $lon;
    public $phoneid;
    public $picurl;
    public $searchtags;
    public $about;

    // Constructor with DB
    public function __construct($db) {
      $this->conn = $db;
    }

    // Get Neighborhoods by pincode
    public function read() {
      // Create query
      $query = 'SELECT
        BusId,
        Name,
        BusDesc,
        UsrId,
        Address,
        PicUrl,
        PhoneId
      FROM
        ' . $this->table . '
      WHERE pincode = ?';

      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Bind ID
      $stmt->bindParam(1, $this->pincode);

      // Execute query
      $stmt->execute();

      return $stmt;
    }

     // Get Neighborhoods by pincode
    public function listnbrpin() {
      // Create query
      $query = 'SELECT
        BusId,
        Name,
        BusDesc,
        UsrId,
        Address,
        PicUrl,
        PhoneId
      FROM
        ' . $this->table . '
      WHERE pincode = ?';

      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Bind ID
      $stmt->bindParam(1, $this->pincode);

      // Execute query
      $stmt->execute();

      return $stmt;
    }

         // Get businesses in the Neighborhood by Nid
    public function busslistneigh() {
      // Create query
      $query = 'SELECT
        BusId,
        Name,
        BusDesc,
        UsrId,
        Address,
        PicUrl,
        PhoneId
      FROM
        ' . $this->table . '
      WHERE pincode = ?';

      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Bind ID
      $stmt->bindParam(1, $this->pincode);

      // Execute query
      $stmt->execute();

      return $stmt;
    }


    // Get Neighborhoods by latitude & longitude
    public function latlon() {
      // Create query
      $query = 'SELECT
        BusId,
        Name,
        BusDesc,
        UsrId,
        Address,
        PicUrl,
        PhoneId,
        SQRT(POW(69.1 * (lat - :lat), 2) + POW(69.1 * ((:lon - lon) * COS(lat / 57.3)), 2)) AS distance
      FROM
        ' . $this->table . '  
      ORDER BY distance limit 100';


      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Bind ID
     
      $stmt-> bindParam(':lat', $this->lat);
      $stmt-> bindParam(':lon', $this->lon);

      // Execute query
      $stmt->execute();
      return $stmt;

    }

    // Get Single Users
    public function businessdetails(){
      // Create query
      $query = 'SELECT
        BusId,
        Name,
        BusDesc,
        UsrId,
        Address,
        PicUrl,
        PhoneId,
        Lat,
        Lon
         FROM
            ' . $this->table . '
        WHERE BusId = ?
        LIMIT 0,1';

        //Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(1, $this->busid);

        // Execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // set properties
        $this->busid = $row['BusId'];
        $this->name = $row['Name'];
        $this->busdesc = $row['BusDesc'];
        $this->usrid = $row['UsrId'];
        $this->address = $row['Address'];
        $this->picurl = $row['PicUrl'];
        $this->phoneid = $row['PhoneId'];
        $this->lat = $row['Lat'];
        $this->lon = $row['Lon'];
    }

    public function bussdelvry(){
      // Create query
      $query = 'SELECT
        BussDelvryId,
        DelvryId
         FROM
            BussDelvry
        WHERE BusId = ?
        LIMIT 0,1';

        //Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(1, $this->busid);
        

        // Execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // set properties
        //$this->busid = $row['BusId'];
        $this->delvryid = $row['DelvryId'];
        $this->bussdelvryid = $row['BussDelvryId'];
    }

    public function isFollowingBuss(){
    // Create query
      $query = 'SELECT count(1) cnt FROM
          Following
      WHERE FromUsrId = :fromusrid 
      AND BusId = :busid';

      //Prepare statement
      $stmt = $this->conn->prepare($query);

      // Bind ID
      $stmt->bindParam(':fromusrid', $this->fromusrid);
      $stmt->bindParam(':busid', $this->busid);

      //echo "FromUsrId".$this->fromusrid."\n";
      //echo "busid".$this->busid."\n";
      // Execute query
      $stmt->execute();

      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      // set properties
      $this->cnt = $row['cnt']; 
      //echo "\n Follow".$this->cnt;

    }

     // Add to favs
    public function addfavnbr(){
      // Create Query
      $query = 'INSERT IGNORE INTO UsrNeigh  SET UsrId = :uid,Nid    = :nid';
      
      // Prepare Statement
      $stmt = $this->conn->prepare($query);

      // Clean data
      //$this->uid = htmlspecialchars(strip_tags($this->uid));
      //$this->nid = htmlspecialchars(strip_tags($this->nid));

      // Bind data
      $stmt-> bindParam(':uid', $this->uid);
      $stmt-> bindParam(':nid', $this->nid);

      // Execute query
      if($stmt->execute()){
        return true;
      }

      // Print error if something goes wrong
      printf("Error: $s.\n", $stmt->error);

      return false;
    
     }

    public function valtoken() {
      
      // Create query
      $query = 'SELECT
          Id,
          PhoneId
         FROM
            Users
         WHERE Id = :uid
         AND PhoneId = :token';

        //Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(':uid', $this->uid);
        $stmt->bindParam(':token', $this->token);

        // Execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        //echo "\n uid :".$this->uid;
        //echo "\n token :".$this->token;

        // set properties
        $this->id = $row['Id'];
        $this->phoneId = $row['PhoneId'];

        return $stmt;
    }


    public function gettimings(){
       // Create query
        $query = 'SELECT
          BussTimingsId,
          BusId,
          WeekDay,
          StartTime,
          EndTime
        FROM
          BussTimings
        WHERE BusId =:busid order by BussTimingsId';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(':busid', $this->busid);

        // Execute query
        $stmt->execute();
        
        return $stmt;

    }
    
    // select Id,Name from Users where Id in (select UsrId from UsrNeigh where Nid=154818);
    // Get Neighborhood followers
    public function getnbrfollowers(){

      //echo "from getnbrfollowers\n"; 
      // Create query
      $query = 'SELECT
        Id,Name,StatusMsg,Status,Invisible,PicUrl,Activity 
      FROM
        Users
      WHERE
        Id in (select UsrId from UsrNeigh where Nid = :nid) and Invisible !=1';

      //echo "\n query : ".$query;
      
      
      // Prepare statement
      $stmt = $this->conn->prepare($query);
      //$this->touserid = htmlspecialchars(strip_tags($this->touserid));

      $stmt-> bindParam(':nid', $this->nid);
      //$stmt-> bindParam(':uid', $this->uid);
      
      //echo "\n nid : ".$this->nid;
      
      // Execute query
      $stmt->execute();

      return $stmt;
    }

    public function addbusiness(){
      // Create Query
      //echo "Inside addbusiness\n";

      $query = 'INSERT INTO ' .$this->table . ' (BusTypeId, Name,BusDesc,UsrId,Address,PicUrl,PhoneId,Lat,Lon)
      VALUES (:bustypeid,:busname,:about,:uid,:address,:picurl,:phoneid,:lat,:lon)';
   
      // Prepare Statement
      $stmt = $this->conn->prepare($query);

      // Clean data
      $this->busname = htmlspecialchars(strip_tags($this->busname));
      $this->bustypeid = htmlspecialchars(strip_tags($this->bustypeid));
      $this->about = htmlspecialchars(strip_tags($this->about));
      $this->uid = htmlspecialchars(strip_tags($this->uid));
      $this->address = htmlspecialchars(strip_tags($this->address));
      $this->phoneid = htmlspecialchars(strip_tags($this->phoneid));
      //$this->url = htmlspecialchars(strip_tags($this->url));
     // $this->searchtags = htmlspecialchars(strip_tags($this->searchtags));
      $this->picurl = htmlspecialchars(strip_tags($this->picurl));
      
      $this->lat = htmlspecialchars(strip_tags($this->lat));
      $this->lon = htmlspecialchars(strip_tags($this->lon));
      
      // Bind data
      $stmt-> bindParam(':busname', $this->busname);
      $stmt-> bindParam(':bustypeid', $this->bustypeid);
      $stmt-> bindParam(':about', $this->about);
      $stmt-> bindParam(':uid', $this->uid);
      $stmt-> bindParam(':address', $this->address);  
      $stmt-> bindParam(':phoneid', $this->phoneid);
      //$stmt-> bindParam(':url', $this->url);
      //$stmt-> bindParam(':searchtags', $this->searchtags);
      $stmt-> bindParam(':picurl', $this->picurl);
      $stmt-> bindParam(':lat', $this->lat);
      $stmt-> bindParam(':lon', $this->lon);
      
      //echo "\n address:".$this->address;
      //echo "\n phoneid:".$this->phoneid;


      if($stmt->execute()) {
        $this->busid = $this->conn->lastInsertId();
        //echo "Inside addbusiness 5\n";
        return true;
      }
  
      //echo "Inside addbusiness 5\n";
        return true;
      // Print error if something goes wrong
      printf("Error: $s.\n", $stmt->error);
      
      return false;

    }
	public function addproducts(){
      // Create Query
      //echo "Inside addbusiness\n";

      $query = 'INSERT INTO products (BusId, CoverImage,ProductName,OriginalPrice,SellPrice,CategoryId,Weight,Tax,Description)
      VALUES (:busId,:coverimage,:productname,:originalprice,:sellprice,:categoryid,:weight,:tax,:description)';
   
      // Prepare Statement
      $stmt = $this->conn->prepare($query);

      // Clean data
      $this->busId = htmlspecialchars(strip_tags($this->busId));
      $this->coverimage = htmlspecialchars(strip_tags($this->coverimage));
      $this->productname = htmlspecialchars(strip_tags($this->productname));
      $this->originalprice = htmlspecialchars(strip_tags($this->originalprice));
      $this->sellprice = htmlspecialchars(strip_tags($this->sellprice));
      $this->categoryid = htmlspecialchars(strip_tags($this->categoryid));
      //$this->url = htmlspecialchars(strip_tags($this->url));
     // $this->searchtags = htmlspecialchars(strip_tags($this->searchtags));
      $this->weight = htmlspecialchars(strip_tags($this->weight));
      
      $this->tax = htmlspecialchars(strip_tags($this->tax));
      $this->description = htmlspecialchars(strip_tags($this->description));
      
      // Bind data
      $stmt-> bindParam(':busId', $this->busId);
      $stmt-> bindParam(':coverimage', $this->coverimage);
      $stmt-> bindParam(':productname', $this->productname);
      $stmt-> bindParam(':originalprice', $this->originalprice);
      $stmt-> bindParam(':sellprice', $this->sellprice);  
      $stmt-> bindParam(':categoryid', $this->categoryid);
      //$stmt-> bindParam(':url', $this->url);
      //$stmt-> bindParam(':searchtags', $this->searchtags);
      $stmt-> bindParam(':weight', $this->weight);
      $stmt-> bindParam(':tax', $this->tax);
      $stmt-> bindParam(':description', $this->description);
      
      //echo "\n address:".$this->address;
      //echo "\n phoneid:".$this->phoneid;


      if($stmt->execute()) {
		//$this->busid = $this->conn->lastInsertId();
        return true;
      }
  
      //echo "Inside addbusiness 5\n";
        return true;
      // Print error if something goes wrong
      printf("Error: $s.\n", $stmt->error);
      
      return false;

    }
	
	public function addcategory(){
      // Create Query
      //echo "Inside addbusiness\n";

      $query = 'INSERT INTO category (CatName, BusTypeId,Picurl,Status,LangCode)
      VALUES (:catname,:bustypeid,:picurl,:status,:langcode)';
   
      // Prepare Statement
      $stmt = $this->conn->prepare($query);

      // Clean data
      $this->catname = htmlspecialchars(strip_tags($this->catname));
      $this->bustypeid = htmlspecialchars(strip_tags($this->bustypeid));
      $this->picurl = htmlspecialchars(strip_tags($this->picurl));
      $this->status = htmlspecialchars(strip_tags($this->status));
      $this->langcode = htmlspecialchars(strip_tags($this->langcode));
     
      
      // Bind data
      $stmt-> bindParam(':catname', $this->catname);
      $stmt-> bindParam(':bustypeid', $this->bustypeid);
      $stmt-> bindParam(':picurl', $this->picurl);
      $stmt-> bindParam(':status', $this->status);
      $stmt-> bindParam(':langcode', $this->langcode);  
      
      
      //echo "\n address:".$this->address;
      //echo "\n phoneid:".$this->phoneid;


      if($stmt->execute()) {
		//$this->busid = $this->conn->lastInsertId();
        return true;
      }
  
      //echo "Inside addbusiness 5\n";
        return true;
      // Print error if something goes wrong
      printf("Error: $s.\n", $stmt->error);
      
      return false;

    }
	
	public function addsubcategory(){
      // Create Query
      //echo "Inside addbusiness\n";

      $query = 'INSERT INTO subcategory (SubCatName, PicUrl,CatId,Status,LangCode)
      VALUES (:subcatname,:picUrl,:catid,:status,:langcode)';
   
      // Prepare Statement
      $stmt = $this->conn->prepare($query);

      // Clean data
      $this->subcatname = htmlspecialchars(strip_tags($this->subcatname));
      $this->picUrl = htmlspecialchars(strip_tags($this->picUrl));
      $this->catid = htmlspecialchars(strip_tags($this->catid));
      $this->status = htmlspecialchars(strip_tags($this->status));
      $this->langcode = htmlspecialchars(strip_tags($this->langcode));
     
      
      // Bind data
      $stmt-> bindParam(':subcatname', $this->subcatname);
      $stmt-> bindParam(':picUrl', $this->picUrl);
      $stmt-> bindParam(':catid', $this->catid);
      $stmt-> bindParam(':status', $this->status);
      $stmt-> bindParam(':langcode', $this->langcode);  
      
      
      //echo "\n address:".$this->address;
      //echo "\n phoneid:".$this->phoneid;


      if($stmt->execute()) {
		//$this->busid = $this->conn->lastInsertId();
        return true;
      }
  
      //echo "Inside addbusiness 5\n";
        return true;
      // Print error if something goes wrong
      printf("Error: $s.\n", $stmt->error);
      
      return false;

    }


    public function addbusdelivery(){
      // Create Query
      //echo "Inside addbusdelivery\n";
      

      $query = 'REPLACE INTO BussDelvry (BusId,DelvryId)
      VALUES (:busid,:homedel)';

      //$query = 'INSERT INTO ' .$this->table . ' (Name,BusDesc,) VALUES (:busname,:about)';

      // Prepare Statement
      $stmt = $this->conn->prepare($query);

      // Clean data
      $this->busid = htmlspecialchars(strip_tags($this->busid));
      $this->homedel = htmlspecialchars(strip_tags($this->homedel));

      
      // Bind data
      $stmt-> bindParam(':busid', $this->busid);
      $stmt-> bindParam(':homedel', $this->homedel);
      
      //echo "Homedel :".$this->homedel."\n";
      //echo "busid :".$this->busid."\n";

      if($stmt->execute()) {
        return true;
      }
  
      // Print error if something goes wrong
      printf("Error: $s.\n", $stmt->error);
      
      return false;

    }

    public function addbustimings(){
      // Create Query
      //echo "Inside addbustimings\n";

      $query = 'REPLACE INTO BussTimings (BusId,WeekDay,StartTime,EndTime)
      VALUES (:busid,:weekday,:starttime,:endtime)';

      //echo "Query [".$query."]\n";

      // Prepare Statement
      $stmt = $this->conn->prepare($query);

      // Clean data
      $this->busid = htmlspecialchars(strip_tags($this->busid));
      $this->weekday = htmlspecialchars(strip_tags($this->weekday));
      $this->starttime = htmlspecialchars(strip_tags($this->starttime));
      $this->endtime = htmlspecialchars(strip_tags($this->endtime));

      
      // Bind data
      $stmt-> bindParam(':busid', $this->busid);
      $stmt-> bindParam(':weekday', $this->weekday);
      $stmt-> bindParam(':starttime', $this->starttime);
      $stmt-> bindParam(':endtime', $this->endtime);
      
      /*
      echo "busid :".$this->busid."\n";
      echo "weekday :".$this->weekday."\n";
      echo "starttime :".$this->starttime."\n";
      echo "endtime :".$this->endtime."\n";
      */
      if($stmt->execute()) {
        //echo "About to return true\n";
        return true;
      }
  
      // Print error if something goes wrong
      printf("Error: $s.\n", $stmt->error);
      
      return false;

    }
    
    public function addbuspayments(){
      // Create Query
      //echo "Inside addbuspayments\n";

      $query = 'REPLACE INTO BussPymts (BusId,PmtId,PmtPhone)
      VALUES (:busid,:pmtid,:phoneno)';

      //$query = 'INSERT INTO ' .$this->table . ' (Name,BusDesc,) VALUES (:busname,:about)';
      //echo "Query [".$query."]\n";

      // Prepare Statement
      $stmt = $this->conn->prepare($query);

      // Clean data
      $this->busid = htmlspecialchars(strip_tags($this->busid));
      $this->pmtid = htmlspecialchars(strip_tags($this->pmtid));
      $this->phoneno = htmlspecialchars(strip_tags($this->phoneno));

      
      // Bind data
      $stmt-> bindParam(':busid', $this->busid);
      $stmt-> bindParam(':pmtid', $this->pmtid);
      $stmt-> bindParam(':phoneno', $this->phoneno);

      /*
      echo "busid :".$this->busid."\n";
      echo "pmtid :".$this->pmtid."\n";
      echo "phoneno :".$this->phoneno."\n";
      */

      if($stmt->execute()) {
        //echo "About to return true\n";
        return true;
      }
  
      // Print error if something goes wrong
      printf("Error: $s.\n", $stmt->error);
      
      return false;

    }
    public function updatebusspic() {
      // Create Query
      $query = 'UPDATE ' .
        $this->table . '
      SET
        PicUrl = :picurl
        WHERE
        BusId = :busid';

      // Prepare Statement
      $stmt = $this->conn->prepare($query);

      // Clean data
      $this->picurl = htmlspecialchars(strip_tags($this->picurl));
      $this->busid = htmlspecialchars(strip_tags($this->busid));
      
      // Bind data
      $stmt-> bindParam(':busid', $this->busid);
      $stmt-> bindParam(':picurl', $this->picurl);

      // Execute query
      if($stmt->execute()) {
        return true;
      }
    }
    public function updatebusiness(){
      $query = 'UPDATE ' .
      $this->table . '
      SET
        Name = :name, BusDesc = :about, Address = :address
        WHERE
        BusId = :busid';

      // Prepare Statement
      $stmt = $this->conn->prepare($query);

      // Clean data
      $this->name = htmlspecialchars(strip_tags($this->busname));
      $this->busid = htmlspecialchars(strip_tags($this->busid));
      $this->about = htmlspecialchars(strip_tags($this->about));
      $this->address = htmlspecialchars(strip_tags($this->address));
      
      // Bind data
      $stmt-> bindParam(':busid', $this->busid);
      $stmt-> bindParam(':name', $this->busname);
      $stmt-> bindParam(':about', $this->about);
      $stmt-> bindParam(':address', $this->address);

      // Execute query
      if($stmt->execute()) {
        return true;
      }

    }

    public function getpayments(){
       // Create query
        $query = 'select Name,LogoUrl,PmtPhone,BusId from PaymentTypes a, BussPymts b where a.PmtId = b.PmtId and b.BusId=:busid';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(':busid', $this->busid);

        // Execute query
        $stmt->execute();
        
        return $stmt;

    }

    public function getproducts(){
       // Create query
        $query = 'select ProdId,ProductName,Description from Products where BusId=:busid';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(':busid', $this->busid);

        // Execute query
        $stmt->execute();
        
        return $stmt;

    }
    public function busstypes() {
      // Create query
      $query = 'SELECT
        BusTypeId,
        Name1,
        Name2,
        Status,
        Doc,
        Dou,
        IconUrl
      FROM
         BusinessTypes 
      WHERE Status = 0 order by Name1,BusTypeId';  #fetch only active groups for the user

      // Prepare statement
      $stmt = $this->conn->prepare($query);
      
      // Execute query
      $stmt->execute();

      return $stmt;
    }
    //globalsearch
    public function searchbusiness() {
      // Create query
      $query = 'SELECT
        BusId,
        Name,
        BusDesc,
        UsrId,
        Address,
        PicUrl,
        PhoneId,
        SQRT(POW(69.1 * (lat - ?), 2) + POW(69.1 * ((? - lon) * COS(lat / 57.3)), 2)) AS distance
      FROM
        ' . $this->table . '  
      where Name like CONCAT("%",?,"%") OR PhoneId = ? ORDER BY distance limit 100';


      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Bind ID
     
      $stmt-> bindParam(1, $this->lat);
      $stmt-> bindParam(2, $this->lon);
      $stmt-> bindParam(3, $this->searchstr);
      $stmt-> bindParam(4, $this->searchstr);
      

      // Execute query
      $stmt->execute();
      return $stmt;

    }

    public function searchproducts() {
      // Create query
      $query = 'SELECT
        a.BusId BusId,
        a.Name Name,
        BusDesc,
        UsrId,
        Address,
        a.PicUrl PicUrl,
        PhoneId,
        SQRT(POW(69.1 * (lat - :lat), 2) + POW(69.1 * ((:lon - lon) * COS(lat / 57.3)), 2)) AS distance
      FROM
        ' . $this->table . '  a, Products b 
      where b.Name like CONCAT("%",:searchstr1,"%") and a.BusId=b.BusId ORDER BY distance limit 100';
      


      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Bind ID
     
      $stmt-> bindParam(':searchstr1', $this->searchstr);
      $stmt-> bindParam(':lon', $this->lon);
      $stmt-> bindParam(':lat', $this->lat);
      // Execute query
      //$stmt->execute();

      // Execute query
      $stmt->execute();
      return $stmt;

    }
    public function getpaymenttypes(){
      //echo "profile id from getlanguages :".$this->uid."\n";
      // Create query
      $query = 'SELECT
          PmtId,
          Name,
          LogoUrl
        FROM
        PaymentTypes
        WHERE Status = 1';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        $stmt->execute();

        return $stmt;
    }

    public function delbusstimings() {
      // Create query
      $query = 'DELETE FROM BussTimings WHERE BussTimingsId = :id';
  
      // Prepare Statement
      $stmt = $this->conn->prepare($query);
  
      // clean data
      $this->bustimingsid = htmlspecialchars(strip_tags($this->bustimingsid));
      
      // Bind Data
      $stmt-> bindParam(':id', $this->bustimingsid);

      //echo "\n bustimingsid:".$this->bustimingsid;
      // Execute query
      if($stmt->execute()) {
        return true;
      }
  
      // Print error if something goes wrong
      printf("Error: $s.\n", $stmt->error);
  
      return false;
      }

}