<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
	include ("../".$jpgraph_home."/src/jpgraph.php");
	include ("../".$jpgraph_home."/src/jpgraph_line.php");



		$result=sybase_query(
"select Ts=convert(varchar,Timestamp,3)+' '+convert(varchar,Timestamp,108),
BlocksRead_s=sum(1000. * BlocksRead)/avg(Interval),
BlocksReadCached_s=sum(1000. * BlocksReadCached)/avg(Interval)
from ".$ServerName."_SQMR
where Timestamp >='".$StartTimestamp."'
and Timestamp <='".$EndTimestamp."'
group by Timestamp",
 $pid);
		while (($row=sybase_fetch_array($result)))
		{
			$Timestamp[]= $row["Ts"];
			$BlocksRead_s[] = $row["BlocksRead_s"];
			$BlocksReadCached_s[] = $row["BlocksReadCached_s"];

		}


	
		
		// New graph with a background image and drop shadow
		$graph = new Graph(1000,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);


		// Set title and subtitle
		$graph->title->Set("Blocks reads");
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
		$r_gr = new LinePlot($BlocksRead_s);
		$r_gr -> setFillColor("yellow");
		$r_gr ->SetLegend("BlocksRead / s");
		$graph->Add($r_gr);
		
		// Create the BlocksReadCached plot
		$rc_gr = new LinePlot($BlocksReadCached_s);
		$rc_gr -> setFillColor("green");
		$rc_gr ->SetLegend("BlocksReadCached / s");
		$graph->Add($rc_gr);
		
		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
