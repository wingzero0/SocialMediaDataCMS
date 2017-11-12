const start = Date.now();

const now = new Date();
const from = new Date(
  now.getFullYear(),
  now.getMonth(),
  now.getDate(),
  now.getHours() - 24
);
const to = new Date(
  now.getFullYear(),
  now.getMonth(),
  now.getDate(),
  now.getHours()
);

const targetColName = 'Post';
const outColName = 'PendingGamePost';
const keywords = {
  '加送': {
    game: 5,
    nonGame: 112,
    ratio: [1.025638606653522, 0.14431150293870695],
  },
  '送出': {
    game: 287,
    nonGame: 265,
    ratio: [0.8151325550648928, 3.5009426041221112],
  },
  '送你': {
    game: 212,
    nonGame: 482,
    ratio: [0.9396358476379657, 1.4217976455504808],
  },
  '送您': {
    game: 64,
    nonGame: 132,
    ratio: [0.9798588029551021, 1.56731038343129],
  },
  '抽奬': {
    game: 6,
    nonGame: 19,
    ratio: [0.9998967721234763, 1.0208139997348533],
  },
  '奬品': {
    game: 22,
    nonGame: 30,
    ratio: [0.9892364637046611, 2.3705569549398264],
  },
  '贏取': {
    game: 212,
    nonGame: 286,
    ratio: [0.8879611489463155, 2.3961764515920687],
  },
  '贏得': {
    game: 108,
    nonGame: 219,
    ratio: [0.9641645860894189, 1.5941478899968944],
  },
  '個朋友': {
    game: 49,
    nonGame: 153,
    ratio: [0.9985403555246078, 1.0352699714086586],
  },
  '位朋友': {
    game: 540,
    nonGame: 212,
    ratio: [0.5784519132637165, 8.233924243144337],
  },
  '送免費': {
    game: 0,
    nonGame: 13,
    ratio: [1.0033880635913475, 0],
  },
  '免費送': {
    game: 37,
    nonGame: 200,
    ratio: [1.0220259250313424, 0.5980268681780017],
  },
  '免費參': {
    game: 18,
    nonGame: 67,
    ratio: [1.0023297917037042, 0.868453701266965],
  },
  '免費換': {
    game: 12,
    nonGame: 80,
    ratio: [1.010930787270577, 0.4848866498740554],
  },
  '免費兌': {
    game: 1,
    nonGame: 8,
    ratio: [1.0012408699464272, 0.4040722082283795],
  },
  '免費獲': {
    game: 65,
    nonGame: 243,
    ratio: [1.0091162882509683, 0.8646812686368616],
  },
  '免費體驗': {
    game: 30,
    nonGame: 39,
    ratio: [0.9847868459787953, 2.486598204482336],
  },
  '體驗免費': {
    game: 0,
    nonGame: 0,
    ratio: [1, NaN],
  },
  '獲取免費': {
    game: 2,
    nonGame: 0,
    ratio: [0.998320738874895, Infinity],
  },
  '獲得免費': {
    game: 3,
    nonGame: 9,
    ratio: [0.9998183460043006, 1.077525888609012],
  },
  '得到免費': {
    game: 5,
    nonGame: 2,
    ratio: [0.9963194157148817, 8.08144416456759],
  },
  '參加辦法': {
    game: 197,
    nonGame: 36,
    ratio: [0.8424704247068887, 17.68938333799795],
  },
  '有奬遊戲': {
    game: 4,
    nonGame: 3,
    ratio: [0.9974186871163743, 4.310103554436048],
  },
  '毋須消費': {
    game: 2,
    nonGame: 15,
    ratio: [1.0022255136032192, 0.4310103554436048],
  },
  '毋需消費': {
    game: 0,
    nonGame: 0,
    ratio: [1, NaN],
  },
  '無須消費': {
    game: 0,
    nonGame: 0,
    ratio: [1, NaN],
  },
  '無需消費': {
    game: 0,
    nonGame: 1,
    ratio: [1.0002598077422709, 0],
  },
  'giveaway': {
    game: 111,
    nonGame: 66,
    ratio: [0.9226173042001053, 5.436607892527288],
  },
  'opportunity': {
    game: 2,
    nonGame: 160,
    ratio: [1.0416083589887117, 0.04040722082283795],
  },
  'prize': {
    game: 73,
    nonGame: 225,
    ratio: [0.9969715394192072, 1.0487918649127717],
  },
  'tag your friend': {
    game: 3,
    nonGame: 35,
    ratio: [1.0066323111408961, 0.2770780856423174],
  },
  '59分': {
    game: 181,
    nonGame: 39,
    ratio: [0.8567051803949899, 15.00247583371009],
  },
  ':59': {
    game: 279,
    nonGame: 323,
    ratio: [0.8358692461679209, 2.792226528686511],
  },
  '：59': {
    game: 14,
    nonGame: 20,
    ratio: [0.9934057213259586, 2.2628043660789254],
  },
  '截止': {
    game: 524,
    nonGame: 154,
    ratio: [0.5833683179401064, 10.999160369437448],
  },
  '條款及細則': {
    game: 383,
    nonGame: 277,
    ratio: [0.731016723758255, 4.4695929458908115],
  },
  '名額': {
    game: 434,
    nonGame: 166,
    ratio: [0.6642403075545782, 8.451437993788756],
  },
  '得奬': {
    game: 23,
    nonGame: 15,
    ratio: [0.9845243060458874, 4.956619087601455],
  },
  '參加方法': {
    game: 194,
    nonGame: 14,
    ratio: [0.8401668229482678, 44.79429051217465],
  },
  'jetso': {
    game: 96,
    nonGame: 24,
    ratio: [0.9251627140827509, 12.930310663308145],
  },
  '有機會': {
    game: 619,
    nonGame: 388,
    ratio: [0.534094287941382, 5.157127770997256],
  },
  '專人通知': {
    game: 123,
    nonGame: 10,
    ratio: [0.8990606633081444, 39.76070528967254],
  },
  '遊戲': {
    game: 526,
    nonGame: 257,
    ratio: [0.5982922760297743, 6.616092810214089],
  },
  '將保留': {
    game: 99,
    nonGame: 20,
    ratio: [0.9216644415360635, 16.00125944584383],
  },
  '填寫個人資料': {
    game: 95,
    nonGame: 4,
    ratio: [0.9211921793412459, 76.7737195633921],
  },
  '簡單問題': {
    game: 82,
    nonGame: 2,
    ratio: [0.931634259719902, 132.53568429890848],
  },
  '以下步驟': {
    game: 96,
    nonGame: 14,
    ratio: [0.9227509239000534, 22.166246851385388],
  },
  '送完即止': {
    game: 77,
    nonGame: 233,
    ratio: [0.9956017472301129, 1.0682767393505657],
  },
  '報名': {
    game: 53,
    nonGame: 202,
    ratio: [1.0084082740436313, 0.8481515657863016],
  },
  '收集': {
    game: 61,
    nonGame: 11,
    ratio: [0.9515011102851135, 17.926112510495383],
  },
  '完成以下': {
    game: 77,
    nonGame: 20,
    ratio: [0.9402327727758011, 12.44542401343409],
  },
  '可免費': {
    game: 59,
    nonGame: 262,
    ratio: [1.0198656403891317, 0.7279468789457829],
  },
  '優先場': {
    game: 34,
    nonGame: 13,
    ratio: [0.9747439039254314, 8.45443389523994],
  },
  '回答以下': {
    game: 45,
    nonGame: 1,
    ratio: [0.9624666160139734, 145.46599496221663],
  },
  '設定為公開': {
    game: 62,
    nonGame: 7,
    ratio: [0.9496695770800738, 28.631402183039462],
  },
  '着數': {
    "game": 7,
    "nonGame": 4,
    "ratio": [0.9951565149087912, 5.657010915197313],
  },
};
const threshold = 2.586062132661629;

