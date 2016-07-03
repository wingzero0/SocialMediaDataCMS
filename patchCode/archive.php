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
define("POST","Post");

use MongoDB\Client as MongoDBClient;
use MongoDB\Collection as MongoDBCollection;

$cli = new MongoDBClient();

$pageCol = $cli->selectCollection(MNEMONO, FACEBOOK_PAGE);
$feedCol = $cli->selectCollection(MNEMONO, FACEBOOK_FEED);
$feedTimestampCol = $cli->selectCollection(MNEMONO, FACEBOOK_FEED_TIMESTAMP);
$postCol = $cli->selectCollection(MNEMONO, POST);

$nDayAgo = new \DateTime();
$nDayAgo->setTimezone(new DateTimeZone("GMT"));
$nDayAgo->sub(new \DateInterval('P30D'));

$archivePageCol = $cli->selectCollection(MNEMONO_ARCHIVE, FACEBOOK_PAGE);
$archiveFeedCol = $cli->selectCollection(MNEMONO_ARCHIVE, FACEBOOK_FEED);
$archiveFeedTimestampCol = $cli->selectCollection(MNEMONO_ARCHIVE, FACEBOOK_FEED_TIMESTAMP);
$archivePostCol = $cli->selectCollection(MNEMONO_ARCHIVE, POST);

$cursor = $pageCol->find();
foreach ($cursor as $page)
{
    upsert($archivePageCol, $page);
    $id = $page["_id"];
    $query = [
        "fbPage.\$id" => $id,
        "created_time" => ["\$lte" => $nDayAgo->format(\DateTime::ISO8601)]
    ];
    $options = [
        "sort" => ["_id" => -1],
        "skip" => 25
    ];
    $feedCur = $feedCol->find(
        $query,
        $options
    );

    foreach ($feedCur as $feed)
    {
        upsert($archiveFeedCol, $feed);
        deleteOne($feedCol, $feed);
        $historyCur = $feedTimestampCol->find(
            ["fbFeed.\$id" => $feed["_id"]]
        );
        foreach($historyCur as $timestamp){
            upsert($archiveFeedTimestampCol, $timestamp);
            deleteOne($feedTimestampCol, $timestamp);
        }

        $postCur = $postCol->find(
            [
                "importFromRef.\$id" => $feed["_id"],
                "importFromRef.\$ref" => FACEBOOK_FEED,
            ]
        );
        foreach($postCur as $post){
            $post["importFromRef"]["\$db"] = MNEMONO_ARCHIVE;
            upsert($archivePostCol, $post);
            deleteOne($postCol, $post);
        }
    }
}

function upsert(MongoDBCollection $col, \ArrayObject $oldData){
    $col->updateOne(
        ["_id" => $oldData["_id"]],
        ["\$set" => $oldData],
        ["upsert" => true]
    );
}

function deleteOne(MongoDBCollection $col, \ArrayObject $oldData){
    $col->deleteOne(["_id" => $oldData["_id"]]);
}