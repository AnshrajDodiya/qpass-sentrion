#!/bin/bash
set -e
mkdir -p certs
openssl req -x509 -newkey ec -pkeyopt ec_paramgen_curve:prime256v1 \
  -keyout certs/server.key -out certs/server.crt \
  -days 365 -nodes \
  -subj "/CN=qpass.local" \
  -addext "subjectAltName=DNS:qpass.local,DNS:localhost,IP:127.0.0.1"
echo "Generated certs/server.crt and certs/server.key"
