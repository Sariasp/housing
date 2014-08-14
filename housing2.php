<?php
            
			
			
			
			
require('config.php');
$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE) or die('Connection error');

$state = $_GET['s'];	

//Display Agency drop-down after state is selected 
$query= 'SELECT * FROM sheet1 WHERE SUBSTRING(agency_code,1,2 ) = "'.$state.'" ORDER BY agency_name';
$result = mysqli_query($con,$query);
if(!isset($_GET['a'])){

	echo "<select class=\"multiselect2\" name=\"agencySelector\" id=\"agencySelector\" onchange=\"agencyInfo(this.value)\"  multiple=\"multiple\"> 
			";
	
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
	$stateUR;
	$stateUR_A=array();
		
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
				 $stateUMA=$stateUMA+$uma; 
				 }
				else $stateUMA="--";
				if(isset($row['uml_cy'.$year])){ // If selected year exist
					$uml= (float)str_replace(',' , '',$row['uml_cy'.$year]);
					$stateUML=$stateUML+$uml;	

				}
				else $stateUML="--";
		 		 //Calculate sum of UMA and UML for the State
				
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
		/* $avgUMA=$stateUMA_A[$m] / $row_cnt; 
		array_push($avgUMA_A,$avgUMA);
		 
		 $avgUML=$stateUML_A[$m] / $row_cnt; 
		array_push($avgUML_A,$avgUML);
		
		 if($avgUML==1 ||$avgUMA==1){
			 $avgUR="--";
		 }
		 else $avgUR=($avgUML/$avgUMA*100);
		array_push($avgUR_A,$avgUR);
		*/
		if ($stateUML_A[$m] =="--" || $stateUMA_A[$m] =="--"){
			$stateUR="--";
		}
		else{
		$stateUR=($stateUML_A[$m]/$stateUMA_A[$m]*100);
		}
		array_push($stateUR_A,$stateUR);
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
	$nationalUR_A=array();
	
	$nationalResultsArr = array();
	while($row = mysqli_fetch_array($nationalResult)) {
		array_push($nationalResultsArr,$row);
	}
			
		foreach($yearArray as $year){
			
			foreach($yearArray as $year){
			
			for($i=0;$i<count($nationalResultsArr);$i++) {
				$row = $nationalResultsArr[$i];
				
				if(isset($row['uma_cy'.$year])){ // If selected year exist
					//strip commas out of data and convert to float
					$uma= (float) str_replace(',' , '',$row['uma_cy'.$year]); 
					$nationalUMA=$nationalUMA+$uma;
				}
				else $nationalUMA="--";
				if(isset($row['uml_cy'.$year])){ // If selected year exist
					$uml= (float)str_replace(',' , '',$row['uml_cy'.$year]);
					$nationalUML=$nationalUML+$uml;	
				}
				else $nationalUML="--";


 				//Calculate sum of UMA and UML for the Nation
				  
				 
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
		/* $nationalavgUMA=$nationalUMA_A[$m] / $national_row_cnt; 
		array_push($nationalavgUMA_A,$nationalavgUMA);
		 
		 $nationalavgUML=$nationalUML_A[$m] / $national_row_cnt; 
		array_push($nationalavgUML_A,$nationalavgUML);
		
		 
		  if($nationalavgUMA==1 || $nationalUML==1){
			 $nationalavgUR="--";
			}
			else{ $nationalavgUR=($nationalavgUML/$nationalavgUMA)*100;}
		
	 array_push($nationalavgUR_A,$nationalavgUR);
		 
	 }
	*/
	if ($nationalUML_A[$m] =="--" || $nationalUMA_A[$m] =="--"){
			$nationalUR="--";
		}
		else{
		$nationalUR=($nationalUML_A[$m]/$nationalUMA_A[$m]*100);
		}
		array_push($nationalUR_A,$nationalUR);

		}
	
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
			 
			  
			  if($uma==1 || $uml==1){
			 $ur="--";
			}
			else $ur=($uml/$uma*100);
			
			array_push($returnUR,$ur);
										  
			 array_push($returnUMA, $uma);
		  	 array_push($returnUML, $uml);
		
				   
       		}
			$uma=0;
			$uml=0;
			$ur=0;
	}
	
	
/*----------------------------------Display Tables---------------------------*/

	echo "<table id=\"table1\" ><tr class=\"header\"><th >Agency Name</th><th >Code</th>";
	
	//Loop to display all of years selected
	foreach($yearArray as $year){
			echo "<td>";
			echo"<table border=\"0\" class=\"table2\" ><tr><th colspan=\"3\">20".$year."</th></tr>";
			echo "<tr ><th>Authorized Vouchers</th><th>Leased Vouchers</th><th>Utilization Rate</th></tr></table>";
			echo"</td>";
	}
	echo"</tr>";
		
					
	//Display agency row(s)
		$num=0;
		for($n=0; $n<$row_cnt2;$n++){
			 echo "<tr>";
	   		 echo "<td >" . $returnAgencyName[$n]  . "</td>";
	    	 echo "<td>" . $returnAgency[$n] . "</td>";
			
			for($k=0; $k<count($yearArray); $k++){
				
				echo "<td><table class=\"table2\" ><tr>";
				
				if ($returnUMA[$num] == 1){echo "<td> -- </td>";}
				else {echo "<td>" .number_format( $returnUMA[$num]) . "</td>";}
				
				if ($returnUML[$num] == 1){echo "<td> -- </td>";}
	   	 		else {echo "<td>" .number_format($returnUML[$num] ). "</td>";}
	    	
				
				if($returnUR[$num]!="--")$returnUR[$num]=number_format($returnUR[$num]);
					echo"<td>".$returnUR[$num]."%	</td></tr></table></td>";
				
				
	     		$num++;
			}
			
		 }
		   echo "</tr>";
 	 
		//Display State row
			echo"<tr class=\"highlight\">
				<td>State</td><td></td>
				";
				for($j=0; $j<count($yearArray); $j++){
					echo"<td><table class=\"table2\" ><tr>";
					
					if ($stateUMA_A[$j] =="--"){echo "<td> -- </td>";}
					else {echo"<td>".number_format($stateUMA_A[$j]). "</td>";}
					
					if ($stateUML_A[$j] =="--"){echo "<td> -- </td>";}
					else {echo"<td>".number_format($stateUML_A[$j])."</td>";}
					
					if($stateUR_A[$j]!="--"){$stateUR_A[$j]=number_format($stateUR_A[$j]);}
					echo"<td>".$stateUR_A[$j]."%	</td></tr></table></td>";
				 }
			 echo"</tr>";
			 
		//Display National row
			echo"<tr class=\"highlight\">
				<td>National</td><td></td>
				";
				for($j=0; $j<count($yearArray); $j++){
					echo"<td><table class=\"table2\" ><tr>";
					
					if ($nationalUMA_A[$j] =="--"){echo "<td> -- </td>";}
					else {echo"<td>".number_format($nationalUMA_A[$j]). "</td>";}
					//echo"<td>".$nationalavgUMA_A[$j]. "</td>";
					
					if ($nationalUML_A[$j] =="--"){echo "<td> -- </td>";}
					else{ echo "<td>".number_format($nationalUML_A[$j])."</td>";}
					
					if($nationalUR_A[$j]!="--"){$nationalUR_A[$j]=number_format($nationalUR_A[$j]);}
					echo"<td>".$nationalUR_A[$j]."%	</td></tr></table></td>";
				 
				 }
					 
				echo"</tr>";
	
				
				
	 echo"</table>";
	
}




?>







