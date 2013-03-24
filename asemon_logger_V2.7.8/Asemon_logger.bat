@echo off

rem set ASEMON_LOGGER_HOME=
rem set JAVA_HOME=

if "%ASEMON_LOGGER_HOME%" == "" ( 
  set ASEMON_LOGGER_HOME=.
)
set ASEMON_LOGGER_HOME

if "%JAVA_HOME%" == "" ( 
  set JAVA_HOME=%ASEMON_LOGGER_HOME%\..\jreWin160
)

set JAVA_HOME

set JCONNECT_HOME=%ASEMON_LOGGER_HOME%\jConnect-7_0
set classpath=%ASEMON_LOGGER_HOME%\dist\asemon_logger.jar;%ASEMON_LOGGER_HOME%\lib\jdom.jar;%ASEMON_LOGGER_HOME%\lib\xerces.jar;%ASEMON_LOGGER_HOME%\lib\java-getopt-1.0.9.jar;%JCONNECT_HOME%\classes\jconn4.jar;%JCONNECT_HOME%\classes\jTDS3.jar;



set param_string=

@echo off 
set All=%*
set N=0
:boucle 
for /F "tokens=1,*" %%A in ("%All%") do (set P=%%A & set All=%%B) 
set /A N=%N%+1 
rem echo Parametre %N%=%P% 
set param_string=%param_string% %P%
if "%All%" NEQ "" goto boucle 

rem %JAVA_HOME%\bin\java -DSYBASE=%SYBASE% -DASEMON_LOGGER_HOME=%ASEMON_LOGGER_HOME% asemon_logger/Asemon_logger %1 %2 %3 %4 %5 %6 %7 %8 %9
rem The next formulation allow more than 9 parameters
"%JAVA_HOME%"\bin\java -Xmx512m -DSYBASE=%SYBASE% -DASEMON_LOGGER_HOME=%ASEMON_LOGGER_HOME% -Dsun.net.inetaddr.ttl=0 asemon_logger/Asemon_logger %param_string%

