<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
  include ("../".$jpgraph_home."/src/jpgraph.php");
  include ("../".$jpgraph_home."/src/jpgraph_line.php");


  $result=sybase_query(
          "select Ts=convert(varchar,Timestamp,3)+' '+convert(varchar,Timestamp,108), CurrentCacheSize_Kb,MinCacheSize, MaxCacheSize
          from ".$ServerName."_IQAsaStat
          where Timestamp >='".$StartTimestamp."'
          and Timestamp <='".$EndTimestamp."'
          order by Timestamp",
          $pid);
  while (($row=sybase_fetch_array($result)))
	{
			$Timestamp[]= $row["Ts"];
			$CurrentCacheSize_Kb[] = $row["CurrentCacheSize_Kb"];
			$MinCacheSize[] = $row["MinCacheSize"];
			$MaxCacheSize[] = $row["MaxCacheSize"];

	}


	
		
		// New graph with a background image and drop shadow
		$graph = new Graph(1000,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);


		// Set title and subtitle
		$graph->title->Set("Catalog cache size");
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
		  		
		// Create the CurrentCacheSize_Kb line plot
		$cur_gr = new LinePlot($CurrentCacheSize_Kb);
		$cur_gr ->SetLegend("CurrentCacheSize_Kb");
		
		// Create the min size plot
		$min_gr = new LinePlot($MinCacheSize);
		$min_gr ->SetLegend("MinCacheSize");

		// Create the max size plot
		$max_gr = new LinePlot($MaxCacheSize);
		$max_gr ->SetLegend("MaxCacheSize");

		
		$graph -> yaxis-> scale -> setAutoMin(0);

		//$graph->Add($acc_gr);
		$graph->Add($cur_gr);
		$graph->Add($min_gr);
		$graph->Add($max_gr);
		
		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

		
        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
