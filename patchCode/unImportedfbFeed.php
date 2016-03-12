<?php
/**
 * User: kit
 * Date: 12/03/16
 * Time: 4:42 PM
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

function countUnImportedFeed($dateStr){
    $cli = new \MongoClient();
    $col = $cli->selectCollection("Mnemono", "FacebookFeed");
    $cursor = $col->find(array("created_time" => array("\$lte" => $dateStr)));
    $unImported = 0;

    foreach($cursor as $feed){
        $isRef = isRefByOtherCollection($feed);
        if (!$isRef){
            $unImported++;
        }
    }
    echo "removed:" . $unImported . "\n";
}

$options = getopt(null,array("date:"));
$dateStr = $options["date"];
countUnImportedFeed($dateStr);