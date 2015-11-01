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

### generate sample data
if you have the CMSTestData.tgz (include collection: MnemonoBiz, FacebookPage)
    tar zxvf CMSTestData.tgz
    mongorestore CMSTestData

if you use the generator
    app/console doctrine:mongodb:fixtures:load

### create MnemonoBiz by FacebookPage
created by single fans page
    app/console mnemono:sync:fbpagetobiz --action createFromFb --fbId xxxxx
created by batch
    app/console mnemono:sync:fbpagetobiz --action createFromFbCollection
    app/console mnemono:sync:fbpagetobiz --action updateFromFbCollection

### create Post by FacebookFeed
created by single feed
    app/console mnemono:sync:fbfeedtopost --action createFromFb --fbId xxxxxx
By batch
    app/console mnemono:sync:fbfeedtopost --action createFromFbCollection --fromDate 2015-06-01 --toDate 2015-06-30
    app/console mnemono:sync:fbfeedtopost --action updateFromFbCollection --fromDate 2015-06-01 --toDate 2015-06-30

### create user account
    http://yourServer/app_dev.php/register

### create collection and index (remove the post embedded attribute first)
    app/console doctrine:mongodb:schema:create

### rank
    app/console mnemono:rank:biz --id xxxxx
    app/console mnemono:rank:post --id xxxxx

### review
    app/console mnemono:post:review