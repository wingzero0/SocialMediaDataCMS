<?php
/**
 * User: kit
 * Date: 29/06/15
 * Time: 19:45
 */

namespace AppBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Document\MnemonoBiz;

class LoadBiz implements FixtureInterface{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $biz = new MnemonoBiz();
        $biz->setName('TestData');
        $biz->setUrls(array('http://localhost', 'https://localhost'));

        $manager->persist($biz);
        $manager->flush();
    }
}