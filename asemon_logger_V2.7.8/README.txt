See asemon wiki at : http://sourceforge.net/apps/mediawiki/asemon/index.php?title=Main_Page


Presentation
------------

Asemon_logger is a tool for monitoring any Sybase serveur (ASE, RS, IQ, RAO, ...) : it collects metrics, and archive them in an "archive database"
The "archive database" is a Sybase ASE database (V12.5 ou higher) and can be hosted by any ASE serveur on the network (preferably not on the monitored server)
For ASE , asemon_logger exploits new monitoring tables introduced with ASE V12.5.0.3.

Asemon_logger automatically creates archive tables if they don't already
exists in the "archive database". Archive tables are named : "nameOfMonitoredServer_nameOfMetricGroup".
When installing a new version of asemon_logger, archive tables are automatically altered.
	
The metric group for ASE are (but more can be added, and have been added since the first version) :
	 IOQueue   : for monIOQueue counters
         DevIO     : for monDeviceIO counters
         SysWaits  : for monSysWaits counters
         OpObjAct  : for monOpenObjectActivity counters
         DataCache : for monDataCache counters
         CachePool : for monCachePool counters
         ProcCache : for monProcedureCache counters
         OpenDbs   : for monOpenDatabases counters
         NetworkIO : for monNetworkIO counters
         Engines   : for monEngine counters
         ProcActiv : for monProcessActivity counters
         RaActiv   : for Replication Agents counters
         BlockedP  : for monitoring of blocked processes
         
Installation
------------

See INSTALL file provided with this distribution.

Start
-----
(use asemon_logger.bat on Windows , or asemon_logger.sh on Unix)

	asemon_logger  -c config_file.xml
	(the SYBASE env variable is used to specify the location of sybase_home, in order to find the sql.ini file)


        (make your own config file, customized for your needs. A model of config file is provided in the conf subdirectory)
        
Stop
----
       Kill asemon_logger process (not sybase connection since asemon_logger always tries to reconnect)
       
       
Platforms
---------

Tested on Windows and Unix (Sun, Ibm, Linux)

Requirements
------------

J2SE 1.6 


Copyright
---------

Copyright (C) 2004    Jean-Paul Martin (jpmartin@sybase.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2, or (at your option)
any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with asemon; see the file COPYING.  If not, write to
the Free Software Foundation, Inc., 59 Temple Place, Suite 330,
Boston, MA 02111-1307, USA.

