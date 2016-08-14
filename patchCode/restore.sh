#!/bin/bash

for tgzFile in `ls MnemonoBefore*.tgz`
do
    tar zxvf $tgzFile
done

for folder in `ls -d MnemonoBefore*/`
do
    mongorestore --db Mnemono "$folder"/MnemonoArchive
done
