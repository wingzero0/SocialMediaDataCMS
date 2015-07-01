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

class LoadBiz implements FixtureInterface{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i< 3;$i++){
            $biz = new MnemonoBiz();
            $biz->setName('TestData' . $i);
            $biz->setWebsites(array('http://localhost', 'https://localhost'));

            $fbPage = new FacebookPage();
            $fbPage->setFbId('9999999' . $i);
            $manager->persist($fbPage);

            $biz->setImportFromRef($fbPage);
            $biz->setImportFrom('facebookPage');
            $manager->persist($biz);
        }
        for ($i = 3; $i< 6;$i++){
            $biz = new MnemonoBiz();
            $biz->setName('TestData' . $i);
            $biz->setWebsites(array('http://localhost', 'https://localhost'));

            $directory = new Directory();
            $directory->setName('9999999' . $i);
            $manager->persist($directory);

            $biz->setImportFromRef($directory);
            $biz->setImportFrom('directory');
            $manager->persist($biz);
        }
        $manager->flush();
    }
}