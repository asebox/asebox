<?php

  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
	include ("../".$jpgraph_home."/src/jpgraph.php");
	include ("../".$jpgraph_home."/src/jpgraph_line.php");


		$result=sybase_query(
"select Ts=convert(varchar(5),Timestamp,108), activecnx=count(*) 
from ".$ServerName."_CnxActiv
where Timestamp >='".$StartTimestamp."'
and Timestamp <='".$EndTimestamp."'
group by Timestamp
order by Timestamp",
 $pid);
		while (($row=sybase_fetch_array($result)))
		{
			$Timestamp[]= $row["Ts"];
			$activecnx[] = $row["activecnx"];

		}

		
		// New graph with a background image and drop shadow
		$graph = new Graph(380,200,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);


		// Set title and subtitle
		$graph->title->Set("Connections");
		//$graph->subtitle->Set("From: ".$StartTimestamp." To: ".$EndTimestamp);

		// Use built in font
		$graph->title->SetFont(FF_FONT1,FS_BOLD);

		// Make the margin around the plot a little bit bigger
		// then default
		//$graph->img->SetMargin(95,20,40,100);	
		$graph->img->SetMargin(50,10,30,40);	


		// Display every 10:th datalabel
		$graph->xaxis->SetTickLabels($Timestamp);
		$nbVal=sizeof($Timestamp);
		if ( $nbVal>20 )
		  $graph->xaxis->SetTextTickInterval($nbVal/20);
		else
		  $graph->xaxis->SetTextTickInterval(1);
		  		

		// Create the Num_Active line plot
		$b1 = new LinePlot($activecnx);
		$graph->Add($b1);
		$b1 ->SetLegend("Active");

		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

		$graph -> yaxis -> scale -> setAutoMin(0);

        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
