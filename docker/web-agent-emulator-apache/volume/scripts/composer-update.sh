#!/usr/bin/env bash
#------------------------------------------------------------------------------
printf '=%.0s' {1..80} && echo ""
echo '>script: '$0
echo 'pwd:'
pwd
echo 'home:'
ls -l ~
echo '.composer:'
ls -l ~/.composer

cd /app/www
echo 'pwd:'
pwd
ls -l

echo 'run composer update:'
composer --no-interaction -www update
#------------------------------------------------------------------------------
