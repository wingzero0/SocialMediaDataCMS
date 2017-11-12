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
var targetColName = 'Post';
var outColName = 'PostOverallStats';

var mapFunc = function () {
  var year = String(this.createAt.getFullYear());
  var month = String(this.createAt.getMonth() + 1);
  var date = String(this.createAt.getDate());
  var hour = this.createAt.getHours();
  var key = [
    year,
    (1 === month.length) ? '0' + month : month,
    (1 === date.length) ? '0' + date : date,
  ].join('-');
  var payload = {
    'count': 1,
    '00': (0 === hour) ? 1 : 0,
    '01': (1 === hour) ? 1 : 0,
    '02': (2 === hour) ? 1 : 0,
    '03': (3 === hour) ? 1 : 0,
    '04': (4 === hour) ? 1 : 0,
    '05': (5 === hour) ? 1 : 0,
    '06': (6 === hour) ? 1 : 0,
    '07': (7 === hour) ? 1 : 0,
    '08': (8 === hour) ? 1 : 0,
    '09': (9 === hour) ? 1 : 0,
    '10': (10 === hour) ? 1 : 0,
    '11': (11 === hour) ? 1 : 0,
    '12': (12 === hour) ? 1 : 0,
    '13': (13 === hour) ? 1 : 0,
    '14': (14 === hour) ? 1 : 0,
    '15': (15 === hour) ? 1 : 0,
    '16': (16 === hour) ? 1 : 0,
    '17': (17 === hour) ? 1 : 0,
    '18': (18 === hour) ? 1 : 0,
    '19': (19 === hour) ? 1 : 0,
    '20': (20 === hour) ? 1 : 0,
    '21': (21 === hour) ? 1 : 0,
    '22': (22 === hour) ? 1 : 0,
    '23': (23 === hour) ? 1 : 0,
  };
  emit(key, payload);
};
var reduceFunc = function (key, values) {
  var payload = {
    'count': 0,
    '00': 0,
    '01': 0,
    '02': 0,
    '03': 0,
    '04': 0,
    '05': 0,
    '06': 0,
    '07': 0,
    '08': 0,
    '09': 0,
    '10': 0,
    '11': 0,
    '12': 0,
    '13': 0,
    '14': 0,
    '15': 0,
    '16': 0,
    '17': 0,
    '18': 0,
    '19': 0,
    '20': 0,
    '21': 0,
    '22': 0,
    '23': 0,
  };
  values.forEach(function (v) {
    payload.count += v.count;
    payload['00'] += v['00'];
    payload['01'] += v['01'];
    payload['02'] += v['02'];
    payload['03'] += v['03'];
    payload['04'] += v['04'];
    payload['05'] += v['05'];
    payload['06'] += v['06'];
    payload['07'] += v['07'];
    payload['08'] += v['08'];
    payload['09'] += v['09'];
    payload['10'] += v['10'];
    payload['11'] += v['11'];
    payload['12'] += v['12'];
    payload['13'] += v['13'];
    payload['14'] += v['14'];
    payload['15'] += v['15'];
    payload['16'] += v['16'];
    payload['17'] += v['17'];
    payload['18'] += v['18'];
    payload['19'] += v['19'];
    payload['20'] += v['20'];
    payload['21'] += v['21'];
    payload['22'] += v['22'];
    payload['23'] += v['23'];
  });
  return payload;
};

var res = db.runCommand({
  mapreduce: targetColName,
  map: mapFunc,
  reduce: reduceFunc,
  query: {createAt: {'$gte': from, '$lt': to}},
  out: {replace: outColName},
});

var end = Date.now();
print((end - start) / 1000 + 's');
