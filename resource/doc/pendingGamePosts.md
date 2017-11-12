# Pending Game Posts

### Objective

To find out pending game posts according to game-relatd keywords matching, and dispaly this pending list in CMS for review.

### First-time setup

Add the following line in `crontab -e`

    > crontab -e
    2 16,21 * * * mongo 127.0.0.1/Mnemono /home/webmaster/mnemonoAPI/tools/pending-game-posts.js

### Pending list in CMS

See [http://api.mnemono.com/cms/dashboard/pending-game-posts](http://api.mnemono.com/cms/dashboard/pending-game-posts)
