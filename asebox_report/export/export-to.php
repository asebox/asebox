<?php

  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");

<<<<<<< HEAD
	$param_list=array(
	    'QUERY',
      't'
	);
=======
  $param_list=array(
	    'QUERY',
      't',
      'title'
  );
>>>>>>> 3.1.0
	foreach ($param_list as $param)
	@$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];
	
	
	if ($t==0){
		$file_type = "msword";
		$file_ending = "doc";
	}
	elseif ($t==1){
		$file_type = "vnd.ms-excel";
		$file_ending = "xls";
<<<<<<< HEAD
	}
	
	header("Content-Type: application/$file_type");
	header("Content-Disposition: attachment; filename=asemon_report.$file_ending");
	header("Pragma: no-cache");
	header("Expires: 0");

	//get contents
	//define date for title
	$now_date = date('d-m-Y H:i');
	$title = "Asemon Excel Report $now_date";
=======
		//$file_type = "txt";
		//$file_ending = "txt";
	}
	
	header("Content-Type: application/$file_type");
	header("Content-Disposition: attachment; filename=".$ServerName."@".$title.".$file_ending");
	header("Pragma: no-cache");
	header("Expires: 0");
    
	//get contents
	//define date for title
	$now_date = date('d-m-Y H:i');
	//$title = "Asemon Excel Report $now_date";
	
echo '
<html>
<head>
<style type="text/css">
h1 {color:red;}
h2 {color:gray;}
p {color:blue;}
th {color:blue; font-weight:bold;}
td {color:blue;}
</style>
</head>	
<body>
<table>
<tr><h1>'.$title.'</h1></tr>
<tr><h2>'.$ServerName.'</h2></tr>
<tr>From: '.$StartTimestamp.' &nbsp&nbsp To: '.$EndTimestamp.'</tr></table>';	
>>>>>>> 3.1.0

	$result = sybase_query($QUERY,$pid);
	
	if ($result==false){ 
		sybase_close($pid); 
		$pid=0;
		include ("../connectArchiveServer.php");	
		echo "Error";
<<<<<<< HEAD
		return(0);
	}
	
	$sep = "\t";
	
	for ($i = 0; $i < sybase_num_fields($result); $i++) {
		$info = sybase_fetch_field($result,$i);
		echo $info->name . "\t";
	}
=======
		echo $QUERY;
		return(0);
	}
	
	echo "<table>";
	
	$sep = "\t";

	echo "<tr>";	
	for ($i = 0; $i < sybase_num_fields($result); $i++) {
		$info = sybase_fetch_field($result,$i);
		echo "<th>";
		echo $info->name . "\t";
		echo "</th>";
	}
	echo "</tr>";
>>>>>>> 3.1.0
	
	print("\n");

	$i = 0;
	while($row = sybase_fetch_row($result))
	{
<<<<<<< HEAD

		$schema_insert = "";
		for($j=0; $j<sybase_num_fields($result);$j++)
		{
=======
		$schema_insert = "";
		for($j=0; $j<sybase_num_fields($result);$j++)
		{
			$schema_insert .= "<td>";
>>>>>>> 3.1.0
			if(!isset($row[$j]))
				$schema_insert .= "NULL".$sep;
			elseif ($row[$j] != "")
				$schema_insert .= "$row[$j]".$sep;
			else
				$schema_insert .= "".$sep;
<<<<<<< HEAD
		}

		$schema_insert = str_replace($sep."$", "", $schema_insert);
=======
				
			$schema_insert .= "</td>";
		}

		$schema_insert = "<tr>".str_replace($sep."$", "", $schema_insert)."</tr>";
>>>>>>> 3.1.0
		$schema_insert .= "\t";
		print(trim($schema_insert));
		print "\n";
		$i++;
<<<<<<< HEAD
	}
=======
		
	}

	echo "</table></table></body></html>";

>>>>>>> 3.1.0
	return (true);
?>