const classify = (content) => {
  const m = content ? content.toLowerCase() : null;
  let ratio = 1;
  if (m) {
    Object.keys(keywords).forEach(w => {
      if (m.indexOf(w) !== -1) {
        if (!isNaN(keywords[w].ratio[1])) {
          ratio = ratio * keywords[w].ratio[1];
        }
      } else {
        if (!isNaN(keywords[w].ratio[0])) {
          ratio = ratio * keywords[w].ratio[0];
        }
      }
    });
    return (ratio > threshold) ? 'game' : null;
  }
  return null;
};

const cursor = db[targetColName].find(
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

cursor.forEach(function (x) {
  if ('game' === classify(x.content)) {
    db[outColName].updateOne(
      {
        _id: x._id,
      },
      {
        $set: {
          created_at: x.createAt,
          import_from: x.importFrom,
          import_from_ref: DBRef(x.importFromRef.$ref, x.importFromRef.$id),
          by_nb: true,
        }
      },
      {
        upsert: true,
      }
    );
  }
});

const outColIndexes = db[outColName].getIndexes();
const outColIndexNames = outColIndexes.map(function (index) {
  return index.name;
});
if (outColIndexNames.indexOf('created_at_-1') === -1) {
  db[outColName].createIndex({created_at: -1}, {background: true});
}

const end = Date.now();
print((end - start) / 1000 + 's');
