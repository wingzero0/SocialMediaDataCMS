#/bin/bash

path=$(dirname $0)

if [ $(id -u) -eq 0 ];
then
  cd $path
  cd ..
  php bin/console cache:clear --env prod
  chown nginx:nginx -R var/cache/prod
  supervisorctl stop mnemonoPostReviewService:*
  supervisorctl stop mnemonoPostScoreService:*
  supervisorctl stop mnemonoSyncFbFeedService:*
  supervisorctl stop mnemonoSyncFbPageService:*
  supervisorctl stop mnemonoSyncWeiboFeedService:*
  supervisorctl stop mnemonoSyncWeiboPageService:*
  supervisorctl start mnemonoPostReviewService:*
  supervisorctl start mnemonoPostScoreService:*
  supervisorctl start mnemonoSyncFbFeedService:*
  supervisorctl start mnemonoSyncFbPageService:*
  supervisorctl start mnemonoSyncWeiboFeedService:*
  supervisorctl start mnemonoSyncWeiboPageService:*
else
  echo 'please run this script with root account'
fi
