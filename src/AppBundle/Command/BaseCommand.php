<?php
/**
 * User: kit
 * Date: 15/07/15
 * Time: 21:04
 */

namespace AppBundle\Command;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseCommand extends ContainerAwareCommand{
    protected $facebookPageDocumentPath = "AppBundle:Facebook\\FacebookPage";
    protected $facebookFeedDocumentPath = "AppBundle:Facebook\\FacebookFeed";
    protected $facebookFeedTimestampDocumentPath = "AppBundle:Facebook\\FacebookFeedTimestamp";
    protected $mnemonoBizDocumentPath = "AppBundle:MnemonoBiz";
    protected $postDocumentPath = "AppBundle:Post";
    private $documentManager = null;
    /**
     * @param bool $reset
     * @return null|DocumentManager
     */
    protected function getDM($reset = false){
        if ($this->documentManager == null){
            $this->documentManager = $this->getContainer()->get("doctrine_mongodb")->getManager();
        }
        if ($reset == true && $this->documentManager instanceof DocumentManager){
            $this->documentManager = DocumentManager::create(new Connection(), $this->documentManager->getConfiguration());
        }
        return $this->documentManager;
    }
    protected function resetDM(){
        $this->getDM(true);
    }
}