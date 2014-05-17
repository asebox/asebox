<?php

    include ("../ARContext_restore.php");
    include ("../connectArchiveServer.php");    
    include ("../".$jpgraph_home."/src/jpgraph.php");
    include ("../".$jpgraph_home."/src/jpgraph_line.php");


    $param_list=array(
    'dbid',
    'dbname',
    'isMixedLog',
    'typeGraph'
    );
    foreach ($param_list as $param)
        @$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];

    $result=sybase_query("select diff=datediff(dd,'".$StartTimestamp."', '".$EndTimestamp."')", $pid);
    $row=sybase_fetch_array($result);
    $diffdays = $row["diff"];
    
    if ($diffdays < 10) {
        $result=sybase_query(
        "declare @pgsz int
        select @pgsz=PageSize from ".$ServerName."_MonState where Timestamp=(select max(Timestamp) from ".$ServerName."_MonState where Timestamp>='".$StartTimestamp."' and Timestamp <='".$EndTimestamp."')
        
        select TS=convert(varchar,Timestamp,3)+' '+convert(varchar,Timestamp,108),
        DataSize_Mb=(1.*(case when isMixedLog=0 then  Total_pgs-logTotal_pgs else  Total_pgs end)) * @pgsz/(1024*1024),
        DataUsed_Mb=(1.*(case when isMixedLog=0 then  Total_pgs-logTotal_pgs else  Total_pgs end) - dbFree_pgs )*@pgsz/(1024*1024), 
        LogSize_Mb=  1.* (case when isMixedLog=0 then logTotal_pgs else 0 end) * @pgsz/(1024*1024),
        LogUsed_Mb=1.*logUsed_pgs*@pgsz/(1024*1024), 
        LogClr_Mb= 1.*logClr_pgs*@pgsz/(1024*1024)
        
        from ".$ServerName."_AseDbSpce
        where Timestamp>='".$StartTimestamp."'
        and Timestamp <='".$EndTimestamp."'
        and dbid=".$dbid."
        order by Timestamp",
        $pid);
    }
    
    else {
        $result=sybase_query(
        "declare @pgsz int
        select @pgsz=PageSize from ".$ServerName."_MonState where Timestamp=(select max(Timestamp) from ".$ServerName."_MonState where Timestamp>='".$StartTimestamp."' and Timestamp <='".$EndTimestamp."')
        
        select TS=convert(varchar,(dateadd(hh,datepart(hh, Timestamp), convert(datetime,convert(varchar,Timestamp,102)) )),3)+' '+
                  convert(varchar,datepart(hh,(    dateadd(hh,datepart(hh, Timestamp), convert(datetime,convert(varchar,Timestamp,102)) ))  ))+'h',
        DataSize_Mb=max((1.*(case when isMixedLog=0 then  Total_pgs-logTotal_pgs else  Total_pgs end)) * @pgsz/(1024*1024)),
        DataUsed_Mb=max((1.*(case when isMixedLog=0 then  Total_pgs-logTotal_pgs else  Total_pgs end) - dbFree_pgs )*@pgsz/(1024*1024)), 
        LogSize_Mb=  max(1.* (case when isMixedLog=0 then logTotal_pgs else 0 end) * @pgsz/(1024*1024)),
        LogUsed_Mb=max(1.*logUsed_pgs*@pgsz/(1024*1024)), 
        LogClr_Mb= max(1.*logClr_pgs*@pgsz/(1024*1024))
        
        from ".$ServerName."_AseDbSpce
        where Timestamp>='".$StartTimestamp."'
        and Timestamp <='".$EndTimestamp."'
        and dbid=".$dbid."
        group by dateadd(hh,datepart(hh, Timestamp), convert(datetime,convert(varchar,Timestamp,102)) )
        order by dateadd(hh,datepart(hh, Timestamp), convert(datetime,convert(varchar,Timestamp,102)) )",
        $pid);
    }


    while (($row=sybase_fetch_array($result)))
    {
        $Timestamp[]= $row["TS"];
        $DataSize_Mb[] = $row["DataSize_Mb"];
        $DataUsed_Mb[] = $row["DataUsed_Mb"];
        $LogSize_Mb[] = $row["LogSize_Mb"];
        $LogUsed_Mb[] = $row["LogUsed_Mb"];
        $LogClr_Mb[] = $row["LogClr_Mb"];
    }


    
        
        // New graph with a background image and drop shadow
        $graph = new Graph(1000,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);


        // Set title and subtitle
        $graph->title->Set($dbname." : ".$typeGraph." usage (Mb)");
        $graph->subtitle->Set("From: ".$StartTimestamp." To: ".$EndTimestamp);

        // Use built in font
        $graph->title->SetFont(FF_FONT1,FS_BOLD);

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
                  
        $graph->xaxis->SetFont(FF_COURIER,FS_NORMAL,8);
        $graph->xaxis->SetLabelAngle(45);
        $graph->xaxis->SetTickSide(SIDE_DOWN);

        if ($typeGraph=="DATA") {
            // Create the data line plot
            $dataSize_gr = new LinePlot($DataSize_Mb);
            $dataSize_gr -> setColor("red");
            $dataSize_gr ->SetLegend("DataSize");
    
            $dataUsed_gr = new LinePlot($DataUsed_Mb);
            $dataUsed_gr -> setColor("blue");
            $dataUsed_gr ->SetLegend("DataUsed");
    
            $graph->Add($dataSize_gr);
            $graph->Add($dataUsed_gr);
    
            if ($isMixedLog==1) {
                $LogUsed_gr = new LinePlot($LogUsed_Mb);
                $LogUsed_gr -> setFillColor("green");
                $LogUsed_gr ->SetLegend("LogUsed");
                
                $LogClr_gr = new LinePlot($LogClr_Mb);
                $LogClr_gr -> setFillColor("yellow");
                $LogClr_gr ->SetLegend("LogClr");
                
                //$graph->Add($LogSize_gr);
                
                $accLog_gr = new AccLinePlot(array($LogUsed_gr, $LogClr_gr ));
                $graph->Add($accLog_gr);
              }
        }
        else {
            $LogSize_gr = new LinePlot($LogSize_Mb);
            $LogSize_gr -> setColor("red");
            $LogSize_gr ->SetLegend("LogSize");
            $graph->Add($LogSize_gr);
            
            if ($isMixedLog==0) {
                $LogUsed_gr = new LinePlot($LogUsed_Mb);
                $LogUsed_gr -> setFillColor("green");
                $LogUsed_gr ->SetLegend("LogUsed");
                
                $LogClr_gr = new LinePlot($LogClr_Mb);
                $LogClr_gr -> setFillColor("yellow");
                $LogClr_gr ->SetLegend("LogClr");
                
                //$graph->Add($LogSize_gr);
                
                $accLog_gr = new AccLinePlot(array($LogUsed_gr, $LogClr_gr ));
                $graph->Add($accLog_gr);
            }
        }

        
        $graph -> yaxis->SetFont(FF_COURIER,FS_NORMAL,8);
        $graph->yaxis->SetTickSide(SIDE_LEFT);

        
        $graph -> legend  -> SetLayout(LEGEND_HOR);
        $graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

        
        $graph->graph_theme=null; // This fix bottom margin bad computation
        // Finally output the  image
        $graph->Stroke();

    ?>
