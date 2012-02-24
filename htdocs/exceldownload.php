<?php
    	// file name for download
 		$filename = "exercise_".date('Ymd').".xls";
 		    header("Content-Type: application/vnd.ms-excel");
 		header("Content-Disposition: attachment; filename=\"$filename\"");
  		//echo "lololol";
  		//echo $_POST['download'];
  		if($_POST['download']){
  			//var_dump(headers_list());
  			//readfile($file);
    		print $_POST['download'];
		}
?>