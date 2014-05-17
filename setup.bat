REM Execute this command file if you don't have Sybase Open Client installed on your machine
REM In that case, you must change the sql.ini file (in c:\AsemonReportSRV\sybase\ini) to define your ASE serveur containing the archived monitored data
set SYBASE=c:\ASEBOX\sybase
set SYBASE_OCS=OCS-15_0
set PATH=%SYBASE%\%SYBASE_OCS%\bin;%SYBASE%\%SYBASE_OCS%\dll;%SYBASE%\%SYBASE_OCS%\lib3p;%PATH%
set lang=enu
