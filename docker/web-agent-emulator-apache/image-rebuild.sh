#!/usr/bin/env bash
#------------------------------------------------------------------------------
#переход в директорию текущего скрипта
cd $(dirname $(readlink -e $0))
#------------------------------------------------------------------------------
# Сборка
#-----
printf '=%.0s' {1..80} && echo ""
echo '>> '$0
bash image-remove.sh
bash image-build.sh
#------------------------------------------------------------------------------
