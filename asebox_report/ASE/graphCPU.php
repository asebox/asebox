<?php
	
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");
  include ("../".$jpgraph_home."/src/jpgraph.php");
  include ("../".$jpgraph_home."/src/jpgraph_line.php");
  
		$SQLText = "select Ts=convert(varchar,Timestamp,3)
		+' '+convert(varchar,Timestamp,108)
		,avgCpu=avg(convert(float,UserCPUTime*1000)/Interval*100)
		,avgCpuSystem=avg(convert(float,SystemCPUTime*1000)/Interval*100)
		,avgCpuIdle=avg(convert(float,IdleCPUTime*1000)/Interval*100)
		from ".$ServerName."_Engines
		where Timestamp >='".$StartTimestamp."'
		and Timestamp <='".$EndTimestamp."'
		and ContextSwitches>0
		group by Timestamp
		order by Timestamp";
		
		$result=sybase_query($SQLText, $pid);
		 
		
		while (($row=sybase_fetch_array($result)))
		{
			$Timestamp[]= $row["Ts"];
			$avgCpu[] = $row["avgCpu"];
			$avgCpuSystem[] = $row["avgCpuSystem"];
			$avgCpuIdle[] = $row["avgCpuIdle"];
		}

		if (count($Timestamp)==0) 	exit("No values");
		
		// New graph with a background image and drop shadow
		$graph = new Graph(1000,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);

		// Set title and subtitle
		$graph->title->Set("Avg CPU");

		// Use built in font
		$graph->title->SetFont(FF_FONT1,FS_BOLD);
//		$graph->title->SetColor('black');
		$graph->subtitle->Set($ServerName." From: ".$StartTimestamp." To: ".$EndTimestamp);

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

		// Create the line plot
		$b1 = new LinePlot($avgCpu);
		//$b1 -> setcolor("blue");
		//$b1 -> setFillColor("blue");
		$b1 ->SetLegend("CPU User");

		// Create the line plot
		$b2 = new LinePlot($avgCpuSystem);
		$b2 -> setcolor("green");
		$b2 -> setFillColor("green");
		$b2 ->SetLegend("CPU System");

		// Create the line plot
		$b3 = new LinePlot($avgCpuIdle);
		$b3 -> setcolor("yellow");
		$b3 -> setFillColor("yellow");
		$b3 ->SetLegend("CPU Idle");

		//$acc_gr = new AccLinePlot(array($b1));


		$graph -> yaxis -> scale -> setAutoMin(0);
		$graph -> yaxis -> scale -> setAutoMax(100);
		
		// The order the plots are added determines who's ontop
		//$graph->Add($acc_gr);
		$graph->Add($b1);

		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
