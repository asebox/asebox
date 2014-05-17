<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
  include ("../".$jpgraph_home."/src/jpgraph.php");
  include ("../".$jpgraph_home."/src/jpgraph_line.php");
  include ("../".$jpgraph_home."/src/jpgraph_date.php");	


  $param_list=array(
   'counter_name',
   'type'
  );
  foreach ($param_list as $param)
  @$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];
 
  //JpGraphError::Raise("ID=".$ID);
  

  $cnt=0;
  

  $query = 
  "Select ts=convert(varchar,Timestamp,116),
            stat=".$counter_name.",
            rate=".$counter_name."*1000./Interval
   from  ".$ServerName."_RAOSTATS
   where Timestamp >='".$StartTimestamp."'
     and Timestamp <='".$EndTimestamp."'
     and Interval>0
   order by Timestamp"; 
  //sybase_query("insert into tracesql values (\"".$query."\")", $pid);
  //JpGraphError::Raise($query);
  $result=sybase_query($query,  $pid);

  $Timestamp[]= date_format(date_create($StartTimestamp),'U');
	$counter[] = null;
      $rate[] = null;

  $cnt=0;
  while (($row=sybase_fetch_array($result)))
  {
   $Timestamp[]=  date_format(date_create($row["ts"]),'U');
   $counter[] = $row["stat"];
   $rate[] = $row["rate"];
   $cnt++;
  }
  if ($cnt == 0)
    JpGraphError::Raise("No data for this period");
    
  $Timestamp[]= date_format(date_create($EndTimestamp),'U');
  $counter[] = null;
  $rate[] = null;

  
  // New graph with a background image and drop shadow
  $graph = new Graph(1000,300,"auto");
  $graph->SetScale("datlin");
  $theme_class = new AsemonTheme;
  $graph->SetTheme($theme_class);
  $graph->xaxis->scale->SetTimeAlign( MINADJ_1,  MINADJ_1  ); 
  
  
  // Set title and subtitle
  $graph->title->Set(trim($counter_name));
  $graph->subtitle->Set("From: ".$StartTimestamp." To: ".$EndTimestamp);
  
  // Use built in font
  $graph->title->SetFont(FF_FONT1,FS_BOLD);
  
  // Make the margin around the plot a little bit bigger
  // then default
  $graph->img->SetMargin(95,50,40,100); 
  
  
      
  $graph->xaxis->scale->SetDateFormat( 'd/m/Y H:i ' );
  $graph->SetTickDensity( TICKD_NORMAL, TICKD_SPARSE );
  
  
  // Create the counter plot
  $counter_plot = new LinePlot($counter, $Timestamp);
  $counter_plot ->SetLegend("value");
  
  // Create the rate_x_sec plot
  $rate_x_sec_plot = new LinePlot($rate, $Timestamp);
  $rate_x_sec_plot ->SetLegend("rate / sec");

  if ($type=="SUM") {
      $graph->Add($rate_x_sec_plot);
  }
  else {
      $graph->Add($counter_plot);  
  }

  $graph ->yaxis->SetFont(FF_COURIER,FS_NORMAL,8);
  $graph ->yaxis->scale -> setAutoMin(0);


  $graph -> legend  -> SetLayout(LEGEND_HOR);
  $graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");
  
  
  $graph->graph_theme=null; // This fix bottom margin bad computation
  // Finally output the  image
  $graph->Stroke();

 ?>
