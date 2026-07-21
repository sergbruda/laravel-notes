#!/bin/bash
cd /opt/webstack/src
git add -A
git diff --cached --quiet || git commit -m "Автосохранение: $(date +'%H:%M %d.%m')" --quiet
