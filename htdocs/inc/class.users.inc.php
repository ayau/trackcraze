<?php
 
/**
 * Handles user interactions within the app
 *
 * PHP version 5
 *
 * @author Alex Yau
 * @author Timothy Tse
 * @copyright 
 * @license  
 *
 */

 class GymScheduleUsers
{
    /**
     * The database object
     *
     * @var object
     */
    private $_db;
 
    /**
     * Checks for a database object and creates one if none is found
     *
     * @param object $db
     * @return void
     */
    public function __construct($db=NULL)
    {
        if(is_object($db))
        {
            $this->_db = $db;
        }
        else
        {
            $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
            $this->_db = new PDO($dsn, DB_USER, DB_PASS);
        }
    } 
     /**
     * Checks and inserts a new account email into the database
     *
     * @return string    a message indicating the action status
     */
     //returns 1 if email in use
     //returns 0 if no error
    public function accountCreate(){
    	//echo "sex";
    	$v = sha1(time());
    	$u = trim($_POST['email']);
    	$sql = "SELECT COUNT(Username) AS theCount
                FROM users
                WHERE Username=:email";
        if($stmt = $this->_db->prepare($sql)) {
            $stmt->bindParam(":email", $_POST['email'], PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch();
            if($row['theCount']!=0) {
                return 1; //Email in use;
            }
            $this->sendVerificationEmail($u, $v, $_POST['firstname']);
            /*if(!$this->sendVerificationEmail($u, $v)) {
                return "<h2> Error </h2>"
                    . "<p> There was an error sending your"
                    . " verification email. Please "
                    . "<a href='mailto:help@localhost'>contact "
                    . "us</a> for support. We apologize for the "
                    . "inconvenience. </p>";
            }*/
            $stmt->closeCursor();
        }
        $sql = "INSERT INTO users(Username, Password, ver_code)
        		VALUES(:email, MD5(:pass), :ver)";
          try
          {
          	$stmt = $this->_db->prepare($sql);
            $stmt->bindParam(":email", $u, PDO::PARAM_STR);
            $stmt->bindParam(":ver", $v, PDO::PARAM_STR);
            $stmt->bindParam(":pass", $_POST['password'], PDO::PARAM_STR);
            $stmt->execute();
            $stmt->closeCursor();
            $userid=$this->_db->lastInsertId();
        }
        catch(PDOException $e)
		{
			return $e->getMessage();
		}
		 //Create profile
 			 $sql = "INSERT INTO profile(UserID, Surname, Forename, Gender)
        		VALUES(:uid, :sn, :fn, :sex)";
        	try{
        	$stmt = $this->_db->prepare($sql);
            $stmt->bindParam(":uid", $userid, PDO::PARAM_INT);
            $stmt->bindParam(":sn", $_POST['surname'], PDO::PARAM_STR);
            $stmt->bindParam(":fn",$_POST['firstname'], PDO::PARAM_STR);
            $stmt->bindParam(":sex",$_POST['sex'], PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
            return  0;
        }
        catch(PDOException $e)
		{
			return $e->getMessage();
		}
        
    }
     /**
     * Sends an email to a user with a link to verify their new account
     *
     * @param string $email    The user's email address
     * @param string $ver    The random verification code for the user
     * @return boolean        TRUE on successful send and FALSE on failure
     */
   
    private function sendVerificationEmail($email, $ver, $firstname)
    {
        $e = sha1($email); // For verification purposes
        $to = trim($email);
     
        $subject = "[trackCraze] Please Verify Your Account";
 
        $headers = <<<MESSAGE
From: trackCraze <noreply@trackcraze.com>
Content-Type: text/plain;
MESSAGE;
 
        $msg = <<<EMAIL
Congratulations $firstname! You are on your way to creating a TrackCraze account! 

Simply click on www.trackcraze.com/accountverify.php?v=$ver&e=$e to verify your account.
 


Enjoy!
 
Team TrackCraze
www.trackcraze.com
EMAIL;
 
        return mail($to, $subject, $msg, $headers);
        //echo $msg;
    }
    public function verifyAccount()//ADD USER ID TO HERE, SO AFTER THEY VERIFY ACCOUNT THE USERID THING DOESN"T CRASH"
    {
    	$v = $_POST['vercode'];
    	$e = $_POST['email']; //Not the actual email, this is the string we get after applying SHA1 to the email
        $sql = "SELECT Username, UserID
                FROM users
                WHERE ver_code=:ver
                AND SHA1(Username)=:user
                AND verified=0";
 
        if($stmt = $this->_db->prepare($sql))
        {
            $stmt->bindParam(':ver', $v, PDO::PARAM_STR);
            $stmt->bindParam(':user', $e, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch();
            if(isset($row['Username']))
            {
                // Logs the user in if verification is successful
                $sql = "UPDATE users
                    SET verified=1
                WHERE SHA1(Username)=:user
                    LIMIT 1";
            try{
            	$stmt = $this->_db->prepare($sql);
                $stmt->bindParam(':user', $e, PDO::PARAM_STR);
                $stmt->execute();
                $stmt->closeCursor();
                }
                catch(PDOException $t)
		{
			return $t->getMessage();
		}
                $_SESSION['Username'] = $row['Username'];
                $_SESSION['LoggedIn'] = 1;
                $_SESSION['UserID'] = $row['UserID'];     
                $userid = $row['UserID'];       
            }else{
            	return 0;
            }
            $stmt->closeCursor();
            
		$sql = "INSERT INTO weightsoption(UserID, start, final, color)
        		VALUES(:uid, :sd, :fd, 0)";
        	try{
        	$stmt = $this->_db->prepare($sql);
            $stmt->bindParam(":uid", $userid, PDO::PARAM_INT);
            $stmt->bindParam(":sd", date("Y-m-d"), PDO::PARAM_STR);
            $stmt->bindParam(":fd",date("Y-m-d", mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"))), PDO::PARAM_STR);
            $stmt->execute();
            $stmt->closeCursor();
        }
        catch(PDOException $e)
		{
			return $e->getMessage();
		}
		//Add Other program in list
		$sql = "INSERT INTO lists(UserID, ProgramName, ProgramPosition, ListURL, public)
        		VALUES(:uid, 'Default Program', 1, 0, 0)";
        	try{
        	$stmt = $this->_db->prepare($sql);
            $stmt->bindParam(":uid", $userid, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
            $opid=$this->_db->lastInsertId();
        }
        catch(PDOException $e)
		{
			return $e->getMessage();
		}
		//Add OtherprogramID in users
		$sql = "UPDATE users
            SET OtherProgramID =:opid
                WHERE UserID=:uid
                    LIMIT 1";
            try{
            	$stmt = $this->_db->prepare($sql);
                $stmt->bindParam(':uid', $userid, PDO::PARAM_INT);
                $stmt->bindParam(':opid', $opid, PDO::PARAM_INT);
                $stmt->execute();
                $stmt->closeCursor();
                }
                catch(PDOException $t)
		{
			return $t->getMessage();
		}
		//Add SectionID
		$sql = "INSERT INTO sections(ProgramID, SectionName, SectionPosition)
        		VALUES(:pid, 'Other Exercises', 1)";
        	try{
        	$stmt = $this->_db->prepare($sql);
            $stmt->bindParam(":pid", $opid, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
            $opid=$this->_db->lastInsertId();
        }
        catch(PDOException $e)
		{
			return $e->getMessage();
		}
            // No error message is required if verification is successful
            return 1;
        }
        else
        {
            echo "<h2>Error</h2>n<p>Database error.</p>";
        }
    }
    public function updatePassword()
    {
        if(isset($_POST['p'])
        && isset($_POST['r'])
        && $_POST['p']==$_POST['r'])
        {
            $sql = "UPDATE users
                    SET Password=MD5(:pass), verified=1
                    WHERE ver_code=:ver
                    LIMIT 1";
            try
            {
                $stmt = $this->_db->prepare($sql);
                $stmt->bindParam(":pass", $_POST['p'], PDO::PARAM_STR);
                $stmt->bindParam(":ver", $_POST['v'], PDO::PARAM_STR);
                $stmt->execute();
                $stmt->closeCursor();
 
                return TRUE;
            }
            catch(PDOException $e)
            {
                return FALSE;
            }
        }
        else
        {
            return FALSE;
        }
    }
    
    public function accountLogin()
    {
        $sql = "SELECT Username, UserID
                FROM users
                WHERE Username=:user
                AND Password=MD5(:pass)
                LIMIT 1";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':user', $_POST['username'], PDO::PARAM_STR);
            $stmt->bindParam(':pass', $_POST['password'], PDO::PARAM_STR);
            $stmt->execute();
            while($row = $stmt->fetch()) {
            	$_SESSION['UserID'] = $row['UserID'];
        	}
	        if($stmt->rowCount()==1)
            {
                $_SESSION['Username'] = htmlentities($_POST['username'], ENT_QUOTES);
                $_SESSION['LoggedIn'] = 1;

                return TRUE;
            }
            else
            {
                return FALSE;
            }
        }
        catch(PDOException $e)
        {
            return FALSE;
        }
    }
 /**
     * Retrieves the ID and verification code for a user
     *
     * @return mixed    an array of info or FALSE on failure
     */
    public function retrieveAccountInfo()
    {
        $sql = "SELECT UserID, ver_code
                FROM users
                WHERE Username=:user";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':user', $_SESSION['Username'], PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch();
            $stmt->closeCursor();
            return array($row['UserID'], $row['ver_code']);
        }
        catch(PDOException $e)
        {
            return FALSE;
        }
    }
/**
     * Changes a user's email address
     *
     * @return boolean    TRUE on success and FALSE on failure
     */
    public function updateEmail()
    {
        $sql = "UPDATE users
                SET Username=:email
                WHERE UserID=:user
                LIMIT 1";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':email', $_POST['username'], PDO::PARAM_STR);
            $stmt->bindParam(':user', $_POST['userid'], PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
     
            // Updates the session variable
            $_SESSION['Username'] = htmlentities($_POST['username'], ENT_QUOTES);
     
            return TRUE;
        }
        catch(PDOException $e)
        {
            return FALSE;
        }
    }
public function verificationCheck($UID){
	$sql = "SELECT verified
		FROM users
		WHERE UserID=:uid
		LIMIT 1";
	  if($stmt = $this->_db->prepare($sql))
	    {
	    	$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
	    	$stmt->execute();
	    	$row = $stmt->fetch();
	    	if ($row['verified']==0){
	    		return TRUE;
	    	}
	    	$stmt->closeCursor();
	    }
      else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		} 
}
	public function loadProfileByUser($UID)
 {
  $sql = "SELECT
      profile.UserID, Surname, Forename, Gender, DOB, Weight, BodyMeasurements, MeasurementUnit, LBKG, Height, Heighti, Phone, Email, Location, Privacy, TrackerO, status, Setting
     FROM profile
     WHERE UserID=:uid
     LIMIT 1";
    if($stmt = $this->_db->prepare($sql))
    {
      $stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
      $stmt->execute();
     while($row = $stmt->fetch())
     {
     $userid = $row['UserID'];
     $SN = $row['Surname'];
     $FN = $row['Forename'];
     $G = $row['Gender'];
     $DOB = $row['DOB'];
     $W = $row['Weight'];
     $Measure = $row['BodyMeasurements'];
     $MeasureU = $row['MeasurementUnit'];
     $L = $row['LBKG'];
     $H = $row['Height'];
     $Hi = $row['Heighti'];
     $PH = $row['Phone'];
     $EM = $row['Email'];
     $LOC = $row['Location'];
     $PR = $row['Privacy'];
     $TO = $row['TrackerO'];
     $status = $row['status'];
     $setting = $row['Setting'];     
     }
   $stmt->closeCursor();

   // If there aren't any list items saved, no list ID is returned
  }
  else
  {
   echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
  }
	if (isset($userid)){
		//$DOB = date_format(date_create($DOB),'jS M Y');		
		return array($SN, $FN, $G, $DOB, $W, $L, $H, $Hi, $PH, $EM, $LOC, $PR, $TO, $status, $Measure, $MeasureU, $setting);
	}
	else{
		return NULL;
	}
}
//get gender	
	public function getGender($SEX)
		{
			if($SEX==0)
				{
					return "Male";
				}
			elseif($SEX==1)
				{
					return "Female";
				}
			else
				{
					return "Private";
				}
		}
	
	public function getAge($DOfB)
		{
			if(isset($DOfB)){
			date_default_timezone_set('UTC');//FIGUREOUT HOW TO DO THEIR TIMEZONE
			$today = getdate();
			list($YOfB,$MOfB,$DDOfB) = explode("-",$DOfB);
			$fage = $today['year']-$YOfB;
			if($today['mon']<$MOfB or ($today['mon'] == $MOfB and $today['mday']<$DDOfB))
				{
					$age = $fage-1;
					return $age;
				}
			else
				{
					return $fage;
					
				}}
		}
	public function formatDate($sdate)
		{
			$date = date_create($sdate);
			return (date_format($date, 'jS M Y'));
		}
	public function dateDropbox() 
		{
			echo "<select id=\"YOfB\">";
			for ($i=0; $i<100; $i++)
				{
					$today = getdate();
					$year = $today['year']-$i;
					echo "<option value=\"$year\">$year</option>";
				}
			echo "</select>";
		}
	public function editProfile()
  {
   $UID = $_SESSION["UserID"];
   $GV = strip_tags(urldecode(trim($_POST["gender"])), WHITELIST);
   $BM = strip_tags(urldecode(trim($_POST["birthmonth"])), WHITELIST);
   $BD = strip_tags(urldecode(trim($_POST["birthday"])), WHITELIST);
   $BY = strip_tags(urldecode(trim($_POST["birthyear"])), WHITELIST);
   $W = $_POST["weight"];
   $L = strip_tags(urldecode(trim($_POST["lbkg"])), WHITELIST);
   $H = strip_tags(urldecode(trim($_POST["height"])), WHITELIST);
   $Hi = strip_tags(urldecode(trim($_POST["heighti"])), WHITELIST);
   $S = strip_tags(urldecode(trim($_POST["sports"])), WHITELIST);
   $Ph = strip_tags(urldecode(trim($_POST["phone"])), WHITELIST);
   $Em = strip_tags(urldecode(trim($_POST["email"])), WHITELIST);
   $Loc = strip_tags(urldecode(trim($_POST["location"])), WHITELIST);
   $P = (array($_POST["privacy1"],$_POST["privacy2"],$_POST["privacy3"],$_POST["privacy4"],$_POST["privacy5"],$_POST["privacy6"],$_POST["privacy7"],$_POST["privacy8"],$_POST["privacy9"],$_POST["privacy10"],$_POST["privacy11"],$_POST["privacy12"],$_POST["privacy13"],$_POST["privacy14"],$_POST["privacy15"],$_POST["privacy16"]));
   $T = (array($_POST["tprivacy0"],$_POST["tprivacy1"],$_POST["tprivacy2"],$_POST["tprivacy3"],$_POST["tprivacy4"],$_POST["tprivacy5"],$_POST["tprivacy6"]));
   $DOB = $BY."-".$BM."-".$BD;
   $Pr = 0;
   $To = 0;
   $settingu = 0;
   $measure = serialize(array($_POST["m0"],$_POST["m1"],$_POST["m2"],$_POST["m3"],$_POST["m4"],$_POST["m5"],$_POST["m6"],$_POST["m7"],$_POST["m8"])); //Body measurements
   $unit = array($_POST["u0"],$_POST["u1"],$_POST["u2"],$_POST["u3"],$_POST["u4"],$_POST["u5"],$_POST["u6"],$_POST["u7"],$_POST["u8"]); //Body measurement units
   $setting = array($_POST["setting0"]); //Value to tell if weight updates automatically
   $measureunit = 0;
   for ($i=0; $i<sizeof($P); $i++)
	   {
	   	$Pr = $Pr+$P[$i]*pow(3, $i);
	   }
	for ($i=0; $i<sizeof($setting); $i++)
		{
		$settingu = $settingu+$setting[$i]*pow(2,$i);
		}
	for ($i=0; $i<sizeof($T); $i++)
		{
			$To = $To+$T[$i]*pow(2, $i);
		}
	for ($i=0; $i<sizeof($unit); $i++)
		{
			$measureunit = $measureunit+$unit[$i]*pow(2, $i);
		}
        $sql = "UPDATE profile
       SET
          Gender = '$GV',
          DOB = '$DOB',
                Weight = '$W',
                BodyMeasurements = '$measure',
                MeasurementUnit = '$measureunit',
                LBKG = '$L',
                Height = '$H',
                Heighti = '$Hi',
                Phone = '$Ph',
                Email = '$Em',
                Location = '$Loc',
                Sports = '$S',
                Privacy = '$Pr',
                TrackerO = '$To',
                Setting = '$settingu'
                WHERE UserID=:id
                LIMIT 1"; //how many results to show
        if($stmt = $this->_db->prepare($sql)) {
            //$stmt->bindParam('Exercise', $mySplitResult[0], PDO::PARAM_STR);
            $stmt->bindParam(':id', $UID, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
            echo $UID;
		
        } else {
            echo "Error saving, sorry about that!";    
        }
  }
  public function getTrackerO(){
  	$UID=$_POST['UID'];
  	$sql = "SELECT 
  profile.TrackerO
  FROM profile
  WHERE UserID=:uid
  LIMIT 1";
  if($stmt = $this->_db->prepare($sql))
   {
    $stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();
    return $row['TrackerO'];
    $stmt->closeCursor();
   }
  else
   {
    echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
   }  	
  }
  public function trackthisperson(){
  	$TrackerO = $_POST['TrackerO'];
  	$tracker = $_SESSION['UserID'];
  	$trackee = $_POST['trackee'];
  	$sql = "INSERT INTO relationship
					(Tracker, Trackee, Verified) 
    			VALUES (:tracker, :trackee, :trackero)";
		try
		{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(':tracker', $tracker, PDO::PARAM_INT);
			$stmt->bindParam(':trackee', $trackee, PDO::PARAM_INT);
			$stmt->bindParam(':trackero', $TrackerO, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();
		}
		catch(PDOException $e)
		{
			return $e->getMessage();
		}
  }
  
 public function trackingCheck($SID,$UID){
  $sql = "SELECT 
  relationship.Verified
  FROM relationship
  WHERE Tracker=:sid AND Trackee=:uid";
  if($stmt = $this->_db->prepare($sql))
   {
    $stmt->bindParam(':sid', $SID, PDO::PARAM_INT);
    $stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();
    $V = $row['Verified'];
    $stmt->closeCursor();
   }
  else
   {
    echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
   }
  if ($V==NULL)
   {
    return "0";
   }
  elseif ($V==0)
   {
    return "1";
   }
  else
   {
    return "2";
   }
  
 }
 public function getTopTracks($UID){
 	 $sql = "SELECT 
  			Trackee
  FROM relationship
  WHERE Tracker=:uid AND Verified=1 AND Toptrack=1
  ORDER BY Trackee";
  if($stmt = $this->_db->prepare($sql))
   {
    $stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
    $stmt->execute();
    $news = new GSNews();
    $search = array();
    while($row = $stmt->fetch()){
    	echo "<table>";
    	$name = $news->getName($row['Trackee']);
    	echo "<tr><td>".$name."</td></tr>";
		}
		echo "</table>";
	}else{
	echo "Top tracks cannot be retrieved as of now.";
	}
 }
 public function getTracking($UID){
 $sql = "SELECT 
  relationship.Trackee, Toptrack
  FROM relationship
  WHERE Tracker=:uid AND Verified=1
  ORDER BY Toptrack DESC";
  if($stmt = $this->_db->prepare($sql))
   {
    $stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
    $stmt->execute();
    $news = new GSNews();
    $string ="";
    $search = array();
    while($row = $stmt->fetch()){
    	$name = $news->getName($row['Trackee']);
    	if ($_SESSION['UserID']==$UID){
    	if ($row['Toptrack']==1){
    		$checkbox = "<input type='checkbox' value='yes' checked='true'/>";
    	}
    	else{
    		$checkbox = "<input type='checkbox' value='yes'/>";
    	}}
    	else{
    		if ($row['Toptrack']==1){
    		$checkbox = "checked image to indicate this is a top track";
    	}
    	else{
    		$checkbox = "";
    	}
    	}
    	$string = $string."<tr id='".$row['Trackee']."'><td>".$checkbox."</td><td>".$name."</td><td class='toptrackerror'></td></tr>";
    	array_push($search,array(strip_tags($name),$row['Trackee']));
	}
	return array($string, $search);
    $stmt->closeCursor();
   }
  else
   {
    echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
   } 	
 }
 public function getTrackers($UID){
 $sql = "SELECT 
  relationship.Tracker
  FROM relationship
  WHERE Trackee=:uid AND Verified=1";
  if($stmt = $this->_db->prepare($sql))
   {
    $stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
    $stmt->execute();
    $news = new GSNews();
    $string ="";
    $search = array();
    while($row = $stmt->fetch()){
    	$name = $news->getName($row['Tracker']);
    	$string = $string."<tr><td>".$name."</td></tr>";
    	array_push($search,array(strip_tags($name),$row['Tracker']));
	}
	return array($string, $search);
    $stmt->closeCursor();
   }
  else
   {
    echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
   } 	
 }
 public function getrandTrackers($UID){
 $sql = "SELECT 
  relationship.Tracker
  FROM relationship
  WHERE Trackee=:uid AND Verified=1
  ORDER BY rand() limit 10";
  if($stmt = $this->_db->prepare($sql))
   {
    $stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
    $stmt->execute();
    $news = new GSNews();
    $out = "";
    while($row = $stmt->fetch()){
    	$name = strip_tags($news->getName($row['Tracker']));
    	$profilepic = $news->getProfilePic($row['Tracker']);
    	$out = $out."<div><div class='miniphoto' title=\"".$name."\">".$profilepic."</div></div>";
	}
	return $out;
    $stmt->closeCursor();
   }
  else
   {
    echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
   } 	
 }
  public function countTrack($UID,$pn){
  	if ($pn ==0){
  		$text = 'Tracker';
  		$text1= 'Trackee';
  	}else{
  		$text = 'Trackee';
  		$text1 = 'Tracker';
  	}
 $sql = "SELECT COUNT(".$text.") AS theCount
  FROM relationship
  WHERE ".$text1."=:uid AND Verified=1";
  if($stmt = $this->_db->prepare($sql))
   {
    $stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();
   	return $row['theCount'];
    $stmt->closeCursor();
   }
  else
   {
    echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
   } 	
 }
 public function getrandTracking($UID,$pg){
 	if ($pg==1||$pg==2){
 		$text = " AND Toptrack=1 ";
 	}else{
 		$text = "";
 	}
 $sql = "SELECT 
  relationship.Trackee
  FROM relationship
  WHERE Tracker=:uid AND Verified=1".$text."
  ORDER BY rand() limit 10";
  if($stmt = $this->_db->prepare($sql))
   {
    $stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
    $stmt->execute();
    $news = new GSNews();
    $out = "";
    if($pg==2){
		 while($row = $stmt->fetch()){
    		$name = strip_tags($news->getName($row['Trackee']));
    		$profilepic = $news->getProfilePic($row['Trackee']);
    		$out = $out."<a href=\"/board.php?user=".$row['Trackee']."\">".$name."</a>";
		}    	
    }else{
    	while($row = $stmt->fetch()){
    		$name = strip_tags($news->getName($row['Trackee']));
    		$profilepic = $news->getProfilePic($row['Trackee']);
    		$out = $out."<div><div class='miniphoto' title=\"".$name."\">".$profilepic."</div></div>";
		}
	}
	return $out;
    $stmt->closeCursor();
   }
  else
   {
    echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
   } 	
 }
 
 public function newsForRecord($type, $content){
 	if($type==0) $UID = $_POST['postto'];
 	else $UID = $_SESSION['UserID'];
	$time = date("Y-m-d H:i:S");
	 $sql = "SELECT 
  			newsContent, newsType, UserID
  			FROM news
  			WHERE UserID=:uid AND newsType=4 AND newsContent=:nc";
  if($stmt = $this->_db->prepare($sql))
   {
    $stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
    $stmt->bindParam(':nc', $content, PDO::PARAM_STR);
    $stmt->execute();
    if($row = $stmt->fetch()){
    	$sql = "UPDATE news
				SET	newsTime =:time
				WHERE UserID=:uid
				AND newsType=4
					AND newsContent=:nc";
		 if($stmt = $this->_db->prepare($sql)){
    		$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
   	 		$stmt->bindParam(':nc', $content, PDO::PARAM_STR);
   	 		$stmt->bindParam(':time', $time, PDO::PARAM_STR);
    		$stmt->execute();
    		
    		$stmt->closeCursor();
   		}
  		else
   		{
    		echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
   		} 	
    	
    }else{
    	$this->newsUpdate($type, $content);
    }
    
    $stmt->closeCursor();
   }
  else
   {
    echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
   } 	
	
 	
 }
 
 //For certain news, like track acceptance, check if exist -> update time
 public function newsUpdate($type, $content){
 	if($type==0) $UID = $_POST['postto'];
 	else $UID = $_SESSION['UserID'];
 	
 	//reverse the order if tracking
 	if($type==20){
 		$temp = $UID;
 		$UID = $content;
 		$content = $temp;
 	}
	$time = date("Y-m-d H:i:S");
 	$sql = "INSERT INTO news
 		(UserID, newsTime, newsType, newsContent)
 	VALUES (:uid, :time, :type, :content)";
 		try
	 		{
		 		$stmt = $this->_db->prepare($sql);
				$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
				$stmt->bindParam(':time', $time, PDO::PARAM_INT);
				$stmt->bindParam(':type', $type, PDO::PARAM_INT);
				$stmt->bindParam(':content', $content, PDO::PARAM_INT);
				$stmt->execute();
				$stmt->closeCursor();
	 		}
	 		catch(PDOException $e)
		{
			return $e->getMessage();
		}
 }
 public function updatestatus(){
 	$status = strip_tags(urldecode(trim($_POST["status"])), WHITELIST);
 	$UID = $_SESSION['UserID'];
 	$sql = "UPDATE profile
       SET
          status =:status
                WHERE UserID=:id
                LIMIT 1"; //how many results to show
        if($stmt = $this->_db->prepare($sql)) {
            //$stmt->bindParam('Exercise', $mySplitResult[0], PDO::PARAM_STR);
            $stmt->bindParam(':id', $UID, PDO::PARAM_INT);
             $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->closeCursor();
            echo $status;
		
        } else {
            echo "Error saving, sorry about that!";    
        }
 }
 public function addNewGoal(){
 	$UID = $_SESSION['UserID'];
 	$type = $_POST['goaltype'];
 	$lbkg = $_POST['goallbkg'];
 	$undate = $_POST['goaldate'];
 	$weight = $_POST['goalweight'];
 	$reps = $_POST['goalreps'];
 	$other = $_POST['goalother'];
 	list($month,$day,$year) = explode("/",$undate);
 	$datearray = array($year,$month,$day);
 	$date = implode("-",$datearray);
 	$sql = "INSERT INTO goals
 		(UserID, goaltype, goalweight, goalweightlbkg, goalreps, goaldate, goalother)
 	VALUES (:uid, :type, :weight, :lbkg, :reps, :gdate, :other)";
 		try
	 		{
		 		$stmt = $this->_db->prepare($sql);
				$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
				$stmt->bindParam(':type', $type, PDO::PARAM_INT);
				$stmt->bindParam(':weight', $weight, PDO::PARAM_INT);
				$stmt->bindParam(':lbkg', $lbkg, PDO::PARAM_INT);
				$stmt->bindParam(':reps', $reps, PDO::PARAM_INT);
				$stmt->bindParam(':gdate', $date, PDO::PARAM_INT);
				$stmt->bindParam(':other', $other, PDO::PARAM_STR);
				$stmt->execute();
				$stmt->closeCursor();
				$lastid =$this->_db->lastInsertId();
				return $lastid;
	 		}
	 		catch(PDOException $e)
		{
			return $e->getMessage();
		}
	}
	public function deleteGoal(){
		$goalID = $_POST['goalid'];
		$UID = $_SESSION['UserID'];
		$sql = "DELETE FROM goals
                WHERE goalID=:gid AND UserID=:uid
		        LIMIT 1"; 
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':gid',$goalID, PDO::PARAM_INT);
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
		}
	 catch(Exception $e)
        {
            return $e->getMessage();
        }
    }
    public function loadGoalsByUserID($UID,$page){
		$sql = "SELECT
		Weight, LBKG
		FROM profile
		WHERE UserID=:id
		LIMIT 1";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':id', $UID, PDO::PARAM_INT);
			$stmt->execute();
			while ($row = $stmt->fetch()){
				$userweight = $row['Weight'];
				$weightunit = $row['LBKG'];
			}
			$stmt->closeCursor();
		}
		else{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
		if ($page==2){//USER ON BOARD
			$sqltext=" AND goalshown=0 ORDER BY rand() LIMIT 1";	//goalshown 0 = shown. (weird..right?)
		}
		else {
			$sqltext=" ORDER BY goalcomplete ASC";
		}
    	$sql = "SELECT goalID, goaltype, goalweight, goalweightlbkg, goalreps, goaldate, goalother, goalcomplete, goalshown
				FROM goals
				WHERE UserID=:uid".$sqltext;
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->execute();
			$count = 0;
			while($row = $stmt->fetch()){
				$count ++;
			if($_SESSION['UserID']==$UID || ($row['goalshown']==0 && $_SESSION['UserID']!=$UID)){
				$type = $row['goaltype'];
				$completed = $row['goalcomplete'];
				$lbkg = $this->getlbkg($row['goalweightlbkg']);
				$date =  date_format(date_create($row['goaldate']),'m/d/Y');
				if($page==0){//User is viewing the edit profile page
					$buttonecho="<td><button type='button' class='removebutton red small box'>Remove</button></td>";
					if ($row['goalshown']==1){
						$checkbox = "<td><input type='checkbox' class='goalshowornot' id='showgoal".$row['goalID']."'></td>";
					}
					else{
					$checkbox = "<td><input type='checkbox' class='goalshowornot' id='showgoal".$row['goalID']."' checked='checked'></td>";
					}
					$checklabelstart = "<label for='showgoal".$row['goalID']."'>";
					$checklabelend = "</label>";
				}
				else{//User is viewing the profile page or BOARD
					$buttonecho="";
					$checkbox="";
					$checklabelstart = "";
					$checklabelend = "";
				}
				if ($type==0){
					if ($page==2){
						$goaltext="Get to ".$row['goalweight']." ".$lbkg." ";
					}
					else{
						$goaltext="I will be ".$row['goalweight']." ".$lbkg." massive ";
						$totarget = $this->getWeightToTarget($userweight, $row['goalweight'], $weightunit, $row['goalweightlbkg']);
						$targettext = "<td class='targettext'>".$totarget." ".$lbkg." to go!!</td>";
					}
				}
				else if($type==1){
					if ($page==2){
						$goaltext="Get to ".$row['goalweight']." ".$lbkg." ";						
					}
					else{
						$goaltext="I will  be ".$row['goalweight']." ".$lbkg." slim ";
						$totarget = $this->getWeightToTarget($userweight, $row['goalweight'], $weightunit, $row['goalweightlbkg']);
						$targettext = "<td class='targettext'>".$totarget." ".$lbkg." to go!!</td>";
					}
				}
				else if($type==2){
					if($page==2){
						$goaltext="Bench ".$row['goalweight']." ".$lbkg.", ".$row['goalreps']." reps ";						
					}
					else{
						$goaltext="I will Bench ".$row['goalweight']." ".$lbkg." for ".$row['goalreps']." reps ";
						$targettext="<td></td>";
					}
				}
				else if ($type==3){
					if($page==2){
						$goaltext="Deadlift ".$row['goalweight']." ".$lbkg.", ".$row['goalreps']." reps ";
					}
					else{
						$goaltext = "I will Deadlift ".$row['goalweight']." ".$lbkg." for ".$row['goalreps']." reps ";
						$targettext="<td></td>";
					}
				}
				else if ($type==4){
					if ($page==2){
						$goaltext = "Squat ".$row['goalweight']." ".$lbkg.", ".$row['goalreps']." reps ";
					}
					else{
						$goaltext = "I will Squat ".$row['goalweight']." ".$lbkg." for ".$row['goalreps']." reps ";
						$targettext="<td></td>";
					}
				}
				
				if($completed==1){
					$completedtext="<div class='ticked sp'></div>";
					$targettext="<td class='19'>Goal Achieved, Congratulations!!</td>";
				}
				else {
					$completedtext="";
				}
				if ($page==2){
					$targettext="";
				}
				if($type==5){
					$targettext="<td></td>";
					echo "<tr id='".$row['goalID']."'>".$checkbox."<td>".$checklabelstart.$row['goalother'].$checklabelend."</td><td>".$completedtext."</td>".$targettext.$buttonecho."</tr>";
				}
				else{
					echo "<tr id='".$row['goalID']."'>".$checkbox."<td>".$checklabelstart.$goaltext."by ".$date.$checklabelend."</td><td>".$completedtext."</td>".$targettext.$buttonecho."</tr>";
				}
			}
			}
			if($count ==0) return false;
			else return true;
			$stmt->closeCursor();
		}
		else{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	public function getlbkg($lbkg){
		if($lbkg==0){
			return "lbs";
		}
		else{
			return "kg";
		}
	}
	public function getWeightToTarget($userweight, $goalweight, $userunit, $goalunit){
		if ($goalunit==0 && $userunit==1){//Goal is in lbs but weight is in kg
				$userweight = round($userweight*2.20462262,1);
			}
			else if ($goalunit==1 && $userunit==0){//Goal in kg but weight in lbs
				$userweight = round($userweight*0.45359237,1);
			}
			return round(abs($userweight - $goalweight),1);
	}
	function checkgoalWeight(){
		$weight = $_POST['weight'];
		$lbkg = $_POST['lbkg'];
		$date= $_POST['date'];
		$stoday=substr($date, 6,4)."-".substr($date, 0,2)."-".substr($date, 3,2);
		$sql = "SELECT goalID, goaltype, goalweight, goalweightlbkg, goaldate
				FROM goals
				WHERE UserID=:uid AND goalcomplete<>1 AND (goaltype=1 OR goaltype=0)";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_INT);
			$stmt->execute();
			while($row = $stmt->fetch()){
				if ($lbkg=='1'){
					$weight=2.20462262*$weight;
				}
				if ($row['goalweightlbkg']==1){
					$goalweight= $row['goalweight']*2.20462262;
				}else{
					$goalweight= $row['goalweight'];
				}
				if (($row['goaltype']==0 && $goalweight<=$weight)||($row['goaltype']==1 && $goalweight>=$weight)){
					$sql = "UPDATE goals
								SET	goalcomplete = 1,
									goalcompletedate=:wd
								WHERE goalID=:id
								LIMIT 1";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':id', $row['goalID'], PDO::PARAM_INT);
			$stmt->bindParam(':wd', $stoday, PDO::PARAM_STR);
			$stmt->execute();
			$stmt->closeCursor();
			if ($row['goalweightlbkg']==1){
					$lbkgtext = 'kg';
				}else{
					$lbkgtext = 'lbs';
				}
			if($row['goaltype']==0){
				$text = "becoming ".$row['goalweight']." ".$lbkgtext." massive.";
			}else{
				$text = "becoming ".$row['goalweight']." ".$lbkgtext." slim.";
			}
			
			$this->newsUpdate(7, $text);
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
				}
			
				
			}
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	public function goalshownChange(){
		$goalid = $_POST['goalid'];
		$shown = $_POST['goalshown'];
		$sql = "UPDATE goals
			SET	goalshown=:s
				WHERE goalID=:id
				LIMIT 1";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':id', $goalid, PDO::PARAM_INT);
			$stmt->bindParam(':s', $shown, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	public function addSport(){
		if(isset($_POST['sportid'])){
		 $sportid = $_POST['sportid'];
		}
		else{
			$sportid="";
		}
		$sql = "INSERT INTO sports
					(SportID, UserID, SportName) 
    			VALUES (:sportid, :uid, :sportname)";
		try
		{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(':sportid', $sportid, PDO::PARAM_INT);
			$stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_INT);
			$stmt->bindParam(':sportname', $_POST['sportname'], PDO::PARAM_STR);
			$stmt->execute();
			$stmt->closeCursor();
		}
		catch(PDOException $e)
		{
			return $e->getMessage();
		}
	}
	public function deleteSport(){
		if(isset($_POST['sportattr'])){
		$sportattr = $_POST['sportattr'];
		}
		$UID = $_SESSION['UserID'];
		$sql = "DELETE FROM sports
                WHERE (SportID=:sid OR SportName=:sname) AND UserID=:uid
		        LIMIT 1"; 
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':sid',$sportattr, PDO::PARAM_INT);
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->bindParam(':sname', $sportattr, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->closeCursor();
		}
	 catch(Exception $e)
        {
            return $e->getMessage();
        }
	}
	public function loadSports($UID,$page){
		if ($page==2){//USER IS ON BOARD
			$sqltext =" ORDER BY rand() LIMIT 3";
		}else{
			$sqltext = " ORDER BY SportID ASC";
		}
			$sql = "SELECT SportID, SportName
			FROM sports
			WHERE UserID=:uid".$sqltext;
			if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->execute();
			if ($page ==0){//USER IS ON EDITPROFILE PAGE
				while($row = $stmt->fetch()){
				$removebutton = "<button type='button' class='sportremove red small box'>Remove</button>";
				if ($row['SportID']==0){
					$attr = $row['SportName'];			
				}
				else{
					$attr = $row['SportID'];
				}
				echo "<li sports='".$attr."'><p>".$row['SportName']."</p>	".$removebutton."</li>";
			}
			}else if($page ==1){	//Profile page
				while($row = $stmt->fetch()){
					if ($row['SportID']==0)
						echo "<li class='box nohover'>".$row['SportName']."</li>";
					else echo "<li class='box'><a href='sport.php?sport=".$row['SportID']."'>".$row['SportName']."</a></li>";
				}
			}else{	//Board. Limit the size
				while($row = $stmt->fetch()){
					if ($row['SportID']==0)
						echo $row['SportName']." ";
					else echo "<a href='sport.php?sport=".$row['SportID']."'>".$row['SportName']."</a>"." ";
				}
			}
		//else{
		//	echo "<a href='editprofile.php' class='toEditProfile'>Add sports that you play</a>";
		//}
	
			$stmt->closeCursor();
		}
  else
   {
    echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
   }
   }
   public function getSport($SID){//for exercise.php
 $sql = "SELECT 
  UserID, SportName
  FROM sports
  WHERE SportID=:sid";
  if($stmt = $this->_db->prepare($sql))
   {
    $stmt->bindParam(':sid', $SID, PDO::PARAM_INT);
    $stmt->execute();
    $news = new GSNews();
    $string ="";
    $search = array();
    while($row = $stmt->fetch()){
    	$name = $news->getName($row['UserID']);
    	$string = $string."<tr><td>".$name."</td></tr>";
    	array_push($search,array(strip_tags($name),$row['UserID']));
	}
	return array($string, $search);
    $stmt->closeCursor();
   }
  else
   {
    echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
   } 	
 }
 public function getSportName($SID){
 	$sql = "SELECT 
 SportName
  FROM sports
  WHERE SportID=:sid
  LIMIT 1";
  if($stmt = $this->_db->prepare($sql))
   {
    $stmt->bindParam(':sid', $SID, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();
    return $row['SportName'];
    $stmt->closeCursor();
   }
  else
   {
    echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
   } 	
 }
 public function getEasterSportEgg(){
 	$sql = "SELECT 
 UserID, SportName
  FROM sports
	  WHERE SportID=0";//PUT A LIMIT!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
  if($stmt = $this->_db->prepare($sql))
   {
    $stmt->execute();
   	$news = new GSNews();
    $string ="";
    $search = array();
    while($row = $stmt->fetch()){
    	$name = $news->getName($row['UserID']);
    	$string = $string."<tr><td>".$name."</td><td>enjoys</td><td>".$row['SportName']."</td></tr>";
    	array_push($search,array(strip_tags($row['SportName']),$row['UserID']));//THIS SEARCH IS WEIRD
	}
	return array($string, $search);
    $stmt->closeCursor();
   }
  else
   {
    echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
   } 	
 }

public function changeTopTrack(){
	$sql = "UPDATE relationship
    	SET Toptrack=:toptrack
	    WHERE Tracker=:tracker AND Trackee=:trackee
        LIMIT 1";
    try{
    	$stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':toptrack',$_POST['toptrack'], PDO::PARAM_INT);
        $stmt->bindParam(':tracker',$_SESSION['UserID'], PDO::PARAM_INT);
        $stmt->bindParam(':trackee',$_POST['trackee'], PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    catch(PDOException $t)
	{
		return $t->getMessage();
	}
	}
public function addemail(){
	$email = $_POST['email'];
	$date= $_POST['date'];
	$time = date("Y-m-d H:i:s");
	$sql = "SELECT email
  FROM emails
	  WHERE email=:em";//PUT A LIMIT!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
  if($stmt = $this->_db->prepare($sql))
   {
   	$stmt->bindParam(':em',$email, PDO::PARAM_STR);
    $stmt->execute();
    if(!$stmt->fetch()){
    	$sql = "INSERT INTO emails(email, emaildate)
        		VALUES(:em, :wd)";
        	try{
        	$stmt = $this->_db->prepare($sql);
            $stmt->bindParam(":em", $email, PDO::PARAM_STR);
            $stmt->bindParam(":wd", $time, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->closeCursor();
            return "Thank you. We will notify you when we start testing.";
        }
        catch(PDOException $e)
		{
			return $e->getMessage();
		}
	}
	return "This email is already in use.";
    $stmt->closeCursor();
   }
  else
   {
    echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
   }
   }
/*public function boardProfileBox($SID, $UID){
	$relationship = trackingCheck($SID, $UID);
	$sql = "SELECT Gender, Height, Weight, LBKG, DOB, privacy
		FROM profile
		WHERE UserID=:uid
		LIMIT 1";
	  if($stmt = $this->_db->prepare($sql))
	    {
	    	$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
	    	$stmt->execute();
	    while($row = $stmt->fetch()){
	    	$PrivacyBaseTen = $row['Privacy'];
	    	$Privacy = array();
	    	for ($i=0; $i<3; $i++)
				{
					array_push($Privacy,$PrivacyBaseTen%3);
					$PrivacyBaseTen = ($PrivacyBaseTen - $PrivacyBaseTen%3)/3;
				}
	    	if(isset($row['Height'])&&$row['Height']!="0"){
	    		$Height = $row['Height']." cm";
	    	}
	    	else{
	    		$Height="";
	    	}
	    	if($Privacy[2]==2){
	    		$Height = "";
	    	}
	    	elseif($Privacy[2]==1 && ($relationship==0||$relationship==1)){
	    		$Height ="";
	    	}
	    	if($row['LBKG']==0){
	    		$lbkg = "lbs";
	    	}
	    	else{
	    		$lbkg = "kg";
	    	}
	    	if(isset($row['Weight'])&&$row['Weight']!="0.0"){
	    		$Weight = "<td class='tal'>Weight:</td><td>".$row['Weight']." ".$lbkg."</td>";
	    	}
	    	else{
	    		$Weight = "";
	    	}
	    	if($Privacy[1]==2){
	    		$Weight = "";
	    	}
	    	elseif($Privacy[1]==1 && ($relationship==0||$relationship==1)){
	    		$Weight ="";
	    	}
	    	if ($row['Gender']==0){
	    		$Gender = "<tr><td class='tal'>Gender:</td><td style='min-width:70px'>Male</td>";
	    	}
	    	elseif ($row['Gender']==1){
	    		$Gender= "<tr><td class='tal'>Gender:</td><td style='min-width:70px'>Female</td>";
	    	}
	    	else{
	    		$Gender = "<tr><td class='tal'>Gender:</td><td style='min-width:70px'>Private</td>";
	    	}
	    	if (isset($row['DOB'])){
	    		$Age = "<tr><td class='tal'>Age:</td><td>".getAge($row['DOB'])."</td>";
	    		$Birthday = "<tr><td class='tal'>Birthday:</td><td>"date_format(date_create($row['DOB']),'jS M Y')."</td></tr>";
	    	}
	    	else{
	    		$Age = "";
	    		$Birthday = "";
	    	}
	    	if ($Privacy[0]==2){
	    		$Age = "";
	    		$Birthday = "";
	    	}
	    	elseif($Privacy[0]==1){
	    		$Age = "";
	    		$Birthday = "<tr><td class='tal'>Birthday:</td><td>".date_format(date_create($row['DOB']),'jS M')."</td></tr>";
	    	}
	    }
	    	$stmt->closeCursor();
	    }	    
	else
	{
		echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
	}
	echo "<table>".$Gender."</table>"
		}*/
   public function addfailemail(){
	$email = $_POST['email'];
	$date= $_POST['date'];
	$time = date("Y-m-d H:i:s");
	return $time;
	$sql = "SELECT email
  FROM failemails
	  WHERE email=:em LIMIT 1";//PUT A LIMIT!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
  if($stmt = $this->_db->prepare($sql))
   {
   	$stmt->bindParam(':em',$email, PDO::PARAM_STR);
    $stmt->execute();
    if(!$stmt->fetch()){
    	$sql = "INSERT INTO failemails(email, emaildate)
        		VALUES(:em, :wd)";
        	try{
        	$stmt = $this->_db->prepare($sql);
            $stmt->bindParam(":em", $email, PDO::PARAM_STR);
            $stmt->bindParam(":wd", $time, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->closeCursor();
            
        }
        catch(PDOException $e)
		{
			return $e->getMessage();
		}
	}
    $stmt->closeCursor();
   }
  else
   {
    echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
   }
   }	
   function sendfeedback(){
   	$comments = $_POST['comment'];
   	$email = $_POST['email'];
   	$name = $_POST['name'];
        $to = 'feedback@trackcraze.com';
     
        $subject = "Feedback from trackCraze";
 
        $headers = <<<MESSAGE
From: Support <noreply@trackcraze.com>
Content-Type: text/plain;
MESSAGE;
 
        $msg = <<<EMAIL
name = $name
email = $email

$comments

--------------------------------
EMAIL;
 
        return mail($to, $subject, $msg, $headers);
   }	
   
	function search($item){
		
   		$news = new GSNews();
		
		$sql = "SELECT UserID
                FROM users
                WHERE Username=:email";
        if($stmt = $this->_db->prepare($sql)) {
            $stmt->bindParam(":email", $item, PDO::PARAM_STR);
            $stmt->execute();
            if($row = $stmt->fetch()){
            	$name = $news->getName($row['UserID']);
    			$Ename = strip_tags($news->getName($row['UserID']));
    			$profilepic = $news->getProfilePic($row['UserID']);
    			echo "<tr><td>";
    			echo "<div><div class='miniphoto' title=\"".$Ename."\">".$profilepic."</div></div></td>";
    			echo "<td width='150px'>".$name."</td>";
    			echo "<td> email match: 100% </td>";
    			echo "<tr />";
    			$repeated_user = $row['UserID'];
			}else{
				$repeated_user=-1;
			}
            $stmt->closeCursor();
        }else{
        	
        }
		
		$item1 = urldecode(str_replace('+','|',urlencode($item)));
		$item1 = str_replace("+","|",$item1);
		$count = (strlen($item1)-substr_count($item1,"|"))/(substr_count($item1,"|")+1);//average word length
		$sql = "SELECT *, (Forename REGEXP '$item1') + (Surname REGEXP '$item1') +0.5*('$item' REGEXP Surname)+0.5*('$item' REGEXP Forename)+ 0.5*(1-ABS(length(concat(Forename,Surname))-'$count')/length(concat(Forename,Surname))) AS rel
  				FROM profile
  				WHERE (Surname REGEXP '$item1' OR Forename REGEXP '$item1' OR '$item' REGEXP Surname OR '$item' REGEXP Forename)
	  				AND UserID<>:repeated_user
  				ORDER BY rel DESC, Forename ASC";//LIMIT!!!!!!!!!!!!!!!!!
  			if($stmt = $this->_db->prepare($sql))
   				{
   				$stmt->bindParam(":repeated_user", $repeated_user, PDO::PARAM_INT);		
    			$stmt->execute();
    			while($row = $stmt->fetch()){
    				if($row['rel']>0){
    				$name = $news->getName($row['UserID']);
    				$Ename = strip_tags($news->getName($row['UserID']));
    				$profilepic = $news->getProfilePic($row['UserID']);
    				echo "<tr><td>";
    				echo "<div><div class='miniphoto' title=\"".$Ename."\">".$profilepic."</div></div></td>";
    				echo "<td width='150px'>".$name."</td>";
    				$percent = $row['rel']/3.25 * 100;
    				echo "<td> name match: ".ceil($percent)."%</td>";
    				echo "<tr />";
					}
				}
				echo "<tr><td>end of search</td></tr>";
    			$stmt->closeCursor();
   			}
  				else
   				{
   				 echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
   				} 	
	}
	public function retrievePassword(){
		$v = sha1(time()); //SETS UNIQUE VERIFICATION CODE
		$u = trim($_POST['email']);
	// CHECK IF EMAIL ACTUALLY EXISTS
		$sql = "SELECT COUNT(Username) AS theCount
                FROM users
                WHERE Username=:email";
        if($stmt = $this->_db->prepare($sql)) {
            $stmt->bindParam(":email", $u, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch();
            if($row['theCount']==0) {
                return 1; //Email NOT in use;
            }
            $stmt->closeCursor();
        }
        // IF EMAIL EXISTS THEN GET USERID FROM users TO GET FORENAME FROM PROFILE (to be used in email)
		$sql = "SELECT UserID
				FROM users
				WHERE Username=:user
				LIMIT 1";
		if($stmt = $this->_db->prepare($sql)){
			$stmt->bindParam(':user', $u, PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch();
			$UID = $row['UserID'];
			$stmt->closeCursor();
		}
		else{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
        $sql = "SELECT Forename
				FROM profile
				WHERE UserID=:uid
				LIMIT 1";
		if($stmt = $this->_db->prepare($sql)){
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch();
			$FN = $row['Forename'];
			$stmt->closeCursor();
		}
		else{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
		// NOW THAT FORENAME IS SELECTED, UPDATE THE ForgetPassword FIELD WITH SHA1(time())
		$sql = "UPDATE users
            	SET ForgetPassword=:code
                WHERE Username=:user AND UserID=:uid
                LIMIT 1";
        try{
        	$stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':user', $u, PDO::PARAM_STR);
            $stmt->bindParam(':code', $v, PDO::PARAM_STR);
            $stmt->bindParam(':uid', $UID, PDO::PARAM_INT);//AN EXTRA CHECK, TO BE SAFE
            $stmt->execute();
            $this->sendPasswordEmail($u, $v, $FN);
        	$stmt->closeCursor();
        	//return 0;
        }
            catch(PDOException $t)
		{
			return $t->getMessage();
		}
	}
	private function sendPasswordEmail($email, $ver, $firstname){
        $e = sha1($email); // For verification purposes
        $to = trim($email);
     
        $subject = "[trackCraze] Forgotten Password";
 
        $headers = <<<MESSAGE
From: trackCraze <noreply@trackcraze.com>
Content-Type: text/plain;
MESSAGE;
        $msg = <<<EMAIL
Hi $firstname! 

Simply visit on www.trackcraze.com/retrievepassword.php?v=$ver&e=$e to reset your password.

If you believe that you have recieved this email in error, please do not hesitate to contact us at support@trackcraze.com.
 


Thank you!
 
Team TrackCraze
www.trackcraze.com
EMAIL;
 
        return mail($to, $subject, $msg, $headers);
        //echo $msg;
    }
public function resetPassword(){
	$sql = "UPDATE users
			SET Password=MD5(:password), ForgetPassword=NULL
			WHERE SHA1(Username)=:email AND ForgetPassword=:vercode
			LIMIT 1";
	try{//WE RESET THE FORGETPASSWORD FIELD SO THE USER CANT VISIT THIS LINK AGAIN
        	$stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':password', $_POST['password'], PDO::PARAM_STR); //USERS NEW PASSWORD
            $stmt->bindParam(':email', $_POST['shamail'], PDO::PARAM_STR); //Their email that has been SHA1, gotten from the link
            $stmt->bindParam(':vercode', $_POST['vercode'], PDO::PARAM_STR); //Their forgot password verification code
            $stmt->execute();
        	$stmt->closeCursor();
        }
            catch(PDOException $t)
		{
			return $t->getMessage();
		}
	}
public function checkEmailExists($shamail,$vercode){
	$sql = "SELECT COUNT(Username) AS theCount
                FROM users
                WHERE SHA1(Username)=:email AND ForgetPassword=:vercode";
        if($stmt = $this->_db->prepare($sql)) {
            $stmt->bindParam(":email", $shamail, PDO::PARAM_STR);
            $stmt->bindParam(":vercode", $vercode, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch();
            if($row['theCount']==0) {
                return 1; //Email NOT in use;
            }
            $stmt->closeCursor();
        }
}
}
?>