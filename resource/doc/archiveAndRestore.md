# Archive example

## clear tmp database
	mongo < archiveClear.js

## archive and dump
Archive feed and post that created date is before 2016-05-01T00:00:00+0000

	php archive.php --date 2016-05-01T00:00:00+0000

Script may occurs exception because of timeout (data set too large), repeat the code until no exception occurs.

## Dump db
	mongodump --db MnemonoArchive -o MnemonoBefore201605part1
	tar zcvf MnemonoBefore201605part1.tgz MnemonoBefore201605part1/

# Restore example
## Resort archive
	./restore.sh