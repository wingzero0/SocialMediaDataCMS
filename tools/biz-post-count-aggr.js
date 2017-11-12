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
var targetColName = 'Post';
var outColName = 'BizPostCountStats';

var mapFunc = function () {
  var year = String(this.createAt.getFullYear());
  var month = String(this.createAt.getMonth() + 1);
  var date = String(this.createAt.getDate());
  var dateKey = [
    year,
    (1 === month.length) ? '0' + month : month,
    (1 === date.length) ? '0' + date : date,
  ].join('-');
  var key = {
    biz_id: this.mnemonoBiz.$id,
    date: dateKey,
  };
  emit(key, 1);
};
var reduceFunc = function (key, values) {
  var sum = 0;
  values.forEach(function (v) {
    sum += v;
  });
  return sum;
};

var res = db.runCommand({
  mapreduce: targetColName,
  map: mapFunc,
  reduce: reduceFunc,
  query: {createAt: {'$gte': from, '$lt': to}},
  out: {reduce: outColName},
});

var end = Date.now();
print((end - start) / 1000 + 's');
