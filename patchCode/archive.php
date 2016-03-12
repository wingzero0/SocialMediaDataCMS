<?php
/**
 * User: kitlei
 * Date: 15-Feb-16
 * Time: 9:01 AM
 */

$cli = new \MongoClient();
$col = $cli->selectCollection("Mnemono", "Post");

$testCol = $cli->selectCollection("MnemonoArchive", "Test");

$cursor = $col->find()->sort(array("_id" => 1))->limit(10);
foreach($cursor as $post){
    $testCol->insert(array(
        "manullyRef" => array(
            "\$ref" => "Post",
            "\$id" => $post["_id"],
            "\$db" => "Mnemono",
            "classPath" => "test",
        ))
    );
}


$cursor = $testCol->find()->sort(array("_id" => 1))->limit(10);
$db = $cli->selectDB("Mnemono");
foreach($cursor as $manullyRef ){
    $post = MongoDBRef::get($db, $manullyRef["manullyRef"]);
    var_dump($post);
}