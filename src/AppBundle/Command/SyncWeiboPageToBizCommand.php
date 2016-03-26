<?php
/**
 * User: kit
 * Date: 26/03/16
 * Time: 8:39 PM
 */

namespace AppBundle\Command;

use AppBundle\Utility\GearmanServiceName;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SyncWeiboPageToBizCommand extends BaseCommand{
    const ACTION_CREATE_FROM_UID = "createFromUid";
    const OPTION_UID = 'uid';
    protected function configure(){
        $this->setName("mnemono:sync:weibopagetobiz")
            ->setDescription("sync Weibo page to biz")
            ->addOption(SyncWeiboPageToBizCommand::OPTION_ACTION, null,
                InputOption::VALUE_REQUIRED,
                'available values: createFromUid',
                SyncWeiboPageToBizCommand::ACTION_CREATE_FROM_UID)
            ->addOption('uid', null ,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'the specific weibo (uid) you want to sync')
        ;

    }
    protected function execute(InputInterface $input, OutputInterface $output){
        $action = $input->getOption(SyncWeiboPageToBizCommand::OPTION_ACTION);
        if ($action == SyncWeiboPageToBizCommand::ACTION_CREATE_FROM_UID) {
            $uids = $input->getOption(SyncWeiboPageToBizCommand::OPTION_UID);
            foreach($uids as $uid){
                echo $uid . "\t";
                $this->createPostByMid($uid);
            }
        }
    }

    /**
     * @param string $uid
     */
    private function createPostByMid($uid){
        $json = json_encode(array("uid" => $uid));
        $this->getGearman()->doBackgroundJob(GearmanServiceName::$syncWeiboPageCreateJob, $json);
    }
}