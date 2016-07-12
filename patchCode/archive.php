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
define("MNEMONO_ARCHIVE", "MnemonoArchive");
define("FACEBOOK_PAGE","FacebookPage");
define("FACEBOOK_FEED","FacebookFeed");
define("FACEBOOK_FEED_TIMESTAMP","FacebookFeedTimestamp");
define("FACEBOOK_PAGE_TIMESTAMP","FacebookPageTimestamp");
define("POST","Post");
define("MNEMONO_BIZ", "MnemonoBiz");

use MongoDB\Client as MongoDBClient;
use MongoDB\Collection as MongoDBCollection;
use MongoDB\BSON\ObjectID as MongoObjectID;
use MongoDB\Model\IndexInfo;

$options = getopt("", array("date:"));
if (!isset($options["date"])){
    echo "you must specific date value for archiving";
    exit(-1);
}
$colMapping = new CollectionMapping();

$nDayAgo = \DateTime::createFromFormat(\DateTime::ISO8601, $options["date"]);
$nDayAgo->setTimezone(new DateTimeZone("GMT"));

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
    //$biz["importFromRef"]["\$db"] = MNEMONO_ARCHIVE;

    $id = $page["_id"];
    upsert($colMapping->getArchiveCol(MNEMONO_BIZ), $biz);
    $colMapping->archivePageAndPageTimestamp($id, $nDayAgo);

    $query = [
        "fbPage.\$id" => $id,
        "created_time" => ["\$lte" => $nDayAgo->format(\DateTime::ISO8601)]
    ];
    $options = [
        "sort" => ["created_time" => -1],
        "skip" => 25
    ];
    $feedCur = $colMapping->getCol(FACEBOOK_FEED)->find(
        $query,
        $options
    );

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
            //$post["importFromRef"]["\$db"] = MNEMONO_ARCHIVE;
            unset($post["importFromRef"]["\$db"]);
            //$post["mnemonoBiz"]["\$db"] = MNEMONO_ARCHIVE;
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

    public function archivePageAndPageTimestamp(MongoObjectID $pageMongoId, \DateTime $nDayAgo)
    {
        $mongoDate = new \MongoDB\BSON\UTCDatetime($nDayAgo->getTimestamp() * 1000);
        $query = [
            "batchTime" => ["\$lte" => $mongoDate],
            "fbPage.\$id" => $pageMongoId,
            "fbPage.\$ref" => FACEBOOK_PAGE,
        ];
        $cursor = $this->getCol(FACEBOOK_PAGE_TIMESTAMP)->find($query);
        foreach($cursor as $pageTimestamp){
            upsert($this->getArchiveCol(FACEBOOK_PAGE_TIMESTAMP), $pageTimestamp);
            deleteOne($this->getCol(FACEBOOK_PAGE_TIMESTAMP), $pageTimestamp);
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