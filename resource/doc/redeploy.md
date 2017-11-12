# redeploy step if you configured supervisor

1. wait until gearman job finish (or backup current job queue)
```
watch -n 3 "gearadmin --status" #coding to viewing current job queue
```

2. stop worker by program supervisor with root permission
```
su
supervisorctl -i
stop mnemonoPostReviewService:*
stop mnemonoPostScoreService:*
stop mnemonoSyncFbFeedService:*
stop mnemonoSyncFbPageService:*
exit # exit supervisor
exit # exit root permission
```

2. update repo by git with normal account permission
```
git checkout master
git fetch origin --prune
git merge origin/master
composer.phar install #install new dependent php package if need
./clear.sh
```

3. start worker by program supervisor with root permission
```
su
supervisorctl -i
start mnemonoPostReviewService:*
start mnemonoPostScoreService:*
start mnemonoSyncFbFeedService:*
start mnemonoSyncFbPageService:*
exit # exit supervisor
exit # exit root permission
```