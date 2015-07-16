### download Composer and install project package
    curl -s http://getcomposer.org/installer | php
    php composer.phar install

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
    app/console mnemono:sync:fbpagetobiz --action createFromFb --fbId xxxxx

### create Post by FacebookFeed
created by single feed
    app/console mnemono:sync:fbfeedtopost --action createFromFb --fbId xxxxxx
created by batch
    app/console mnemono:sync:fbfeedtopost --action dumpFromFb --fromDate 2015-06-01 --toDate 2015-06-30

### create user account
    http://yourServer/app_dev.php/register