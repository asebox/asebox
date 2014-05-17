<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
	include ("../".$jpgraph_home."/src/jpgraph.php");
	include ("../".$jpgraph_home."/src/jpgraph_line.php");
	include ("../".$jpgraph_home."/src/jpgraph_bar.php");	
	include ("../".$jpgraph_home."/src/jpgraph_date.php");	



	$param_list=array(
		'Device'
	);
	foreach ($param_list as $param)
		@$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];




  $query = "select ts=convert(varchar,Timestamp,116), 
              Reads_s    =  convert(numeric(16,0), sum(Reads* 1000.) / avg(Interval)),
              APFReads_s =  convert(numeric(16,0), sum(APFReads * 1000.) / avg(Interval)),
              Writes_s   =  convert(numeric(16,0), sum(Writes* 1000.) / avg(Interval))
              from ".$ServerName."_DevIO
              where LogicalName like '".$Device."'
              and Timestamp >= '".$StartTimestamp."' and Timestamp <= '".$EndTimestamp."'
              group by Timestamp
              order by Timestamp";

//JpGraphError::Raise("Query=".$query);

    $previousDT=0;
    $result = sybase_query($query, $pid);		 		
		while (($row=sybase_fetch_array($result)))
		{
			$curDT = date_format(date_create($row["ts"]),'U');
			$diff =  $curDT - $previousDT;
			if (($previousDT!=0)&&($diff>120)) {
			  	// No data in between two samples, add 0 values to the graph
			    //JpGraphError::Raise("previousDT=".$previousDT." curDT= ". $curDT. " diff=".$diff );
          $Timestamp[]=  $previousDT+30;
    			$Reads_s[] = 0;
    			$APFReads_s[] = 0;
    			$Writes_s[] = 0;
			    
          $Timestamp[]=  $curDT-30;
    			$Reads_s[] = 0;
    			$APFReads_s[] = 0;
    			$Writes_s[] = 0;
      }
      $Timestamp[]=  $curDT;
			$Reads_s[] = $row["Reads_s"];
			$APFReads_s[] = $row["APFReads_s"];
			$Writes_s[] = $row["Writes_s"];
			
			$previousDT = $curDT;
		}

		if (count($Timestamp)==0) 
		  JpGraphError::Raise("No values");
		
		// New graph with a background image and drop shadow
		$graph = new Graph(1000,300,"auto");
        $graph->SetScale("datlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);
    $graph->xaxis->scale->SetTimeAlign( MINADJ_1,  MINADJ_1  ); 
    $graph->SetTickDensity( TICKD_NORMAL, TICKD_SPARSE );
		$graph->xaxis->SetFont(FF_COURIER,FS_NORMAL,8);
		$graph->xaxis->SetLabelAngle(45);
    $graph->xaxis->scale->SetDateFormat( 'd/m/y H:i ' );
        $graph->xaxis->SetTickSide(SIDE_DOWN);

		// Set title and subtitle
		$graph->title->Set("Device I/O");

		// Use built in font
		$graph->title->SetFont(FF_FONT1,FS_BOLD);
		$graph->subtitle->Set("From: ".$StartTimestamp." To: ".$EndTimestamp);

		// Make the margin around the plot a little bit bigger
		// then default
		$graph->img->SetMargin(95,20,40,100);	

		  		
		$graph->xaxis->SetFont(FF_COURIER,FS_NORMAL,8);
		$graph->xaxis->SetLabelAngle(45);

		$b1 = new LinePlot($Reads_s, $Timestamp);
		$b1 -> setcolor("blue");
		$b1 -> SetLegend("Reads_s");


		$b2 = new LinePlot($APFReads_s, $Timestamp);
		$b2 -> setcolor("green");
		$b2 -> SetLegend("APFReads_s");

		$b3 = new LinePlot($Writes_s, $Timestamp);
		$b3 -> setcolor("red");
		$b3 -> SetLegend("Writes_s");



		$graph -> yaxis->SetFont(FF_COURIER,FS_NORMAL,8);

		$graph -> yaxis -> scale -> setAutoMin(0);
        $graph->yaxis->SetTickSide(SIDE_LEFT);
		
		// The order the plots are added determines who's ontop

		$graph->Add($b1);
		$graph->Add($b2);
		$graph->Add($b3);

		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();
?>
