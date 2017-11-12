### change log
[change log](resource/doc/changeLog.md)

### business logic
[doc](resource/doc/index.md)

### download Composer and install project package
    curl -s http://getcomposer.org/installer | php
    php composer.phar install
    php bin/console assets:install web --symlink --relative

### clear cache
    php bin/console cache:clear --env=prod
    php bin/console cache:clear --env=dev

### set permission in centos (with root permission)
    HTTPDUSER=$(ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1)
    setfacl -dR -m u:"$HTTPDUSER":rwX -m u:webmaster:rwX var
    setfacl -R -m u:"$HTTPDUSER":rwX -m u:webmaster:rwX var

### create collection and index (remove the post embedded attribute first)
    php bin/console doctrine:mongodb:schema:create

### create CMS Admin user
    php bin/console fos:user:create admin
    php bin/console fos:user:promote admin ROLE_ADMIN
    php bin/console fos:user:change-password admin newp@ssword

### create MnemonoBiz by FacebookPage

created by single fans page

    php bin/console mnemono:sync:fbpagetobiz --action createFromFb --fbId xxxxx
    php bin/console mnemono:sync:fbpagetobiz --action updateFromFb --fbId xxxxx

created by batch

    php bin/console mnemono:sync:fbpagetobiz --action createFromFbCollection
    php bin/console mnemono:sync:fbpagetobiz --action updateFromFbCollection

### create Post by FacebookFeed

created by single feed

    php bin/console mnemono:sync:fbfeedtopost --action createFromFb --fbId xxxxxx
    php bin/console mnemono:sync:fbfeedtopost --action updateFromFb --fbId xxxxxx

By batch

    php bin/console mnemono:sync:fbfeedtopost --action createFromFbCollection --fromDate 2015-06-01 --toDate 2015-06-30
    php bin/console mnemono:sync:fbfeedtopost --action updateFromFbCollection --fromDate 2015-06-01 --toDate 2015-06-30
    php bin/console mnemono:sync:fbfeedtopost --action removePosts --fromDate 2015-12-22T00:00:00+0000 --toDate 2015-12-26T00:00:00+0000 --removeSource

### create Biz or Post by Weibo

created by single page

    php bin/console mnemono:sync:weibopagetobiz --action createFromUid --uid xxx
    php bin/console mnemono:sync:weibopagetobiz --action updateFromUid --uid xxx

created by single feed

    php bin/console mnemono:sync:weibofeedtopost --action createFromMid --mid xxx
    php bin/console mnemono:sync:weibofeedtopost --action updateFromMid --mid xxx

### create user account
    http://yourServer/app_dev.php/register

### score
    php bin/console mnemono:biz:score --id xxxxx
    php bin/console mnemono:post:score --id xxxxx
    php bin/console mnemono:post:score --genTest

### review
    php bin/console mnemono:post:review

### background worker command
    php bin/console gearman:worker:execute MnemonoBackgroundServiceBundleServicesSyncFbPageService --no-interaction
    php bin/console gearman:worker:execute MnemonoBackgroundServiceBundleServicesSyncFbFeedService --no-interaction

    php bin/console gearman:worker:execute MnemonoBackgroundServiceBundleServicesSyncWeiboPageService --no-interaction
    php bin/console gearman:worker:execute MnemonoBackgroundServiceBundleServicesSyncWeiboFeedService --no-interaction

    php bin/console gearman:worker:execute MnemonoBackgroundServiceBundleServicesPostScoreService --no-interaction
    php bin/console gearman:worker:execute MnemonoBackgroundServiceBundleServicesPostReviewService --no-interaction

### oauth
    php bin/console mnemono:oauth-server:client:create --redirect-uri=http://api.mnemono.com/ --grant-type=authorization_code --grant-type=password --grant-type=refresh_token --grant-type=token --grant-type=client_credentials
    test url http://api.mnemono.com/oauth/v2/token?client_id=56889752dab392bc090041a7_4vqifn7du7i8og4gsg0kw4csc4k88okog400wsg40s4ggskcg0&client_secret=13mg9x3otg00w404k0ks4gsk8o4k4k40cso448s80okw4wccw4&grant_type=client_credentials

### Redeploy
[redeploy](/resource/doc/redeploy.md)

### FB Posts Aggregration
[Basics](/resource/doc/fbPostsAggr.md)

[Pending Game Posts](/resource/doc/pendingGamePosts.md)
