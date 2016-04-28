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
        "importFrom" => "facebookFeed",
        "importFromRef.\$id" => $feed["_id"],
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
    $checkingOnMongodId = null;

    $cli = new \MongoClient();
    $col = $cli->selectCollection("Mnemono", "FacebookFeed");
	$cursor = $col->find(array("_id" => array("\$gt" => $lastCheckedMongoId)))->sort(array("_id" => 1));
    $removedCount = 0;

    try{
        foreach($cursor as $feed){
            $checkingOnMongodId = $feed["_id"];
            $isRef = isRefByOtherCollection($feed);
            if (!$isRef){
                $removedCount++;
                removeFeedAndTimestampRef($feed);
            }
            $lastCheckedMongoId = $feed["_id"];
            if ($removedCount >= 3000){
                break;
            }
        }
    }catch (\MongoCursorException $e){
        echo "MongoCursorException on MongoId:" . $feed["_id"] . "\n";
        echo $e->getMessage()."\n";
        echo $e->getTraceAsString()."\n";
    }
    echo "last checked MongodId:" . $lastCheckedMongoId . "\n";
    echo "removed:" . $removedCount . "\n";
}

$options = getopt(null,array("lastMongoId:"));
$lastMongoId = $options["lastMongoId"];
removeFromFb($lastMongoId);
