<?php
/**
 * User: kit
 * Date: 26/03/16
 * Time: 4:43 PM
 */

namespace AppBundle\Command;

use AppBundle\Utility\GearmanServiceName;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SyncWeiboFeedToPostCommand extends BaseCommand{
    const ACTION_CREATE_FROM_MID = "createFromMid";
    const OPTION_MID = 'mid';
    protected function configure(){
        $this->setName("mnemono:sync:weibofeedtopost")
            ->setDescription("sync Weibo feed to post")
            ->addOption(SyncWeiboFeedToPostCommand::OPTION_ACTION, null,
                InputOption::VALUE_REQUIRED,
                'available values: createFromMid',
                SyncWeiboFeedToPostCommand::ACTION_CREATE_FROM_MID)
            ->addOption('mid', null ,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'the specific weibo (mid) you want to sync')
        ;

    }
    protected function execute(InputInterface $input, OutputInterface $output){
        $action = $input->getOption(SyncWeiboFeedToPostCommand::OPTION_ACTION);
        if ($action == SyncWeiboFeedToPostCommand::ACTION_CREATE_FROM_MID) {
            $mids = $input->getOption(SyncWeiboFeedToPostCommand::OPTION_MID);
            foreach($mids as $mid){
                echo $mid . "\t";
                $this->createPostByMid($mid);
            }
        }
    }

    /**
     * @param string $mid
     */
    private function createPostByMid($mid){
        $json = json_encode(array("mid" => $mid));
        $this->getGearman()->doBackgroundJob(GearmanServiceName::$syncWeiboFeedCreateJob, $json);
    }
}