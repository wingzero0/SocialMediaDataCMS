#!/bin/bash
BASEDIR=$(dirname $0)
cd $BASEDIR

php71 bin/console mnemono:post:review >> var/logs/crontab.log 2>> var/logs/crontab.err
