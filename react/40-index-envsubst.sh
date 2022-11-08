#!/bin/sh
# vim:sw=4:ts=4:et

set -e

entrypoint_log() {
    if [ -z "${NGINX_ENTRYPOINT_QUIET_LOGS:-}" ]; then
        echo "$@"
    fi
}

cd /usr/share/nginx/html
envsubst < index.html > index_env.html
entrypoint_log "Wrote enbsubst result to index_env.html"
mv index_env.html index.html
entrypoint_log "Moved index_env.html to index.html"
