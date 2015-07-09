<?php
/**
 * Created by PhpStorm.
 * User: kitlei
 * Date: 7/9/2015
 * Time: 9:35 AM
 */

namespace AppBundle\Command;

use AppBundle\Document\FacebookPage;
use AppBundle\Document\MnemonoBiz;
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
            ->addOption('updateFromFb', null,
                InputOption::VALUE_OPTIONAL,
                'over write current biz value with facebook page')
            ->addOption('dumpFromFb', null,
                InputOption::VALUE_OPTIONAL,
                'add new record from facebook page collection ')
            ;
    }
    protected function execute(InputInterface $input, OutputInterface $output){
        $fbIds = $input->getOption('fbId');
        if (!empty($fbIds)){
            $output->writeln("your first fbId is ". $fbIds[0]);
            $this->createBizByFbPage($fbIds[0]);
        }else{
            $output->writeln("no fbId");
        }
    }
    private function createBizByFbPage($fbId){
        $dm = $this->getDM();
        $page = $dm->createQueryBuilder("AppBundle:FacebookPage")
            //->hydrate(false)
            ->field("fbId")->equals($fbId)
            ->getQuery()->getSingleResult();

        $pageRaw = $dm->createQueryBuilder("AppBundle:FacebookPage")
            ->hydrate(false)
            ->field("fbId")->equals($fbId)
            ->getQuery()->getSingleResult();

        $biz = $dm->createQueryBuilder("AppBundle:MnemonoBiz")
            ->field("importFrom")->equals("facebookPage")
            ->field("importFromRef")->references($page)
            ->getQuery()->getSingleResult();

        if ($biz == null){
            $biz = new MnemonoBiz();
            $biz->setName($pageRaw["name"])
                ->setImportFrom("facebookPage")
                ->setImportFromRef($page)
                ->setLastModDate(new \DateTime());

            $dm->persist($biz);
            $dm->flush();
        }


        return $biz;
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