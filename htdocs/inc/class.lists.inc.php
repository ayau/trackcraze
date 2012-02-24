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

class GymScheduleItems
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
    
    public function loadUserID($PID){
    	$sql = "SELECT
					lists.UserID
				FROM lists
				WHERE lists.ProgramID=:pid
				LIMIT 1";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':pid', $PID, PDO::PARAM_INT);
			$stmt->execute();

			while($row = $stmt->fetch())
			{
				$UID = $row['UserID'];
			}
			$stmt->closeCursor();

			// If there aren't any list items saved, no list ID is returned
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
		if (isset($UID)){
			return ($UID);
		}else{
			return NULL;
		}
    }
	/** loading all splits in a program associated with a user ID. RIGHT NOW ONLY LOADS THE MAIN PROGRAM ID. NEED TO ADJUST
		*/
	public function loadMainProgramByUser($UID){//INSTEAD OF HAVING A SEPARATE FUNCTION TO LOAD MAIN PROGRAM, HAVE A FUNCTION TO FIND THE MAIN PROGRAM AND USE THE PREVIOUS LOADING FUNCTION. THIS IS FOR WHEN THE URL FOR PROGRAM AND EDITPROGRAM $_GET IS NULL
		
		$sql = "SELECT users.MainProgramID
					FROM users
						WHERE users.UserID=:uid
						AND MainProgramID <> ''
				LIMIT 1";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);//ONLY ALLOWS STRING FOR USERNAME?
			$stmt->execute();
			if($row = $stmt->fetch()){
				return $row['MainProgramID'];
			}else{
				$sql = "SELECT COUNT(*) AS count
					FROM lists
						WHERE UserID=:uid";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);//ONLY ALLOWS STRING FOR USERNAME?
			$stmt->execute();
			$row = $stmt->fetch();
			if ($row['count']>0){
				return -1;// If user has programs but does not have a main program
			}else{
				return 0;// IF user does not have any programs
			}
			$stmt->closeCursor();

		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
				return 0;// IF user does not have any programs
			}
			$stmt->closeCursor();

		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}

		//return array($LID, $URL, $order);
	}
	public function loadProgramByUser($pid){//FOR EDITING
		$sql = "SELECT
					lists.ProgramName
				FROM lists
					WHERE ProgramID=:program
				LIMIT 1";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':program', $pid, PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch();
			$PName = $row['ProgramName'];
			echo "<div class='programName' pid='$pid'><h1>$PName</h1><div class='edit editProgram'>Edit</div></div>";		
		$sql = "SELECT
					sections.ProgramID, SectionID, SectionName, SectionPosition, ListURL
				FROM sections
				LEFT JOIN lists
				USING (ProgramID)
				WHERE sections.ProgramID=(
					SELECT lists.ProgramID
					FROM lists
					WHERE lists.UserID=(
						SELECT users.UserID
						FROM users
						WHERE users.Username=:user
					)
				AND lists.ProgramID=:program
				)
				ORDER BY SectionPosition";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':user', $_SESSION['Username'], PDO::PARAM_STR);//ONLY ALLOWS STRING FOR USERNAME?
			$stmt->bindParam(':program', $pid, PDO::PARAM_INT);
			$stmt->execute();
			$order = 0;
			echo"<ul id='splits'>";
			while($row = $stmt->fetch())
			{
				$LID = $row['ProgramID'];
				$SID = $row['SectionID'];
				$SNAME = $row['SectionName'];
				$URL = $row['ListURL'];
				echo "<ul id=\"$SID"."split\" rel=$row[SectionPosition]>\n";
				echo "<div class='sectionname'><h1>$SNAME</h1><div class='edit editSection'>Edit</div><div class='deletered sp deletesection'></div></div>";         
				echo "<h3>&emsp;&emsp;Exercise &emsp; &emsp; &emsp;&emsp;&emsp;&emsp;&nbsp;Sets &emsp;&emsp;&emsp;&nbsp;Weight &emsp;&emsp;&emsp;&emsp;Reps&emsp;&emsp;&nbsp;&nbsp;Comments</h3>";
				echo "\t\t\t<ul class=\"list\">\n";
    			$lists = new GymScheduleItems(); //REMOVED $db BECAUSE ERROR
    			$newOrder = $lists->loadListItemsByUser($SID); //FIX ORDER
    
    			echo "\t\t\t</ul>";
    			echo "<form action='db-interaction/lists.php' class='exerciseAdd' method='post'> 
				<input type='button' class='addsubmit sp' value='Add'/>
				<div class='addexercisediv'><input type='text' class='addexercisetextbox' autocomplete='off' style='width:0px; display:none' />
				<div class='addexercisebuttons' hidden >
					<input type='submit' class='addexercisesubmit' value='Add' />
					<input type='button' class='addexercisecancel' value='Cancel' />
				</div></div>
				<div hidden class='oldexercisediv'> OR &emsp;<a class='chooseoldexercise'>Choose from your existing exercises</a>
            <input type='hidden' class='current-split' name='current-list' value=$SID />
			<input type='hidden' class='new-exercise-position' name='new-list-item-position' value=$newOrder />
			</form>";
                echo "</ul>";
                ++$order;
			}
			echo "</ul>";
			$stmt->closeCursor();

			//IF THERE ARE NO SPLITS (RECENTLY CHANGED. NEED TO LOOK AT OTHER LOADING CODES)
			if(!isset($LID))
			{
				$sql = "SELECT ListURL
						FROM lists
							WHERE ProgramID = :pid
						)";
				if($stmt = $this->_db->prepare($sql))
				{
					$stmt->bindParam(':pid', $_GET['program'], PDO::PARAM_STR);
					$stmt->execute();
					$row = $stmt->fetch();
					$LID = $_GET['program'];
					$URL = $row['ListURL'];
					$stmt->closeCursor();
				}
			}
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
		}else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}

		return array($LID, $URL, $order);
	}
   /**
	 * Loads all list items associated with a user ID
	 * 
	 * This function both outputs <li> tags with list items and returns an
	 * array with the list ID, list URL, and the order number for a new item.
	 * 
	 * @return array	an array containing list ID, list URL, and next order
	 */
	public function loadProgramByProgramID($PID){//FOR VIEWING
		$sql = "SELECT
					lists.ProgramName
				FROM lists
					WHERE ProgramID=:program
				LIMIT 1";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':program', $PID, PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch();
			$PName = $row['ProgramName'];
			echo "<h1 class='programName'>$PName</h1>";
			$sql = "SELECT
					sections.ProgramID, SectionID, SectionName, SectionPosition, ListURL
				FROM sections
				LEFT JOIN lists
				USING (ProgramID)
				WHERE sections.ProgramID=:program
				ORDER BY SectionPosition";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':program', $PID, PDO::PARAM_INT);//ONLY ALLOWS STRING FOR USERNAME?
			$stmt->execute();
			$order = 0;
			echo "<ul id='splits'>";
			while($row = $stmt->fetch())
			{
				$LID = $row['ProgramID'];
				$SID = $row['SectionID'];
				$SNAME = $row['SectionName'];
				$URL = $row['ListURL'];
				echo "<ul id=$SID";
				echo "split>\n";
				echo "<h1>$SNAME</h1>";         
				echo "<h3>&emsp;&emsp;Exercise &emsp; &emsp; &emsp;&emsp;&emsp;&emsp;&nbsp;Sets &emsp;&emsp;&emsp;&nbsp;Weight &emsp;&emsp;&emsp;&emsp;Reps&emsp;&emsp;&nbsp;&nbsp;Comments</h3>";
				echo "\t\t\t<ul class=\"list\">\n";
    			$lists = new GymScheduleItems(); //REMOVED $db BECAUSE ERROR
    			$newOrder = $lists->loadListItemsByProgramID($SID); //FIX ORDER
    
    			echo "\t\t\t</ul>";
                echo "</ul>";
                echo "<br /><br />";//EXTRA SPACE
                ++$order;
			}
			echo "</ul>"; 
			$stmt->closeCursor();

			// If there aren't any list items saved, no list ID is returned
			if(!isset($LID))
			{
				$sql = "SELECT ProgramID, ListURL
						FROM lists
						WHERE UserID = (
							SELECT UserID
							FROM users
							WHERE Username=:user
						)";
				if($stmt = $this->_db->prepare($sql))
				{
					$stmt->bindParam(':user', $_SESSION['Username'], PDO::PARAM_STR);
					$stmt->execute();
					$row = $stmt->fetch();
					$LID = $row['ProgramID'];
					$URL = $row['ListURL'];
					$stmt->closeCursor();
				}
			}
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
		return array($LID, $URL, $order);
	}
	public function loadListItemsByUser($SID)
	{
		$sql = "SELECT
					list_items.SectionID, EID, ListItemID
				FROM list_items
				LEFT JOIN sections
				USING (SectionID)
				WHERE list_items.SectionID=:sid
				ORDER BY ListItemPosition";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':sid', $SID, PDO::PARAM_INT);
			$stmt->execute();
			$order = 1;
			while($row = $stmt->fetch())
			{
				$LID = $row['SectionID'];
				$ID = $row['ListItemID'];
				$exercise = $this->getExerciseName($row['EID']);
            	echo "\t\t\t\t<ul id=\"$row[ListItemID]\" rel=\"$order\"";
				echo "class=\"exerciseEdit\" name=\"exerciseList\" >";
				echo "<p class=exercise>".$exercise."</p><span class=setnrep>";
				echo $this->formatListItems($ID, $row, $order);
				echo "</span><li class='setAdd' hidden><p class='set'></p><p class='weight'></p><p class='lbkg'></p> <p class='rep'></p><p class='comment'></p></li><div class='editbuttons' hidden><input type='button' title='edit' class='jeditable-activate sp' /><input title='add more sets' type='button' class='morelists sp'/><div title='delete' class='deletetab sp'></div></div><div title='hold mouse to drag' class='draggertab sp'></div></ul>\n";
				$order++;
			}
			$stmt->closeCursor();

			// If there aren't any list items saved, no list ID is returned
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}

		return ($order);//REMOVE LID AND URL FROM FUNCTION. DO WE NEED ORDER?
	}
	public function getExerciseName($eid){
		$sql = "SELECT
					ExerciseName
				FROM exercise
					WHERE EID=:eid
				LIMIT 1";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':eid', $eid, PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch();
			return $row['ExerciseName'];
			$stmt->closeCursor();

			// If there aren't any list items saved, no list ID is returned
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
    /**
     * Outputs all list items corresponding to a particular list ID
     *
     * @return void
     */
    public function loadListItemsByProgramID($SID)
    {
       $sql = "SELECT
					list_items.SectionID, EID, ListItemID
				FROM list_items
				LEFT JOIN sections
				USING (SectionID)
				WHERE list_items.SectionID=:sid
				ORDER BY ListItemPosition";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':sid', $SID, PDO::PARAM_INT);
			$stmt->execute();
			$order = 1;

			while($row = $stmt->fetch())
			{
				$LID = $row['SectionID'];
				$ID = $row['ListItemID'];
				$exercise = $this->getExerciseName($row['EID']);
            	echo "\t\t\t\t<ul id=\"$row[ListItemID]\" rel=\"$order\"";
				echo "class=\"exerciseEdit\" name=\"exerciseList\" ><span>";
				echo "<p class=exercise>".$exercise."</p><span class=setnrep>";
				echo $this->formatListItemsByProgramID($ID, $row, $order);
				echo "</span></span></ul>\n";
				$order++;
			}
			$stmt->closeCursor();

			// If there aren't any list items saved, no list ID is returned
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}

		return ($order);//REMOVE LID AND URL FROM FUNCTION. DO WE NEED ORDER?
    }
