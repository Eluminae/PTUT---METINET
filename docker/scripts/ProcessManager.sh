#!/bin/sh

SCRIPT=$1
PIDFILE=$2
LOGFILE=$3
RUNAS=root

start() {
  if [ -f $PIDFILE ] && kill -0 $(cat $PIDFILE); then
    echo 'Process already running' >&2
    return 1
  fi
  echo 'Starting process…' >&2
  local CMD="$SCRIPT &> \"$LOGFILE\" & echo \$!"
  su -c "$CMD" $RUNAS > "$PIDFILE"
  echo 'Process started' >&2
}

stop() {
  if [ ! -f $PIDFILE ] || ! kill -0 $(cat $PIDFILE); then
    echo 'Process not running' >&2
    return 1
  fi
  echo 'Stopping process…' >&2
  kill -15 $(cat "$PIDFILE") && rm -f "$PIDFILE"
  echo 'Process stopped' >&2
}

case "$4" in
  start)
    start
    ;;
  stop)
    stop
    ;;
  restart)
    stop
    start
    ;;
  *)
    echo "Usage: $0 command pid_file_path log_file_path {start|stop|restart}"
esac
