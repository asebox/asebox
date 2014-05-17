<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");		
	include ("../".$jpgraph_home."/src/jpgraph.php");
	include ("../".$jpgraph_home."/src/jpgraph_line.php");




	$param_list=array(
		'Type',
		'Field_id',
		'fldname'
	);
	foreach ($param_list as $param)
		@$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];



  
  if ($Field_id=='') 
     $field_id_clause='';
  else
     $field_id_clause=" and p.field_id=".$Field_id;

  if ($Type=='Contention')
  {
  $SQLText = "select Ts=convert(varchar,Timestamp,3)+' '+convert(varchar,Timestamp,108),
                res=str(avg(waits*100./grabs ),15,4)
                from
                (
                  select 
                  p.Timestamp, grabs=sum(1.*p.d_value),waits=sum(1.*w.d_value)
                  from ".$ServerName."_SysMon w, ".$ServerName."_SysMon p
                  where w.Timestamp >='".$StartTimestamp."' and w.Timestamp <'".$EndTimestamp."'
                  and p.Timestamp >='".$StartTimestamp."' and p.Timestamp <'".$EndTimestamp."'        
                  and w.grpname like 'W'
                  and p.grpname like 'P'
                  and w.field_id=p.field_id
                  and w.Timestamp=p.Timestamp
                  and p.fldname like '".$fldname."'
                  and p.d_value !=0".
                  $field_id_clause
                  ." group by p.Timestamp
                ) spinact
                group by Timestamp
		        order by Timestamp";
      $legend="Contention %";
  }

  if ($Field_id=='') 
     $field_id_clause='';
  else
     $field_id_clause=" and field_id=".$Field_id;

  if ($Type=='Spins')
  {
      $SQLText = "select Ts=convert(varchar,Timestamp,3)+' '+convert(varchar,Timestamp,108),
                res=str(sum(spins),15,4)
                from
                (
                  select 
                  Timestamp, spins=sum(1.*abs(d_value))
                  from ".$ServerName."_SysMon
                  where Timestamp >='".$StartTimestamp."' and Timestamp <'".$EndTimestamp."'
                  and grpname like 'S'
                  and fldname like '".$fldname."'".
                  $field_id_clause
                  ." group by Timestamp
                ) spinact
                group by Timestamp
		        order by Timestamp";
      $legend=$Type;
  }
  if ($Type=='Waits')
  {
  $SQLText = "select Ts=convert(varchar,Timestamp,3)+' '+convert(varchar,Timestamp,108),
                res=str(sum(waits),15,4)
                from
                (
                  select 
                  Timestamp, waits=sum(1.*d_value)
                  from ".$ServerName."_SysMon
                  where Timestamp >='".$StartTimestamp."' and Timestamp <'".$EndTimestamp."'
                  and grpname like 'W'
                  and fldname like '".$fldname."'".
                  $field_id_clause
                  ." group by Timestamp
                ) spinact
                group by Timestamp
                order by Timestamp";
      $legend=$Type;
  }
    
//    JpGraphError::Raise($SQLText);
		
		$result=sybase_query($SQLText, $pid);
		 
		
		
		while (($row=sybase_fetch_array($result)))
		{
			$Timestamp[]= $row["Ts"];
			$res[] = $row["res"];
		}

		if (count($Timestamp)==0) 	exit("No values");
		
		// New graph with a background image and drop shadow
		$graph = new Graph(1000,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);

		// Set title and subtitle
		$graph->title->Set("Spinlock");

		// Use built in font
		$graph->title->SetFont(FF_FONT1,FS_BOLD);
		$graph->subtitle->Set("From: ".$StartTimestamp." To: ".$EndTimestamp);

		// Make the margin around the plot a little bit bigger
		// then default
		$graph->img->SetMargin(95,20,40,100);	


		// Display every 10:th datalabel
		$graph->xaxis->SetTickLabels($Timestamp);
		$nbVal=sizeof($Timestamp);
		if ( $nbVal>20 )
		  $graph->xaxis->SetTextTickInterval($nbVal/20);
		else
		  $graph->xaxis->SetTextTickInterval(1);
		  		
		// Create the line plot
		$b1 = new LinePlot($res);
		$b1 ->SetLegend($legend);
		$graph->Add($b1);



		$graph -> yaxis -> scale -> setAutoMin(0);
		

		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
