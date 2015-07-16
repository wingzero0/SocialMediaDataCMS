<?php
/**
 * User: kit
 * Date: 15/07/15
 * Time: 21:04
 */

namespace AppBundle\Command;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseCommand extends ContainerAwareCommand{
    protected $facebookPageDocumentPath = "AppBundle:Facebook\\FacebookPage";
    protected $facebookFeedDocumentPath = "AppBundle:Facebook\\FacebookFeed";
    protected $mnemonoBizDocumentPath = "AppBundle:MnemonoBiz";
    /**
     * @return null|DocumentManager
     */
    protected function getDM(){
        $dm = $this->getContainer()->get("doctrine_mongodb")->getManager();
        if ($dm instanceof DocumentManager){
            return $dm;
        }else{
            echo "dm is not documentMananger"."\n";
        }
        return null;
    }
}