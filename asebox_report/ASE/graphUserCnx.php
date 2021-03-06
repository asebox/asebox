<?php

  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
	include ("../".$jpgraph_home."/src/jpgraph.php");
	include ("../".$jpgraph_home."/src/jpgraph_line.php");


		$result=sybase_query(
"select Ts=convert(varchar,Timestamp,3)+' '+convert(varchar,Timestamp,108), Num_Active, Max_used 
from ".$ServerName."_MonConf
where Timestamp >='".$StartTimestamp."'
and Timestamp <='".$EndTimestamp."'
and Name='number of user connection'",
 $pid);
		while (($row=sybase_fetch_array($result)))
		{
			$Timestamp[]= $row["Ts"];
			$Num_Active[] = $row["Num_Active"];
			$Max_used[] = $row["Max_used"];

		}


		
		// New graph with a background image and drop shadow
		$graph = new Graph(1000,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);


		// Set title and subtitle
		$graph->title->Set("Number of user connection");
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
		  		
		// Create the Num_Active line plot
		$b1 = new LinePlot($Num_Active);
		$graph->Add($b1);
		$b1 ->SetLegend("Num connected");

		// Create the Max_used line plot
		$b2 = new LinePlot($Max_used);
		$b2 -> SetColor('red');
		$graph->Add($b2);
		$b2 -> SetFillColor('', false);
		$b2 ->SetLegend("Max connected");

		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

		$graph -> yaxis -> scale -> setAutoMin(0);

        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
