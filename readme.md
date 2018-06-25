# Finally Comments Wordpress Plugin - Beta
V0.1.0 - Initial Beta Release

This release is intended for testing and feedback and is not necessarily ready for production enviroments (although there are current no known issues).

## What is Finally Comments

[FinallyComments](http://finallycomments.com/) is an embedable Steem blockchain powered comments system. It differentiates it's from the current alternateives (Disqus & Facebook Comments) using Steems unique ability to distribute token rewards to users. An upvote (like) may also bring with it a small amount of token rewards.

Learn more on the [Finally Comments About Page](https://finallycomments.com/about)

## How To Use The Plugin
This Plugin has not yet been submitted to the Wordpress Codex. Until the plugin is submitted/accepted you can trial it on your Wordpress site by downloading it from Github and re-uploading it to your own site.

Once activated the plugin will show a custom metabox when creating posts.

![Finally WP](https://www.dropbox.com/s/vqwwzh9c9lypomi/FinallyWP.png?dl=1 )

By default Finally Comments is not displayed.

### Use Comment Threads From Steem
All content posted to the Steem blockchain(from any platform) has a comments thread. Make use of this by copying the steemit.com version of the link (even if it was not posted on steemit originally) and selecting *steem* as the tread type. Comments will match across Finally and your own post on busy/d.tube/steemit etc. This option is useful if you are currently using steempress.

### Use Custom Threads
If your content is not posted on the Steem blockchain elsewere or if you prefer a new blank comments thread this is the option to choose.

To make use of custom threads you must first sign in through [https://finallycomments.com/dashboard#api](https://finallycomments.com/dashboard#api) authorise your account and register your site with the Finally API. If you don't do this you will see an error page instead of the comments thread.

Specify the account name your website is autorised with and a custom thread will be generated on the first time you load the Finally Embed for that page. The Wordpress post slug will be used as the slug for you comments thread(this can not be changed at a later date) and can be found in the dashboard - [https://finallycomments.com/dashboard#custom-threads](https://finallycomments.com/dashboard#custom-threads).

## Bugs, Feedback & Contributions
The primary place to contribute will be through [Github Issues](https://github.com/code-with-sam/finallycomments-wp). Specific issues related to the wordpress plugin can be added on that repo, more general requests/ideas may be moved to the main Finallycomments project.

I keep communications related to Steem project centered on Discord (sambillingham#7927) or Steem Dev Slack.  

## Development
Clone the repo and symlink over to your Wordpress development environment for working on this plugin.

## Roadmap
- Submit to Wordpress Codex
- Interface with Steempress automatically
- Set default in settings (nothread/steem/steempress/custom)
- default settings for threads within the Wordpress setting page. e.g Show reputation/profiles/rewards
- settings for individual threads in metabox e.g Show reputation/profiles/rewards
- load thread preview in admin area?
- old comment import feature?
- use posting key instead of Steemconnect through Finallycomments.com dashboard
- enable parsing of all types of links not just steemit.
- redirect to friendly error page if user is not authenticated with Finally API but trying to use custom threads
