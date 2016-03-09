#!/bin/sh
sudo php app/console cache:clear --env=prod --no-debug
sudo php app/console cache:clear --env=dev --no-debug
sudo chown -R nobody app/cache

