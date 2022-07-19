#!/bin/sh
#------------------------------------------------------------------------------
#переход в директорию текущего скрипта
cd $(dirname $(readlink -e $0))
#------------------------------------------------------------------------------
set -e

printf '=%.0s' {1..80} && echo ""
echo '>script: '$0


# Apache gets grumpy about PID files pre-existing
rm -f /usr/local/apache2/logs/httpd.pid

bash ./scripts/initialize.sh

exec apache2-foreground
#-------------------------------------------------------------------------------------------------------------------