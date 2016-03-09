#!/bin/sh
WWW_USER=`ps axo user,group,comm | egrep '(apache|httpd)' | grep -v ^root | uniq | cut -d\  -f 1`
echo "Apache user is: $WWW_USER"
sudo php app/console cache:clear --env=prod --no-debug
sudo php app/console assetic:dump --env=prod --no-debug
sudo chown -R $WWW_USER app/cache/
sudo chown -R $WWW_USER app/logs/

