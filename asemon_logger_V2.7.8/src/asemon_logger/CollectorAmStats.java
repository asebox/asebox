/**
* <p>CollectorAmStats</p>
* <p>Asemon_logger : class for collecting internal asemon_logger statistics</p>
* <p>Copyright: Jean-Paul Martin (jpmartin@sybase.com) Copyright (c) 2004</p>
* @version 2.6.2
*/

package asemon_logger;
import java.sql.*;

public class CollectorAmStats extends Collector {

  CollectorAmStats (MonitoredSRV ms, MetricDescriptor aMetricDescriptor) {
      super(ms, aMetricDescriptor);
  }

  public void getMetrics ()  throws Exception {

      archRows = -1 ; // in case of error or missing config params, AmStats will show this info
      msrv.amStats.savStats(this);

  }

}