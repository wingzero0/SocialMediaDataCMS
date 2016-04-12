### download Composer and install project package
    curl -s http://getcomposer.org/installer | php
    php composer.phar install
    app/console assets:install web --symlink --relative

### clear cache
    app/console cache:clear --env=prod
    app/console cache:clear --env=dev

### set permission in centos (with root permission)
    HTTPDUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
    setfacl -R -m u:"$HTTPDUSER":rwX -m u:webmaster:rwX app/cache app/logs
    setfacl -dR -m u:"$HTTPDUSER":rwX -m u:webmaster:rwX app/cache app/logs

### create collection and index (remove the post embedded attribute first)
    app/console doctrine:mongodb:schema:create

### create MnemonoBiz by FacebookPage
created by single fans page
    app/console mnemono:sync:fbpagetobiz --action createFromFb --fbId xxxxx
    app/console mnemono:sync:fbpagetobiz --action updateFromFb --fbId xxxxx
created by batch
    app/console mnemono:sync:fbpagetobiz --action createFromFbCollection
    app/console mnemono:sync:fbpagetobiz --action updateFromFbCollection

### create Post by FacebookFeed
created by single feed
    app/console mnemono:sync:fbfeedtopost --action createFromFb --fbId xxxxxx
    app/console mnemono:sync:fbfeedtopost --action updateFromFb --fbId xxxxxx
By batch
    app/console mnemono:sync:fbfeedtopost --action createFromFbCollection --fromDate 2015-06-01 --toDate 2015-06-30
    app/console mnemono:sync:fbfeedtopost --action updateFromFbCollection --fromDate 2015-06-01 --toDate 2015-06-30
    app/console mnemono:sync:fbfeedtopost --action removePosts --fromDate 2015-12-22T00:00:00+0000 --toDate 2015-12-26T00:00:00+0000 --removeSource

### create Biz or Post by Weibo
created by single page
    app/console mnemono:sync:weibopagetobiz --action createFromUid --uid xxx
    app/console mnemono:sync:weibopagetobiz --action updateFromUid --uid xxx
created by single feed
    app/console mnemono:sync:weibofeedtopost --action createFromMid --mid xxx
    app/console mnemono:sync:weibofeedtopost --action updateFromMid --mid xxx

### create user account
    http://yourServer/app_dev.php/register

### score
    app/console mnemono:biz:score --id xxxxx
    app/console mnemono:post:score --id xxxxx
    app/console mnemono:post:score --genTest

### review
    app/console mnemono:post:review

### background worker command
    app/console gearman:worker:execute MnemonoBackgroundServiceBundleServicesSyncFbPageService --no-interaction
    app/console gearman:worker:execute MnemonoBackgroundServiceBundleServicesSyncFbFeedService --no-interaction

    app/console gearman:worker:execute MnemonoBackgroundServiceBundleServicesSyncWeiboPageService --no-interaction
    app/console gearman:worker:execute MnemonoBackgroundServiceBundleServicesSyncWeiboFeedService --no-interaction

    app/console gearman:worker:execute MnemonoBackgroundServiceBundleServicesPostScoreService --no-interaction
    app/console gearman:worker:execute MnemonoBackgroundServiceBundleServicesPostReviewService --no-interaction

### oauth
    app/console mnemono:oauth-server:client:create --redirect-uri=http://api.mnemono.com/ --grant-type=authorization_code --grant-type=password --grant-type=refresh_token --grant-type=token --grant-type=client_credentials
    test url http://api.mnemono.com/oauth/v2/token?client_id=56889752dab392bc090041a7_4vqifn7du7i8og4gsg0kw4csc4k88okog400wsg40s4ggskcg0&client_secret=13mg9x3otg00w404k0ks4gsk8o4k4k40cso448s80okw4wccw4&grant_type=client_credentials