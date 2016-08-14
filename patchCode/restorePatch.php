<?php
/**
 * User: kit
 * Date: 14/08/2016
 * Time: 4:29 PM
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


$colMapping = new CollectionMapping();

reviseImportFromRef($colMapping->getCol(MNEMONO_BIZ));
reviseImportFromRef($colMapping->getCol(POST));
reviseMnemonoBiz($colMapping->getCol(POST));

function reviseImportFromRef(MongoDBCollection $col){
    $cursor = $col->find();

    foreach($cursor as $item){
        $backup = $item["importFromRef"];
        unset($item["importFromRef"]);
        $item["importFromRef"]["\$ref"] = $backup["\$ref"];
        $item["importFromRef"]["\$id"] = $backup["\$id"];
        $item["importFromRef"]["\$db"] = "Mnemono";
        $item["importFromRef"]["importFrom"] = $backup["importFrom"];
        $col->updateOne(["_id" => $item["_id"]], ['$set' => $item]);
    }
}


function reviseMnemonoBiz(MongoDBCollection $col){
    $cursor = $col->find();

    foreach($cursor as $item){
        $backup = $item["mnemonoBiz"];
        unset($item["mnemonoBiz"]);
        $item["mnemonoBiz"]["\$ref"] = $backup["\$ref"];
        $item["mnemonoBiz"]["\$id"] = $backup["\$id"];
        $item["mnemonoBiz"]["\$db"] = "Mnemono";
        $col->updateOne(["_id" => $item["_id"]], ['$set' => $item]);
    }
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

