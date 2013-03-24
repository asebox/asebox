<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php"); 
  include ("../".$jpgraph_home."/src/jpgraph.php");
  include ("../".$jpgraph_home."/src/jpgraph_line.php"); 
  include ("../".$jpgraph_home."/src/jpgraph_bar.php"); 
  include ("../".$jpgraph_home."/src/jpgraph_date.php");	

  
  $param_list=array(
   'TrendID',
   'days',
   'hourfrom',
   'hourto'
  );
  foreach ($param_list as $param)
    @$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];
  


  // Set Monday as first day of week and Retrieve Trend conf
  $result=sybase_query("set datefirst 1
                        select description, aggfunction from ".$ServerName."_TrendsCfg where TrendID=".$TrendID, $pid);
  $row=sybase_fetch_array($result);
  $description = $row["description"];
  $aggfunction = $row["aggfunction"];


  // Translate the days clause
  //JpGraphError::Raise("days=".$days);
  if (!isset($days)) $days='';
  if (($days=='' )||($days=='All days'))  $days_clause='';
  if ($days=='no_wenkend')     $days_clause=' and datepart(weekday,dt) in (1,2,3,4,5)'; 
  if ($days=='Monday')         $days_clause=' and datepart(weekday,dt)=1';
  if ($days=='Tuesday')        $days_clause=' and datepart(weekday,dt)=2';
  if ($days=='Wednesday')      $days_clause=' and datepart(weekday,dt)=3';
  if ($days=='Thursday')       $days_clause=' and datepart(weekday,dt)=4';
  if ($days=='Friday')         $days_clause=' and datepart(weekday,dt)=5';
  if ($days=='Saturday')       $days_clause=' and datepart(weekday,dt)=6';
  if ($days=='Sunday')         $days_clause=' and datepart(weekday,dt)=7';

  // Translate the hour clause
  $hour_clause = " and datepart(hour, dt) between ".$hourfrom." and ".$hourto;
  

	// Get dateformat
	if ($DFormat=='mdy') $fmt=101; else $fmt=103;


  // Convert $StartTimestamp and $EndTimestamp to format 116 according to the current dmy/mdy setting
  $query="select sts=convert(varchar, convert(datetime,convert(varchar,
  convert(datetime,'".$StartTimestamp."'),".$fmt.") ) ,116),
                               ets=convert(varchar, convert(datetime,convert(varchar,convert(datetime,'".$EndTimestamp."'),".$fmt.") )  ,116),
							   nbdays=datediff(dd, convert(datetime,convert(varchar,convert(datetime,'".$StartTimestamp."'),".$fmt.") ), convert(datetime,convert(varchar,convert(datetime,'".$EndTimestamp."'),".$fmt.") ))";
 //JpGraphError::Raise( $query);
  $result=sybase_query($query, $pid);
  $row = sybase_fetch_array($result);
  $sts = $row["sts"];
  $ets = $row["ets"];
  $nbdays = $row["nbdays"]+1;


 //JpGraphError::Raise("sts=".$sts."   ets=".$ets   ."    nbdays=".$nbdays);



  // Read the data values
  $query =
     "select Ts=convert(varchar, convert(datetime,convert(varchar,dt,3),3), 116), 
      Value=".$aggfunction."(case when Value <0 then 0 else Value end)  /* Ignore negatives values */
      from ".$ServerName."_Trends
      where TrendID=".$TrendID."
      ".$days_clause."
      ".$hour_clause."
      and dt between '".$StartTimestamp."' and '".$EndTimestamp."'
      group by  convert(datetime,convert(varchar,dt,3),3)
      order by convert(datetime,convert(varchar,dt,3),3)";
  //JpGraphError::Raise($query);
  $result=sybase_query($query,  $pid);
  
  $cnt=0;
  while (($row=sybase_fetch_array($result)))
  {
	if ($cnt==0) {
         //JpGraphError::Raise("Ts=".$row["Ts"]);
        // Case of first value
        //JpGraphError::Raise( "ts=".$row["Ts"]);
		if ($row["Ts"]!=$sts) {
		  $Timestamp[]= date_format(date_create($sts),'U');
          $Value[] = null;
		}
    }
    $cnt++;
    $Timestamp[]= date_format(date_create($row["Ts"]),'U');
    $Value[] = $row["Value"];
  }
  
//    JpGraphError::Raise("Not data for indicator '".$description."'");
  if ($cnt==0) {
    $Timestamp[]= date_format(date_create($sts),'U');
    $Value[]=0;
    $Timestamp[]= date_format(date_create($ets),'U');
    $Value[]=0;
  }
  else {
    // Case of the last value
	if (end($Timestamp)!=$ets) { 
      $Timestamp[]= date_format(date_create($ets),'U');
      $Value[] = null;
	}
  }
  // New graph with a background image and drop shadow
  $graph = new Graph(700,200,"auto");
  $graph->SetScale("datlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);
  $graph->xaxis->scale->SetDateAlign( DAYADJ_1,  DAYADJ_1  ); 
  
  if ($nbdays >20 )
    $graph->xaxis->scale->ticks->Set($nbdays*24*3600/20);
  else
    $graph->xaxis->scale->ticks->Set(24*3600);
  
  $graph->SetTickDensity( TICKD_NORMAL, TICKD_SPARSE );

  // Set title and subtitle
  $graph->title->Set($aggfunction." ".$description);

  // Use built in font
  $graph->title->SetFont(FF_FONT1,FS_BOLD);

  // Make the margin around the plot a little bit bigger
  // then default
  $graph->img->SetMargin(80,10,10,70); 


  // Display every 10:th datalabel
  $graph->xaxis->SetTickLabels($Timestamp);
      
  if ($DFormat=='dmy')
    $graph->xaxis->scale->SetDateFormat( 'd/m/Y' );
  else
    $graph->xaxis->scale->SetDateFormat( 'm/d/Y' );

  // Create the Line plot
  $b1 = new LinePlot($Value, $Timestamp);
  $graph->Add($b1);
//  $b1 -> setcolor("blue");
//  $b1 -> setFillColor("lightcyan");


  $graph -> yaxis -> scale -> setAutoMin(0);
  $graph->graph_theme=null; // This fix bottom margin bad computation
 // Finally output the  image
  $graph->Stroke();


 ?>
