<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php"); 
  include ("../".$jpgraph_home."/src/jpgraph.php");
  include ("../".$jpgraph_home."/src/jpgraph_bar.php"); 
  include ("../".$jpgraph_home."/src/jpgraph_date.php"); 

  
  $param_list=array(
   'DBID',
   'ProcName',
   'days',
   'indic'
  );
  foreach ($param_list as $param)
    @$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];
  


  // Set Monday as first day of week and Retrieve Trend conf
  $result=sybase_query("set datefirst 1", $pid);
  if ($result==false){ 
    sybase_close($pid); 
    $pid=0;
    include ("../connectArchiveServer.php"); 
    echo "Error";
    return(0);
  }

  // Convert $StartTimestamp and $EndTimestamp to format 116 according to the current dmy/mdy setting
  $result=sybase_query("select sts=convert(varchar,min(day),116),
                               ets=convert(varchar,getdate(),116), nbdays=datediff(dd, min(day), getdate()) from ".$ServerName."_TrendProc", $pid);
  $row = sybase_fetch_array($result);
  $sts = $row["sts"];
  $ets = $row["ets"];
  $nbdays = $row["nbdays"];

  $cnt=0;

  // Translate the days clause
  //JpGraphError::Raise("days=".$days);
  if (!isset($days)) $days='';
  if (($days=='' )||($days=='All days'))  $days_clause='';
  if ($days=='no_wenkend')     $days_clause=' and datepart(weekday,day) in (1,2,3,4,5)'; 
  if ($days=='Monday')         $days_clause=' and datepart(weekday,day)=1';
  if ($days=='Tuesday')        $days_clause=' and datepart(weekday,day)=2';
  if ($days=='Wednesday')      $days_clause=' and datepart(weekday,day)=3';
  if ($days=='Thursday')       $days_clause=' and datepart(weekday,day)=4';
  if ($days=='Friday')         $days_clause=' and datepart(weekday,day)=5';
  if ($days=='Saturday')       $days_clause=' and datepart(weekday,day)=6';
  if ($days=='Sunday')         $days_clause=' and datepart(weekday,day)=7';

  
  // Read the data values
  $result=sybase_query(
     "select ts=convert(varchar,day,116), 
      Value=".$indic."
      from ".$ServerName."_TrendProc
      where DBID=".$DBID."
	  and ProcName='".$ProcName."'
      ".$days_clause."
      order by convert(varchar,day,112)",
                $pid);
  if ($result==false){ 
    sybase_close($pid); 
    $pid=0;
    JpGraphError::Raise("Error. No result available.");
    return(0);
  }

  $Timestamp[]= date_format(date_create($sts),'U');
  $Value[] = null;
 
  $cnt=0;
  while (($row=sybase_fetch_array($result)))
  {
    $cnt++;
    $Timestamp[]=  date_format(date_create($row["ts"]),'U');
    $Value[] = $row["Value"];
  }
  $Timestamp[]= date_format(date_create($ets),'U');
  $Value[] = null;

  if (($cnt==0)||($Value[1]==-1))
    JpGraphError::Raise("No data for indicator '".$indic."'");
//  JpGraphError::Raise("nb values = ".count($Timestamp) );

  // New graph with a background image and drop shadow
  $graph = new Graph(700,200,"auto");
  $graph->SetScale("datlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);
  $graph->xaxis->scale->SetTimeAlign( MINADJ_1,  MINADJ_1  ); 


  // Set title and subtitle
  $graph->title->Set($indic);

  // Use built in font
  $graph->title->SetFont(FF_FONT1,FS_BOLD);

  // Make the margin around the plot a little bit bigger
  // then default
  $graph->img->SetMargin(80,10,10,70); 


  // Display every 10:th datalabel
  $graph->xaxis->SetTickLabels($Timestamp);
  $nbVal=sizeof($Timestamp);
  if ( $nbVal>20 )
    $graph->xaxis->SetTextTickInterval($nbVal/20);
  else
    $graph->xaxis->SetTextTickInterval(1);
      
  $graph->xaxis->scale->SetDateFormat( 'd/m/Y' );

  // Create the BAR plot
  $b1 = new BarPlot($Value, $Timestamp);
  $b1 -> SetWidth(610/($nbdays-1) -2); 
  $graph->Add($b1);
  $b1 -> setcolor("blue");
  $b1 -> setFillColor("lightcyan");


  $graph -> yaxis -> scale -> setAutoMin(0);

  $graph->graph_theme=null; // This fix bottom margin bad computation
  // Finally output the  image
  $graph->Stroke();


 ?>
