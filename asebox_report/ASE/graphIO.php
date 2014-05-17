<?php

  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
	include ("../".$jpgraph_home."/src/jpgraph.php");
	include ("../".$jpgraph_home."/src/jpgraph_line.php");



		$result=sybase_query(
"select Timestamp=convert(varchar,Timestamp,3)+' '+convert(varchar,Timestamp,108),
TotIO_data_per_s= sum (convert(numeric(16,0),case when upper(LogicalName) not like '%LOG%' and upper(LogicalName) not like '%RAMFS%' and upper(LogicalName) not like '%TEMPDB%' and lower(LogicalName) not like 'vdisk%' then Reads+APFReads+Writes else 0 end))* 1000 / avg(Interval),
TotIO_log_per_s= sum (convert(numeric(16,0),case when lower(LogicalName) like '%log%' and lower(LogicalName) not like '%tempdb%' and lower(LogicalName) not like '%ramfs%' and lower(LogicalName) not like 'vdisk%' then Reads+APFReads+Writes else 0 end))* 1000 / avg(Interval),
TotIO_tempdb_per_s= sum (convert(numeric(16,0),case when upper(LogicalName) like '%RAMFS%' or upper(LogicalName) like '%TEMPDB%' or lower(LogicalName) like 'vdisk%' then Reads+APFReads+Writes else 0 end))* 1000 / avg(Interval)
from ".$ServerName."_DevIO
where Timestamp >='".$StartTimestamp."'
and Timestamp <='".$EndTimestamp."'
and LogicalName not like 'SYSDEV$[_][_]%' /* ignore crazy values for devices of archive db's */
group by Timestamp
order by Timestamp",
 $pid);
		while (($row=sybase_fetch_array($result)))
		{
			$Timestamp[]= $row["Timestamp"];
			$TotIO_data_per_s[] = $row["TotIO_data_per_s"];
			$TotIO_log_per_s[] = $row["TotIO_log_per_s"];
			$TotIO_tempdb_per_s[] = $row["TotIO_tempdb_per_s"];

		}


	
		
		// New graph with a background image and drop shadow
		$graph = new Graph(1000,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);


		// Set title and subtitle
		$graph->title->Set("Device IO (IO/s DATA, LOG, TEMPDB accumulated)");
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
		$data_gr = new LinePlot($TotIO_data_per_s);
		$data_gr -> setFillColor("blue");
		$data_gr ->SetLegend("Data");
		
		// Create the log line plot
		$log_gr = new LinePlot($TotIO_log_per_s);
		$log_gr -> setFillColor("red");
		$log_gr ->SetLegend("Log");

		// Create the tempdb line plot
		$tempdb_gr = new LinePlot($TotIO_tempdb_per_s);
		$tempdb_gr -> setFillColor("yellow");
		$tempdb_gr ->SetLegend("Tempdb");

		$acc_gr = new AccLinePlot(array($data_gr, $log_gr, $tempdb_gr));
		
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
