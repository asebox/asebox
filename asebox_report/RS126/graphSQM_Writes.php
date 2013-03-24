<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
	include ("../".$jpgraph_home."/src/jpgraph.php");
	include ("../".$jpgraph_home."/src/jpgraph_line.php");


		$result=sybase_query(
"select Ts=convert(varchar,Timestamp,3)+' '+convert(varchar,Timestamp,108),
BlocksWrites_s=sum(1000. * BlocksWritten)/avg(Interval),
BlocksFullWrite_s=sum(1000. * BlocksFullWrite)/avg(Interval)
from ".$ServerName."_SQM
where Timestamp >='".$StartTimestamp."'
and Timestamp <='".$EndTimestamp."'
group by Timestamp",
 $pid);
		while (($row=sybase_fetch_array($result)))
		{
			$Timestamp[]= $row["Ts"];
			$BlocksWrites_s[] = $row["BlocksWrites_s"];
			$BlocksFullWrite_s[] = $row["BlocksFullWrite_s"];

		}


	
		
		// New graph with a background image and drop shadow
		$graph = new Graph(1000,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);


		// Set title and subtitle
		$graph->title->Set("Blocks writes");
		$graph->subtitle->Set("From: ".$StartTimestamp." To: ".$EndTimestamp);

		// Use built in font
		$graph->title->SetFont(FF_FONT1,FS_BOLD);

		// Make the margin around the plot a little bit bigger
		// then default
		$graph->img->SetMargin(95,20,40,100);	


		// Display every 10:th datalabel
		$graph->xaxis->SetTickLabels($Timestamp);
		$nbVal=sizeof($Timestamp);
		if ( $nbVal>20 )
		  $graph->xaxis->SetTextTickInterval($nbVal/20);
		else
		  $graph->xaxis->SetTextTickInterval(1);
		  		
		// Create the BlocksRead plot
		$w_gr = new LinePlot($BlocksWrites_s);
		$w_gr -> setFillColor("yellow");
		$w_gr ->SetLegend("BlocksWrites / s");
		$graph->Add($w_gr);
		
		// Create the BlocksReadCached plot
		$wf_gr = new LinePlot($BlocksFullWrite_s);
		$wf_gr -> setFillColor("green");
		$wf_gr ->SetLegend("BlocksFullWrite / s");
		$graph->Add($wf_gr);
		
		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
