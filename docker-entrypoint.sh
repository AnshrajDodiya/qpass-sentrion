#!/bin/bash
set -e

# Start cron in the background for sentrion's scheduled jobs
service cron start

# Hand off to the main process (apache2-foreground by default)
exec "$@"
