<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
  include ("../".$jpgraph_home."/src/jpgraph.php");
  include ("../".$jpgraph_home."/src/jpgraph_bar.php");
  
  $IQconnID = $_GET['IQconnID'];




		$result=sybase_query(
		   "select Ts=convert(varchar,ListTimestamp.Timestamp,3)+' '+convert(varchar,ListTimestamp.Timestamp,108), 
                           MinKBRelease, 
                           MaxKBRelease
                           from (select distinct Timestamp from ".$ServerName."_IQVersUse 
                          where Timestamp >='".$StartTimestamp."'
                          and Timestamp <='".$EndTimestamp."'
                          ) ListTimestamp  
                          left outer join ".$ServerName."_IQVersUse B on ListTimestamp.Timestamp = B.Timestamp and B.IQconnID=".$IQconnID."
                    order by ListTimestamp.Timestamp",
                    $pid);
		while (($row=sybase_fetch_array($result)))
		{
			$Timestamp[]= $row["Ts"];
			$MinKBRelease[] = $row["MinKBRelease"];
			$MaxKBRelease[] = $row["MaxKBRelease"];

		}


	
		
		// New graph with a background image and drop shadow
		$graph = new Graph(1000,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);


		// Set title and subtitle
		$graph->title->Set("Versionning");
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
		  		
		// Create the MinKBRelease bar plot
		$MinKBRelease_gr = new BarPlot($MinKBRelease);
		$MinKBRelease_gr ->SetLegend("MinKBRelease");
		
		// Create the MaxKBRelease bar plot
		$MaxKBRelease_gr = new BarPlot($MaxKBRelease);
		$MaxKBRelease_gr ->SetLegend("MaxKBRelease");

		$gb_gr = new GroupBarPlot(array($MinKBRelease_gr, $MaxKBRelease_gr));
       $gb_gr -> SetWidth(1); 
		

		$graph->Add($gb_gr);
		
		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

		
        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