/**
     * Generates HTML markup for each list item
     *
     * @param array $row    an array of the current item's attributes
     * @param int $order    the position of the current list item
     * @return string       the formatted HTML string
     */
private function formatListItems($LID, $row1, $order1)
	{
		$sql = "SELECT
					sets.ListItemID, SetsID, Weight, lbkg, Sett, Rep, Comment
				FROM sets
				LEFT JOIN list_items
				USING (ListItemID)
				WHERE sets.ListItemID=:lid
				ORDER BY setsposition";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':lid', $LID, PDO::PARAM_INT);
			$stmt->execute();
			$order = 0;
			while($row = $stmt->fetch())
			{
		// If not logged in, manually append the <span> tag to each item
		
	
		echo $this->formatSets($row, ++$order);
		echo "<div class='dragset' hidden><div class='dragup sp'></div><div class='dragdown sp'></div></div><div class='setdelete sp' hidden></div></li>\n";				
			}
			$stmt->closeCursor();

			// If there aren't any list items saved, no list ID is returned
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}

		//return array($order);//REMOVE LID AND URL FROM FUNCTION. DO WE NEED ORDER? 
		
		//$c = $this->getColorClass($row['ListItemColor']);
		//if($row['Weight']==1)
		//{
		//	$d = '<img class="crossout" src="/images/crossout.png" '
		//		. 'style="width: 100%; display: block;"/>';
		//}
		//else
		//{

	}
	private function formatListItemsByProgramID($LID, $row1, $order1)
	{
		$sql = "SELECT
					sets.ListItemID, SetsID, Weight, lbkg, Sett, Rep, Comment
				FROM sets
				LEFT JOIN list_items
				USING (ListItemID)
				WHERE sets.ListItemID=:lid
				ORDER BY setsposition";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':lid', $LID, PDO::PARAM_INT);
			$stmt->execute();
			$order = 0;
			while($row = $stmt->fetch())
			{
		// If not logged in, manually append the <span> tag to each item
		
	
		echo $this->formatSets($row, ++$order);
		echo "</li>\n";	
			}
			$stmt->closeCursor();

			// If there aren't any list items saved, no list ID is returned
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}

		//return array($order);//REMOVE LID AND URL FROM FUNCTION. DO WE NEED ORDER? 
		
		//$c = $this->getColorClass($row['ListItemColor']);
		//if($row['Weight']==1)
		//{
		//	$d = '<img class="crossout" src="/images/crossout.png" '
		//		. 'style="width: 100%; display: block;"/>';
		//}
		//else
		//{

	}
	private function formatSets($row, $order){
		
		// If not logged in, manually append the <span> tag to each item
		//if(!isset($_SESSION['LoggedIn'])||$_SESSION['LoggedIn']!=1)
		//{
		//	$ss = "<span>";
		//	$se = "</span>";
		//}
		//else
		//{
		//	$ss = NULL;
		//	$se = NULL;
		//}
		return "<li id=\"$row[SetsID]set\" rel=\"$order\" class=\"$row[ListItemID] sets\">"
			//. "class=\"exerciseEdit\" name=\"exerciseList\">"
			."<p class=set>$row[Sett]</p><p class=weight>$row[Weight]</p><p class=lbkg>$row[lbkg]</p><p class=rep>$row[Rep]</p><p class=comment>$row[Comment]</p>";
	}
 
    /**
     * Returns the CSS class that determines color for the list item
     *
     * @param int $color    the color code of an item
     * @return string       the corresponding CSS class for the color code
     */
    private function getColorClass($color)
    {
        switch($color)
        {
            case 1:
                return 'colorBlue';
            case 2:
                return 'colorYellow';
            case 3:
                return 'colorRed';
            default:
                return 'colorGreen';
        }
    }
 /**
     * Adds a list item to the database
     *
     * @return mixed    ID of the new item on success, error message on failure
	     */
    public function addListItem($sid, $pos, $eid)
	{
		$list = $_POST['list'];
		$text = strip_tags(urldecode(trim($_POST['text'])), WHITELIST);
		$pos = $_POST['pos'];

		$sql = "INSERT INTO list_items
					(SectionID, EID, ListItemPosition) 
    			VALUES (:sid, :eid, :pos)";
		try
		{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(':sid', $sid, PDO::PARAM_INT);
			$stmt->bindParam(':eid', $eid, PDO::PARAM_INT);
			$stmt->bindParam(':pos', $pos, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();

			return $this->_db->lastInsertId();
		}
		catch(PDOException $e)
		{
			return $e->getMessage();
		}
	}
	public function addnewListItem()
	{
		$sid = $_POST['sid'];
		$text = strip_tags(urldecode(trim($_POST['text'])), WHITELIST);
		$pos = $_POST['pos'];

		$sql = "INSERT INTO exercise
					(UserID, ExerciseName) 
    			VALUES (:uid, :text)";
		try
		{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_INT);
			$stmt->bindParam(':text', $text, PDO::PARAM_STR);
			$stmt->execute();
			$stmt->closeCursor();
			$lastid = $this->_db->lastInsertId();
			return $this->addListItem($sid, $pos, $lastid);
		}
		catch(PDOException $e)
		{
			return $e->getMessage();
		}
	}
