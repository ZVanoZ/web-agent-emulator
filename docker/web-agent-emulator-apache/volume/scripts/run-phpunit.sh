#!/usr/bin/env bash
#------------------------------------------------------------------------------
printf '=%.0s' {1..80} && echo ""
echo '>script: '$0
cd /app/www
echo 'pwd:'
pwd

composer test
#------------------------------------------------------------------------------
