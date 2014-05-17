<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
  include ("../".$jpgraph_home."/src/jpgraph.php");
  include ("../".$jpgraph_home."/src/jpgraph_line.php");


  $result=sybase_query(
          "select Ts=convert(varchar,Timestamp,3)+' '+convert(varchar,Timestamp,108),
          opers_scanned_per_s=(opers_scanned*1000.0)/Interval,
          opers_processed_per_s=(opers_processed*1000.0)/Interval
          from ".$ServerName."_RAOSTATS
          where Timestamp >='".$StartTimestamp."'
          and Timestamp <='".$EndTimestamp."'
          order by Timestamp",
          $pid);
 
  while (($row=sybase_fetch_array($result)))
  {
      $Timestamp[]= $row["Ts"];
      $opers_scanned_per_s[] = $row["opers_scanned_per_s"];
      $opers_processed_per_s[] = $row["opers_processed_per_s"];
  }


	
		
		// New graph with a background image and drop shadow
		$graph = new Graph(1000,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);


		// Set title and subtitle
		$graph->title->Set("RAO Activity");
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
		  		
		// Create the In line plot
		$in_gr = new LinePlot($opers_scanned_per_s);
		$in_gr -> setFillColor("blue");
		$in_gr ->SetLegend("Oper scanned / s");
		$graph->Add($in_gr);
		
		// Create the Outline plot
		$out_gr = new LinePlot($opers_processed_per_s);
		$out_gr -> setFillColor("green");
		$out_gr ->SetLegend("Oper processed / s");
		$graph->Add($out_gr);
		
		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

		
        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
