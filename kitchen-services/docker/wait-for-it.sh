#!/usr/bin/env bash
#   Use this script to test if a given TCP host/port are available

WAITFORIT_TIMEOUT=15
WAITFORIT_STRICT=0
WAITFORIT_QUIET=0

echoerr() {
  if [ "$WAITFORIT_QUIET" -ne 1 ]; then echo "$@" 1>&2; fi
}

wait_for() {
  if [[ "$WAITFORIT_TIMEOUT" -gt 0 ]]; then
    echoerr "waiting $WAITFORIT_TIMEOUT seconds for $WAITFORIT_HOST:$WAITFORIT_PORT"
  else
    echoerr "waiting for $WAITFORIT_HOST:$WAITFORIT_PORT without a timeout"
  fi
  start_ts=$(date +%s)
  while :
  do
    nc -z "$WAITFORIT_HOST" "$WAITFORIT_PORT" > /dev/null 2>&1
    result=$?
    if [[ $result -eq 0 ]]; then
      end_ts=$(date +%s)
      echoerr "$WAITFORIT_HOST:$WAITFORIT_PORT is available after $((end_ts - start_ts)) seconds"
      break
    fi
    sleep 1
  done
  return $result
}

wait_for_wrapper() {
  # shellcheck disable=SC2086
  if [ "$WAITFORIT_WAITFORIT_STRICT" = "1" ]; then
    wait_for
  else
    wait_for &
    WAITFORIT_PID=$!
    trap "kill -INT -$WAITFORIT_PID" INT
    wait $WAITFORIT_PID
    RESULT=$?
    if [[ $RESULT -ne 0 ]]; then
      echoerr "wait-for-it.sh: timeout occurred"
    fi
    return $RESULT
  fi
}

WAITFORIT_HOST=$(echo "$1" | cut -d: -f1)
WAITFORIT_PORT=$(echo "$1" | cut -d: -f2)
shift
CMD="$@"

wait_for_wrapper
RESULT=$?

if [[ $RESULT -eq 0 ]]; then
  exec $CMD
else
  exit $RESULT
fi
