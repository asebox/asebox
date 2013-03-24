#!/bin/ksh

export ASEMON_LOGGER_HOME=${ASEMON_LOGGER_HOME:-"`pwd`"}
export JAVA_HOME=${JAVA_HOME:-"$ASEMON_LOGGER_HOME/../jre1.5.0_11"}
export JCONNECT_HOME=$ASEMON_LOGGER_HOME/jConnect-6_0
export PATH=$JAVA_HOME/bin:$PATH
export CLASSPATH=$ASEMON_LOGGER_HOME/dist/Asemon_logger.jar:$ASEMON_LOGGER_HOME/lib/jdom.jar:$ASEMON_LOGGER_HOME/lib/xerces.jar:$ASEMON_LOGGER_HOME/lib/java-getopt-1.0.9.jar:$JCONNECT_HOME/classes/jconn3.jar

cd src
javac -g -d ../build/classes asemon_logger/*.java

cd ../build/classes
jar -cvf ../../dist/Asemon_logger.jar asemon_logger/*
cd ../..