/**
	 * Changes the order of a list's items
	 * 
	 * @return string	a message indicating the number of affected items
	 */
	public function changeListItemPosition()
	{
		$SectionID = (int) $_POST['currentSectionID'];
		$startPos = (int) $_POST['startPos'];
		$currentPos = (int) $_POST['currentPos'];
		$direction = $_POST['direction'];

		if($direction == 'up')
		{
			/*
			 * This query modifies all items with a position between the item's
			 * original position and the position it was moved to. If the 
			 * change makes the item's position greater than the item's 
			 * starting position, then the query sets its position to the new
			 * position. Otherwise, the position is simply incremented.
			 */ 
			$sql = "UPDATE list_items
					SET ListItemPosition=(
						CASE 
							WHEN ListItemPosition+1>$startPos THEN $currentPos
							ELSE ListItemPosition+1 
						END) 
					WHERE SectionID=$SectionID 
					AND ListItemPosition BETWEEN $currentPos AND $startPos";
		}
		else
		{
			/*
			 * Same as above, except item positions are decremented, and if the
			 * item's changed position is less than the starting position, its
			 * position is set to the new position.
			 */
			$sql = "UPDATE list_items
					SET ListItemPosition=(
						CASE 
							WHEN ListItemPosition-1<$startPos THEN $currentPos
							ELSE ListItemPosition-1 
						END) 
					WHERE SectionID=$SectionID 
					AND ListItemPosition BETWEEN $startPos AND $currentPos";
		}

		$rows = $this->_db->exec($sql);
	}
    
