<?php
/**
 * User: kit
 * Date: 11-Mar-16
 * Time: 9:25 AM
 */

/**
 * @param array $feed
 * @return bool
 */
function isRefByOtherCollection($feed){
    $cli = new \MongoClient();
    $col = $cli->selectCollection("Mnemono", "Post");
    $cursor = $col->find(array(
        "importFromRef.\$ref" => "FacebookFeed",
        "importFromRef.\$id" => $feed["_id"],
        "importFromRef.\$db" => "Mnemono",
        "importFromRef.importFrom" => "AppBundle\\Document\\Facebook\\FacebookFeed"
    ));
    if ($cursor->hasNext()){
        return true;
    }
    return false;
}

/**
 * @param $feed
 */
function removeFeedAndTimestampRef($feed){
    $cli = new \MongoClient();
    $col = $cli->selectCollection("Mnemono", "FacebookFeedTimestamp");
    $col->remove(array(
        "fbFeed.\$ref" => "FacebookFeed",
        "fbFeed.\$id" => $feed["_id"],
    ));

    $col = $cli->selectCollection("Mnemono", "FacebookFeed");
    $col->remove(array(
        "_id" => $feed["_id"],
    ));
}

function removeFromFb($lastMongoId){
    $lastCheckedMongoId = new MongoId($lastMongoId);

    $cli = new \MongoClient();
    $col = $cli->selectCollection("Mnemono", "FacebookFeed");
    $cursor = $col->find(array("_id" => array("\$gt" => $lastCheckedMongoId)));
    $removedCount = 0;

    try{
        foreach($cursor as $feed){
            $isRef = isRefByOtherCollection($feed);
            if (!$isRef){
                $removedCount++;
                removeFeedAndTimestampRef($feed);
            }
            $lastCheckedMongoId = $feed["_id"];
            if ($removedCount >= 1000){
                break;
            }
        }
    }catch (\MongoCursorException $e){
        echo "MongoCursorException\n";
    }
    echo "last checked MongodId:" . $lastCheckedMongoId . "\n";
    echo "removed:" . $removedCount . "\n";
}

$options = getopt(null,array("lastMongoId:"));
$lastMongoId = $options["lastMongoId"];
removeFromFb($lastMongoId);