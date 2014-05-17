set ASEMON_LOGGER_HOME=C:\jpm\java\Asemon_logger_V2
set JCONNECT_HOME=%ASEMON_LOGGER_HOME%\JCONNECT-6_0
set classpath=%ASEMON_LOGGER_HOME%\lib\jdom.jar;%ASEMON_LOGGER_HOME%\lib\xerces.jar;%ASEMON_LOGGER_HOME%\lib\java-getopt-1.0.9.jar;%JCONNECT_HOME%\classes\jconn3.jar;


echo %classpath%


cd src
rem javac -Xlint -d ../build/classes asemon_logger/*.java
javac -g -d ../build/classes asemon_logger/*.java

cd ..\build\classes
jar -cvf ../../dist/Asemon_logger.jar asemon_logger/*
cd ..\..

