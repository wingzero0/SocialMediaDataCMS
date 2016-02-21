<?php
/**
 * User: kitlei
 * Date: 15-Feb-16
 * Time: 9:01 AM
 */

$cli = new \MongoClient();
$col = $cli->selectCollection("Mnemono", "Post");

$cursor = $col->find()->sort(array("_id" => 1));
foreach($cursor as $post){
    if (isset($post["cities"]) && is_array($post["cities"])){
        $areaCodes = $post["cities"];
    }else{
        $areaCodes = array();
    }

    if (isset($post["tags"]) && is_array($post["tags"])){
        $tags = $post["tags"];
    }else{
        $tags = array();
    }

    $newTags = array();
    foreach($tags as $tag){
        if ($tag == "mo" || $tag == "hk"){
            $areaCodes = array_merge($areaCodes, array($tag));
        }else{
            $newTags[] = $tag;
        }
    }

    $updateQuery = array("\$set" => array("cities" => array_unique($areaCodes), "tags" => $newTags));
    $criteria = array("_id" => $post["_id"]);
    $col->update($criteria, $updateQuery);
}


$col = $cli->selectCollection("Mnemono", "MnemonoBiz");

$cursor = $col->find()->sort(array("_id" => 1));
foreach($cursor as $biz){
    $cities = array();
    $addresses = array();
    if (isset($biz["location"]) &&  isset($biz["location"]["city"])){
        $cities[] = $biz["location"]["city"];

    }
    if (isset($biz["location"]) &&  isset($biz["location"]["address"])){
        $addresses[] = $biz["location"]["address"];
    }
    $updateQuery = array("\$set" => array("cities" => $cities, "addresses" => $addresses));
    $criteria = array("_id" => $biz["_id"]);
    $col->update($criteria, $updateQuery);
}