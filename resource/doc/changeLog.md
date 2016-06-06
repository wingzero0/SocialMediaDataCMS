# Change Log
## Current supported api version
1. application/json (beta api, will be remove when version 1.1 released)
2. application/json;version=1.0 (current api, support multiple areaCode)

## History
### 2016-06-06 tag 1.3.2
Implemented new api /api/home with protobuf. It aims to reduce number of requests in homepage and protected the content.

It combine 3 api as one
/api/posts/hot
/api/ads/
/api/tags/

### 2016-05-05 tag 1.2.2
Add weibo source

### 2016-04-28 tag 1.2.0
Add video link at post
Affected api
1. /api/posts/ (include beta and 1.0)
2. /api/posts/hot (include beta and 1.0)

Add array field **video_links**

### 2016-02-26 tag 1.0
api version 1.0

Affected api
1. /api/posts/
2. /api/posts/hot
3. /api/tags/

* **areaCode** will not support after api version 1.0,
* **areaCodes[]** filter by multiple area codes with 'OR' operator , available at api version 1.0