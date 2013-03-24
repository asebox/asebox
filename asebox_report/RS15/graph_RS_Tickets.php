<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
	include ("../".$jpgraph_home."/src/jpgraph.php");
	include ("../".$jpgraph_home."/src/jpgraph_line.php");	
	include ("../".$jpgraph_home."/src/jpgraph_bar.php");	
	include ("../".$jpgraph_home."/src/jpgraph_date.php");	


$param_list=array(
	'DSIserver',
	'DSIdatabase',
	'origin'
);
foreach ($param_list as $param)
@$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];
	

  // Convert $StartTimestamp and $EndTimestamp to format 116 according to the current dmy/mdy setting
  $result=sybase_query("select sts=convert(varchar,convert(datetime,'".$StartTimestamp."'),116),
                               ets=convert(varchar,case when convert(datetime,'".$EndTimestamp."')> getdate() then getdate() else convert(datetime,'".$EndTimestamp."') end,116)", $pid);
  $row = sybase_fetch_array($result);
  $sts = $row["sts"];
  $ets = $row["ets"];

	
			$Timestamp[]= date_format(date_create($sts),'U');
			$primToExec_ms[] = null;
			$execToDist_ms[] = null;
			$distToRsi_ms[] = null;
			$rsiToDsi_ms[] = null;
			$dsiToRepl_ms[] = null;

    // retreive values
    $result=sybase_query("
      -- select Ts=convert(varchar,dt,3)+' '+convert(varchar,dt,108),
      select ts=convert(varchar,rdb_t,116),
      primToExec_ms = isnull(datediff(ms,pdb_t,exec_t), 0),
      execToDist_ms = isnull(case when dist_t='' then 0 else datediff(ms,exec_t,dist_t) end, 0),
      distToRsi_ms  = isnull(case when dist_t='' or rsi_t='' then 0 else datediff(ms,dist_t,rsi_t) end, 0),
      rsiToDsi_ms   = isnull(case when rsi_t='' then datediff(ms,dist_t,dsi_t) else datediff(ms,rsi_t,dsi_t) end, 0),
      dsiToRepl_ms  = isnull(datediff(ms,dsi_t,rdb_t), 0)
      from ".$DSIserver."_RSTckHist
      where rdb = '".$DSIdatabase."' 
       and rdb_t >='".$StartTimestamp."'
       and rdb_t <='".$EndTimestamp."'
      order by rdb_t",
      $pid);

	
		while (($row=sybase_fetch_array($result)))
		{
			$Timestamp[]= date_format(date_create($row["ts"]),'U');
			$primToExec_ms[] = $row["primToExec_ms"];
			$execToDist_ms[] = $row["execToDist_ms"];
			$distToRsi_ms[] = $row["distToRsi_ms"];
			$rsiToDsi_ms[] = $row["rsiToDsi_ms"];
			$dsiToRepl_ms[] = $row["dsiToRepl_ms"];

		}	

			$Timestamp[]= date_format(date_create($ets),'U');
			$primToExec_ms[] = null;
			$execToDist_ms[] = null;
			$distToRsi_ms[] = null;
			$rsiToDsi_ms[] = null;
			$dsiToRepl_ms[] = null;

  //JpGraphError::Raise("Ts[0]=".$Timestamp[0] );
		
		// New graph with a background image and drop shadow
		$graph = new Graph(1000,300,"auto");
        $graph->SetScale("datlin"); 
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);

        $graph->xaxis->scale->SetTimeAlign( MINADJ_1, MINADJ_1   );
        $graph->SetTickDensity( TICKD_NORMAL, TICKD_SPARSE );


		// Set title and subtitle
		$graph->title->Set("RS Tickets history (ms)");

		// Use built in font
		$graph->title->SetFont(FF_FONT1,FS_BOLD);

		// Make the margin around the plot a little bit bigger
		// then default
        $graph->img->SetMargin(95,50,40,100); 


		// Display every 10:th datalabel

		/*
		$graph->xaxis->SetTickLabels($Timestamp);

		$nbVal=sizeof($Timestamp);
		if ( $nbVal>20 )
		  $graph->xaxis->SetTextTickInterval($nbVal/20);
		else
		  $graph->xaxis->SetTextTickInterval(1);
    */
    		  		
    $graph->xaxis->scale->SetDateFormat( 'd/m/Y H:i ' );
    
		// Create the primToExec_ms BAR plot
		$b1 = new BarPlot($primToExec_ms, $Timestamp);
		$b1 -> setcolor("chartreuse");
		$b1 -> setFillColor("chartreuse");
		$b1 -> SetLegend("primToExec_ms");
    $b1 -> SetWidth(5); 

		// Create the execToDist_ms BAR plot
		$b2 = new BarPlot($execToDist_ms, $Timestamp);
		$b2 -> setcolor("brown");
		$b2 -> setFillColor("brown");
		$b2 -> SetLegend("execToDist_ms");
    $b2 -> SetWidth(5); 

		// Create the distToRsi_ms BAR plot
		$b3 = new BarPlot($distToRsi_ms, $Timestamp);
		$b3 -> setcolor("cadetblue1");
		$b3 -> setFillColor("cadetblue1");
		$b3 -> SetLegend("distToRsi_ms");
    $b3 -> SetWidth(5); 

		// Create the rsiToDsi_ms BAR plot
		$b4 = new BarPlot($rsiToDsi_ms, $Timestamp);
		$b4 -> setcolor("darkblue");
		$b4 -> setFillColor("darkblue");
		$b4 -> SetLegend("rsiToDsi_ms");
    $b4 -> SetWidth(5); 

		// Create the dsiToRepl_ms BAR plot
		$b5 = new BarPlot($dsiToRepl_ms, $Timestamp);
		$b5 -> setcolor("lightsalmon1");
		$b5 -> setFillColor("lightsalmon1");
		$b5 -> SetLegend("dsiToRepl_ms");
    $b5 -> SetWidth(5); 


		// The order the plots are added determines who's ontop
        $accbar = new AccLinePlot(array($b1,$b2, $b3, $b4, $b5)); 

		$graph->Add($accbar);
		//$graph->legend->SetReverse();

		
        $graph -> yaxis-> scale -> setAutoMin(0);

		
		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.02, 0.9, "rigth", "top");

		
        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
