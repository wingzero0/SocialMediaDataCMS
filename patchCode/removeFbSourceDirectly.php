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

function removeFromFb($dateStr){
    $cli = new \MongoClient();
    $col = $cli->selectCollection("Mnemono", "FacebookFeed");
    //$cursor = $col->find(array("_id" => new MongoId("567896ae9e4e0e632c0744aa")));
    $cursor = $col->find(array("created_time" => array("\$lte" => $dateStr)));
    $removedCount = 0;

    foreach($cursor as $feed){
        $isRef = isRefByOtherCollection($feed);
        //echo $feed["_id"] . " ref:" . ($isRef ? "ref" : "no ref") . "\n";
        if (!$isRef){
            $removedCount++;
            removeFeedAndTimestampRef($feed);
        }
    }
    echo "removed:" . $removedCount . "\n";
}

$options = getopt(null,array("date:"));
$dateStr = $options["date"];
removeFromFb($dateStr);