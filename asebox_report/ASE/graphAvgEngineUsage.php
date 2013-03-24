<?php

  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
	include ("../".$jpgraph_home."/src/jpgraph.php");
	include ("../".$jpgraph_home."/src/jpgraph_bar.php");	




		$result=sybase_query(
"select EngineNumber,userCPU_pct=sum(UserCPUTime*1000.)/sum(Interval)*100
from ".$ServerName."_Engines
where Timestamp >='".$StartTimestamp."'
and Timestamp <='".$EndTimestamp."'
group by EngineNumber
having sum(ContextSwitches)>0 -- for online engines only
order by EngineNumber",
 $pid);
		while (($row=sybase_fetch_array($result)))
		{
			$Engines[]= $row["EngineNumber"];
			$avgCpu[] = $row["userCPU_pct"];

		}


	
		
		// New graph with a background image and drop shadow
		$graph = new Graph(450,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);


		// Set title and subtitle
		$graph->title->Set("% Engines Usage");
		$graph->subtitle->Set("From: ".$StartTimestamp." To: ".$EndTimestamp);

		// Use built in font
		$graph->title->SetFont(FF_FONT1,FS_BOLD);

		// Make the margin around the plot a little bit bigger
		// then default
		$graph->img->SetMargin(40,20,40,40);	


		$graph->xaxis->SetTickLabels($Engines);
		$graph->xaxis->SetTextTickInterval(1);
		$graph->xaxis->SetFont(FF_COURIER,FS_NORMAL,8);
//		$graph->xaxis->SetLabelAngle(90);
        $graph->xaxis->SetTickSide(SIDE_DOWN);
        $graph->xaxis->SetTickSize(0,0);

		// Create the line plot
		$b1 = new BarPlot($avgCpu);
		$b1 -> setcolor("blue");
//		$b1 -> SetStyle("solid");
//		$b1 -> setFillColor("blue");

		$graph -> yaxis->SetFont(FF_COURIER,FS_NORMAL,8);
        $graph->yaxis->SetTickSide(SIDE_LEFT);

		$graph -> yaxis -> scale -> setAutoMin(0);
		$graph -> yaxis -> scale -> setAutoMax(100);
		
		// The order the plots are added determines who's ontop
		$graph->Add($b1);

        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
