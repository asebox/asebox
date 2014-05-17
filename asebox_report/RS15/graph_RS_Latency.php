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


        $result=sybase_query(
        "select ts=convert(varchar,Timestamp,116),
         latency=1.*latency/1000
         from ".$DSIserver."_RSLstCmt
         where dbname = '".$DSIdatabase."' 
          and origin=".$origin."
          and Timestamp >='".$StartTimestamp."'
          and Timestamp <='".$EndTimestamp."'
         order by Timestamp",
         $pid);

		$Timestamp[]= date_format(date_create($sts),'U');
		$latency[] = null;


		while (($row=sybase_fetch_array($result)))
		{
			$Timestamp[]=  date_format(date_create($row["ts"]),'U');
			$latency[] = $row["latency"];
		}	
		
		$Timestamp[]= date_format(date_create($ets),'U');
		$latency[] = null;
			
					
		// New graph with a background image and drop shadow
		$graph = new Graph(1000,300,"auto");
        $graph->SetScale("datlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);

        $graph->xaxis->scale->SetTimeAlign( MINADJ_1,  MINADJ_1  ); 
        $graph->SetTickDensity( TICKD_NORMAL, TICKD_SPARSE );




		// Set title and subtitle
		$graph->title->Set("Latency (s) for origin : ".$origin);
		$graph->subtitle->Set("From: ".$StartTimestamp." To: ".$EndTimestamp);

		// Use built in font
		$graph->title->SetFont(FF_FONT1,FS_BOLD);

		// Make the margin around the plot a little bit bigger
		// then default
        $graph->img->SetMargin(95,50,40,100); 

				
        $graph->xaxis->scale->SetDateFormat( 'd/m/Y H:i ' );

		// Create the latency plot
		$latency_gr = new LinePlot($latency, $Timestamp);
        $graph -> yaxis-> scale -> setAutoMin(0);

		$graph->Add($latency_gr);
		
		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

		
        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