/**
     * Removes a list item from the database
     *
     * @return string    message indicating success or failure
     */
    public function deleteListItem()
    {
        $list = $_POST['list'];
        $split = $_POST['split'];
        $pos = $_POST['pos'];
        
        $sql = "DELETE FROM list_items
                WHERE ListItemID=:list
                AND SectionID=:split
                LIMIT 1";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':list', $list, PDO::PARAM_INT);
            $stmt->bindParam(':split', $split, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
            
 			$sql = "DELETE FROM sets
                WHERE ListItemID=:list";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':list', $list, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
            
            $sql = "UPDATE list_items
                    SET ListItemPosition=ListItemPosition-1
                    WHERE SectionID=:split
                    AND ListItemPosition>:pos";
            try
            {
                $stmt = $this->_db->prepare($sql);
                $stmt->bindParam(':split', $split, PDO::PARAM_INT);
                $stmt->bindParam(':pos', $pos, PDO::PARAM_INT);
                $stmt->execute();
                $stmt->closeCursor();
                return "Success!";
            }
            catch(PDOException $e)
            {
                return $e->getMessage();
            }
        }
        catch(Exception $e)
        {
            return $e->getMessage();
        }
    }
                catch(Exception $e)
        {
            return $e->getMessage();
        }
    }
    /**
     * Removes a list item from the database
     *
     * @return string    message indicating success or failure
     */
    public function deleteSection()
    {
    	$list = $_POST['list'];
        $split = $_POST['split'];
        $pos = $_POST['pos'];
        $sql = "DELETE sections.*, list_items.*, sets.* FROM sections
			LEFT JOIN list_items ON sections.SectionID = list_items.SectionID
			LEFT JOIN sets ON list_items.ListItemID = sets.ListItemID
                WHERE sections.SectionID=:split";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':split', $split, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
            
 			$sql = "UPDATE sections
                    SET SectionPosition=SectionPosition-1
                    WHERE ProgramID=:list
                    AND SectionPosition>:pos";
            try
            {
                $stmt = $this->_db->prepare($sql);
                $stmt->bindParam(':list', $list, PDO::PARAM_INT);
                $stmt->bindParam(':pos', $pos, PDO::PARAM_INT);
                $stmt->execute();
                $stmt->closeCursor();
                return "Success!";
            }
            catch(PDOException $e)
            {
                return $e->getMessage();
            }
        
    }
                catch(Exception $e)
        {
            return $e->getMessage();
        }
    }
    public function deleteProgram() //kudos and comments related are removed too. LATER, remove news related to program on delete.
    {//change the main program if it's not selected.
    	$user = $_SESSION['UserID'];
        $list = $_POST['list'];
        $pos = $_POST['pos'];
        $sql = "DELETE lists.*, sections.*, list_items.*, sets.* FROM lists
        	LEFT JOIN sections ON lists.ProgramID = sections.ProgramID
			LEFT JOIN list_items ON sections.SectionID = list_items.SectionID
			LEFT JOIN sets ON list_items.ListItemID = sets.ListItemID
                WHERE lists.ProgramID=:list AND lists.UserID=:uid";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':list', $list, PDO::PARAM_INT);
            $stmt->bindParam(':uid', $user, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
            
            $sql = "DELETE comments.*, kudos.* FROM comments
        	LEFT JOIN kudos ON kudos.PostID = comments.PostID
                WHERE comments.PostID=:list AND comments.CommentType=1 AND kudos.KudosType=1";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':list', $list, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
 			$sql = "UPDATE lists
                    SET ProgramPosition=ProgramPosition-1
                    WHERE UserID=:uid
                    AND ProgramPosition>:pos";
            try
            {
                $stmt = $this->_db->prepare($sql);
                $stmt->bindParam(':uid', $user, PDO::PARAM_INT);
                $stmt->bindParam(':pos', $pos, PDO::PARAM_INT);
                $stmt->execute();
                $stmt->closeCursor();
                if ($this->loadMainProgramByUser($user)==$list){
                $sql = "UPDATE users
            	SET MainProgramID=NULL
                    WHERE UserID=:uid
                    LIMIT 1";
            try
            {
                $stmt = $this->_db->prepare($sql);
                $stmt->bindParam(':uid', $user, PDO::PARAM_INT);
                $stmt->execute();
                $stmt->closeCursor();
            }
            catch(PDOException $e)
            {
                return $e->getMessage();
            }
        }
                return "Success!";
            }
            catch(PDOException $e)
            {
                return $e->getMessage();
            }
            }
            catch(PDOException $e)
            {
                return $e->getMessage();
            }
        
    }
                catch(Exception $e)
        {
            return $e->getMessage();
        }
    }
