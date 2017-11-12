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

var targetColName = 'Post';
var outColName = 'PendingGamePost';
var keywords = [
  '加送',
  '送出',
  '送你',
  '送您',
  '抽奬',
  '奬品',
  '贏取',
  '贏得',
  '個朋友',
  '位朋友',
  '送免費',
  '免費送',
  '免費參',
  '免費換',
  '免費兌',
  '免費獲',
  '免費體驗',
  '體驗免費',
  '獲取免費',
  '獲得免費',
  '得到免費',
  '參加辦法',
  '有奬遊戲',
  '毋須消費',
  '毋需消費',
  '無須消費',
  '無需消費',
  'chance',
  'giveaway',
  'opportunity',
  'opportunities',
  'prize',
  'tag your friend',
  '59分',
  ':59',
  '：59',
];
var compoundKeywords = [
//  ['like', 'share'],
];
var excludedKeywords = [
  '免費登記',
  '想隨時贏取禮物優惠及接收時尚資訊',
  '免費送貨',
  '贏得無數獎項',
  'to get a chance to have your photo featured here',
  'OH！Chance！澳燦旅行資訊',
  '2018年免費換新身份證',
];

var re = new RegExp('(' + keywords.join(')|(') + ')');

var targetColIndexes = db[targetColName].getIndexes();
var targetColIndexNames = targetColIndexes.map(function (index) {
  return index.name;
});
if (targetColIndexNames.indexOf('createAt_-1') === -1) {
  db[targetColName].createIndex({createAt: -1}, {background: true});
}
var cursor = db[targetColName].find(
  {
    createAt: {'$gte': from, '$lt': to},
  },
  {
    content: 1,
    createAt: 1,
    importFrom: 1,
    importFromRef: 1,
  }
);

var isMatched = function (str) {
  if ('string' !== typeof str) {
    return false;
  }
  str = str.toLowerCase();
  for (var i = 0, l = excludedKeywords.length; i < l; i++) {
    var excludedRe = new RegExp(excludedKeywords[i], 'g');
    str = str.replace(excludedRe, ' ');
  }
  if (re.test(str)) {
    return true;
  }
  for (var i = 0, l = compoundKeywords.length; i < l; i++) {
    var res = compoundKeywords[i].map(function (keyword) {
      return -1 !== str.indexOf(keyword);
    });
    if (-1 === res.indexOf(false)) {
      return true;
    }
  }
  return false;
};

cursor.forEach(function (x) {
  if (isMatched(x.content)) {
    db[outColName].updateOne(
      {
        _id: x._id,
      },
      {
        $set: {
          created_at: x.createAt,
          import_from: x.importFrom,
          import_from_ref: DBRef(x.importFromRef.$ref, x.importFromRef.$id),
          by_k: true,
        }
      },
      {
        upsert: true,
      }
    );
  }
});

var outColIndexes = db[outColName].getIndexes();
var outColIndexNames = outColIndexes.map(function (index) {
  return index.name;
});
if (outColIndexNames.indexOf('created_at_-1') === -1) {
  db[outColName].createIndex({created_at: -1}, {background: true});
}

var end = Date.now();
print((end - start) / 1000 + 's');
