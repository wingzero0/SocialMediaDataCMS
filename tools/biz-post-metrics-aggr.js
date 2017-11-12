var start = Date.now();

var now = new Date();
var from = new Date(
  now.getFullYear(),
  now.getMonth(),
  now.getDate() - 1
);
var year = String(from.getFullYear());
var month = String(from.getMonth() + 1);
var date = String(from.getDate());
var fromDateKey = [
  year,
  (1 === month.length) ? '0' + month : month,
  (1 === date.length) ? '0' + date : date,
].join('-');
var to = new Date(
  now.getFullYear(),
  now.getMonth(),
  now.getDate()
);
var year = String(to.getFullYear());
var month = String(to.getMonth() + 1);
var date = String(to.getDate());
var toDateKey = [
  year,
  (1 === month.length) ? '0' + month : month,
  (1 === date.length) ? '0' + date : date,
].join('-');
var targetColName = 'PostStats';
var outColName = 'BizPostMetricStats';

var mapFunc = function () {
  var key = {
    ref_page_id: this._id.ref_page_id,
    date: this._id.date,
  };
  var payload = {
    like: 0,
    comment: 0,
    share: 0,
  };
  if (null !== this.value.today.updated_at) {
    payload.like = this.value.today.like - this.value.yesterday.like;
    payload.comment = this.value.today.comment - this.value.yesterday.comment;
    payload.share = this.value.today.share - this.value.yesterday.share;
    emit(key, payload);
  }
};
var reduceFunc = function (key, values) {
  var payload = {
    like: 0,
    comment: 0,
    share: 0,
  };
  values.forEach(function (v) {
    payload.like += v.like;
    payload.comment += v.comment;
    payload.share += v.share;
  });
  return payload;
};

var res = db.runCommand({
  mapreduce: targetColName,
  map: mapFunc,
  reduce: reduceFunc,
  query: {'_id.date': {'$gte': fromDateKey, '$lt': toDateKey}},
  out: {reduce: outColName},
});

var end = Date.now();
print((end - start) / 1000 + 's');
