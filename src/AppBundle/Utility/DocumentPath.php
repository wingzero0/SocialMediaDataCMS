<?php
/**
 * User: kit
 * Date: 29/10/15
 * Time: 8:46 PM
 */

namespace AppBundle\Utility;

class DocumentPath {
    // TODO change all to const
    public static $facebookPageDocumentPath = "AppBundle:Facebook\\FacebookPage";
    public static $weiboPageDocumentPath = "AppBundle:Weibo\\WeiboPage";
    public static $facebookFeedDocumentPath = "AppBundle:Facebook\\FacebookFeed";
    public static $weiboFeedDocumentPath = "AppBundle:Weibo\\WeiboFeed";
    public static $facebookFeedTimestampDocumentPath = "AppBundle:Facebook\\FacebookFeedTimestamp";
    public static $weightingDocumentPath = "AppBundle:Settings\\Weighting";
    public static $managedTagDocumentPath = "AppBundle:ManagedTag";
    public static $managedTagFolderPath = "AppBundle\\Document\\ManagedTag";
    public static $mnemonoBizDocumentPath = "AppBundle:MnemonoBiz";
    public static $mnemonoBizFolderPath = "AppBundle\\Document\\MnemonoBiz";
    public static $userDocumentPath = "AppBundle:User";
    public static $userFolderPath = "AppBundle\\Document\\User";
    public static $deviceInfoDocumentPath = "AppBundle:DeviceInfo";
    public static $postDocumentPath = "AppBundle:Post";
    public static $postFolderPath = "AppBundle\\Document\\Post";
    public static $postForReviewDocumentPath = "AppBundle:PostForReview";
    public static $spotlightAdsDocumentPath = "AppBundle:SpotlightAds";
    public static $spotlightAdsFolderPath = "AppBundle\\Document\\SpotlightAds";
    public static $logRecordDocumentPath = "AppBundle:Utility\\LogRecord";
}