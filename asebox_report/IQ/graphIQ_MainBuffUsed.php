<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
  include ("../".$jpgraph_home."/src/jpgraph.php");
  include ("../".$jpgraph_home."/src/jpgraph_line.php");


  $result=sybase_query(
          "select Ts=convert(varchar,Timestamp,3)+' '+convert(varchar,Timestamp,108), MainIQBufferUsedCount,MainIQBufferLockedCount,MainIQBufferCapacityCount
          from ".$ServerName."_IQStatus
          where Timestamp >='".$StartTimestamp."'
          and Timestamp <='".$EndTimestamp."'
          order by Timestamp",
          $pid);
  while (($row=sybase_fetch_array($result)))
	{
			$Timestamp[]= $row["Ts"];
			$MainIQBufferUsedCount[] = $row["MainIQBufferUsedCount"];
			$MainIQBufferLockedCount[] = $row["MainIQBufferLockedCount"];
			$MainIQBufferCapacityCount[] = $row["MainIQBufferCapacityCount"];

	}


	
		
		// New graph with a background image and drop shadow
		$graph = new Graph(1000,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);


		// Set title and subtitle
		$graph->title->Set("Main buffers used (number of buffers)");
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
		  		
		
		// Create the Pct_used line plot
		$Used_gr = new LinePlot($MainIQBufferUsedCount);
		$Used_gr ->SetLegend("Used");
		$graph->Add($Used_gr);
		
		// Create the size plot
		$Locked_gr = new LinePlot($MainIQBufferLockedCount);
		$Locked_gr ->SetColor("red");
        $Locked_gr->SetFillGradient("red", 'white',GRAD_HOR);
		$Locked_gr ->SetLegend("Locked");
		$graph->Add($Locked_gr);

		// Create the Avail_Buff line plot
		$Avail_Buff_gr = new LinePlot($MainIQBufferCapacityCount);
		$Avail_Buff_gr ->SetColor("green");
		$Avail_Buff_gr ->SetLegend("Avail_Buff");
		$graph->Add($Avail_Buff_gr);
		
		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");
		
        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
