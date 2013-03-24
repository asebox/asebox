<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
	include ("../".$jpgraph_home."/src/jpgraph.php");
	include ("../".$jpgraph_home."/src/jpgraph_line.php");
	include ("../".$jpgraph_home."/src/jpgraph_date.php");	



	$param_list=array(
		'WaitEventID',
		'ClassDesc',
		'EventDesc'
	);
	foreach ($param_list as $param)
		@$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];




  $query = "select ts=convert(varchar,Timestamp,116), 
              AvgWaitTime_ms=(1000.*WaitTime) / (1.*Waits)
              from ".$ServerName."_SysWaits
              where WaitEventID=".$WaitEventID."
              and Timestamp >= '".$StartTimestamp."' and Timestamp <= '".$EndTimestamp."'
              order by Timestamp";

//JpGraphError::Raise("Query=".$query);

    $result = sybase_query($query, $pid);		 		
		while (($row=sybase_fetch_array($result)))
		{
      $Timestamp[]=  date_format(date_create($row["ts"]),'U');
			$AvgWaitTime_ms[] = $row["AvgWaitTime_ms"];
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

		// Set title and subtitle
		$graph->title->Set($ClassDesc." : ".$EventDesc);

		// Use built in font
		$graph->title->SetFont(FF_FONT1,FS_BOLD);
		$graph->subtitle->Set("From: ".$StartTimestamp." To: ".$EndTimestamp);

		// Make the margin around the plot a little bit bigger
		// then default
		$graph->img->SetMargin(95,20,40,100);	

		  		
		$graph->xaxis->SetFont(FF_COURIER,FS_NORMAL,8);
		$graph->xaxis->SetLabelAngle(45);

		$b1 = new LinePlot($AvgWaitTime_ms, $Timestamp);
		$b1 -> setcolor("blue");
		$b1 -> SetLegend("AvgWaitTime_ms");


		$graph -> yaxis->SetFont(FF_COURIER,FS_NORMAL,8);

		$graph -> yaxis -> scale -> setAutoMin(0);
		
		// The order the plots are added determines who's ontop

		$graph->Add($b1);

		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
