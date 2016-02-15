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
    $tags = array();
    $areaCode = "";
    if (isset($post["tags"])){
        $tags = $post["tags"];
    }
    foreach($tags as $tag){
        if ($tag == "mo" || $tag == "hk"){
            $areaCode = $tag;
            break;
        }
    }
    if (!empty($areaCode)){
        $updateQuery = array("\$set" => array("cities" => array($areaCode)));
        $criteria = array("_id" => $post["_id"]);
        var_dump($updateQuery);
        $col->update($criteria, $updateQuery);
    }
}