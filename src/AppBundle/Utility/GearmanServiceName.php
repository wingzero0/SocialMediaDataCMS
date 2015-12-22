<?php
/**
 * User: kit
 * Date: 12/18/2015
 * Time: 2:41 PM
 */

namespace AppBundle\Utility;


class GearmanServiceName {
    public static $postScoreService = "MnemonoBackgroundServiceBundleServicesPostScoreService";
    public static $postScoreUpdateJob = "MnemonoBackgroundServiceBundleServicesPostScoreService~updateScore";
    public static $syncFbFeedSrevice = "MnemonoBackgroundServiceBundleServicesSyncFbFeedService";
    public static $syncFbFeedCreateJob = "MnemonoBackgroundServiceBundleServicesSyncFbFeedService~createPost";
    public static $syncFbFeedUpdateJob = "MnemonoBackgroundServiceBundleServicesSyncFbFeedService~updatePost";
    public static $syncFbPageCreateJob = "MnemonoBackgroundServiceBundleServicesSyncFbPageService~createBiz";
    public static $syncFbPageUpdateJob = "MnemonoBackgroundServiceBundleServicesSyncFbPageService~updateBiz";
}