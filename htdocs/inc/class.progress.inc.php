<?php
/**
 * Handles list interactions within the app
 *
 * PHP version 5
 *
 * @author Alex Yau
 * @author Timothy Tse
 * @copyright 
 * @license  
 *
 */

class GSProgress
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
    public function loadTrackExercise(){
    	$SID = $_POST['SID'];
    	echo "<select id='exerciseoption'>";
    	$sql = "SELECT
					ListItemID, EID
				FROM list_items
					WHERE SectionID=:sid
				ORDER BY ListItemPosition";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':sid', $SID, PDO::PARAM_STR);
			$stmt->execute();
			while($row = $stmt->fetch())
			{
				$eName = $this->getExerciseName($row['EID']);
				echo "<option value='$row[ListItemID]'>$eName</option>";
			}
			echo "</select>";
			$stmt->closeCursor();

		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
    	
    }
    public function loadProgramsOptionByUser()
	{
		$osid = $this->getOtherProgram($_SESSION['UserID']);
		$sql = "SELECT
					lists.ProgramID, ProgramName, ListURL, MainProgramID
				FROM lists
				LEFT JOIN users
				USING (UserID)
				WHERE lists.UserID=:userid
					AND ProgramID<>:osid
				ORDER BY ProgramPosition";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':userid', $_SESSION['UserID'], PDO::PARAM_INT);
			$stmt->bindParam(':osid', $osid, PDO::PARAM_INT);
			$stmt->execute();
			echo "<select id='programoption' onchange=' fill_Splits ($(this).find(\":selected\").attr(\"value\"));loadInputBySplitID($(\"#splitoption\").find(\":selected\").attr(\"value\"));'>";
			while($row = $stmt->fetch())
			{
				$MPID=$row['MainProgramID'];
				echo $this->formatProgramsOption($row);
			}
			echo '</select>';
			echo "<select id='splitoption' name='splitoption' onchange='loadInputBySplitID($(this).find(\":selected\").attr(\"value\"));' ></select>";
			echo "<script language='javascript'>
					var i;
					function fill_Splits(i){
					var thiscache = $(\"#splitoption\");
					splitoption = document.getElementById('splitoption');
					splitoption.options.length=0;
					switch(i){";
			echo $this->loadSplitsOptionByUser($_SESSION['UserID']);
			echo"						}
				}
				</script>";
			$stmt->closeCursor();
			?><script>fill_Splits ($('#programoption').find(":selected").attr("value"));
			loadInputBySplitID($("#splitoption").find(":selected").attr("value"));</script><?php

			// If there aren't any list items saved, no list ID is returned
			//if(!isset($LID))
			//{
		//		$sql = "SELECT ProgramID, ListURL
		//				FROM lists
		//				WHERE UserID = (
		//					SELECT UserID
		//					FROM users
		//					WHERE Username=:user
		//				)";
		//		if($stmt = $this->_db->prepare($sql))
		//		{
		//			$stmt->bindParam(':user', $_SESSION['Username'], PDO::PARAM_STR);
		//			$stmt->execute();
		//			$row = $stmt->fetch();
		//			$LID = $row['ProgramID'];
		//			$URL = $row['ListURL'];
		//			$stmt->closeCursor();
		//		}
		//	}
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}

		//return ($MPID);
	}
	private function formatProgramsOption($row)
	{
			$d = NULL;
		if ($row['ProgramID']==$row['MainProgramID']){
			$ss = 'SELECTED';
		} else{
			$ss = NULL;
		}
		return "<option $ss value=$row[ProgramID]>$row[ProgramName]</option>";

	}
		public function loadSplitsOptionByUser($UID)
	{
		$sql = "SELECT
					lists.ProgramID, ProgramPosition
				FROM lists
				WHERE lists.UserID=:userid
				ORDER BY ProgramPosition";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':userid', $UID, PDO::PARAM_STR);
			$stmt->execute();
			while($row = $stmt->fetch())
			{
				$PID = $row['ProgramID'];
				echo "case \"$row[ProgramID]\": \n";
				echo $this->loadSplitsOptionByUser2($PID);
				echo "break;\n";
			}
			$stmt->closeCursor();

		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	public function loadSplitsOptionByUser2($PID)
	{
		$sql = "SELECT
					sections.SectionID, SectionName, SectionPosition
				FROM sections
				WHERE sections.ProgramID=:pid
				ORDER BY SectionPosition";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':pid', $PID, PDO::PARAM_STR);
			$stmt->execute();
			while($row = $stmt->fetch())
			{
				echo "splitoption.options[$row[SectionPosition]-1] = new Option('$row[SectionName]','$row[SectionID]');\n";
			}
			$stmt->closeCursor();

		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	public function loadPrevRecordByDate($UID, $date,$PN){
		if ($PN==0||$PN==1){//prev or prev and that day
			if ($PN==0){
				$equal="";
			}else{
				$equal="=";
			}
		$sql = "SELECT
					RecordDate
				FROM records
				WHERE UserID=:uid
						AND RecordDate<".$equal.":wd
				ORDER BY RecordDate DESC
				LIMIT 1";
			}else{
				$sql = "SELECT
					RecordDate
				FROM records
					WHERE UserID=:uid
						AND RecordDate>:wd
				ORDER BY RecordDate ASC
				LIMIT 1";
			}
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->bindParam(':wd', $date, PDO::PARAM_STR);
			$stmt->execute();
			if ($row = $stmt->fetch()){
			$this->loadRecordsByDate($UID, $row['RecordDate']);
		}else{
			if ($PN == 0||$PN==1){
				$date = date ( 'Y-m-d' ,strtotime ( '-1 day' , strtotime ($date)));
			echo "<table class=\"".$date."\" border=0 style='height:300px'><tr><td>There are no more records past this date.</td></tr></table>";
			}else {
				$date = date ( 'Y-m-d' ,strtotime ( '+1 day' , strtotime ($date)));
				echo "<table class=\"".$date."\" border=0 style='height:300px'><tr><td>There are no more records past this date.</td></tr></table>";
			}
		}
		}else{
			echo "error";
		}
	}
	public function loadRecordsByDate($UID, $stoday){
		$sql = "SELECT
					records.ListItemID, SectionID, EID, Weight, lbkg, Rep
				FROM records
				LEFT JOIN list_items
				USING (ListItemID)
				WHERE records.ListItemID<>''
					AND UserID=:uid
						AND RecordDate=:wd
				ORDER BY SectionID, records.ListItemID, RecordPosition";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->bindParam(':wd', $stoday, PDO::PARAM_STR);
			$stmt->execute();
			$date =  new DateTime($stoday);
			echo "<table class=\"".$stoday."\"border='0'>";
			echo "<tr><td colspan='3'>"."<h2>".date_format($date, 'D, d F Y')."</h2></td></tr>";
			if($row=$stmt->fetch()){
				
					$Comment = $this->getComments($row['ListItemID'], $stoday);
					$count = $this->getRecordCommentsCount($row['ListItemID'], $stoday);
									
					$Name = $this->getProgramSectionName($row['SectionID'],0);
					echo "<tr><td colspan=3><h3>".$Name."</h3></td></tr>";
					echo "<tr><td class='InputTitle' width=110>Exercise</td><td class='InputTitle' width=110>Weight</td><td class='InputTitle' width='60'>Rep</td><td class='InputTitle' width='250'>Comment</td></tr>";
					$eName = $this->getExerciseName($row['EID']);
					echo "<tr><td>".$eName."</td>";
				echo "<td>$row[Weight] $row[lbkg]</td><td>$row[Rep]</td><td class='ta' rowspan='$count'>$Comment</td></tr>";
				$tempExercise = $row['EID'];
				$tempSection = $row['SectionID'];
			while($row = $stmt->fetch())
			{
				
				if ($tempSection !=$row['SectionID']){
					$Name = $this->getProgramSectionName($row['SectionID'],0);
					echo "<tr><td colspan=3><h3>".$Name."</h3></td></tr>";
					echo "<tr><td class='InputTitle' width=110>Exercise</td><td class='InputTitle' width=110>Weight</td><td class='InputTitle' width='60'>Rep</td><td class='InputTitle' width='250'>Comment</td></tr>";
				}
				if ($tempExercise !=$row['EID']){
					$Comment = $this->getComments($row['ListItemID'], $stoday);
					$count = $this->getRecordCommentsCount($row['ListItemID'], $stoday);
					$eName = $this->getExerciseName($row['EID']);
					echo "<tr><td>".$eName."</td>";
					echo "<td>$row[Weight] $row[lbkg]</td><td>$row[Rep]</td><td class='ta' rowspan='$count'>$Comment</td></tr>";
				}else{
					echo "<tr><td></td>";
					echo "<td>$row[Weight] $row[lbkg]</td><td>$row[Rep]</td></tr>";
				}
				$tempExercise = $row['EID'];
				$tempSection = $row['SectionID'];
			}
		}
		echo "</table>";
		}else{
			echo "<tr><td>This Split is empty.</td></tr>";
		}
	}
	
	public function getRecordCommentsCount($LID, $date){
		//Get non null or empty comment for exercise
		$sql = "SELECT
					COUNT(*) as count
				FROM records
				WHERE ListItemID=:lid
					AND RecordDate=:wd";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':lid', $LID, PDO::PARAM_INT);
			$stmt->bindParam(':wd', $date, PDO::PARAM_STR);
			$stmt->execute();
			$row = $stmt->fetch();
			return $row['count'];
			}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	
	public function fillCalendar(){
		$UID = $_POST['UID'];
		$wd = $_POST['date'];
		$ed = date ( 'Y-m-d' ,strtotime ( '+1 month' , strtotime ($wd)));
		$sql = "SELECT
					DISTINCT SectionID,  RecordDate
				FROM records
				LEFT JOIN list_items
				USING (ListItemID)
				WHERE records.ListItemID<>''
					AND UserID=:uid
						AND RecordDate>=:wd
							AND RecordDate<:ed
				ORDER BY RecordDate ASC";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->bindParam(':wd', $wd, PDO::PARAM_STR);
			$stmt->bindParam(':ed', $ed, PDO::PARAM_STR);
			$stmt->execute();
			$search = array();
			while($row = $stmt->fetch()){
			$Name = $this->getProgramSectionName($row['SectionID'],1);
			array_push($search,array($row['RecordDate'],$Name));
			}
			return json_encode($search);
			//return $search;
		}else{
			return "Line 303; classprogressinc";
		}
	}
	public function getProgramSectionName($SID,$fh){
		$sql = "SELECT
					SectionName, ProgramName
				FROM sections
				LEFT JOIN lists
				USING (ProgramID)
						WHERE sections.SectionID=:sid
				LIMIT 1";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':sid', $SID, PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch();
			if ($fh==0){
				return $row['ProgramName']." - ".$row['SectionName'];
			}else{
				return $row['SectionName'];
			}
		}else{
			echo "<tr><td>This Split is empty.</td></tr>";
		}
	}
	public function getOtherProgram($UID){
		//Retrieving OtherProgramID
		$sql = "SELECT
					OtherProgramID
				FROM users
					WHERE UserID=:uid
				LIMIT 1";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch();
			return $row['OtherProgramID'];
		}else{
			//HOW TO HANDLE ERROR!??!!!!!!!!!!!!!!!!!!!!!!
		}
	}
	public function loadInputExercise(){
		$SID = $_POST['SID'];
		$date = $_POST['date'];
		$stoday=substr($date, 6,4)."-".substr($date, 0,2)."-".substr($date, 3,2);
		$sql = "SELECT
					sections.SectionID, SectionName
				FROM sections
				WHERE sections.SectionID=:sid
				LIMIT 1";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':sid', $SID, PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch();
			echo "<h3>$row[SectionName]</h3>";
			$stmt->closeCursor();
			$sql = "SELECT
					ListItemID, EID, ListItemPosition
				FROM list_items
				WHERE SectionID=:sid
				ORDER BY ListItemPosition";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':sid', $SID, PDO::PARAM_INT);
			$stmt->execute();
			echo "<table border='0'>";
			if($row = $stmt->fetch()){//A LOT OF SQL THINGSY ARE LIKE THIS. THE FIRST ROW IS UNIQUE THEN REST ARE THE SAME. FIX???
			echo "<tr><td class='InputTitle' width=110>Exercise</td><td class='InputTitle' width=80>Previous</td><td class='InputTitle' width=110>Weight</td><td class='InputTitle' width=50>Rep</td><td width=68></td><td class='InputTitle' width=190>Comments</td><td width=10></td></tr>";
				echo $this->loadInputSet($row["ListItemID"],$stoday,$row['EID']);
			while($row = $stmt->fetch())
			{
				echo $this->loadInputSet($row["ListItemID"],$stoday,$row['EID']);
			}
		}else{
			echo "<tr><td>This Split is empty.</td></tr>";
		}
		//Retrieving OtherProgramID and OtherSplitID
		$sql = "SELECT
					OtherProgramID
				FROM users
					WHERE UserID=:uid
				LIMIT 1";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch();
			$opid = $row['OtherProgramID'];
			$sql = "SELECT
					SectionID
				FROM sections
					WHERE ProgramID=:opid
				LIMIT 1";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':opid', $opid, PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch();
			$opsid = $row['SectionID'];
			}else{
			echo "<tr><td>Something is wrong with your Default Split.</td></tr>";	//Better error handling?
		}		
		}else{
			echo "<tr><td>Something is wrong with your Default program.</td></tr>";	//Better error handling?
		}
		//Doesn't save to programs yet. How to deal with this!? UNCOMMENT AFTERWARDS
		//Join Database
		$sql = "SELECT
					DISTINCT list_items.ListItemID, EID 
				FROM list_items
				LEFT JOIN records
				USING (ListItemID)
				WHERE RecordDate=:rd
				AND list_items.SectionID=:opsid
					AND UserID=:uid
				ORDER BY ListItemID, RecordPosition";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':rd', $stoday, PDO::PARAM_STR);
			$stmt->bindParam(':opsid', $opsid, PDO::PARAM_INT);
			$stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_INT);
			$stmt->execute();
			if($row= $stmt->fetch()){
				echo "<tr><td class='InputTitle' width=110>New Exercises</td><td class='InputTitle' width=80>Previous</td><td class='InputTitle' width=110>Weight</td><td class='InputTitle' width=50>Rep</td><td width=68></td><td class='InputTitle' width=190>Comments</td><td width=10></td></tr>";
				echo $this->loadInputSet($row["ListItemID"],$stoday,$row['EID']);//OVERKILL. But works for now. Cannot edit name though!?
			}
			while($row = $stmt->fetch())
			{
				echo $this->loadInputSet($row["ListItemID"],$stoday,$row['EID']);//OVERKILL. But works for now. Cannot edit name though!?
			}

		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
		//Future implementation. Load the input boxes and data separately.
		//1. Load input boxes from program
		//2. Load data from records (jusing javascript to add in)
		//3. If Record does not belong to program, add "other" input boxes (javascript).
		//Putting Other program ID in the other textbox
		echo "<tr id='somethingnewrow'><td colspan='2'><b>Trying something new today?</b></td></tr><tr sid='$opsid'><td colspan='2'><input id='newExercise' size='27'/></td><td><input id='newWeight' class='weightInputTable' maxlength = '5' size='4' onkeypress='return onlyNumbers(event,1)' /><select id='newlbkg'><option selected value='lbs'>lbs</option><option value='kg'>kg</option></select></td><td><input id='newRep' class='repInputTable' maxlength = '3' size='1' onkeypress='return onlyNumbers(event,0)'/></td><td colspan='2'><textarea id='newComment' class='commentInputTable'/></td><td class='zeropadding'><input id='addExerciseInputTable' type=button value='ne' /></td></tr>";//for if people want to add new exercises on the go
		echo "<tr id='somethingoldrow'><td colspan='5'><b>Trying something you have already done before?</b></td></tr><tr sid='$opsid'><td colspan='2'>";
		echo $this->loadExercise($_SESSION['UserID']);
		echo"</td><td><input id='oldWeight' class='weightInputTable' maxlength = '5' size='4' onkeypress='return onlyNumbers(event,1)' /><select id='oldlbkg'><option selected value='lbs'>lbs</option><option value='kg'>kg</option></select></td><td><input id='oldRep' class='repInputTable' maxlength = '3' size='1' onkeypress='return onlyNumbers(event,0)'/></td><td colspan='2'><textarea id='oldComment' class='commentInputTable'/></td><td class='zeropadding'><input id='addoldExerciseInputTable' type=button value='ne' /></td></tr>";//for if people want to add existing exercises on the go
		echo "</table></tr>";
			$stmt->closeCursor();

		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	public function getExerciseName($EID){
		$sql = "SELECT
					ExerciseName
				FROM exercise
				WHERE EID=:eid
				LIMIT 1";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':eid', $EID, PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch();
			return $row['ExerciseName'];
			$stmt->closeCursor();

		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	
	public function getComments($LID, $date){
		//Get non null or empty comment for exercise
		$sql = "SELECT
				Comment
				FROM records
				WHERE ListItemID=:lid
					AND RecordDate=:wd
				AND Comment IS NOT NULL AND Comment <> ''";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':lid', $LID, PDO::PARAM_INT);
			$stmt->bindParam(':wd', $date, PDO::PARAM_STR);
			$stmt->execute();
			$row = $stmt->fetch();
			return $row['Comment'];
			}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	
	/*Populates Progress -> Record page
		 Inputs: 	LID - list ID to load.
			 		date - date to load records (can be refactored out later)
				 	EID - ID of the exercise
		*/
	public function loadInputSet($LID,$date,$EID){
		
		//Count the number of records for rowspan
		$sql = "SELECT
				SUM(Sett) as sum
				FROM sets
				WHERE sets.ListItemID=:lid";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':lid', $LID, PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch();
			$Sum = $row['sum'];
			}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
		//$Sum = $this->getCommentsCount($LID)
		//Getting max from records
		$max = $this->getmaxRecords($LID,$date);
		if($Sum<$max) $Sum = $max;
		
		$Comment = $this->getComments($LID, $date);
		
		
		$sql = "SELECT
				sets.SetsID, Sett, Weight, lbkg, Rep, Comment, setsposition
				FROM sets
				WHERE sets.ListItemID=:lid
				ORDER BY setsposition";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':lid', $LID, PDO::PARAM_INT);
			$stmt->execute();
			
			$eName = $this->getExerciseName($EID);	//get the name from EID
			
			if ($row = $stmt->fetch()){
				if($Sum>1) $ta = "tap";
				else $ta = "";
				echo "<tr list=\"".$LID."\" class='recordtable' rel='1'><td rowspan='$Sum' class='exerciseTable ".$ta."'>$eName</td>";
				$rowRecords=$this->getRecords($LID, $row['setsposition'],$date);
				$rowPrev=$this->getPrev($LID, $row['setsposition'],$date);
				echo $this->formatInputTable($row,$rowRecords,$rowPrev);	//row = program
				if($rowPrev['Comment']!="") $oldnotes = "Old notes: ";
				else $oldnotes = "";
				if($Comment==""){
				echo "<td class='ta prevInputTable commentSpan' rowspan='$Sum'><div>$oldnotes$rowPrev[Comment]</div>"
					."<div style='display:none' class='prevnotes sp'></div>"
					."<div class='mid orange box font14 recordsCommentBtn'>New Notes</div>"
					."<textarea hidden class='recordsComment' spellcheck='false' placeholder='New Notes'>$Comment</textarea>"
					."<input hidden type='button' value='Clear' class='recordsCommentCncl'/</td>";
				}else{
					echo "<td class='ta prevInputTable commentSpan' rowspan='$Sum'><div style='display:none'>$oldnotes$rowPrev[Comment]</div>"
					."<div  class='prevnotes sp'></div>"
					."<div style='display:none' class='mid orange box font14 recordsCommentBtn'>New Notes</div>"
					."<textarea class='recordsComment' spellcheck='false' placeholder='New Notes'>$Comment</textarea>"
					."<input type='button' value='Clear' class='recordsCommentCncl'/</td>";
				}
				$order = 2;
				//Same set of sets
				while ($order<=$row["Sett"]){
					echo "</tr>";
					echo "<tr list=\"".$LID."\" class='recordtable' rel=\"".$order."\">
						<td class='prevSetTable'></td>";
					$rowRecords=$this->getRecords($LID, $order ,$date);
					$rowPrev=$this->getPrev($LID, $order,$date);
					echo $this->formatInputTable($row,$rowRecords,$rowPrev);
					$order++;
				}
				//Different sets
			while($row = $stmt->fetch())
			{
				$count=$row["Sett"];
				while ($count>0){
					echo "</tr>";
					echo "<tr list=\"".$LID."\" class='recordtable' rel=\"".$order."\">";
					$rowRecords=$this->getRecords($LID, $order,$date);
					$rowPrev=$this->getPrev($LID, $order,$date);
					echo $this->formatInputTable($row,$rowRecords,$rowPrev);
					$count--;
					$order++;
				}
			}
			//Loading from records
			//$max = $this->getmaxRecords($LID,$date); Moved to up top
			while($max>=$order){
				echo "</tr>";
				echo "<tr list=\"".$LID."\" class='recordtable' rel=\"".$order."\">";
				$rowRecords=$this->getRecords($LID, $order,$date);
				$rowPrev=$this->getPrev($LID, $order,$date);
				echo $this->formatInputTable($row,$rowRecords,$rowPrev);
				$order++;
			}
			echo "<td class='zeropadding'><input class='addnewset' type=button value=ad /></td></tr>";
		}else{
			echo "<tr list=\"".$LID."\" rel='1'><td class='exerciseTable'>$eName</td>";
			echo "<td colspan=5>No sets are specified for this exercise</td>";
		}
			$stmt->closeCursor();

		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	//row = row from program
	//row1 = data recorded for today
	//row2 = prev data from records
	//Even though previous seems empty, the Prev button is still there. Remove it dynamically or not?
	function formatInputTable($row, $row1,$row2){
		if ($row2!=NULL){
			echo"<td class='prevInputTable' weight='$row2[Weight]' lbkg='$row2[lbkg]' rep='$row2[Rep]'>$row2[Weight]$row2[lbkg] x $row2[Rep]</td>";
		}else if($row!=NULL){
		echo"<td class='prevInputTable'weight='$row[Weight]' lbkg='$row[lbkg]' rep='$row[Rep]' >$row[Weight]$row[lbkg] x $row[Rep]</td>";
		}else{
			echo"<td class='prevInputTable'></td>";		//Change: removed the weight, lbkg and rep attributes. Might cause problems later?
		}
		echo "<td><input class='weightInputTable' maxlength = '5' size='4' onkeypress='return onlyNumbers(event,1)' value='$row1[Weight]'/><select>";
			if($row1['lbkg']=='kg'){
				echo "<option value='lbs'>lbs</option><option selected value='kg'>kg</option>";
			}else if($row1['lbkg']=='lbs'){
				echo "<option selected value='lbs'>lbs</option><option value='kg'>kg</option>";
			}else if($row['lbkg']=='kg'){
			echo "<option value='lbs'>lbs</option><option selected value='kg'>kg</option>";
			} else {
				echo "<option selected value='lbs'>lbs</option><option value='kg'>kg</option>";
		}
		echo "</select></td><td><input class='repInputTable' maxlength = '3' size='1' onkeypress='return onlyNumbers(event,0)' value='$row1[Rep]'/></td><td><div class='samelast sp'></div><div class='sameprev sp'></div>";
		
	}
	function getmaxRecords($LID, $date){
		$sql = "SELECT
	MAX(RecordPosition) as max
				FROM records
					WHERE ListItemID=:lid
						AND RecordDate=:wd
				LIMIT 1";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':lid', $LID, PDO::PARAM_INT);
			$stmt->bindParam(':wd', $date, PDO::PARAM_STR);
			$stmt->execute();
			$row = $stmt->fetch();
			return $row['max'];
			$stmt->closeCursor();
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	function getRecords($LID, $rp, $date){
		$sql = "SELECT
					Weight, lbkg, Rep, Comment
				FROM records
					WHERE ListItemID=:lid
						AND RecordDate=:wd
							AND RecordPosition=:rp
				LIMIT 1";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':lid', $LID, PDO::PARAM_INT);
			$stmt->bindParam(':wd', $date, PDO::PARAM_STR);
			$stmt->bindParam(':rp', $rp, PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch();
			return $row;
			$stmt->closeCursor();
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	function getPrev($LID, $rp, $date){
		$sql = "SELECT
					Weight, lbkg, Rep, Comment
				FROM records
					WHERE ListItemID=:lid
							AND RecordPosition=:rp
						AND RecordDate!=:wd
				ORDER BY RecordDate DESC LIMIT 1";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':lid', $LID, PDO::PARAM_INT);
			$stmt->bindParam(':wd', $date, PDO::PARAM_STR);
			$stmt->bindParam(':rp', $rp, PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch();
			return $row;
			$stmt->closeCursor();
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	function addWeight(){
		$weight = $_POST['weight'];
		$date = $_POST['date'];
		$lbkg = $_POST['lbkg'];
		$stoday=substr($date, 6,4)."-".substr($date, 0,2)."-".substr($date, 3,2);
		$sql = "SELECT
					weightdate
				FROM weights
				WHERE UserID=:uid
					AND weightdate=:wd
				LIMIT 1";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_INT);
			$stmt->bindParam(':wd', $stoday, PDO::PARAM_STR);
			$stmt->execute();
			$row = $stmt->fetch();	
			if ($row['weightdate']== NULL){
				$sql = "INSERT INTO weights
					(UserID, weight, lbkg, weightdate) 
    			VALUES (:uid, :text, :lbkg, :wd)";
		try
		{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_INT);
			$stmt->bindParam(':text', $weight, PDO::PARAM_INT);
			$stmt->bindParam(':lbkg', $lbkg, PDO::PARAM_STR);
			$stmt->bindParam(':wd', $stoday, PDO::PARAM_STR);
			$stmt->execute();
			$stmt->closeCursor();

			//return (getdate());//$this->_db->lastInsertId();
		}
		catch(PDOException $e)
		{
			return $e->getMessage();
		}
			} else {
				$sql = "UPDATE weights
 					SET weight =:text,
	 					lbkg =:lbkg
		                WHERE UserID=:uid
		                AND weightdate=:wd
		                LIMIT 1"; 
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_INT);
			$stmt->bindParam(':lbkg', $lbkg, PDO::PARAM_STR);
			$stmt->bindParam(':text', $weight, PDO::PARAM_INT);
			$stmt->bindParam(':wd', $stoday, PDO::PARAM_STR);
			$stmt->execute();
			$stmt->closeCursor();
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}		
			}
			$stmt->closeCursor();
			if ($this->checkautoWeight($_SESSION['UserID'], $weight, $lbkg)==true){
				$this->checkgoalWeight($_SESSION['UserID'], $weight, $lbkg, $stoday);
			}
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	function checkautoWeight($UID, $weight, $lbkg){
		if ($lbkg=='lbs'){
			$lbkgs=0;
		}else{
			$lbkgs=1;
		}
		$sql = "SELECT
					Setting
				FROM profile
				WHERE UserID=:uid
				LIMIT 1";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch();
			if ($row['Setting']%2==1){
				$sql = "UPDATE profile
 				SET Weight =:weight,
                	LBKG =:lbkg
		                WHERE UserID=:uid
		                LIMIT 1"; 
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->bindParam(':weight', $weight, PDO::PARAM_INT);
			$stmt->bindParam(':lbkg', $lbkgs, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();
			return true;
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
			}else{
				return false;
			}
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	function checkgoalWeight($UID, $weight, $lbkg, $stoday){
		$sql = "SELECT goalID, goaltype, goalweight, goalweightlbkg, goaldate
				FROM goals
				WHERE UserID=:uid AND goalcomplete<>1 AND (goaltype=1 OR goaltype=0)";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->execute();
			while($row = $stmt->fetch()){
				if ($lbkg=='kg'){
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
			$this->newsUpdate(7,$text);
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
	 public function newsUpdate($type, $content){
 	$UID = $_SESSION['UserID'];
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
	function loadWeight(){
		$UID = $_POST['UID'];
		$sql = "SELECT
					weight, weightdate
				FROM weights
				WHERE UserID=:uid
				ORDER BY weightdate";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->execute();
			echo"[";
			$row = $stmt->fetch();
			$date= (date_format(date_create($row["weightdate"]), 'd-M-Y'));
				echo ("['$date'".","."$row[weight]]");
			while($row = $stmt->fetch())
			{
				echo ",";
				$date= (date_format(date_create($row["weightdate"]), 'd-M-Y'));
				echo ("['$date',$row[weight]]");
			}
			echo "]";
			$stmt->closeCursor();
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	function maxminWeight(){
		$UID = $_POST['UID'];
		$start = $_POST['startdate'];
		$final = $_POST['enddate'];
		$sstart=substr($start, 6,4)."-".substr($start, 0,2)."-".substr($start, 3,2);
		$sfinal=substr($final, 6,4)."-".substr($final, 0,2)."-".substr($final, 3,2);
		$sql = "SELECT
	MAX(weight) as max, MIN(weight) as min
				FROM weights
				WHERE UserID=:uid
					AND weightdate>=:start
				AND weightdate<=:end";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->bindParam(':start', $sstart, PDO::PARAM_STR);
			$stmt->bindParam(':end', $sfinal, PDO::PARAM_STR);
			$stmt->execute();
			$row = $stmt->fetch();
			echo "[".$row['max'].",".$row['min']."]";
			$stmt->closeCursor();
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	function loadWeightOption(){//CHART COLOR!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		$UID = $_POST['UID'];
		$sql = "SELECT
					start, final, color
				FROM weightsoption
				WHERE UserID=:uid
				LIMIT 1";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch();
			$start= (date_format(date_create($row["start"]), 'M j Y'));
			$final= (date_format(date_create($row["final"]), 'M j Y'));
			$diff = floor((strtotime($row["final"]) - strtotime($row["start"])) / (60*60*24*6));
			$weightstart = (date_format(date_create($row["start"]), 'm/d/Y'));
			$weightfinal = (date_format(date_create($row["final"]), 'm/d/Y'));
			echo"['$start','$final','$diff"." day','$weightstart','$weightfinal']";
			$stmt->closeCursor();
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	//If owner changes weight option, updates it. Returns nothing.
	//If user is not owner or if start>final, skips update.
	function changeWeightOption(){
		$UID = $_POST['UID'];
		$start = $_POST['weightstart'];
		$final = $_POST['weightfinal'];
		$sstart=substr($start, 6,4)."-".substr($start, 0,2)."-".substr($start, 3,2);
		$sfinal=substr($final, 6,4)."-".substr($final, 0,2)."-".substr($final, 3,2);
		if ($UID == $_SESSION['UserID'] && (strtotime($sfinal) - strtotime($sstart))>0){
		$sql = "UPDATE weightsoption
 				SET start = '$sstart',
                	final = '$sfinal'
		                WHERE UserID=:uid
		                LIMIT 1"; 
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	$start1= (date_format(date_create($sstart), 'M-j-Y'));
	$final1= (date_format(date_create($sfinal), 'M-j-Y'));
	$diff = floor((strtotime($sfinal) - strtotime($sstart)) / (60*60*24*6));
	echo"['$start1','$final1','$diff"." day','$start','$final']";
		
	}
	function deleteweight(){
		$UID = $_SESSION['UserID'];//CHANGE THIS!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		$date = $_POST['date'];
		$sdate = date('Y-m-d', strtotime($date));
		$sql = "DELETE FROM weights
                WHERE weightdate='$sdate'
	                AND UserID=:uid
                LIMIT 1";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
        }
                catch(Exception $e)
        {
            return $e->getMessage();
        }
    }
    function recordsubmit(){
    	$weight = $_POST['weight'];
    	$lbkg=$_POST['lbkg'];
    	$rep=$_POST['rep'];
    	$lbkg = strip_tags(urldecode(trim($lbkg)), WHITELIST);//filter the rest to?
    	$rep = strip_tags(urldecode(trim($rep)), WHITELIST);
    	$pos=$_POST['pos'];
    	$LID=$_POST['lid'];
    	$comment = $_POST['comment'];
		$date = $_POST['date'];
		$stoday=substr($date, 6,4)."-".substr($date, 0,2)."-".substr($date, 3,2); //USE COUNT TO SEE IF THERE IS RECORD?
		$sql = "SELECT
					RecordDate
				FROM records
				WHERE RecordDate=:wd
				AND ListItemID=:lid
				AND RecordPosition=:pos
				LIMIT 1";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':wd', $stoday, PDO::PARAM_STR);
			$stmt->bindParam(':lid', $LID, PDO::PARAM_INT);
			$stmt->bindParam(':pos', $pos, PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch();	
			if ($row['RecordDate']== NULL){
				echo $LID.$stoday.$weight.$lbkg.$rep.$pos;
				$sql = "INSERT INTO records
					(ListItemID, UserID, RecordDate, Weight, lbkg, Rep, RecordPosition, Comment) 
    			VALUES (:lid, :uid, :wd, :weight, :lbkg, :rep, :pos, :comment)";
		try
		{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(':lid', $LID, PDO::PARAM_INT);
			$stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_INT);
			$stmt->bindParam(':wd', $stoday, PDO::PARAM_STR);
			$stmt->bindParam(':weight', $weight, PDO::PARAM_INT);
			$stmt->bindParam(':lbkg', $lbkg, PDO::PARAM_STR);
			$stmt->bindParam(':rep', $rep, PDO::PARAM_INT);
			$stmt->bindParam(':pos', $pos, PDO::PARAM_INT);
			$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
			$stmt->execute();
			$stmt->closeCursor();

			//return (getdate());//$this->_db->lastInsertId();
		}
		catch(PDOException $e)
		{
			return $e->getMessage();
		}
			} else {
				$sql = "UPDATE records
					SET Weight =:weight,
                	lbkg =:lbkg,
               		Rep =:rep,
	               		Comment =:comment
				WHERE RecordDate=:wd
				AND ListItemID=:lid
				AND RecordPosition=:pos
		                LIMIT 1"; 
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':lid', $LID, PDO::PARAM_INT);
			$stmt->bindParam(':wd', $stoday, PDO::PARAM_STR);
			$stmt->bindParam(':weight', $weight, PDO::PARAM_INT);
			$stmt->bindParam(':lbkg', $lbkg, PDO::PARAM_STR);
			$stmt->bindParam(':rep', $rep, PDO::PARAM_INT);
			$stmt->bindParam(':pos', $pos, PDO::PARAM_INT);
			$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
			$stmt->execute();
			$stmt->closeCursor();
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}		
			}
			$stmt->closeCursor();
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}	
    }
    function loadbyExercise(){
    	$exercise = $_POST['exercise'];
    	$UID = $_POST['UserID'];
    	//$start = $_POST['startdate'];
    	$page = $_POST['page'];
    	$limit = 30; //$_POST['rows'];
    	$start = $limit*$page - $limit;
    	$sidx = $_POST['sidx']; // get index row - i.e. user click to sort
		$sord = $_POST['sord']; // get the direction
		if(!$sidx) $sidx =1; 
    	$sql = "SELECT
			COUNT(*) AS count
				FROM records
				WHERE ListItemID=:exercise
					AND UserID=:uid";//.$string;
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->bindParam(':exercise', $exercise, PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch();
			$count = $row['count'];
			$stmt->closeCursor();
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
		if( $count >0 ) {
			$total_pages = ceil($count/$limit);
		} else {
			$total_pages = 0;
		}
		if ($page > $total_pages) $page=$total_pages;
		$sql = "SELECT
				RecordDate, Weight, lbkg, Rep
				FROM records
				WHERE ListItemID=:exercise
					AND UserID=:uid
				ORDER BY $sidx $sord, RecordPosition ASC
				LIMIT $start, $limit";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->bindParam(':exercise', $exercise, PDO::PARAM_INT);
			$stmt->execute();
			$responce->total = $total_pages;
			$responce->page = $page;
			$responce->records = $count;
			$i=0;
			if($row2 = $stmt->fetch()){
				$date2 = $row2['RecordDate'];
				while($row = $stmt->fetch()){
					$set=1;
					$rep=$row2['Rep'];
						while($row['RecordDate']==$row2['RecordDate'] && $row['Weight']==$row2['Weight'] && $row['lbkg']==$row2['lbkg']){
							if($row['Rep']!=$row2['Rep']){
								$rep = $rep.", ".$row['Rep'];
							}
							$set++;
							$row = $stmt->fetch();
						}
						if($row['RecordDate']==$row2['RecordDate']){
			 				$date ="";
						}else{
							$date=$row['RecordDate'];
						}
					$responce->rows[$i]['id']=$row2['RecordDate'];
    				$responce->rows[$i]['cell']=array($date2,$row2['Weight'].' '.$row2['lbkg'],$rep,$set,$count);
    				$i++;
    				$row2=$row;
    				$date2=$date;
				}
				if($row2){
				$responce->rows[$i]['id']=$row2['RecordDate'];
    			$responce->rows[$i]['cell']=array($date2,$row2['Weight'].' '.$row2['lbkg'],$row2['Rep'],$set,'last');
				}
    			//$responce->userdata['startdate'] = $row2['RecordDate'];
			}
			echo json_encode($responce);
			$stmt->closeCursor();
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
//$SQL = "SELECT a.id, a.invdate, b.name, a.amount,a.tax,a.total,a.note FROM invheader a, clients b WHERE a.client_id=b.client_id ORDER BY $sidx $sord LIMIT $start , $limit";

    }
    function loadGeneralExercise(){
    	$UID = $_POST['uid'];
    	$exercise = $_POST['LID'];
    	$start = $_POST['startdate'];
    	$end = $_POST['enddate'];
    	$sql = "SELECT
	MAX(IF(lbkg='lbs',Weight,Weight*2.25)) AS max, AVG(Weight) AS avg, MAX(RecordDate) AS endrd, MIN(RecordDate) AS startrd
				FROM records
				WHERE ListItemID=:exercise
					AND UserID=:uid
					AND RecordDate<=:end
					AND RecordDate>=:start";//.$string;
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->bindParam(':exercise', $exercise, PDO::PARAM_INT);
			$stmt->bindParam(':start', $start, PDO::PARAM_STR);
			$stmt->bindParam(':end', $end, PDO::PARAM_STR);
			$stmt->execute();
			$row = $stmt->fetch();
			$max = round($row['max'],2);
			$avg = round($row['avg'],2);
			$endrd = $row['endrd'];
			$startrd = $row['startrd'];
			$stmt->closeCursor();
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
		$sql = "SELECT
	AVG(Weight) as endvol
				FROM records
				WHERE ListItemID=:exercise
					AND UserID=:uid
					AND RecordDate=:endrd";//.$string;
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->bindParam(':exercise', $exercise, PDO::PARAM_INT);
			$stmt->bindParam(':endrd', $endrd, PDO::PARAM_STR);
			$stmt->execute();
			$row = $stmt->fetch();
			$endvol = $row['endvol'];
			$stmt->closeCursor();
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
		$sql = "SELECT
	AVG(Weight) as startvol
				FROM records
				WHERE ListItemID=:exercise
					AND UserID=:uid
					AND RecordDate=:startrd";//.$string;
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->bindParam(':exercise', $exercise, PDO::PARAM_INT);
			$stmt->bindParam(':startrd', $startrd, PDO::PARAM_STR);
			$stmt->execute();
			$row = $stmt->fetch();
			$startvol = $row['startvol'];
			$stmt->closeCursor();
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
		$startdate = date($startrd);
    	$enddate = date($endrd);
    	$diff = strtotime($enddate)-strtotime($startdate);
    	$diff = $diff/(60*60*24);
    	if ($diff!=0){
    		$gain = round((($endvol - $startvol)/$diff),3)*12;
    		$gain = $gain." lbs per 12 weeks";
			$pergain = round(($gain/$startvol*100),1);
			$pergain = $pergain."% per 12 weeks";
    	}else{
    		$gain = 'Workout more often and I\'ll tell you!';
    		$pergain = 'Workout more often and I\'ll tell you!';
    	}
    	if($max){
    		$max = $max." lbs";
		}else{
			$max = 'none yet';
		}
		if($avg){
			$avg = $avg." lbs";
		}else{
			$avg = 'none yet';
		}
    	echo "<table id='generalbyExercise'><tr><td>Maximum Weight used: </td><td>$max</td></tr><tr><td>Average Weight used: </td><td>$avg</td></tr><tr><td>Average gain: </td><td>$gain</td></tr><tr><td>Percentage gain: </td><td>$pergain</td></tr><tr><td>Target set for this exercise: </td><td>150 lbs by Jan 15</td></tr></table>";
    }
    public function printbyExercise(){
    	$UID = $_POST['UserID'];
    	$exercise = $_POST['exercise'];
    	$start = $_POST['startdate'];
    	$end = $_POST['enddate'];
    	$sidx = $_POST['sidx'];
    	$sord = $_POST['sord'];
    	$eName = $_POST['eName'];

		
  		$flag = false;
    	$sql = "SELECT
				RecordDate, Weight, lbkg, Rep
				FROM records
				WHERE ListItemID=:exercise
					AND UserID=:uid
				ORDER BY $sidx $sord, RecordPosition ASC";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->bindParam(':exercise', $exercise, PDO::PARAM_INT);
			$stmt->execute();
	function cleanData(&$str){
    // escape tab characters
    $str = preg_replace("/\t/", "\\t", $str);

    // escape new lines
    $str = preg_replace("/\r?\n/", "\\n", $str);

    // convert 't' and 'f' to boolean values
    if($str == 't') $str = 'TRUE';
    if($str == 'f') $str = 'FALSE';

    // force certain number/date formats to be imported as strings
    if(preg_match("/^0/", $str) || preg_match("/^\+?\d{8,}$/", $str) || preg_match("/^\d{4}.\d{1,2}.\d{1,2}/", $str)) {
      $str = "'$str";
    }

    // escape fields that include double quotes
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
  }
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
				if(!$flag) {
      				// display field/column names as first row
      				echo implode("\t", array_keys($row)) . "\r\n";
      				$flag = true;
    			}
   				//array_walk($row, 'cleanData');
   				echo implode("\t", array_values($row)) . "\r\n";
   				
 			}
			$stmt->closeCursor();
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
    }
    //Adding exercise on the go. Called when the user clicks 'add new exercise' on Record. New exercise is added to 'Others' database.
    //Returns a div of textboxes that allows users to add more sets of this new exercise.
    function addnewset(){
    	$UID = $_SESSION['UserID'];
    	$exercise = $_POST['exercisename'];
    	$rep = $_POST['rep'];
    	$weight = $_POST['weight'];
    	$lbkg = $_POST['lbkg'];
    	$date = $_POST['date'];
    	$stoday=substr($date, 6,4)."-".substr($date, 0,2)."-".substr($date, 3,2);
    	$osid = $_POST['OSID'];
    	$comment = $_POST['comment'];
    	//Add new exercise
    	$sql = "INSERT INTO exercise
					(UserID, ExerciseName) 
    			VALUES (:uid, :ename)";
		try
		{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->bindParam(':ename', $exercise, PDO::PARAM_STR);
			$stmt->execute();
			$stmt->closeCursor();
			$eid=$this->_db->lastInsertId();
		}
		catch(PDOException $e)
		{
			return $e->getMessage();
		}
		//Find next highest position
		$sql = "SELECT
					MAX(ListItemPosition) as pos
				FROM list_items
				WHERE SectionID=:sid";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':sid', $osid, PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch();
			$pos = intval($row['pos']+1);
			$stmt->closeCursor();
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
		//Insert into listitem
		$sql = "INSERT INTO list_items
					(SectionID, EID, ListItemPosition) 
    			VALUES (:sid, :eid, :pos)";
		try
		{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(':sid', $osid, PDO::PARAM_INT);
			$stmt->bindParam(':eid', $eid, PDO::PARAM_INT);
			$stmt->bindParam(':pos', $pos, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();
			$lid=$this->_db->lastInsertId();
		}
		catch(PDOException $e)
		{
			return $e->getMessage();
		}
		//Insert into sets
		//Set = 1 because this is a new set, pos = 1
		$sql = "INSERT INTO sets
					(ListItemID, Sett, Weight, lbkg, Rep, Comment, setsposition) 
    			VALUES (:lid, 1, :weight, :lbkg, :rep, :comment, 1)";
		try
		{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(':lid', $lid, PDO::PARAM_INT);
			$stmt->bindParam(':weight', $weight, PDO::PARAM_INT);
			$stmt->bindParam(':lbkg', $lbkg, PDO::PARAM_STR);
			$stmt->bindParam(':rep', $rep, PDO::PARAM_INT);
			$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
			$stmt->execute();
			$stmt->closeCursor();
			$sid=$this->_db->lastInsertId();
		}
		catch(PDOException $e)
		{
			return $e->getMessage();
		}
		//Save the record!!!
		$sql = "INSERT INTO records
					(ListItemID, UserID, RecordDate, Weight, lbkg, Rep, Comment, RecordPosition) 
    			VALUES (:lid, :uid, :wd, :weight, :lbkg, :rep, :comment, 1)";
		try
		{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(':lid', $lid, PDO::PARAM_INT);
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->bindParam(':wd', $stoday, PDO::PARAM_STR);
			$stmt->bindParam(':weight', $weight, PDO::PARAM_INT);
			$stmt->bindParam(':lbkg', $lbkg, PDO::PARAM_STR);
			$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
			$stmt->bindParam(':rep', $rep, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();
		}
		catch(PDOException $e)
		{
			return $e->getMessage();
		}
				
		
		if($lbkg=='lbs'){
			$newselectoption = "<select ><option selected value='lbs'>lbs</option><option value='kg'>kg</option></select>";
		}else{
			$newselectoption = "<select ><option value='lbs'>lbs</option><option selected value='kg'>kg</option></select>";
		}
    	echo "<tr class='recordtable newexercise' list=\"".$lid."\" rel='1'><td colspan='2'><input size='27' value=\"".$exercise."\"/></td><td><input class='weightInputTable' maxlength = '5' size='4' onkeypress='return onlyNumbers(event,1)' value=\"".$weight."\"/>".$newselectoption."</td><td><input class='repInputTable' maxlength = '3' size='1' onkeypress='return onlyNumbers(event,0)' value=\"".$rep."\"/></td><td></td>";
    	
    	echo "<td class='ta prevInputTable commentSpan' rowspan='1'><div style='display:none'></div>"
					."<div class='prevnotes sp'></div>"
					."<div style='display:none' class='mid orange box font14 recordsCommentBtn'>New Notes</div>"
					."<textarea class='recordsComment' spellcheck='false' placeholder='New Notes'>$comment</textarea>"
					."<input type='button' value='Clear' class='recordsCommentCncl'/</td>";
					
    	echo "<td class='zeropadding'><input class='addnewset' type=button value=ad /></td></tr>";
    }
    //Loads exercise into dropdown box
    //kinda repeated functionality.
    function loadExercise($UID){
		$sql = "SELECT
					ExerciseName, EID
				FROM exercise
				WHERE UserID=:uid
				ORDER BY ExerciseName ASC	";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->execute();
			echo "<select id='OldExerciseSelect'>";
			while ($row = $stmt->fetch()){
				echo "<option value=\"".$row['EID']."\">".$row['ExerciseName']."</option>";
			}
			echo "</select>";
			$stmt->closeCursor();
		}
		else
		{
			//echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	 function addOldExerciseSet(){
    	$UID = $_SESSION['UserID'];
    	$EID = $_POST['EID'];
    	$rep = $_POST['rep'];
    	$weight = $_POST['weight'];
    	$lbkg = $_POST['lbkg'];
    	$date = $_POST['date'];
    	$stoday=substr($date, 6,4)."-".substr($date, 0,2)."-".substr($date, 3,2);
    	$osid = $_POST['OSID'];
    	$comment = $_POST['comment'];

		//See if already in Other Program
		$sql = "SELECT
					ListItemID
				FROM list_items
				WHERE SectionID=:sid
				AND EID=:eid";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':sid', $osid, PDO::PARAM_INT);
			$stmt->bindParam(':eid', $EID, PDO::PARAM_INT);
			$stmt->execute();
			if($row = $stmt->fetch()){
				$LID = $row['ListItemID'];			//If already exist in other programs
			}else{									//If not..
				//Find next highest position
		$sql = "SELECT
					MAX(ListItemPosition) as pos
				FROM list_items
				WHERE SectionID=:sid";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':sid', $osid, PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch();
			$pos = intval($row['pos']+1);
			$stmt->closeCursor();
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
		//Insert into listitem
		$sql = "INSERT INTO list_items
					(SectionID, EID, ListItemPosition) 
    			VALUES (:sid, :eid, :pos)";
		try
		{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(':sid', $osid, PDO::PARAM_INT);
			$stmt->bindParam(':eid', $EID, PDO::PARAM_INT);
			$stmt->bindParam(':pos', $pos, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();
			$LID=$this->_db->lastInsertId();
		}
		catch(PDOException $e)
		{
			return $e->getMessage();
		}
		//Insert into sets
		//Set = 1 because this is a new set, pos = 1
		$sql = "INSERT INTO sets
					(ListItemID, Sett, Weight, lbkg, Rep, Comment, setsposition) 
    			VALUES (:lid, 1, :weight, :lbkg, :rep, :comment, 1)";
		try
		{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(':lid', $LID, PDO::PARAM_INT);
			$stmt->bindParam(':weight', $weight, PDO::PARAM_INT);
			$stmt->bindParam(':lbkg', $lbkg, PDO::PARAM_STR);
			$stmt->bindParam(':rep', $rep, PDO::PARAM_INT);
			$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
			$stmt->execute();
			$stmt->closeCursor();
			$sid=$this->_db->lastInsertId();
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
		
		
		
		
		//Save the record!!!
		$sql = "INSERT INTO records
					(ListItemID, UserID, RecordDate, Weight, lbkg, Rep, Comment, RecordPosition) 
    			VALUES (:lid, :uid, :wd, :weight, :lbkg, :rep, :comment, 1)";
		try
		{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(':lid', $LID, PDO::PARAM_INT);
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->bindParam(':wd', $stoday, PDO::PARAM_STR);
			$stmt->bindParam(':weight', $weight, PDO::PARAM_INT);
			$stmt->bindParam(':lbkg', $lbkg, PDO::PARAM_STR);
			$stmt->bindParam(':comment',$comment, PDO::PARAM_STR);
			$stmt->bindParam(':rep', $rep, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();
		}
		catch(PDOException $e)
		{
			return $e->getMessage();
		}
			
		$eName = $this->getExerciseName($EID);
		
		if($lbkg=='lbs'){
			$newselectoption = "<select ><option selected value='lbs'>lbs</option><option value='kg'>kg</option></select>";
		}else{
			$newselectoption = "<select ><option value='lbs'>lbs</option><option selected value='kg'>kg</option></select>";
		}
		//change to oldexercise?
    	echo "<tr class='recordtable oldexercise' list=\"".$LID."\" rel='1'><td colspan='2'>".$eName."</td><td><input class='weightInputTable' maxlength = '5' size='4' onkeypress='return onlyNumbers(event,1)' value=\"".$weight."\"/>".$newselectoption."</td><td><input class='repInputTable' maxlength = '3' size='1' onkeypress='return onlyNumbers(event,0)' value=\"".$rep."\"/></td><td></td>";
    	
    	echo "<td class='ta prevInputTable commentSpan' rowspan='1'><div style='display:none'></div>"
					."<div class='prevnotes sp'></div>"
					."<div style='display:none' class='mid orange box font14 recordsCommentBtn'>New Notes</div>"
					."<textarea class='recordsComment' spellcheck='false' placeholder='New Notes'>$comment</textarea>"
					."<input type='button' value='Clear' class='recordsCommentCncl'/</td>";
					
    	echo "<td class='zeropadding'><input class='addnewset' type=button value=ad /></td></tr>";
    }
}