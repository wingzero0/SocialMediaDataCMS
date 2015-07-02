<?php
/**
 * User: kit
 * Date: 29/06/15
 * Time: 19:45
 */

namespace AppBundle\DataFixtures\MongoDB;

use AppBundle\Document\Directory;
use AppBundle\Document\FacebookPage;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Document\MnemonoBiz;
use AppBundle\Document\Post;

class LoadBiz implements FixtureInterface{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i< 6;$i++){
            $biz = new MnemonoBiz();
            $biz->setName('TestData' . $i);
            $biz->setWebsites(array('http://localhost', 'https://localhost'));
            $biz->setTag(array('randomTag'.$i , 'bizTag'));

            $source = null;
            if ($i < 3){
                $fbPage = new FacebookPage();
                $fbPage->setFbId('9999999' . $i);
                $manager->persist($fbPage);
                $biz->setImportFromRef($fbPage);
                $biz->setImportFrom('facebookPage');
            }else{
                $directory = new Directory();
                $directory->setName('9999999' . $i);
                $manager->persist($directory);
                $biz->setImportFromRef($directory);
                $biz->setImportFrom('directory');
                $lastBiz = $biz;
            }
            $manager->persist($biz);
        }
        $post = new Post();
        $post->setMnemonoBiz($lastBiz);
        $post->setTags(array('postTag', 'postTags'));
        $post->setContent("Hello World");
        $manager->persist($post);


        $manager->flush();
    }
}