#!/bin/bash
BASEDIR=$(dirname $0)
cd $BASEDIR

app/console mnemono:post:review >> app/logs/crontab.log 2>> app/logs/crontab.err