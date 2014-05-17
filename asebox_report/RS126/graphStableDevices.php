<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
	include ("../".$jpgraph_home."/src/jpgraph.php");
	include ("../".$jpgraph_home."/src/jpgraph_line.php");




		$result=sybase_query("select Timestamp, 
		                 Capacity_Mb=sum(Total_segs), 
		                 Used_Mb=sum(Used_segs)
		from ".$ServerName."_DISKSPCE
		where Timestamp >='".$StartTimestamp."'
		and Timestamp <='".$EndTimestamp."' group by Timestamp", $pid);
		while (($row=sybase_fetch_array($result)))
		{
			$Timestamp[]= $row["Timestamp"];
			$CapacityAvg_Mb[] = $row["Capacity_Mb"];
			$UsedAvg_Mb[] = $row["Used_Mb"];

		}


	
		
		// New graph with a background image and drop shadow
		$graph = new Graph(1000,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);


		// Set title and subtitle
		$graph->title->Set("Stable devices usage");
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
		  		
		// Create the data line plot
		$used_gr = new LinePlot($UsedAvg_Mb);
		$used_gr ->SetLegend("Used Space");
		$graph->Add($used_gr);
		
		// Create the CapacityAvg_Mb line plot
		$cap = new LinePlot($CapacityAvg_Mb);
		$cap -> setcolor("red");
		$cap ->SetLegend("Max capacity (Mb)");
		$cap -> SetStyle(1);
		$graph->Add($cap);
		
		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

		
        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
