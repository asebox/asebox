<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
  include ("../".$jpgraph_home."/src/jpgraph.php");
  include ("../".$jpgraph_home."/src/jpgraph_line.php");
  include ("../".$jpgraph_home."/src/jpgraph_date.php");	


  $param_list=array(
   'ID',
   'instance_id',
   'counter_id',
   'counter_name'
  );
  foreach ($param_list as $param)
  @$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];
 
  //JpGraphError::Raise("ID=".$ID);
  if ($ID > 0) 
     $ID_search_clause = " and S.ID = ".$ID;
  else
     $ID_search_clause = "";

  //JpGraphError::Raise("ID_search_clause=".$ID_search_clause);
  

  // Convert $StartTimestamp and $EndTimestamp to format 116 according to the current dmy/mdy setting
  $result=sybase_query("select sts=convert(varchar,convert(datetime,'".$StartTimestamp."'),116),
                               ets=convert(varchar,case when convert(datetime,'".$EndTimestamp."')> getdate() then getdate() else convert(datetime,'".$EndTimestamp."') end,116)", $pid);
  $row = sybase_fetch_array($result);
  $sts = $row["sts"];
  $ets = $row["ets"];

  $cnt=0;
  

  // Find which counters are not set
  $query = "select sum_counter_total=sum(1.*counter_total), 
                   sum_avg_ttl_obs=sum(1.*avg_ttl_obs)
            from ".$ServerName."_RSStats S, ".$ServerName."_Instances I
            where S.ID = I.ID
              ".$ID_search_clause."
              and I.instance_id = ".$instance_id."
              and S.Timestamp >='".$StartTimestamp."'
              and S.Timestamp <='".$EndTimestamp."'
              and S.counter_id = ".$counter_id;
  //JpGraphError::Raise($query);
  $result=sybase_query($query,  $pid);
  $row=sybase_fetch_array($result);
  $sum_counter_total=$row["sum_counter_total"];
  $sum_avg_ttl_obs=$row["sum_avg_ttl_obs"];
  //  JpGraphError::Raise("sum_avg_ttl_obs : ".$sum_avg_ttl_obs);
  if ( strlen($sum_avg_ttl_obs)== 0) {
      // This is an "observer" (only counter_obs and rate_x_sec are set)
      $counter_type = "observer";
      //JpGraphError::Raise("1ere branche");
  }
  else
      if (strlen($sum_counter_total) == 0){
          // JpGraphError::Raise("2eme branche");
          // This is a monitor (only counter_obs, counter_last, counter_max and avg_ttl_obs are set)
          $counter_type = "monitor";
      }
      else {
          // This is a counter (all are set)
          // JpGraphError::Raise("3eme branche");
          $counter_type = "counter";
      }
  //  JpGraphError::Raise("Counter type : ".$counter_type);
  	
  $query = 
  "Select ts=convert(varchar,timetable.Timestamp,116),
            counter_obs = isnull(counter_obs, 0),
            counter_total = isnull(counter_total, 0),
            counter_last = isnull(counter_last, 0),
            counter_max = isnull(counter_max, 0),
            avg_ttl_obs = isnull(avg_ttl_obs, 0),
            rate_x_sec = isnull(rate_x_sec,0)
   from 
     (
       select Timestamp from ".$ServerName."_RSStats
                  where counter_id=18000
                    and Timestamp >='".$StartTimestamp."'
                    and Timestamp <='".$EndTimestamp."'
     ) timetable left outer join 
     (
       Select S.Timestamp,
            counter_obs,
            counter_total,
            counter_last,
            counter_max,
            avg_ttl_obs,
            rate_x_sec -- = (counter_total*1000./S.Interval)    -- recompute rate to get more precision
       from ".$ServerName."_RSStats S, ".$ServerName."_Instances I
       where  S.ID = I.ID
              ".$ID_search_clause."
              and I.instance_id = ".$instance_id."
              and S.Timestamp >='".$StartTimestamp."'
              and S.Timestamp <='".$EndTimestamp."'
              and S.counter_id = ".$counter_id."
     ) data
   on timetable.Timestamp=data.Timestamp
   order by timetable.Timestamp"; 
  //sybase_query("insert into tracesql values (\"".$query."\")", $pid);
  //JpGraphError::Raise($query);
  $result=sybase_query($query,  $pid);

  $Timestamp[]= date_format(date_create($sts),'U');
	$counter_obs[] = null;
	$counter_total[] = null;
	$counter_last[] = null;
	$counter_max[] = null;
	$avg_ttl_obs[] = null;
	$rate_x_sec[] = null;

  $cnt=0;
  while (($row=sybase_fetch_array($result)))
  {
   $Timestamp[]=  date_format(date_create($row["ts"]),'U');
   $counter_obs[] = $row["counter_obs"];
   $counter_total[] = $row["counter_total"];
   $counter_last[] = $row["counter_last"];
   $counter_max[] = $row["counter_max"];
   $avg_ttl_obs[] = $row["avg_ttl_obs"];
   $rate_x_sec[] = $row["rate_x_sec"];
   $cnt++;
  }
  if ($cnt == 0)
    JpGraphError::Raise("No data for this period");
    
  $Timestamp[]= date_format(date_create($ets),'U');
	$counter_obs[] = null;
	$counter_total[] = null;
	$counter_last[] = null;
	$counter_max[] = null;
	$avg_ttl_obs[] = null;
	$rate_x_sec[] = null;
  
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
  
  // Create the counter_obs plot
  //$counter_obs_plot = new LinePlot($counter_obs);
  //$counter_obs_plot -> setColor("blue");
  //$counter_obs_plot ->SetLegend("counter_obs");
  
  // Create the counter_total plot
  //$counter_total_plot = new LinePlot($counter_total);
  //$counter_total_plot -> setColor("blue");
  //$counter_total_plot ->SetLegend("counter_total");
  
  // Create the counter_last plot
  //$counter_last_plot = new LinePlot($counter_last);
  //$counter_last_plot -> setColor("blue");
  //$counter_last_plot ->SetLegend("counter_last");
  
  // Create the counter_max plot
  $counter_max_plot = new LinePlot($counter_max, $Timestamp);
  $counter_max_plot -> setColor("red");
  $counter_max_plot ->SetLegend("counter_max");
  
  // Create the avg_ttl_obs plot
  $avg_ttl_obs_plot = new LinePlot($avg_ttl_obs, $Timestamp);
  $avg_ttl_obs_plot -> setColor("blue");
  $avg_ttl_obs_plot ->SetLegend("avg_ttl_obs");

  $graph -> yaxis-> scale -> setAutoMin(0);

  // Create the rate_x_sec plot
  $rate_x_sec_plot = new LinePlot($rate_x_sec, $Timestamp);
  $rate_x_sec_plot -> setColor("darkgreen");
  $rate_x_sec_plot ->SetLegend("rate / sec");
  
  if ($counter_type=="observer")
    $graph->Add($rate_x_sec_plot);  
  else if ($counter_type == "monitor") {
    $graph->Add($counter_max_plot);
    $graph->Add($avg_ttl_obs_plot);
    }
    else {
    	// This is a monitor display 2 curves with two axis : left one for rate /s , right one for Avg_ttl_obs
      $graph->yaxis->SetColor("darkgreen");
    	$graph->Add($rate_x_sec_plot);
      $graph->SetY2Scale("lin");
      $graph->y2axis->SetColor("blue");
      $graph->y2axis-> scale -> setAutoMin(0);
      $graph->y2axis->SetTickSide(SIDE_RIGHT);
    	$graph->AddY2($avg_ttl_obs_plot);
    }


  $graph -> legend  -> SetLayout(LEGEND_HOR);
  $graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");
  
  
        $graph->graph_theme=null; // This fix bottom margin bad computation
  // Finally output the  image
  $graph->Stroke();

 ?>
