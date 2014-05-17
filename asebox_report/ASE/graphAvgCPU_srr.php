<?php

  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
	include ("../".$jpgraph_home."/src/jpgraph.php");
	include ("../".$jpgraph_home."/src/jpgraph_bar.php");	




		$result=sybase_query(
"
select ts=convert(varchar, Timestamp, 105), avgCpu=max(EngineNumber)+1 
from ".$ServerName."_Engines
where Timestamp >='".$StartTimestamp."'
  and Timestamp <='".$EndTimestamp."'
  and ContextSwitches>0
group by convert(varchar, Timestamp, 105)
",
 $pid);
		while (($row=sybase_fetch_array($result)))
		{
			$Timestamp[]= $row["ts"];
			$avgCpu[] = $row["avgCpu"];

			//print $row["title_id"]." : ".$row["price"]."     ";
		}


if (count($Timestamp)==0) 	exit("No values");
		
		// New graph with a background image and drop shadow
		$graph = new Graph(450,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);


		// Set title and subtitle
		$graph->title->Set("Avg Engines");
		$graph->subtitle->Set("From: ".$StartTimestamp." To: ".$EndTimestamp);

		// Use built in font
		$graph->title->SetFont(FF_FONT1,FS_BOLD);

		// Make the margin around the plot a little bit bigger
		// then default
		$graph->img->SetMargin(95,20,40,100);	

		// Display every 10:th datalabel
		/*
		$graph->xaxis->SetTickLabels($Timestamp);
		$nbVal=sizeof($Timestamp);
		if ( $nbVal>20 )
			$graph->xaxis->SetTextTickInterval($nbVal/20);
		else
		  $graph->xaxis->SetTextTickInterval(1);
		  		
		$graph->xaxis->SetFont(FF_COURIER,FS_NORMAL,8);
		$graph->xaxis->SetLabelAngle(45);

		// Create the line plot
		$b1 = new LinePlot($avgCpu);
		$b1 -> setcolor("blue");
		$b1 -> SetStyle("solid");
		$b1 -> setFillColor("blue");

		$graph -> yaxis->SetFont(FF_COURIER,FS_NORMAL,8);

		$graph -> yaxis -> scale -> setAutoMin(0);
		$graph -> yaxis -> scale -> setAutoMax(15);
		*/
		
		//srr
		$graph->xaxis->SetTickLabels($Timestamp);
		$graph->xaxis->SetTextTickInterval(1);
		$graph->xaxis->SetFont(FF_COURIER,FS_NORMAL,8);
		$graph->xaxis->SetLabelAngle(45);

		$b1 = new BarPlot($avgCpu);
		$b1 -> setcolor("red");
		$b1 -> setfillcolor("blue");

		$graph -> yaxis->SetFont(FF_COURIER,FS_NORMAL,8);

		$graph -> yaxis -> scale -> setAutoMin(0);
//		$graph -> yaxis -> scale -> setAutoMax(15);
		//srr
				
		// The order the plots are added determines who's ontop
		$graph->Add($b1);

        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
