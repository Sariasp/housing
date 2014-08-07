<?php

require('config.php');
$con = mysqli_connect(DB_SERVER, DB_USER, DB_DATABASE, DB_PASSWORD) or die('Connection error');

$state = $_GET['s'];	
//$year=$_GET['y'];
$count=0;

//Display Agency drop-down after state is slected 
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

	
//Display Table of Results After recieving agency data
if (isset($_GET['a'])){
	$agency=$_GET['a'];
	$agencyArray=explode("-", $agency);
	$stateUMA=0;
	$stateUML=0;
	
	$years=$_GET['y'];
	$yearArray=explode("x", $years);
	
	$stateUMA_A = array();
	$stateUML_A = array();
	$avgUMA_A=array();
	$avgUML_A=array();
	$avgUR_A=array();
	
	$row_cnt = $result->num_rows;
	
	$resultsArr = array();
	
	while($row = mysqli_fetch_array($result)) {
		array_push($resultsArr,$row);
	}
			
		foreach($yearArray as $year){
			
			for($i=0;$i<count($resultsArr);$i++) {
				$row = $resultsArr[$i];
				
				$uma= (float) str_replace(',' , '',$row['uma_cy'.$year]); //strip commas out of data and convert to float
				$uml= (float)str_replace(',' , '',$row['uml_cy'.$year]);
		 
		 		 //Calculate sum of UMA and UML for the State
				 $stateUMA=$stateUMA+$uma; 
				 $stateUML=$stateUML+$uml;	
				// echo "Year: ".$year." UMA: ".$uma." StateUMA: ".$stateUMA. "</br/>";
				 
			  }
			
		array_push($stateUMA_A,$stateUMA); 
		array_push($stateUML_A,$stateUML);
		
		$stateUMA=0;
		$stateUML=0;
		}
	
		
	   //Calculate average UMA, UML and UR for State
	 for ($m=0;$m<count($yearArray);$m++) {
		 $avgUMA=number_format($stateUMA_A[$m] / $row_cnt,2); 
		array_push($avgUMA_A,$avgUMA);
		 
		 $avgUML=number_format($stateUML_A[$m] / $row_cnt,2); 
		array_push($avgUML_A,$avgUML);
		
		 
		 $avgUR=number_format($avgUML/$avgUMA, 2);
		array_push($avgUR_A,$avgUR);
	 }
	
		
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
				  //strip commas out of data and convert to float
				  $uma=  $row2['uma_cy'.$year];
				  $uml= $row2['uml_cy'.$year];
				  $ur= (float) str_replace(',' , '',$uml)/(float) str_replace(',' , '',$uma);
				  $ur=number_format($ur,2);
				  
				   array_push($returnUMA, $uma);
			   	   array_push($returnUML, $uml);
				   array_push($returnUR,$ur);
				   
       			}
		   }

	echo "<table id=\"table1\" ><tr ><th >Agency Name</th><th >Code</th>";
	
	//Loop to display all of years selected
		foreach($yearArray as $year){
			echo "<td>";
			echo"<table border=\"0\" class=\"table2\" ><tr><th colspan=\"3\">20".$year."</th></tr>";
			echo "<tr ><th>UMA</th><th>UML</th><th>UR</th></tr></table>";
			echo"</td>";
	}
	echo"</tr>";

$num=0;
		for($n=0; $n<$row_cnt2;$n++){
			 echo "<tr>";
	   		 echo "<td>" . $returnAgencyName[$n]  . "</td>";
	    	 echo "<td>" . $returnAgency[$n] . "</td>";
			
			for($k=0; $k<count($yearArray); $k++){
				
				echo "<td><table class=\"table2\" ><tr><td>" . $returnUMA[$num] . "</td>";
	   	 		echo "<td>" .$returnUML[$num] . "</td>";
	    		echo "<td>" . $returnUR[$num] . "</td></tr></table></td>";
	     		$num++;
			}
			
		 }
		echo "</tr>";
 	 

	echo"<tr id=\"state\">
		<td>State</td><td></td>
		";
		for($j=0; $j<count($yearArray); $j++){
			echo"<td><table class=\"table2\" ><tr><td>".$avgUMA_A[$j]. "</td>
			<td>".$avgUML_A[$j]."</td><td>".$avgUR_A[$j]."	</td></tr></table></td>";
		 }
		echo"</tr></table>";
	
}




?>







