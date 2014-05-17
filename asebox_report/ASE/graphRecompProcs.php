<?php
	
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
	include ("../".$jpgraph_home."/src/jpgraph.php");
	include ("../".$jpgraph_home."/src/jpgraph_line.php");




		$SQLText = "select Ts=convert(varchar,Timestamp,3)
		+' '+convert(varchar,Timestamp,108)
		,requests_s=avg(Requests*1000./Interval)
		,loads_s=avg(Loads*1000./Interval)
		,writes_s=avg(Writes*1000./Interval)
		from ".$ServerName."_ProcCache
		where Timestamp >='".$StartTimestamp."'
		and Timestamp <='".$EndTimestamp."'
		group by Timestamp
		order by Timestamp";
		
		$result=sybase_query($SQLText, $pid);
		 
		$_SESSION['SQLTEXT'] = $SQLText;
		$_SESSION['SQLTEXT'] = $_SESSION['SQLTEXT']."\n**********";
		
		
		while (($row=sybase_fetch_array($result)))
		{
			$Timestamp[]= $row["Ts"];
			$requests_s[] = $row["requests_s"];
			$loads_s[] = $row["loads_s"];
			$writes_s[] = $row["writes_s"];
		}

		if (count($Timestamp)==0) 	exit("No values");
		
		// New graph with a background image and drop shadow
		$graph = new Graph(1000,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);

		// Set title and subtitle
		$graph->title->Set("Stored procs activity (for Loads/s and Writes/s : use the right axis)");

		// Use built in font
		$graph->title->SetFont(FF_FONT1,FS_BOLD);
		$graph->subtitle->Set("From: ".$StartTimestamp." To: ".$EndTimestamp);

		// Make the margin around the plot a little bit bigger
		// then default
		$graph->img->SetMargin(90,40,40,100);	


		// Display every 10:th datalabel
		$graph->xaxis->SetTickLabels($Timestamp);
		$nbVal=sizeof($Timestamp);
		if ( $nbVal>20 )
		  $graph->xaxis->SetTextTickInterval($nbVal/20);
		else
		  $graph->xaxis->SetTextTickInterval(1);
		  		
		// Create the line plot
		$b1 = new LinePlot($requests_s);
		$b1 -> setcolor("blue");
		$graph->Add($b1);
		$b1 ->SetLegend("Requests / s");

		// Create the line plot
		$b2 = new LinePlot($loads_s);
		$b2 -> setcolor("red");
    	$graph->AddY2($b2);
		$b2 ->SetLegend("Loads / s");

		// Create the line plot
		$b3 = new LinePlot($writes_s);
		$b3 -> setcolor("yellow");
    	$graph->AddY2($b3);
		$b3 ->SetLegend("Writes / s");

		$graph -> yaxis -> scale -> setAutoMin(0);
		


        $graph->SetY2Scale("lin");
        $graph->y2axis->HideLine(false);
        $graph->y2axis->SetColor("red");
        $graph->y2axis->SetWeight(2);
        $graph->y2axis-> scale -> setAutoMin(0);
        $graph->y2axis->SetTickSide(SIDE_RIGHT);




		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
