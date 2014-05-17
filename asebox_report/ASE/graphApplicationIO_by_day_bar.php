<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
	include ("../".$jpgraph_home."/src/jpgraph.php");
	include ("../".$jpgraph_home."/src/jpgraph_bar.php");	


$param_list=array(
	'Application1',
	'Application2'
);
foreach ($param_list as $param)
@$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];
	

			$result = sybase_query ("select convert(varchar,Timestamp,105) Timestamp, Application, sum(convert(float, LogicalReads)) LogicalReads, sum(convert(float,PhysicalReads)) PhysicalReads
			from ".$ServerName."_ProcActiv
			where Timestamp >='".$StartTimestamp."'        
			and Timestamp <='".$EndTimestamp."' 
			and Application like '%".$Application1."%' 
			group by convert(varchar,Timestamp,105), Application 
			", $pid);
		
			while (($row=sybase_fetch_array($result)))
			{
				$Timestamp[]= $row["Timestamp"];
				$Application[] = $row["Application"];
				$LogicalReads[] = $row["LogicalReads"];
				$PhysicalReads[] = $row["PhysicalReads"];
			}
		
        // New graph with a background image and drop shadow
        $graph = new Graph(1000,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);
	
			// Set title and subtitle
			$graph->title->Set("Application IO by date");
	
			// Use built in font
			$graph->title->SetFont(FF_FONT1,FS_BOLD);
	
			// Make the margin around the plot a little bit bigger
			// then default
			$graph->img->SetMargin(90,20,40,70);	
	
			$graph->xaxis->SetTickLabels($Timestamp);
			$graph->xaxis->SetTextTickInterval(1);
			$graph->xaxis->SetFont(FF_COURIER,FS_NORMAL,8);
			$graph->xaxis->SetLabelAngle(45);
	
			// Create the line plot
			
				$b1 = new BarPlot($LogicalReads);
				$b1->SetLegend($Application1);
				$b1 -> setcolor("blue");
				$b1 -> setFillColor("blue");

/*	
				$b2 = new BarPlot($LogicalReads2);
				$b2->SetLegend($Application2);
				$b2 -> setcolor("red");
				$b2 -> setFillColor("red");
*/
			
			//$bp = new GroupBarPlot(array($b1,$b2));
			$bp = new GroupBarPlot(array($b1));
			
			$graph -> yaxis->SetFont(FF_COURIER,FS_NORMAL,8);
	
			$graph -> yaxis -> scale -> setAutoMin(0);
			//$graph -> yaxis -> scale -> setAutoMax(1000000000);
			
			// The order the plots are added determines who's ontop
			$graph->Add($bp);
	
        $graph->graph_theme=null; // This fix bottom margin bad computation
        // Finally output the  image
        $graph->Stroke();

	?>
