# Data structure

## Post (Mnemono post)

[Mnemono post](/src/AppBundle/Document/Post.php) has 3 data source.

1. facebook feed
2. weibo feed
3. admin input

It has a field **importFrom** as a discriminator to indicate the source.

And different data source will contain different **meta** fields.

### Admin input
	$importFrom = null;
	$meta = null;

### Facebook Feed

	$importFrom = 'facebookFeed';
	$meta = new AppBundle\Document\Facebook\FacebookMeta(); //with fb_total_likes and fb_total_comments

### Weibo Feed

	$importFrom = 'weiboFeed';
	$meta = new AppBundle\Document\Weibo\WeiboMeta(); //with like_count, comments_count, reposts_count, fb_total_likes and fb_total_comments

fb_total_likes and fb_total_comments will kept until mobile client still support weibo type