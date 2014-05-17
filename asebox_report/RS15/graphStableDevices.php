<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
  include ("../".$jpgraph_home."/src/jpgraph.php");
  include ("../".$jpgraph_home."/src/jpgraph_line.php");	
  include ("../".$jpgraph_home."/src/jpgraph_bar.php");	
  include ("../".$jpgraph_home."/src/jpgraph_date.php");	

  $blocksize = 16; // This is currently fixed but should be based on config

  // Convert $StartTimestamp and $EndTimestamp to format 116 according to the current dmy/mdy setting
  $result=sybase_query("select sts=convert(varchar,convert(datetime,'".$StartTimestamp."'),116),
                               ets=convert(varchar,case when convert(datetime,'".$EndTimestamp."')> getdate() then getdate() else convert(datetime,'".$EndTimestamp."') end,116)", $pid);
  $row = sybase_fetch_array($result);
  $sts = $row["sts"];
  $ets = $row["ets"];

  // Check if table xxxx_RSWhoSQM exists
  $query = "select id from sysobjects where name ='".$ServerName."_RSWhoSQM'";
  $result = sybase_query($query,$pid);
  $rw=0;
  while($row = sybase_fetch_array($result))
  {
    $rw++;
  }	
  if ($rw == 1)   $RSWhoSQM_exists=1;
  else $RSWhoSQM_exists=0;

  //JpGraphError::Raise("rswhosqlexists = ".$RSWhoSQM_exists);

  $result=sybase_query("select ts=convert(varchar,Timestamp,116),
		                 Capacity_Mb=sum(Total_segs)*64*".$blocksize."/1024, 
		                 Used_Mb=sum(Used_segs)*64*".$blocksize."/1024
		from ".$ServerName."_DISKSPCE
		where Timestamp >='".$StartTimestamp."'
		and Timestamp <='".$EndTimestamp."' 
		group by Timestamp 
        order by Timestamp", 
		$pid);
  $Timestamp1[]= date_format(date_create($sts),'U');
  $CapacityAvg_Mb[] = null;
  $UsedAvg_Mb[] = null;
  while (($row=sybase_fetch_array($result)))
  {
    $Timestamp1[]= date_format(date_create($row["ts"]),'U');
    $CapacityAvg_Mb[] = $row["Capacity_Mb"];
    $UsedAvg_Mb[] = $row["Used_Mb"];
  }
  $Timestamp1[]= date_format(date_create($ets),'U');
  $CapacityAvg_Mb[] = null;
  $UsedAvg_Mb[] = null;

  if ($RSWhoSQM_exists==1) {
    $query = "select ts=convert(varchar,Timestamp,116),

    saved_queue_sz_Mb=sum(
    (convert(numeric(20,0), substring(Next_Read, 1, patindex('%.%',Next_Read))))*64. -
    convert(numeric(20,0), substring(Save_Int_Seg, patindex('%:%',Save_Int_Seg)+1, datalength(Save_Int_Seg)))*64.
    )*".$blocksize."/1024,

    active_queue_sz_Mb=sum(
    (
    convert(numeric(20,0), substring(Last_Seg_Block, 1, patindex('%.%',Last_Seg_Block)))*64.+
    (convert(numeric(20,0), substring(Last_Seg_Block, patindex('%.%',Last_Seg_Block)+1, datalength(Last_Seg_Block))))*1.)
    -
    (
    convert(numeric(20,0), substring(Next_Read, 1, patindex('%.%',Next_Read)))*64.+
    (convert(numeric(20,0), substring(Next_Read, patindex('%.%',Next_Read)+1, datalength(Next_Read)))-1)*1.)
    )*".$blocksize."/1024
		from ".$ServerName."_RSWhoSQM
		where Timestamp >='".$StartTimestamp."'
		and Timestamp <='".$EndTimestamp."' 
		group by Timestamp
        order by Timestamp";

	$result=sybase_query($query,$pid);	
    //JpGraphError::Raise($query);
//    $Timestamp2[]= date_format(date_create($sts),'U');
//    $saved_queue_sz_Mb[] = null;
//    $active_queue_sz_Mb[] = null;
    while (($row=sybase_fetch_array($result)))
    {
      $Timestamp2[]= date_format(date_create($row["ts"]),'U');
      $saved_queue_sz_Mb[] = $row["saved_queue_sz_Mb"];
      $active_queue_sz_Mb[] = $row["active_queue_sz_Mb"];
    }
//    $Timestamp2[]= date_format(date_create($ets),'U');
//    $saved_queue_sz_Mb[] = null;
//    $active_queue_sz_Mb[] = null;
  
  }

	
		
        // New graph with a background image and drop shadow
        $graph = new Graph(1000,330,"auto");
        $graph->SetScale("datlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);

        $graph->xaxis->scale->SetTimeAlign( MINADJ_1,  MINADJ_1  ); 
        $graph->SetTickDensity( TICKD_NORMAL, TICKD_SPARSE );


		// Set title and subtitle
		$graph->title->Set("Stable devices usage");
		$graph->subtitle->Set("From: ".$StartTimestamp." To: ".$EndTimestamp);

		// Use built in font
		$graph->title->SetFont(FF_FONT1,FS_BOLD);

		// Make the margin around the plot a little bit bigger
		// then default
		$graph->img->SetMargin(95,20,70,100);


        $graph->xaxis->scale->SetDateFormat( 'd/m/Y H:i ' );
        
        
        if ($RSWhoSQM_exists==0) {
            // Create the Used Space line plot
            $used_gr = new LinePlot($UsedAvg_Mb, $Timestamp1);
            $used_gr ->SetLegend("Used Space");
            $graph->Add($used_gr);
        }		
        
        if ($RSWhoSQM_exists==1) {
          // Create the saved_queue_sz_Mb line plot
          $saved_gr = new LinePlot($saved_queue_sz_Mb, $Timestamp2);
          $saved_gr ->SetLegend("Space used for Saved segments");
        
          // Create the active_queue_sz_Mb line plot
          $active_gr = new LinePlot($active_queue_sz_Mb, $Timestamp2);
          $active_gr ->SetLegend("Active segments");
        
          $acc_gr = new AccLinePlot(array($saved_gr, $active_gr));
          $graph->Add($acc_gr);
        
        }		
        
        // Create the CapacityAvg_Mb line plot
        $cap = new LinePlot($CapacityAvg_Mb, $Timestamp1);
        $cap -> setcolor("red");
        $cap ->SetLegend("Max capacity (Mb)");
        $cap -> SetStyle(1);
        $graph->Add($cap);

        $graph -> yaxis-> scale -> setAutoMin(0);

        $graph -> legend  -> SetLayout(LEGEND_HOR);
        $graph -> legend  -> Pos(0.05, 0.12, "rigth", "top");

        $graph->graph_theme=null; // This fix bottom margin bad computation
        // Finally output the  image
        $graph->Stroke();

?>
