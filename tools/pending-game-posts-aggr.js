var start = Date.now();

var now = new Date();
var from = new Date(
  now.getFullYear(),
  now.getMonth(),
  now.getDate() - 7
);
var to = new Date(
  now.getFullYear(),
  now.getMonth(),
  now.getDate()
);
var targetColName = 'PendingGamePost';
var outColName = 'PendingGamePostStats';
var postColName = 'Post';

var mapFunc = function () {
  var year = String(this.created_at.getFullYear());
  var month = String(this.created_at.getMonth() + 1);
  var date = String(this.created_at.getDate());
  var key = [
    year,
    (1 === month.length) ? '0' + month : month,
    (1 === date.length) ? '0' + date : date,
  ].join('-');
  var payload = {
    count: 1,
    k_count: (true === this.by_k) ? 1 : 0,
    nb_count: (true === this.by_nb) ? 1 : 0,
    knb_count: (true === this.by_k && true === this.by_nb) ? 1 : 0,
    game_count: 0,
  };
  emit(key, payload);
};
var reduceFunc = function (key, values) {
  var payload = {
    count: 0,
    k_count: 0,
    nb_count: 0,
    knb_count: 0,
    game_count: 0,
  };
  values.forEach(function (v) {
    payload.count += v.count;
    payload.k_count += v.k_count;
    payload.nb_count += v.nb_count;
    payload.knb_count += v.knb_count;
    payload.game_count += v.game_count;
  });
  return payload;
};

db.runCommand({
  mapreduce: targetColName,
  map: mapFunc,
  reduce: reduceFunc,
  query: {created_at: {'$gte': from, '$lt': to}},
  out: {replace: outColName},
});

var cursor = db[targetColName].find({created_at: {'$gte': from, '$lt': to}}, {_id: 1});
var ids = [];
cursor.forEach(x => {
  ids.push(x._id);
});

var mapFunc = function () {
  var year = String(this.createAt.getFullYear());
  var month = String(this.createAt.getMonth() + 1);
  var date = String(this.createAt.getDate());
  var key = [
    year,
    (1 === month.length) ? '0' + month : month,
    (1 === date.length) ? '0' + date : date,
  ].join('-');
  if (this.tags && this.tags.indexOf('game') > 0) {
    var payload = {
      count: 0,
      k_count: 0,
      nb_count: 0,
      knb_count: 0,
      game_count: 1,
    };
    emit(key, payload);
  }
};
var reduceFunc = function (key, values) {
  var payload = {
    count: 0,
    k_count: 0,
    nb_count: 0,
    knb_count: 0,
    game_count: 0,
  };
  values.forEach(function (v) {
    payload.count += v.count;
    payload.k_count += v.k_count;
    payload.nb_count += v.nb_count;
    payload.knb_count += v.knb_count;
    payload.game_count += v.game_count;
  });
  return payload;
};

db.runCommand({
  mapreduce: postColName,
  map: mapFunc,
  reduce: reduceFunc,
  query: {_id: {'$in': ids}},
  out: {reduce: outColName},
});

var end = Date.now();
print((end - start) / 1000 + 's');
