<?php
/**
 * Created by PhpStorm.
 * User: kitlei
 * Date: 7/9/2015
 * Time: 9:35 AM
 */

namespace AppBundle\Command;

use AppBundle\Document\FacebookPage;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SyncFbPageToBizCommand extends ContainerAwareCommand{
    protected function configure(){
        $this->setName("mnemono:sync:fbpagetobiz")
            ->setDescription("sync facebook page to biz")
            ->addOption('fbId', null ,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'the specific fbId you want to sync')
            ;
    }
    protected function execute(InputInterface $input, OutputInterface $output){
        $fbIds = $input->getOption('fbId');
        if (!empty($fbIds)){
            $output->writeln("your first fbId is ". $fbIds[0]);
            $this->updateByFbPage($fbIds[0]);
        }else{
            $output->writeln("no fbId");
        }
    }
    private function updateByFbPage($fbId){
        $dm = $this->getDM();
        $pages = $dm->createQueryBuilder("AppBundle:FacebookPage")->hydrate(false)->field("fbId")->equals($fbId)
                ->getQuery()->execute();
        $rawPageData = array();
        foreach($pages as $page){
            print_r($page);
            $rawPageData[] = $page;
        }
        return $rawPageData;
    }

    /**
     * @return null|DocumentManager
     */
    private function getDM(){
        $dm = $this->getContainer()->get("doctrine_mongodb")->getManager();
        if ($dm instanceof DocumentManager){
            return $dm;
        }else{
            echo "dm is not documentMananger"."\n";
        }
        return null;
    }
}