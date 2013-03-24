#!/bin/ksh
export ASEMON_LOGGER_HOME=${ASEMON_LOGGER_HOME:-"`pwd`"}
export JAVA_HOME=${JAVA_HOME:-"$ASEMON_LOGGER_HOME/../jre1.6.0_21"}
 
export JCONNECT_HOME=$ASEMON_LOGGER_HOME/jConnect-7_0

export PATH=$JAVA_HOME/bin:$PATH
export CLASSPATH=$ASEMON_LOGGER_HOME/dist/Asemon_logger.jar:$ASEMON_LOGGER_HOME/lib/jdom.jar:$ASEMON_LOGGER_HOME/lib/xerces.jar:$ASEMON_LOGGER_HOME/lib/java-getopt-1.0.9.jar:$JCONNECT_HOME/classes/jconn4.jar:$JCONNECT_HOME/classes/jTDS3.jar
java -Xmx512m -DSYBASE=$SYBASE -DASEMON_LOGGER_HOME=$ASEMON_LOGGER_HOME -Dsun.net.inetaddr.ttl=0 asemon_logger/Asemon_logger $1 $2 $3 $4 $5 $6 $7 $8 $9 ${10} ${11} ${12} ${13} ${14} ${15} ${16} ${17} ${18} ${19} ${20} 
