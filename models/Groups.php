<?php

  # 
  # creategroup
  # getgroupmembers
  # getusrgroups
  # isadmin
  # addgroupadmin
  # addgroupmem
  # read_singlegroup
  # updategroup

  # deletegroup
  # deletegroupmembers
  # 

  class Groups {
    // DB Stuff
    private $conn;
    private $table = 'Groups';

    // Properties
    public $GroupId;
    public $Name;
    public $Description;
    public $State;
    public $Status;
    public $Admin;
    public $UsrId;
    public $Doc;
    public $Dou;

    // Constructor with DB
    public function __construct($db) {
      $this->conn = $db;
    }
   
   public function getgroupinfo() {
      // Create query
      $query = 'SELECT
        GroupId,
        Name,
        Description,
        State,
        Status,
        Doc,
        Dou,
        PicUrl
      FROM
        ' . $this->table. ' 
      WHERE GroupId  =:groupid';  #fetch only active groups for the user

      // Prepare statement
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':groupid', $this->groupid);
      //echo "From getgroupinfo:".$this->groupid."\n"; 
      // Execute query
      $stmt->execute();

      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      // set properties
      $this->groupid = $row['GroupId'];
      $this->name = $row['Name'];
      $this->description = $row['Description'];
      $this->state = $row['State'];
      $this->status = $row['Status'];
      $this->doc = $row['Doc'];
      $this->dou = $row['Dou'];
      $this->picurl = $row['PicUrl'];
      //return $stmt;
    }




   // Get get users groups
    public function getusrgroups() {
      // Create query
      $query = 'SELECT
        GroupId,
        Name,
        Description,
        State,
        Status,
        Doc,
        Dou,
        PicUrl
      FROM
        ' . $this->table. ' 
      WHERE Status = 1 
      and   GroupId in (SELECT GroupId FROM UsrGroups where UsrId =:uid) order by Doc desc';  #fetch only active groups for the user

      // Prepare statement
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':uid', $this->uid);
      // Execute query
      $stmt->execute();

      return $stmt;
    }

    // Is usr Admin?
    public function isadmin(){
    // Create query
    $query = 'SELECT
        Admin
       FROM
          UsrGroups
      WHERE GroupId = :groupid 
      AND UsrId = :uid';

      //Prepare statement
      $stmt = $this->conn->prepare($query);

      // Bind ID
      $stmt->bindParam(':groupid', $this->groupid);
      $stmt->bindParam(':uid', $this->uid);

      // Execute query
      $stmt->execute();

      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      // set properties
      $this->admin = $row['Admin'];
      //echo "\n admin from isadmin".$this->admin;

    }


    // Get Single Groups
    public function read_singlegroup(){
    // Create query
      $query = 'SELECT
          GroupId,
          Name,
          Description,
          State,
          Status,
          Doc,
          Dou,
          PicUrl
         FROM
            ' . $this->table . '
        WHERE GroupId = ?
        LIMIT 0,1';

        //Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(1, $this->groupid);
        echo "\n GroupId :",$this->groupid;
        // Execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // set properties
        $this->groupid = $row['GroupId'];
        $this->name = $row['Name'];
        $this->description = $row['Description'];
        $this->state = $row['State'];
        $this->status = $row['Status'];
        $this->doc = $row['Doc'];
        $this->dou = $row['Dou'];
        $this->picurl = $row['PicUrl'];

    }

  // Create Groups
  public function creategroup() {


    // Create Query
    $query = 'INSERT INTO ' .
      $this->table . ' (Name,Description,State,Status,PicUrl)
    VALUES (:name,:description,:state,:status,:picurl)';



    // Prepare Statement
    $stmt = $this->conn->prepare($query);

    // Clean data
    $this->name = htmlspecialchars(strip_tags($this->name));
    $this->description = htmlspecialchars(strip_tags($this->description));
    $this->state = htmlspecialchars(strip_tags($this->state));
    $this->status = htmlspecialchars(strip_tags($this->status));
    $this->picurl = htmlspecialchars(strip_tags($this->picurl));

    // Bind data
    $stmt-> bindParam(':name', $this->name);
    $stmt-> bindParam(':description', $this->description);
    $stmt-> bindParam(':state', $this->state);
    $stmt-> bindParam(':status', $this->status);
    $stmt-> bindParam(':picurl', $this->picurl);
    // Execute query
    if($stmt->execute()) {
      $this->groupid = $this->conn->lastInsertId();
      return true;
    }
    
    // Print error if something goes wrong
    printf("Error: $s.\n", $stmt->error);
    
    return false;
  }

  // Add Group Admin
  public function addgroupadmin() {
    // Create Query
    $query = 'INSERT INTO UsrGroups (UsrId,GroupId,Admin)
    VALUES (:uid,:groupid,:admin)';

    // Prepare Statement
    $stmt = $this->conn->prepare($query);

    // Clean data
    $this->uid = htmlspecialchars(strip_tags($this->uid));
    $this->groupid = htmlspecialchars(strip_tags($this->groupid));
    $this->admin = htmlspecialchars(strip_tags($this->admin));
    $this->admin =0;
    // Bind data
    $stmt-> bindParam(':uid', $this->uid);
    $stmt-> bindParam(':groupid', $this->groupid);
    $stmt-> bindParam(':admin', $this->admin);

    // Execute query
    if($stmt->execute()) {
      return true;
    }

    // Print error if something goes wrong
    printf("Error: $s.\n", $stmt->error);

    return false;
  }
  
    // Check if mobile registered in Clozbii
  public function userexists() {
    // Create Query
    $query = 'SELECT Id,Name,Mobile,PicUrl,Device,FcmToken from Users 
    WHERE  Mobile = :mobile';

    // Prepare Statement
    $stmt = $this->conn->prepare($query);

    // Clean data
    $this->mobile = htmlspecialchars(strip_tags($this->mobile));
    // Bind data
    $stmt-> bindParam(':mobile', $this->mobile);


    //echo "form userexists: mobile:".$this->mobile;
    // Execute query
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

      // set properties
    $this->uid = $row['Id'];
    $this->name = $row['Name'];
    $this->mobile = $row['Mobile'];
    $this->picurl = $row['PicUrl'];
    $this->device = $row['Device'];
    $this->fcmtoken = $row['FcmToken'];

    return $stmt;
  }

