#!/usr/bin/sh

# Script to use in your crontab to start asemon automaticaly
# Script for Linux (for other OS, change the 'ps' command. Ex. '/usr/ucb/ps auxww' for Solaris)
#
# Usage : crontab_start_asemon.sh [xxxxxx] [version_asemon]
#         (monitored_server : name of monitored server)
#         (asemon_version_dir : directory, under ASEMON_HOME, where asemon_logger distrib has been copied (Ex. asemon_V2.5)
#                               This allow you to support different versions of asemon_logger on same machine

USAGE="USAGE : crontab_start_asemon.sh [monitored_server] [asemon_version_dir]"


ASEMON_HOME=XXXXset the path of your asemon installation hereXXXX

cd $ASEMON_HOME

ASEMON_LOGGER_HOME=$ASEMON_HOME/$2
export ASEMON_LOGGER_HOME
CFGFILE=$ASEMON_HOME/conf/config_$1.xml
LOGDIR=$ASEMON_HOME/log
LOGFILE=$LOGDIR/$1.log



# checks
[ -z "$CFGFILE" -o ! -r "$CFGFILE" ] && { echo "$USAGE" ; exit 1 ; }
mkdir -p "$LOGDIR" 2>&-
[ ! -w "$LOGDIR" ] && { echo "$(date) : Cannot write to log directory or temp directory" >&2 ; exit 1 ; }

# check if asemon_logger is already started for this monitored server
# (for Solaris use "/usr/ucb/ps")
CNT=`ps auxww|grep "asemon_logger\/Asemon_logger.*config\_$1\.xml"|wc -l`
if [ $CNT = 1 ]
then
echo `date` ": Asemon_logger for $1 is already started, exit..."
exit 0
fi


echo `date` ": start asemon_logger "
# Start ASEMON_LOGGER
$ASEMON_LOGGER_HOME/asemon_logger.sh -c "$CFGFILE" >> "$LOGFILE"