public function deleteSetItem(){
		$list = $_POST['list'];
        $setid = $_POST['setid'];
        $pos = $_POST['pos'];
        
        $sql = "DELETE FROM sets
                WHERE SetsID=:setid
                AND ListItemID=:list
                LIMIT 1";
        try
            {
                $stmt = $this->_db->prepare($sql);
                $stmt->bindParam(':setid', $setid, PDO::PARAM_INT);
                $stmt->bindParam(':list', $list, PDO::PARAM_INT);
                $stmt->execute();
                $stmt->closeCursor();
                
                $sql = "UPDATE sets
                    SET setsposition=setsposition-1
                    WHERE ListItemID=:list
                    AND setsposition>:pos";
            try
            {
                $stmt = $this->_db->prepare($sql);
                $stmt->bindParam(':list', $list, PDO::PARAM_INT);
                $stmt->bindParam(':pos', $pos, PDO::PARAM_INT);
                $stmt->execute();
                $stmt->closeCursor();
                return "Success!";
            }
            catch(PDOException $e)
            {
                return $e->getMessage();
            }
            }
            catch(PDOException $e)
            {
                return $e->getMessage();
            }
}
public function updateListItem()
    {
        $listItemID = $_POST["listID"];
        $newValue = strip_tags(urldecode(trim($_POST["value"])), WHITELIST);
     
        $sql = "UPDATE list_items
            SET Exercise=:text
                WHERE ListItemID=:id
                LIMIT 1";
        if($stmt = $this->_db->prepare($sql)) {
            $stmt->bindParam(':text', $newValue, PDO::PARAM_STR);
            $stmt->bindParam(':id', $listItemID, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
     
           echo($newValue);
        } else {
            echo "Error saving, sorry about that!";    
        }
    }
public function updateExerciseItem()
    {
        $SID = $_POST["listID"];
        $set = strip_tags(urldecode(trim($_POST["set"])), WHITELIST);
        $weight = strip_tags(urldecode(trim($_POST["weight"])), WHITELIST);
        $lbkg = strip_tags(urldecode(trim($_POST["lbkg"])), WHITELIST);
        $rep = strip_tags(urldecode(trim($_POST["rep"])), WHITELIST);
        $comment = strip_tags(urldecode(trim($_POST["comment"])), WHITELIST);
		if (strlen($set)==0||strlen($weight)==0||strlen($rep)==0){
        	?><script>alert("wtf are you doing? set, weight or reps can't be empty");$(".setAdd").hide();</script><?php // MAKE THIS LESS RUDEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEE
        	$sql = "SELECT
					Sett, Weight, lbkg, Rep, Comment
				FROM sets
				WHERE SetsID=:sid
				LIMIT 1";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':sid', $SID, PDO::PARAM_STR);
			$stmt->execute();
			$row = $stmt->fetch();			
				echo "<p class=set>$row[Sett]</p>" . "<p class=weight>$row[Weight]</p>" . "<p class=lbkg>$row[lbkg]</p>"."<p class=rep>$row[Rep]</p>"."<p class=comment>$row[Comment]</p><div class='dragset'><div class='dragup sp'></div><div class='dragdown sp'></div></div><div class='setdelete sp' hidden></div>";
			$stmt->closeCursor();
		}
        }else{
		//for ($i=0;$i< sizeof($mySplitResult); $i++){
          // $newValue[$i] = strip_tags(urldecode(trim($mySplitResult[$i])), WHITELIST);
    	//}
    	//$newValue = strip_tags(urldecode(trim($_POST["value"])), WHITELIST);
        $sql = "UPDATE sets
 				SET Sett = '$set',
                	Weight = '$weight',
                	lbkg = '$lbkg',
               		Rep = '$rep',
                	Comment = '$comment'
		                WHERE SetsID=:id
		                LIMIT 1"; //how many results to show
        if($stmt = $this->_db->prepare($sql)) {
            //$stmt->bindParam('Exercise', $mySplitResult[0], PDO::PARAM_STR);
            $stmt->bindParam(':id', $SID, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
            echo "<p class=set>$set</p>" . "<p class=weight>$weight</p>" . "<p class=lbkg>$lbkg</p>"."<p class=rep>$rep</p>"."<p class=comment>$comment</p><div class='dragset'><div class='dragup sp'></div><div class='dragdown sp'></div></div><div class='setdelete sp' hidden></div>";

        } else {
            echo "Error saving, sorry about that!";    
        }
    }
    }
    public function addSet(){
    	$list = $_POST['listID'];
        $pos = $_POST['pos'];
        $set = strip_tags(urldecode(trim($_POST["set"])), WHITELIST);
        $weight = strip_tags(urldecode(trim($_POST["weight"])), WHITELIST);
        $lbkg = strip_tags(urldecode(trim($_POST["lbkg"])), WHITELIST);
        $rep = strip_tags(urldecode(trim($_POST["rep"])), WHITELIST);
        $comment = strip_tags(urldecode(trim($_POST["comment"])), WHITELIST);
        if (strlen($set)==0||strlen($weight)==0||strlen($rep)==0){
        	?><script>alert("wtf are you doing? set, weight or reps can't be empty");$(".setAdd").hide();</script><?php // MAKE THIS LESS RUDEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEE
        	echo"<p class='set'></p><p class='weight'></p><p class='lbkg'></p> <p class='rep'></p><p class='comment'></p>";
        }else{
        $sql = "INSERT INTO sets 
			(ListItemID, Sett, Weight, lbkg, Rep, Comment, setsposition) 
			VALUES (:list, :set, :weight, :lbkg, :rep, :comment, :pos)";
		try
		{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(':list', $list, PDO::PARAM_INT);
			$stmt->bindParam(':set', $set, PDO::PARAM_INT);
			$stmt->bindParam(':weight', $weight, PDO::PARAM_INT);
			$stmt->bindParam(':lbkg', $lbkg, PDO::PARAM_STR);
			$stmt->bindParam(':rep', $rep, PDO::PARAM_INT);
			$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
			$stmt->bindParam(':pos', $pos, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();
			$setid = ($this->_db->lastInsertId());
			?>
			<script>
     			var pos = "<?php echo($pos)?>" //CHANGE TAG LATER
     				sets = "<?php echo($set)?>",
     				weight="<?php echo($weight)?>",
     				lbkg="<?php echo($lbkg)?>",
     				rep="<?php echo($rep)?>",
     				comment="<?php echo ($comment)?>",
     				list = "<?php echo ($list)?>",
     				setid = "<?php echo($setid)?>";
     				//alert(setid);
     			$('#'+list).find(".setnrep").append("<li id='"+ setid+"set' rel='"+pos+"' class='"+list+"'><p class='set'>"+sets+"</p><p class='weight'>"+weight+"</p><p class='lbkg'>"+lbkg+"</p> <p class='rep'>"+rep+"</p><p class='comment'>"+comment+"</p><div class='dragset'><div class='dragup sp'></div><div class='dragdown sp'></div></div><div class='setdelete sp' hidden></div></li>");
     		    bindAllTabs("#"+setid+'set');
				$(".setAdd").hide();
     		</script><?php
     		
     		echo"<p class='set'></p><p class='weight'></p><p class='lbkg'></p> <p class='rep'></p><p class='comment'></p>";
		}
		catch(PDOException $e)
		{
			return $e->getMessage();
		}
	}
    }
   
    public function addSplit(){
    	$list = $_POST['list'];
		$text = strip_tags(urldecode(trim($_POST['text'])), WHITELIST);
		$pos = $_POST['pos'];

		$sql = "INSERT INTO sections
					(ProgramID, SectionName, SectionPosition) 
    			VALUES (:list, :text, :pos)";
		try
		{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(':list', $list, PDO::PARAM_INT);
			$stmt->bindParam(':text', $text, PDO::PARAM_STR);
			$stmt->bindParam(':pos', $pos, PDO::PARAM_INT);
			$stmt->execute();
			echo $this->_db->lastInsertId();
			$stmt->closeCursor();
		}
		catch(PDOException $e)
		{
			return $e->getMessage();
		}
    }
    public function dragset(){
    	$set = (int) $_POST['set'];
		$pos = (int) $_POST['pos'];
		$list = (int) $_POST['list'];
		$direction = $_POST['direction'];
		$maxpos = $_POST['maxpos'];
		if($direction == 'up'&&$pos>1)
		{

			$sql = "UPDATE sets
					SET setsposition=(
						CASE 
							WHEN setsposition=$pos THEN $pos-1
							ELSE $pos
						END) 
					WHERE ListItemID=$list 
					AND setsposition BETWEEN ($pos-1) AND $pos";
		}
		else if ($direction == 'down' && $pos < $maxpos)
		{

			$sql = "UPDATE sets
					SET setsposition=(
						CASE 
							WHEN setsposition=$pos THEN $pos+1
							ELSE $pos
						END) 
					WHERE ListItemID=$list 
					AND setsposition BETWEEN $pos AND ($pos+1)";
		}

		$rows = $this->_db->exec($sql);
	}
 /**
	 * Loads all list items associated with a user ID
	 * 
	 * This function both outputs <li> tags with list items and returns an
	 * array with the list ID, list URL, and the order number for a new item.
	 * 
	 * @return array	an array containing list ID, list URL, and next order
	 */
	public function loadProgramsByUser($UID)
	{
		$sql = "SELECT
					lists.ProgramID, ProgramName, ListURL
				FROM lists
				LEFT JOIN users
				USING (UserID)
				WHERE lists.UserID=:userid
				ORDER BY ProgramPosition";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':userid', $UID, PDO::PARAM_STR);
			$stmt->execute();
			$order = 0;
			while($row = $stmt->fetch())
			{
				echo $this->formatPrograms($row, ++$order);
			}
			$stmt->closeCursor();

			// If there aren't any list items saved, no list ID is returned THIS PART IS WEIRD
			//if(!isset($LID))
			//{
			//	$sql = "SELECT ProgramID, ListURL
			//			FROM lists
			//			WHERE UserID = (
			//				SELECT UserID
			//				FROM users
			//				WHERE Username=:user
			//			)";
			//	if($stmt = $this->_db->prepare($sql))
			//	{
			//		$stmt->bindParam(':user', $_SESSION['Username'], PDO::PARAM_STR);
			//		$stmt->execute();
			//		$row = $stmt->fetch();
			//		$LID = $row['ProgramID'];
			//		$URL = $row['ListURL'];
			//		$stmt->closeCursor();
			//	}
			//}
		}
		else
		{
			//echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}

		return array($order);
	}
	private function formatPrograms($row, $order)
	{

		$sql = "SELECT
					sections.SectionName
				FROM sections
					WHERE sections.ProgramID=:pid
				ORDER BY SectionPosition";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':pid', $row['ProgramID'], PDO::PARAM_INT);
			$stmt->execute();
			$splits="";
			while($row1 = $stmt->fetch())
			{
				$splits=$splits."<p class='hiddensplits'>".$row1['SectionName']."</p>";
			}
			$stmt->closeCursor();

			//IF THERE ARE NO SPLITS (RECENTLY CHANGED. NEED TO LOOK AT OTHER LOADING CODES)
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
		

		return "<tr id=\"$row[ProgramID]\" rel=\"$order\""
			."class=\"exerciseEdit\" name=\"exerciseList\">"
			."<td><Input type = 'Radio' class='programprivacy' name='mainprogram' value= '$row[ProgramID]'>"
			."<td class=program>$row[ProgramName]</td><td class='toggle'>(Splits)</td>"
			."<td><select class='programprivacy programprivacyselect'><option value='0'>Public</option><option value='1'>Trackers only</option><option value='2'>Private</option></select></td>" 
			. "<td><a class=programview href='/program.php?program=$row[ProgramID]'>View</a></td>"
			."<td><a class =\"programedit programprivacy\" href='/programedit.php?program=$row[ProgramID]'>Edit</a></td><td><div class='deletered sp programprivacy deleteprogram'></div></td><td class='tablesure'></td></tr>"
			."<tr><td colspan='7'><div class='hidden' hidden>".$splits."</div></td></tr>";
	}
	 public function addProgram(){
    	$list = $_POST['list'];
		$text = strip_tags(urldecode(trim($_POST['text'])), WHITELIST);
		if (isset($_POST['pos'])){
			$pos = $_POST['pos'];//pos might be wrong because user deleted something (oradded). Need to run sql check and reorder everything
		}else{
			$pos = 1;//might be wrong
		}
		$sql = "INSERT INTO lists
					(UserID, ProgramName, ProgramPosition, public) 
    			VALUES (:list, :text, :pos, 0)";
		try
		{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(':list', $list, PDO::PARAM_INT);
			$stmt->bindParam(':text', $text, PDO::PARAM_STR);
			$stmt->bindParam(':pos', $pos, PDO::PARAM_INT);
			$stmt->execute();
			echo $this->_db->lastInsertId();
			$stmt->closeCursor();
		}
		catch(PDOException $e)
		{
			return $e->getMessage();
		}
    }
	function getprogramprivacy($PID){
		$sql = "SELECT
					lists.public
				FROM lists
				WHERE ProgramID=:pid
				LIMIT 1";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':pid', $PID, PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch();
			return $row['public'];
			$stmt->closeCursor();
		}
		else
		{
			//echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	function updateprogramprivacy($PID,$val){
		$sql = "UPDATE lists
            	SET public=:val
                    WHERE ProgramID=:pid
                    LIMIT 1";
            try
            {
                $stmt = $this->_db->prepare($sql);
                $stmt->bindParam(':pid', $PID, PDO::PARAM_INT);
                $stmt->bindParam(':val', $val, PDO::PARAM_INT);
                $stmt->execute();
                $stmt->closeCursor();
            }
            catch(PDOException $e)
            {
                return $e->getMessage();
            }
	}
	function updatemainprogram(){
		$val = $_POST['val'];
		$UID = $_SESSION['UserID'];
		$sql = "UPDATE users
            	SET MainProgramID=:val
                    WHERE UserID=:uid
                    LIMIT 1";
            try
            {
                $stmt = $this->_db->prepare($sql);
                $stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
                $stmt->bindParam(':val', $val, PDO::PARAM_INT);
                $stmt->execute();
                $stmt->closeCursor();
            }
            catch(PDOException $e)
            {
                return $e->getMessage();
            }
	}
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
			while ($row = $stmt->fetch()){
				echo "<li class=\"".$row['EID']."\">".$row['ExerciseName']."</li>";
			}
			$stmt->closeCursor();
		}
		else
		{
			//echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	function loadExerciseSearch(){
		$sql = "SELECT
					ExerciseName, EID
				FROM exercise
				WHERE UserID=:uid
				ORDER BY ExerciseName ASC";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_INT);
			$stmt->execute();
			$string="<ul>";
			$search = array();
			while ($row = $stmt->fetch()){
				$string = $string."<li class=\"".$row['EID']." exercisecontent\">".$row['ExerciseName']."</li>";
				array_push($search,array($row['ExerciseName'],$row['EID']));
			}
			$string = $string."</ul>";
			$stmt->closeCursor();
			return json_encode(array($string, $search));
		}
		else
		{
			//echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	function updateSectionname(){//check if section is users'
		$sname = $_POST['sname'];
		$SID = $_POST['sid'];
		$sql = "UPDATE sections
            	SET SectionName=:sname
                    WHERE SectionID=:sid
                    LIMIT 1";
            try
            {
                $stmt = $this->_db->prepare($sql);
                $stmt->bindParam(':sid', $SID, PDO::PARAM_INT);
                $stmt->bindParam(':sname', $sname, PDO::PARAM_STR);
                $stmt->execute();
                $stmt->closeCursor();
                echo $sname;
            }
            catch(PDOException $e)
            {
                return $e->getMessage();
            }
	}
	function updateProgramname(){//check if section is users'
		$pname = $_POST['pname'];
		$PID = $_POST['pid'];
		$sql = "UPDATE lists
            	SET ProgramName=:pname
                    WHERE ProgramID=:pid
	                    AND UserID=:uid
                    LIMIT 1";
            try
            {
                $stmt = $this->_db->prepare($sql);
                $stmt->bindParam(':pid', $PID, PDO::PARAM_INT);
                $stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_INT);
                $stmt->bindParam(':pname', $pname, PDO::PARAM_STR);
                $stmt->execute();
                $stmt->closeCursor();
                echo $pname;
            }
            catch(PDOException $e)
            {
                return $e->getMessage();
            }
	}
}