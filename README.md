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
    mongorestore Mnemono

if you use the generator
    app/console doctrine:mongodb:fixtures:load

### create MnemonoBiz by FacebookPage
    app/console mnemono:sync:fbpagetobiz --action createFromFb --fbId xxxxx

### create user account
    http://yourServer/app_dev.php/register