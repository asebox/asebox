<?php
include ("../ARContext_restore.php");
include ("../connectArchiveServer.php");	
include ("../".$jpgraph_home."/src/jpgraph.php");
include ("../".$jpgraph_home."/src/jpgraph_line.php");

$result=sybase_query(
"select Timestamp=convert(varchar(5),Timestamp,108), Num_Active, Max_used , ConfigLocks=Num_Free+Num_Active
from ".$ServerName."_MonConf
where Timestamp >='".$StartTimestamp."'
and Timestamp <='".$EndTimestamp."'
and Name='number of locks'",
 $pid);
		while (($row=sybase_fetch_array($result)))
		{
			$Timestamp[]= $row["Timestamp"];
			$Num_Active[] = $row["Num_Active"];
			$Max_used[] = $row["Max_used"];
			$ConfigLocks[] = $row["ConfigLocks"];

		}


if (count($Timestamp)==0) 	exit("No values");
		
		// New graph with a background image and drop shadow
		$graph = new Graph(380,200,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);


		// Set title and subtitle
		$graph->title->Set("Locks");
		//$graph->subtitle->Set("From: ".$StartTimestamp." To: ".$EndTimestamp);

		// Use built in font
		$graph->title->SetFont(FF_FONT1,FS_BOLD);

		// Make the margin around the plot a little bit bigger
		// then default
		//$graph->img->SetMargin(95,20,40,100);	
		$graph->img->SetMargin(70,10,30,40);	


		// Display every 10:th datalabel
		$graph->xaxis->SetTickLabels($Timestamp);
		$nbVal=sizeof($Timestamp);
		if ( $nbVal>20 )
		  $graph->xaxis->SetTextTickInterval($nbVal/20);
		else
		  $graph->xaxis->SetTextTickInterval(1);
		  		
		$graph->xaxis->SetFont(FF_COURIER,FS_NORMAL,8);
		$graph->xaxis->SetLabelAngle(45);
        $graph->xaxis->SetTickSide(SIDE_DOWN);

		// Create the Num_Active line plot
		$b1 = new LinePlot($Num_Active);
		$graph->Add($b1);
		$b1 ->SetLegend("Active");

		// Create the Max_used line plot
		$b2 = new LinePlot($Max_used);
		$graph->Add($b2);
		$b2 -> setcolor("red");
		$b2 -> SetFillColor("",false);
		$b2 ->SetLegend("Max");
		$b2 -> SetStyle(1);
		$graph -> yaxis->SetFont(FF_COURIER,FS_NORMAL,8);
        $graph->yaxis->SetTickSide(SIDE_LEFT);


		// Create the Max_used line plot
		$b3 = new LinePlot($ConfigLocks);
		$graph->Add($b3);
		$b3 -> SetFillColor("",false);
		$b3 -> setcolor("green");
		//$b3 ->SetLegend("Num locks configured");
		$b3 -> SetStyle(1);
		

		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

		$graph -> yaxis -> scale -> setAutoMin(0);

        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
