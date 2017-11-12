var start = Date.now();

var now = new Date();
var from = new Date(
  now.getFullYear(),
  now.getMonth(),
  now.getDate() - 1
);
var to = new Date(
  now.getFullYear(),
  now.getMonth(),
  now.getDate()
);
var targetColName = 'FacebookFeedTimestamp';
var outColName = 'PostStats';

var mapFunc = function () {
  var year = String(this.updateTime.getFullYear());
  var month = String(this.updateTime.getMonth() + 1);
  var date = String(this.updateTime.getDate());
  var dateKey = [
    year,
    (1 === month.length) ? '0' + month : month,
    (1 === date.length) ? '0' + date : date,
  ].join('-');
  var key = {
    ref_page_id: this.fbPage.$id,
    ref_post_id: this.fbFeed.$id,
    date: dateKey,
  };
  var payload = {
    yesterday: {
      like: 0,
      comment: 0,
      share: 0,
      updated_at: null,
    },
    today: {
      like: parseInt(this.reactions_like_total_count, 10) || 0,
      comment: parseInt(this.comments_total_count, 10) || 0,
      share: parseInt(this.shares_total_count, 10) || 0,
      updated_at: this.updateTime,
    },
  };
  emit(key, payload);
  var tomorrow = new Date(this.updateTime.getFullYear(),
                          this.updateTime.getMonth(),
                          this.updateTime.getDate() + 1);
  var year = String(tomorrow.getFullYear());
  var month = String(tomorrow.getMonth() + 1);
  var date = String(tomorrow.getDate());
  var dateKey = [
    year,
    (1 === month.length) ? '0' + month : month,
    (1 === date.length) ? '0' + date : date,
  ].join('-');
  var key = {
    ref_page_id: this.fbPage.$id,
    ref_post_id: this.fbFeed.$id,
    date: dateKey,
  };
  var payload = {
    yesterday: {
      like: parseInt(this.reactions_like_total_count, 10) || 0,
      comment: parseInt(this.comments_total_count, 10) || 0,
      share: parseInt(this.shares_total_count, 10) || 0,
      updated_at: this.updateTime,
    },
    today: {
      like: 0,
      comment: 0,
      share: 0,
      updated_at: null,
    },
  };
  emit(key, payload);
};
var reduceFunc = function (key, values) {
  var payload = {
    yesterday: {
      like: 0,
      comment: 0,
      share: 0,
      updated_at: null,
    },
    today: {
      like: 0,
      comment: 0,
      share: 0,
      updated_at: null,
    },
  };
  var setPayloadYesterday = function (obj) {
    payload.yesterday = {
      like: obj.like,
      comment: obj.comment,
      share: obj.share,
      updated_at: obj.updated_at,
    };
  };
  var setPayloadToday = function (obj) {
    payload.today = {
      like: obj.like,
      comment: obj.comment,
      share: obj.share,
      updated_at: obj.updated_at,
    };
  };
  values.forEach(function (v) {
    if (null !== v.yesterday.updated_at &&
        null === payload.yesterday.updated_at) {
      setPayloadYesterday(v.yesterday);
    } else if (null !== v.yesterday.updated_at &&
               null !== payload.yesterday.updated_at) {
      if (v.yesterday.updated_at > payload.yesterday.updated_at) {
        setPayloadYesterday(v.yesterday);
      }
    }
    if (null !== v.today.updated_at &&
        null === payload.today.updated_at) {
      setPayloadToday(v.today);
    } else if (null !== v.today.updated_at &&
               null !== payload.today.updated_at) {
      if (v.today.updated_at > payload.today.updated_at) {
        setPayloadToday(v.today);
      }
    }
  });
  return payload;
};

var res = db.runCommand({
  mapreduce: targetColName,
  map: mapFunc,
  reduce: reduceFunc,
  query: {updateTime: {'$gte': from, '$lt': to}},
  out: {reduce: outColName},
});

var end = Date.now();
print((end - start) / 1000 + 's');
