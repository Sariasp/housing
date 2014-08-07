
<?php
$state = $_GET['s'];
$year = $_GET['year'];
$con = mysqli_connect('localhost', 'root', 'cbppdb','housing') or die('Connection error');
$query= 'SELECT * FROM sheet1 WHERE SUBSTRING(agency_code,1,2 ) = "'.$state.'"';
$result = mysqli_query($con,$query);

echo "<select>";

while($row = mysqli_fetch_array($result)) {
  echo "<option>". $row['agency_name'] . "</option>";
   }
echo "</select>";








/*



if(!in_array("all", $year)){
	$query .=  " AND (";
	for($i=0; $i<count($year);$i++){
		$state[$i]=	$state	
	}
	
}





echo "<table border='1'>
<tr><th>Agency Name</th><th>Code</th><th>UML</th><th>UMA</th><th>Utilization Rate</th></tr>
";

while($row = mysqli_fetch_array($result)) {
  echo "<tr>";
  echo "<td>" . $row['agency_name'] . "</td>";
  echo "<td>" . $row['agency_code'] . "</td>";
  echo "<td>" . $row['Age'] . "</td>";
  echo "<td>" . $row['Hometown'] . "</td>";
  echo "<td>" . $row['Job'] . "</td>";
  echo "</tr>";
}
echo "</table>";

mysqli_close($con);
*/
?>









