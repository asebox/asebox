<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
  include ("../".$jpgraph_home."/src/jpgraph.php");
  include ("../".$jpgraph_home."/src/jpgraph_line.php");	
  include ("../".$jpgraph_home."/src/jpgraph_bar.php");	
  include ("../".$jpgraph_home."/src/jpgraph_date.php");	


  // Convert $StartTimestamp and $EndTimestamp to format 116 according to the current dmy/mdy setting
  $result=sybase_query("select sts=convert(varchar,convert(datetime,'".$StartTimestamp."'),116),
                               ets=convert(varchar,case when convert(datetime,'".$EndTimestamp."')> getdate() then getdate() else convert(datetime,'".$EndTimestamp."') end,116)", $pid);
  $row = sybase_fetch_array($result);
  $sts = $row["sts"];
  $ets = $row["ets"];


		$result=sybase_query("select ts=convert(varchar,Timestamp,116),
		                 Memory_in_Use_Mb= 1.*Memory_in_Use/(1024*1024)
		from ".$ServerName."_RSMem
		where Timestamp >='".$StartTimestamp."'
		and Timestamp <='".$EndTimestamp."' 
		order by Timestamp", 
		$pid);

        $Timestamp[]= date_format(date_create($sts),'U');
        $Memory_in_Use_Mb[] = null;
        
        while (($row=sybase_fetch_array($result)))
		{
			$Timestamp[]= date_format(date_create($row["ts"]),'U');
			$Memory_in_Use_Mb[] = $row["Memory_in_Use_Mb"];

		}
        $Timestamp[]= date_format(date_create($ets),'U');
        $Memory_in_Use_Mb[] = null;


	
		
        // New graph with a background image and drop shadow
        $graph = new Graph(1000,300,"auto");
        $graph->SetScale("datlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);

        $graph->xaxis->scale->SetTimeAlign( MINADJ_1,  MINADJ_1  ); 
        $graph->SetTickDensity( TICKD_NORMAL, TICKD_SPARSE );
        $graph->xaxis->scale->SetDateFormat( 'd/m/Y H:i ' );


		// Set title and subtitle
		$graph->title->Set("Memory Usage");
		$graph->subtitle->Set("From: ".$StartTimestamp." To: ".$EndTimestamp);

		// Use built in font
		$graph->title->SetFont(FF_FONT1,FS_BOLD);

		// Make the margin around the plot a little bit bigger
		// then default
		$graph->img->SetMargin(95,20,40,100);	


		// Create the data line plot
		$used_gr = new LinePlot($Memory_in_Use_Mb,$Timestamp);
		$used_gr ->SetLegend("Mem_In_Use (Mb)");
		
		$graph -> yaxis-> scale -> setAutoMin(0);
        $graph->yaxis->SetTickSide(SIDE_LEFT);


		$graph->Add($used_gr);
		
		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

		
        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
