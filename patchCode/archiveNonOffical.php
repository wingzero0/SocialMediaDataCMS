<?php
/**
 * User: kit
 * Date: 3-Jul-16
 * Time: 9:01 AM
 *
 * Archive the post which is 1 months old and it's not the latest 50 feed;
 */

require_once __DIR__ . "/vendor/autoload.php";
define("MNEMONO", "Mnemono");
define("MNEMONO_ARCHIVE", "MnemonoUnOfficialArchive");
define("FACEBOOK_PAGE","FacebookPage");
define("FACEBOOK_FEED","FacebookFeed");
define("FACEBOOK_FEED_TIMESTAMP","FacebookFeedTimestamp");
define("FACEBOOK_PAGE_TIMESTAMP","FacebookPageTimestamp");
define("POST","Post");
define("MNEMONO_BIZ", "MnemonoBiz");

use MongoDB\Client as MongoDBClient;
use MongoDB\Collection as MongoDBCollection;
use MongoDB\Model\IndexInfo;

$colMapping = new CollectionMapping();

$pageCur = $colMapping->getCol(FACEBOOK_PAGE)->find();
foreach ($pageCur as $page)
{
    upsert($colMapping->getArchiveCol(FACEBOOK_PAGE), $page);
    $biz = $colMapping->getCol(MNEMONO_BIZ)->findOne(
        [
            "importFromRef.\$id" => $page["_id"],
            "importFromRef.\$ref" => FACEBOOK_PAGE,
        ]
    );
    unset($biz["importFromRef"]["\$db"]);

    $id = $page["_id"];
    upsert($colMapping->getArchiveCol(MNEMONO_BIZ), $biz);

    $query = [
        "fbPage.\$id" => $id,
        "\$where" => "this.fbID.indexOf(this.from.id) != 0"
    ];
    $feedCur = $colMapping->getCol(FACEBOOK_FEED)->find($query);

    foreach ($feedCur as $feed)
    {
        upsert($colMapping->getArchiveCol(FACEBOOK_FEED), $feed);
        deleteOne($colMapping->getCol(FACEBOOK_FEED), $feed);
        $historyCur = $colMapping->getCol(FACEBOOK_FEED_TIMESTAMP)->find(
            ["fbFeed.\$id" => $feed["_id"]]
        );
        foreach($historyCur as $timestamp){
            upsert($colMapping->getArchiveCol(FACEBOOK_FEED_TIMESTAMP), $timestamp);
            deleteOne($colMapping->getCol(FACEBOOK_FEED_TIMESTAMP), $timestamp);
        }

        $postCur = $colMapping->getCol(POST)->find(
            [
                "importFromRef.\$id" => $feed["_id"],
                "importFromRef.\$ref" => FACEBOOK_FEED,
            ]
        );
        foreach($postCur as $post){
            unset($post["importFromRef"]["\$db"]);
            unset($post["mnemonoBiz"]["\$db"]);
            upsert($colMapping->getArchiveCol(POST), $post);
            deleteOne($colMapping->getCol(POST), $post);
        }
    }
}

function upsert(MongoDBCollection $col, \ArrayObject $oldData = null){
    if ($oldData == null){
        return;
    }
    $col->updateOne(
        ["_id" => $oldData["_id"]],
        ["\$set" => $oldData],
        ["upsert" => true]
    );
}

function deleteOne(MongoDBCollection $col, \ArrayObject $oldData){
    $col->deleteOne(["_id" => $oldData["_id"]]);
}

class CollectionMapping{
    private $colKeys;
    private $colArray;
    private $archiveColArray;
    private $cli;
    public function __construct()
    {
        $this->cli = new MongoDBClient();
        $this->colKeys = array(FACEBOOK_PAGE,FACEBOOK_FEED,FACEBOOK_FEED_TIMESTAMP,FACEBOOK_PAGE_TIMESTAMP,POST,MNEMONO_BIZ);
        $this->colArray = array();
        $this->archiveColArray = array();
        foreach($this->colKeys as $key){
            $this->colArray[$key] = $this->cli->selectCollection(MNEMONO, $key);
            $this->archiveColArray[$key] = $this->cli->selectCollection(MNEMONO_ARCHIVE, $key);
        }
        $this->copyIndex();
    }

    public function copyIndex(){
        foreach($this->colKeys as $key){
            $indexIterator = $this->getCol($key)->listIndexes();
            foreach ($indexIterator as $index){
                if ($index instanceof IndexInfo){
                    if ($index->getName() != "_id_"){
                        $this->getArchiveCol($key)->createIndex($index->getKey());
                    }
                }
            }
        }
    }

    /**
     * @param string $key
     * @return MongoDBCollection
     */
    public function getCol($key){
        return $this->colArray[$key];
    }

    /**
     * @param string $key
     * @return MongoDBCollection
     */
    public function getArchiveCol($key){
        return $this->archiveColArray[$key];
    }
}