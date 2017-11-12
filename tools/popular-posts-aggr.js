var start = Date.now();

var now = new Date();
var from = new Date(
  now.getFullYear(),
  now.getMonth(),
  now.getDate(),
  now.getHours() - 24
);
var to = new Date(
  now.getFullYear(),
  now.getMonth(),
  now.getDate(),
  now.getHours()
);
var year = String(from.getFullYear());
var month = String(from.getMonth() + 1);
var date = String(from.getDate());
var dateKey = [
  year,
  (month.length === 1) ? '0' + month : month,
  (date.length === 1) ? '0' + date : date,
].join('');
var pageColName = 'FacebookPage';
var postColName = 'FacebookFeed';
var targetColName = 'FacebookFeedTimestamp';
var tempColName = 'TempPopularPostAggr';
var outColName = 'PopularPost';
var metrics = [
  'angry',
  'haha',
  'like',
  'love',
  'sad',
  'wow',
  'comment',
  'share',
];

var mapFunc = function () {
  var payload = {
    angry: parseInt(this.reactions_angry_total_count, 10) || 0,
    haha: parseInt(this.reactions_haha_total_count, 10) || 0,
    like: parseInt(this.reactions_like_total_count, 10) || 0,
    love: parseInt(this.reactions_love_total_count, 10) || 0,
    sad: parseInt(this.reactions_sad_total_count, 10) || 0,
    wow: parseInt(this.reactions_wow_total_count, 10) || 0,
    comment: parseInt(this.comments_total_count, 10) || 0,
    share: parseInt(this.shares_total_count, 10) || 0,
    updatedAt: this.updateTime,
    batchTime: this.batchTime,
    postCreatedTime: this.post_created_time,
    snapshot: 1,
  };
  emit(this.fbFeed.$id.str, payload);
};
var reduceFunc = function (key, values) {
  var payload = {
    angry: 0,
    haha: 0,
    like: 0,
    love: 0,
    sad: 0,
    wow: 0,
    comment: 0,
    share: 0,
    updatedAt: null,
    batchTime: null,
    postCreatedTime: null,
    snapshot: 0,
  };
  values.forEach(function (v) {
    if (null === payload.updatedAt ||
        payload.updatedAt < v.updatedAt) {
      payload.angry = v.angry;
      payload.haha = v.haha;
      payload.like = v.like;
      payload.love = v.love;
      payload.sad = v.sad;
      payload.wow = v.wow;
      payload.comment = v.comment;
      payload.share = v.share;
      payload.updatedAt = v.updatedAt;
      payload.batchTime = v.batchTime;
      payload.postCreatedTime = v.postCreatedTime;
    }
    payload.snapshot += v.snapshot;
  });
  return payload;
};

var res = db.runCommand({
  mapreduce: targetColName,
  map: mapFunc,
  reduce: reduceFunc,
  query: {post_created_time: {'$gte': from, '$lt': to}},
  out: {replace: tempColName},
});

/* temp collection index */
var tempColIndexes = db[tempColName].getIndexes();
var tempColIndexNames = tempColIndexes.map(function (index) {
  return index.name;
});
metrics.forEach(function (metric) {
  var name = 'value.' + metric + '_-1';
  if (tempColIndexNames.indexOf(name) === -1) {
    var index = {};
    index['value.' + metric] = -1;
    db[tempColName].createIndex(index, {background: true});
  }
});

/* output collection index */
var outColIndexes = db[outColName].getIndexes();
var outColIndexNames = outColIndexes.map(function (index) {
  return index.name;
});
if (outColIndexNames.indexOf('key_-1') === -1) {
  db[outColName].createIndex({key: -1}, {background: true});
}

metrics.forEach(function (metric) {
  var sort = {};
  sort['value.' + metric] = -1;
  var itemCursor = db[tempColName].find().sort(sort).limit(20);
  var postObjectIds = [];
  var pageObjectIds = [];
  var itemObj = {};
  itemCursor.forEach(function (item) {
    postObjectIds.push(ObjectId(item._id));
    item.value._id = item._id;
    itemObj[item._id] = item.value;
  });
  var postCursor = db[postColName].find(
    {'_id': {'$in': postObjectIds}},
    {'_id': 1, 'fbID': 1, 'fbPage': 1}
  );
  postCursor.forEach(function (post) {
    pageObjectIds.push(post.fbPage.$id);
    var postIdStr = post._id.str;
    if (itemObj[postIdStr]) {
      itemObj[postIdStr].fbPostId = post.fbID;
      itemObj[postIdStr].fbPostLink = post.link;
      itemObj[postIdStr].fbPageId = post.fbID.split('_')[0];
    }
  });
  var pageCursor = db[pageColName].find(
    {'_id': {'$in': pageObjectIds}},
    {'_id': 0, 'fbID': 1, 'name': 1}
  );
  pageObj = {};
  pageCursor.forEach(function (page) {
    if (!pageObj[page.fbID]) {
      pageObj[page.fbID] = page.name;
    }
  });
  var docs = [];
  Object.keys(itemObj).forEach(function (key) {
    if (itemObj[key].fbPostId) {
      itemObj[key].fbPageName = pageObj[itemObj[key].fbPageId] ?
        pageObj[itemObj[key].fbPageId] : null;
      docs.push(itemObj[key]);
    }
  });
  db[outColName].update(
    {key: dateKey, metric: metric},
    {
      from: from,
      to: to,
      key: dateKey,
      metric: metric,
      value: docs,
    },
    {upsert: true}
  );
});

var end = Date.now();
print((end - start) / 1000 + 's');
