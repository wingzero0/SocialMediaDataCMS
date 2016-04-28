# API version
## Current supported version
1. application/json (beta api, will be remove when version 1.1 released)
2. application/json;version=1.0 (current api, support multiple areaCode)

## change log
### 2016-04-xx Add video link at post
Affected api
1. /api/posts/ (include beta and 1.0)
2. /api/posts/hot (include beta and 1.0)

Add array field **video_links**

### 2016-02-26 version 1.0 released
Affected api
1. /api/posts/
2. /api/posts/hot
3. /api/tags/

* **areaCode** will not support after version 1.0,
* **areaCodes[]** filter by multiple area codes with 'OR' operator , available at version 1.0