// Add Group Admin
  public function addgroupmem() {
    //echo "Inside addgroupmem";
    // Create Query
    $query = 'INSERT IGNORE INTO UsrGroups (UsrId,GroupId,Admin)
    VALUES (:memid,:groupid,:admin)';

    // Prepare Statement
    $stmt = $this->conn->prepare($query);

    // Clean data
    $this->memid = htmlspecialchars(strip_tags($this->memid));
    $this->groupid = htmlspecialchars(strip_tags($this->groupid));
    $this->admin = htmlspecialchars(strip_tags($this->admin));
    //$this->admin =1;
    // Bind data
    $this->memid=str_replace('"', '', $this->memid);

    $stmt-> bindParam(':memid', $this->memid);
    $stmt-> bindParam(':groupid', $this->groupid);
    $stmt-> bindParam(':admin', $this->admin);


    //echo "\n memid:[".$this->memid."] groupid:[".$this->groupid."] isAdmin:".$this->admin;
    // Execute query
    if($stmt->execute()) {
      return true;
    }

    // Print error if something goes wrong
    printf("Error: $s.\n", $stmt->error);

    return false;
  }

  // Update Groups
  public function updategroup() {
    // Create Query
    //echo "from updategroup\n";
    $query = 'UPDATE ' .$this->table . '
      SET
        Name = :name,
        Description = :description,
        State = :state,
        Status = :status,
        PicUrl = :picurl
        WHERE
        GroupId = :groupid';

      // Prepare Statement
      $stmt = $this->conn->prepare($query);

      // Clean data
      $this->name = htmlspecialchars(strip_tags($this->name));
      $this->description = htmlspecialchars(strip_tags($this->description));
      $this->state = htmlspecialchars(strip_tags($this->state));
      $this->status = htmlspecialchars(strip_tags($this->status));
      $this->picurl = htmlspecialchars(strip_tags($this->picurl));
      $this->groupid = htmlspecialchars(strip_tags($this->groupid));

      // Bind data
      $stmt-> bindParam(':name', $this->name);
      $stmt-> bindParam(':description', $this->description);
      $stmt-> bindParam(':state', $this->state);
      $stmt-> bindParam(':status', $this->status);
      $stmt-> bindParam(':picurl', $this->picurl);
      $stmt-> bindParam(':groupid', $this->groupid);

      // Execute query
      if($stmt->execute()) {
        return true;
      }

      // Print error if something goes wrong
      printf("Error: $s.\n", $stmt->error);

      return false;
  }

  // Delete Groups
  public function deletegroup() {
    // Create query
    $query = 'DELETE FROM ' . $this->table . ' WHERE GroupId = :groupid';

    // Prepare Statement
    $stmt = $this->conn->prepare($query);

    // clean data
    $this->groupid = htmlspecialchars(strip_tags($this->groupid));

    // Bind Data
    $stmt-> bindParam(':groupid', $this->groupid);

    // Execute query
    if($stmt->execute()) {
      return true;
    }

    // Print error if something goes wrong
    printf("Error: $s.\n", $stmt->error);

    return false;
  }

  public function deletegroupmembers() {
    // Create query
    
    $query = 'DELETE FROM UsrGroups WHERE GroupId = :groupid';

    // Prepare Statement
    $stmt = $this->conn->prepare($query);

    // clean data
    $this->groupid = htmlspecialchars(strip_tags($this->groupid));

    // Bind Data
    $stmt-> bindParam(':groupid', $this->groupid);

    //echo "groupid:".$this->groupid;

    // Execute query
    if($stmt->execute()) {
      return true;
    }

    // Print error if something goes wrong
    printf("Error: $s.\n", $stmt->error);
 
    return false;
  }

    
  public function deletememberfromgroup() {
    // Create query
    //echo "From deletememberfromgroup ".$groups->memid."\n";

    $query = 'DELETE FROM UsrGroups WHERE GroupId = :groupid and UsrId =:memid';

    // Prepare Statement
    $stmt = $this->conn->prepare($query);

    // clean data
    $this->groupid = htmlspecialchars(strip_tags($this->groupid));

    // Bind Data
    $stmt-> bindParam(':groupid', $this->groupid);
    $stmt-> bindParam(':memid', $this->memid);

    // Execute query
    if($stmt->execute()) {
      return true;
    }

    // Print error if something goes wrong
    printf("Error: $s.\n", $stmt->error);
 
    return false;
  }

  public function getgroupmembers() {
    // Create query
    
    $query = 'SELECT Id,Name,StatusMsg,Status,Invisible,PicUrl,Activity,FcmToken,Device,Admin 
              FROM    Users a, UsrGroups b 
              WHERE   a.Id = b.UsrId and b.GroupId= :groupid';

    // Prepare Statement
    $stmt = $this->conn->prepare($query);

    // clean data
    $this->groupid = htmlspecialchars(strip_tags($this->groupid));

    // Bind Data
    $stmt-> bindParam(':groupid', $this->groupid);

    // Execute query
    $stmt->execute();

    return $stmt;
  }


  // Group Exists?
  public function checkgroup(){
    $query = 'SELECT
      GroupId
     FROM
        Groups
    WHERE GroupId = :groupid';

    //Prepare statement
    $stmt = $this->conn->prepare($query);

    // Bind ID
    $stmt->bindParam(':groupid', $this->groupid);
    
    // Execute query
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // set properties
    $this->chkgroupid = $row['GroupId'];
    //echo "\n admin from isadmin".$this->admin;

  }

  public function updategrouppic() {
    // Create Query
    $query = 'UPDATE 
        Groups
    SET
      PicUrl = :picurl
      WHERE
      GroupId = :groupid';

    // Prepare Statement
    $stmt = $this->conn->prepare($query);

    // Clean data
    $this->picurl = htmlspecialchars(strip_tags($this->picurl));
    $this->groupid = htmlspecialchars(strip_tags($this->groupid));
    
    // Bind data
    $stmt-> bindParam(':groupid', $this->groupid);
    $stmt-> bindParam(':picurl', $this->picurl);

    // Execute query
    if($stmt->execute()) {
      return true;
    }
  }

  public function latestgrpdiscussion() {
       // Create query
        $query = 'SELECT group_id,max(dou) from messages where group_id in (SELECT
          GroupId
        FROM
          UsrGroups
        WHERE  UsrId = :uid and Status !=0) group by 1 order by 2 desc';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(':uid', $this->uid);

        // Execute query
        $stmt->execute();

        return $stmt;

  }

  public function listpublicgroups() {
    // Create query
     $query = 'SELECT group_id,max(dou) from messages where group_id in (select GroupId from Groups where State =0 and Status != 0) group by 1 order by 2 desc limit 50';

     // Prepare statement
     $stmt = $this->conn->prepare($query);

     // Bind ID
     //$stmt->bindParam(':uid', $this->uid);

     // Execute query
     $stmt->execute();

     return $stmt;

  }

  public function listprivategroups() {
    // Create query
     //echo "\n From listprivategroups";

     $query = 'SELECT group_id,max(dou) from messages where group_id in (SELECT
       GroupId
     FROM
       UsrGroups
     WHERE  UsrId = :uid and GroupId in (select GroupId from Groups where State =1 and Status != 0)) group by 1 order by 2 desc';

     //echo "\n Query :",$query;
     // Prepare statement
     $stmt = $this->conn->prepare($query);

     // Bind ID
     $stmt->bindParam(':uid', $this->uid);

     //echo "\n uid :",$this->uid;
     // Execute query
     $stmt->execute();

     return $stmt;

  }
 }
