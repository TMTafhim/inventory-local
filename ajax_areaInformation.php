<?php
include("BDB/DBConnEction.php");



//District Start
if(isset($_POST["division_name_post"]))
		{
			$division=$_POST["division_name_post"];
		echo '<option value="" selected>Select District</option>'; 	
			$district_info = $pdo->query("SELECT distinct district AS district FROM area_information where  division='$division'");
	     while($rowdatadistrict_info = $district_info->fetch()){
	       echo '<option>'.$rowdatadistrict_info["district"].'</option>';      
	    }
			
		}
		
//Thana Start

if(isset($_POST["district_name_post"]))
		{
			$division=$_POST["division"];
			$district=$_POST["district_name_post"];
		echo '<option value="" selected>Select Thana</option>'; 	
			$district_info = $pdo->query("SELECT * FROM area_information where  division='$division' and district='$district'");
	     while($rowdatadistrict_info = $district_info->fetch()){
	       echo '<option>'.$rowdatadistrict_info["upazila"].'</option>';      
	    }
			
		}












?>