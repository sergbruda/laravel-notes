#!/bin/bash
echo 'Starting deployment...'
docker compose up -d --build
echo 'Deployment finished!'
