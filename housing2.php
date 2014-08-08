<?php
            
			
			
			
			
require('config.php');
$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE) or die('Connection error');

$state = $_GET['s'];	

//Display Agency drop-down after state is selected 
$query= 'SELECT * FROM sheet1 WHERE SUBSTRING(agency_code,1,2 ) = "'.$state.'" ORDER BY agency_name';
$result = mysqli_query($con,$query);
if(!isset($_GET['a'])){

	echo "Agency: <select name=\"agencySelector\" id=\"agencySelector\" onchange=\"agencyInfo(this.value)\"  multiple=\"multiple\"> 
			<option value=\"\">SELECT AN AGENCY</option>";
	
	while($row = mysqli_fetch_array($result)) {
	  	echo "<option value='".$row['agency_code']."'>". $row['agency_name'] . "</option>";
	}
	echo "</select>";
}

	
//Display Table of Results After receiving agency data
if (isset($_GET['a'])){
	
	$years=$_GET['y'];
	$yearArray=explode("x", $years);
	$agency=$_GET['a'];
	$agencyArray=explode("-", $agency);
	
	
	/*--------------------------------Calculate State Data-----------------------------*/
	
	
	$stateUMA=0;
	$stateUML=0;
	$stateUMA_A = array();
	$stateUML_A = array();
	$avgUMA_A=array();
	$avgUML_A=array();
	$avgUR_A=array();
	$uma=0;
	$uml=0;
	
	$row_cnt = $result->num_rows;
	
	$resultsArr = array();
	
	while($row = mysqli_fetch_array($result)) {
		array_push($resultsArr,$row);
	}
			
		foreach($yearArray as $year){
			
			for($i=0;$i<count($resultsArr);$i++) {
				$row = $resultsArr[$i];
				
				if(isset($row['uma_cy'.$year])){ // If selected year exist
					//strip commas out of data and convert to float
					$uma= (float) str_replace(',' , '',$row['uma_cy'.$year]); 
				}
				else $uma=1;
				if(isset($row['uml_cy'.$year])){ // If selected year exist
					$uml= (float)str_replace(',' , '',$row['uml_cy'.$year]);
				}
				else $uml=1;
		 		 //Calculate sum of UMA and UML for the State
				 $stateUMA=$stateUMA+$uma; 
				 $stateUML=$stateUML+$uml;	
			  }
			
		array_push($stateUMA_A,$stateUMA); 
		array_push($stateUML_A,$stateUML);
		
		$stateUMA=0;
		$stateUML=0;
		$uml=0;
	    $uma=0;
		}
	
		
	   //Calculate average UMA, UML and UR for State
	 for ($m=0;$m<count($yearArray);$m++) {
		 $avgUMA=$stateUMA_A[$m] / $row_cnt; 
		array_push($avgUMA_A,$avgUMA);
		 
		 $avgUML=$stateUML_A[$m] / $row_cnt; 
		array_push($avgUML_A,$avgUML);
		
		 
		 $avgUR=$avgUML/$avgUMA;
		array_push($avgUR_A,$avgUR*100);
	 }
	
	
	
	
	
	/*---------------------------  Calculate National data -------------------- */

	$nationalQuery='Select * from sheet1';
	$nationalResult = mysqli_query($con,$nationalQuery);
	$national_row_cnt = $nationalResult->num_rows;
	
	$nationalUMA=0;
	$nationalUML=0;
	$nationalUR=0;
	
	$nationalUMA_A = array();
	$nationalUML_A = array();
	$nationalavgUMA_A=array();
	$nationalavgUML_A=array();
	$nationalavgUR_A=array();
	
	$nationalResultsArr = array();
	while($row = mysqli_fetch_array($nationalResult)) {
		array_push($nationalResultsArr,$row);
	}
			
		foreach($yearArray as $year){
			
			for($i=0;$i<count($nationalResultsArr);$i++) {
				$row = $nationalResultsArr[$i];
				
				if(isset($row['uma_cy'.$year])){ // If selected year exist
					//strip commas out of data and convert to float
					$uma= (float) str_replace(',' , '',$row['uma_cy'.$year]); 
				}
				else $uma=1;
				if(isset($row['uml_cy'.$year])){ // If selected year exist
					$uml= (float)str_replace(',' , '',$row['uml_cy'.$year]);
				}
				else $uml=1;


 				//Calculate sum of UMA and UML for the Nation
				 $nationalUMA=$nationalUMA+$uma; 
				 $nationalUML=$nationalUML+$uml;	
			  }
			
		array_push($nationalUMA_A,$nationalUMA); 
		array_push($nationalUML_A,$nationalUML);
		
		$nationalUMA=0;
		$nationalUML=0;
		$uml=0;
		$uma=0;
		}
	
		
	   //Calculate average UMA, UML and UR for Nation
	 for ($m=0;$m<count($yearArray);$m++) {
		 $nationalavgUMA=$nationalUMA_A[$m] / $national_row_cnt; 
		array_push($nationalavgUMA_A,$nationalavgUMA);
		 
		 $nationalavgUML=$nationalUML_A[$m] / $national_row_cnt; 
		array_push($nationalavgUML_A,$nationalavgUML);
		
		 
		 $nationalavgUR=$nationalavgUML/$nationalavgUMA;
		array_push($nationalavgUR_A,$nationalavgUR*100);
	 }
	
	
	
	
	
	
	/*-------------------------Calculate Agency data------------------------*/
	
		
	$query2= 'SELECT * FROM sheet1 WHERE ';
	 for ($i=0;$i<count($agencyArray);$i++) {
		$query2 .= "agency_code = '" . $agencyArray[$i]."'";
			if ($i<count($agencyArray)-1){
				 $query2 .= " OR ";
			 }
	 }
	
	 $query2 .=  ' ORDER BY agency_name';
	
	$result2 = mysqli_query($con,$query2);
	$row_cnt2 = $result2->num_rows;

	$returnAgency=array(); 
	$returnAgencyName=array();
	$returnUMA=array();
	$returnUML=array();
	$returnUR=array();
     $count=0;
	 
	 while($row2 = mysqli_fetch_array($result2)) {
	       array_push($returnAgency, $row2['agency_code']);
		   array_push($returnAgencyName,$row2['agency_name']);
			
		  foreach($yearArray as $year){
				if(isset($row2['uma_cy'.$year])){ // If selected year exist
					//strip commas out of data and convert to float
					$uma= (float) str_replace(',' , '',$row2['uma_cy'.$year]); 
				}
				else $uma=1;
				if(isset($row2['uml_cy'.$year])){ // If selected year exist
					$uml= (float)str_replace(',' , '',$row2['uml_cy'.$year]);
				}
				else $uml=1;
			 
			 $ur=$uml/$uma;
							  
			 array_push($returnUMA, $uma);
		  	 array_push($returnUML, $uml);
			 array_push($returnUR,$ur*100);
				   
       		}
			$uma=0;
			$uml=0;
			$ur=0;
	}
	
	
	
/*----------------------------------Display Tables---------------------------*/

	echo "<table id=\"table1\" ><tr ><th >Agency Name</th><th >Code</th>";
	
	//Loop to display all of years selected
	foreach($yearArray as $year){
			echo "<td>";
			echo"<table border=\"0\" class=\"table2\" ><tr><th colspan=\"3\">20".$year."</th></tr>";
			echo "<tr ><th>UMA</th><th>UML</th><th>UR</th></tr></table>";
			echo"</td>";
	}
	echo"</tr>";
		
		//Display agency row(s)
		$num=0;
		for($n=0; $n<$row_cnt2;$n++){
			 echo "<tr>";
	   		 echo "<td>" . $returnAgencyName[$n]  . "</td>";
	    	 echo "<td>" . $returnAgency[$n] . "</td>";
			
			for($k=0; $k<count($yearArray); $k++){
				
				echo "<td><table class=\"table2\" ><tr>";
				
				if ($returnUMA[$num] == 1){echo "<td> -- </td>";}
				else {echo "<td>" .number_format( $returnUMA[$num]) . "</td>";}
				
				if ($returnUML[$num] == 1){echo "<td> -- </td>";}
	   	 		else {echo "<td>" .number_format($returnUML[$num] ). "</td>";}
	    		echo "<td>" . number_format($returnUR[$num]) . "%</td></tr></table></td>";
	     		$num++;
			}
			
		 }
		echo "</tr>";
 	 
		//Display State row
			echo"<tr id=\"state\">
				<td>State</td><td></td>
				";
				for($j=0; $j<count($yearArray); $j++){
					echo"<td><table class=\"table2\" ><tr>";
					
					if ($avgUMA_A[$j] ==1){echo "<td> -- </td>";}
					else {echo"<td>".number_format($avgUMA_A[$j]). "</td>";}
					
					if ($avgUML_A[$j] ==1){echo "<td> -- </td>";}
					else {echo"<td>".number_format($avgUML_A[$j])."</td>";}
					
					echo"<td>".number_format($avgUR_A[$j])."%	</td></tr></table></td>";
				 }
			 echo"</tr>";
			 
		//Display National row
			echo"<tr id=\"national\">
				<td>National</td><td></td>
				";
				for($j=0; $j<count($yearArray); $j++){
					echo"<td><table class=\"table2\" ><tr>";
					
					if ($nationalavgUMA_A[$j] ==1){echo "<td> -- </td>";}
					else {echo"<td>".number_format($nationalavgUMA_A[$j]). "</td>";}
					//echo"<td>".$nationalavgUMA_A[$j]. "</td>";
					
					if ($nationalavgUML_A[$j] ==1){echo "<td> -- </td>";}
					else{ echo "<td>".number_format($nationalavgUML_A[$j])."</td>";}
					echo"<td>".number_format($nationalavgUR_A[$j])."%	</td></tr></table></td>";
				 }
					 
				echo"</tr></table>";
	
}




?>







