# FB Posts Aggregration

## Popular FB Posts (sorting by metric)

### Objective

To find out facebook posts according to latest snapshot value of 8 metrics (angry, haha, like, love, sad, wow, comment, share).

### Intro

This aggregration process is schedued to be run at 18:00 daily, by the script `popular-posts-aggr.js`. The script is actually doing the following

1. map/reduce: obtain snapshot count and latest metric values of each posts created in last 24 hours, save in a temp collection `TempPopularPostAggr`. Note that `TempPopularPostAggr` will be replaced in each script running.
2. sort: for each metrics, fetch top N posts in `TempPopularPostAggr`. Save the results in the collection `PopularPost`.

### Aggregration schedule first-time setup

Add the following line in `crontab -e`

    > crontab -e
    0 18 * * * mongo 127.0.0.1/Mnemono /home/webmaster/mnemonoAPI/tools/popular-posts-aggr.js

### Aggregration results in CMS

Read the results in CMS [http://api.mnemono.com/cms/dashboard/popular-posts](http://api.mnemono.com/cms/dashboard/popular-posts)


## Trending FB Posts (sorting by metric diff per min)

### Objective

To find out trending facebook posts according to the changes of 8 metrics (angry, haha, like, love, sad, wow, comment, share).

### Intro

This aggregration process is schedued to be run at 18:01 daily, by the script `trending-posts-aggr.js`. The script is actually doing the following

1. map/reduce/finalize: fetch all post snapshots in the collection `FacebookFeedTimestamp` within aggregration time range, calculate metric changes per minute of each posts, save in a temp collection `TempTrendingPostAggr`. Note that `TempTrendingPostAggr` will be replaced in each script running.
2. sort: for each metrics, fetch top N posts in `TempTrendingPostAggr`. Save the results in the collection `TrendingPost`.

### Aggregration schedule first-time setup

Add the following line in `crontab -e`

    > crontab -e
    1 18 * * * mongo 127.0.0.1/Mnemono /home/webmaster/mnemonoAPI/tools/trending-posts-aggr.js

## Aggregration results in CMS

Read the results in CMS [http://api.mnemono.com/cms/dashboard/trending-posts](http://api.mnemono.com/cms/dashboard/trending-posts)
