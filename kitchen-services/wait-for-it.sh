#!/usr/bin/env bash
# wait-for-it.sh

set -e

host="$1"
shift
port="$1"
shift

timeout="${WAITFORIT_TIMEOUT:-15}"

until nc -z "$host" "$port"; do
  echo "⏳ Esperando $host:$port..."
  sleep 2
  timeout=$((timeout - 2))
  if [ "$timeout" -le 0 ]; then
    echo "❌ Timeout esperando $host:$port"
    exit 1
  fi
done

echo "✅ $host:$port está disponible"

exec "$@"
