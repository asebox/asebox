<?php

  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");

	$param_list=array(
	    'QUERY',
      't'
	);
	foreach ($param_list as $param)
	@$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];
	
	
	if ($t==0){
		$file_type = "msword";
		$file_ending = "doc";
	}
	elseif ($t==1){
		$file_type = "vnd.ms-excel";
		$file_ending = "xls";
	}
	
	header("Content-Type: application/$file_type");
	header("Content-Disposition: attachment; filename=asemon_report.$file_ending");
	header("Pragma: no-cache");
	header("Expires: 0");

	//get contents
	//define date for title
	$now_date = date('d-m-Y H:i');
	$title = "Asemon Excel Report $now_date";

	$result = sybase_query($QUERY,$pid);
	
	if ($result==false){ 
		sybase_close($pid); 
		$pid=0;
		include ("../connectArchiveServer.php");	
		echo "Error";
		return(0);
	}
	
	$sep = "\t";
	
	for ($i = 0; $i < sybase_num_fields($result); $i++) {
		$info = sybase_fetch_field($result,$i);
		echo $info->name . "\t";
	}
	
	print("\n");

	$i = 0;
	while($row = sybase_fetch_row($result))
	{

		$schema_insert = "";
		for($j=0; $j<sybase_num_fields($result);$j++)
		{
			if(!isset($row[$j]))
				$schema_insert .= "NULL".$sep;
			elseif ($row[$j] != "")
				$schema_insert .= "$row[$j]".$sep;
			else
				$schema_insert .= "".$sep;
		}

		$schema_insert = str_replace($sep."$", "", $schema_insert);
		$schema_insert .= "\t";
		print(trim($schema_insert));
		print "\n";
		$i++;
	}
	return (true);
?>
