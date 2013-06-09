<?php

  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
	include ("../".$jpgraph_home."/src/jpgraph.php");
	include ("../".$jpgraph_home."/src/jpgraph_line.php");



		$result=sybase_query(
"select Timestamp=convert(varchar,Timestamp,3)+' '+convert(varchar,Timestamp,108),
bytes_sent = sum (convert(numeric(16,0), BytesSent))* 1000 / avg(Interval),
bytes_recv = sum (convert(numeric(16,0), BytesReceived))* 1000 / avg(Interval)
from ".$ServerName."_NetworkIO where Timestamp >='".$StartTimestamp."'
and Timestamp <='".$EndTimestamp."'
group by Timestamp
order by Timestamp",
 $pid);
		while (($row=sybase_fetch_array($result)))
		{
			$Timestamp[]= $row["Timestamp"];
			$bytes_sent[] = $row["bytes_sent"];
			$bytes_recv[] = $row["bytes_recv"];
		}

		// New graph with a background image and drop shadow
		$graph = new Graph(1000,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);


		// Set title and subtitle
		$graph->title->Set("Network IO");
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
		  		
		$graph->xaxis->SetFont(FF_COURIER,FS_NORMAL,8);
		$graph->xaxis->SetLabelAngle(45);
        $graph->xaxis->SetTickSide(SIDE_DOWN);

		// Create the data line plot
		$sent_gr = new LinePlot($bytes_sent);
		$sent_gr -> setFillColor("blue");
		$sent_gr ->SetLegend("Bytes Sent");
		
		// Create the log line plot
		$recv_gr = new LinePlot($bytes_recv);
		$recv_gr -> setFillColor("yellow");
		$recv_gr ->SetLegend("Bytes Recvd");


		$acc_gr = new AccLinePlot(array($sent_gr, $recv_gr));
		
		$graph -> yaxis->SetFont(FF_COURIER,FS_NORMAL,8);
        $graph->yaxis->SetTickSide(SIDE_LEFT);

		$graph->Add($acc_gr);
		
		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

//		$graph -> SetBackgroundGradient("azure2", "white", GRAD_VER, BGRAD_MARGIN);
		
        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
