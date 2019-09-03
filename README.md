# WP Admin Feedback Modal

[![License](https://img.shields.io/badge/license-GPL--3.0%2B-red.svg)](https://github.com/seb86/wp-admin-feedback-modal/blob/master/LICENSE.md)
[![GitHub forks](https://img.shields.io/github/forks/seb86/wp-admin-feedback-modal.svg?style=flat)](https://github.com/seb86/wp-admin-feedback-modal/network)
[![Tweet](https://img.shields.io/twitter/url/http/shields.io.svg?style=social)](https://twitter.com/intent/tweet?text=Message%20Placed%20Here%20—&url=https://github.com/seb86/github-repo-slug/&via=sebd86&hashtags=WordPress)

The WP Admin Feedback Modal is a class for asking the user for feedback and providing the user with a solution based on the user selection.

Quick Links: [Overview](#-overview) | [Guide](#-guide) | [Support](#-support) | [Contribute](#-contribute) 
###### Follow me

💻 [Website](https://sebastiendumont.com) 🐦[Twitter](https://twitter.com/sebd86)

## 🔔 Overview

> This project was inspired by WP Rocket.

One of the down sides to WordPress is that you don't know why a user deactivates your plugin should they fail to contact you for help.

With WP Admin Feedback Modal you can provide the user with a possible answer or solution before they finally deactivate your plugin and be told why they deactivated the plugin.

### What does it do?

It displays a modal when a user deactivates your WordPress plugin and displays a list of responses for them to choose from.

![Modal Example](https://raw.githubusercontent.com/seb86/wp-admin-feedback-modal/master/screenshot.png)

Once the user has selected from your list of responses, the modal can display a ready response if one exists. Below the primary button is then enabled to send the reason for deactivating the plugin.

The two other options allow the user to cancel deactivation or deactivate the plugin without sending feedback.

To see a live example, download the repository and activate the example plugin provided then deactivate the plugin for the modal to show.

What you will see are just a few examples. You can change the responses to what ever you like. Only the first and last option will be there by default.

Feedback is sent via email but you are welcome to fork the project and change it to send maybe to your private Slack channel for example. It's up to you how the feedback is sent.

## 📘 Guide

To get started, you'll simply need to load and initialize the class. The modal provides the required CSS and JS for display and functionality.

**Class Loader**

First step is to load the class into your plugin. This would typically appear in _functions.php_ or in the __construct_ of your plugin Class.

```php
if ( class_exists( 'WP_Admin_FB_Modal' ) ) {
	$wp_admin_fb_modal->init( 'plugin-slug', 'Plugin Name', __FILE__, 'feedback@yourdomain.xyz', $responses );
}
```

Then you need to replace the parameters passed. Plugin slug, name, the main plugin file and the e-mail address to reveive the feedback.

**Responses**

Next is to pass the responses to display for the feedback modal. Below I have shown three different types of responses.

1. Provide a simple response possibly with a solution.
2. Same as the first only you can provide an external source of information.
3. Asks the user to provide more details.

```php
$responses = array(
	array(
		'id'           => 'too-complicated',
		'value'        => __( 'Too Complicated' ),
		'label'        => sprintf( __( 'The plugin is %1$stoo complicated to configure.%2$s' ), '<strong>', '</strong>' ),
		'hidden_field' => 'no',
		'reason'       => array(
			'title'   => __( 'The plugin is too complicated to configure.' ),
			'content' => '<p>' . __( 'I\'m sorry to hear you are finding it difficult to use.' ) . '</p>'
		),
	),
	array(
		'id'           => 'with-external-button',
		'value'        => __( 'This option has a button' ),
		'label'        => __( 'I\'m struggling to get the plugin working.' ),
		'hidden_field' => 'no',
		'reason'       => array(
			'title'   => __( 'This option has a button.' ),
			'content' => '<p>' . __( 'Your content goes here.' ) . '</p>' .
						'<div class="text-center"><a class="external-button" href="' . esc_url( 'https://github.com/seb86/wp-admin-feedback-modal' ) . '" target="_blank">' . __( 'Button Text' ) . '</a></div>',
			),
	),
	array(
		'id'                 => 'another-plugin',
		'value'              => __( 'Another Plugin' ),
		'label'              => __( 'I\'m using another plugin I find better.' ),
		'hidden_field'       => 'yes',
		'hidden_placeholder' => __( 'What is the name of this plugin?' ),
	)
);
```

And that's it. Happy coding 😄

## ⭐ Support

WP Admin Feedback Modal is released freely and openly. Feedback or ideas and approaches to solving limitations in WP Admin Feedback Modal is greatly appreciated.

I **do not offer support** for WP Admin Feedback Modal. Please understand this is a non-commercial project. As such:

* Any development time for it is effectively being donated and is therefore, limited.
* Critical issues may not be resolved promptly.

#### 📝 Reporting Issues

If you think you have found a bug in the project or want to see a new feature added, please [open a new issue](https://github.com/seb86/wp-admin-feedback-modal/issues/new) and I will do my best to help you out.

## 👍 Contribute

If you or your company use **WP Admin Feedback Modal** or appreciate the work I’m doing in open source, please consider supporting me directly so I can continue maintaining it and keep evolving the project.

You'll be helping to ensure I can spend the time not just fixing bugs, adding features or releasing new versions but also keeping the project afloat. Any contribution you make is a big help and is greatly appreciated.

Please also consider starring ✨ and sharing 👍 the project repository! This helps the project getting known and grow with the community. 🙏

I accept one-time donations and monthly via [BuyMeACoffee.com](https://www.buymeacoffee.com/sebastien)

* [My PayPal](https://www.paypal.me/codebreaker)
* [BuyMeACoffee.com](https://www.buymeacoffee.com/sebastien)
* Bitcoin (BTC): `3L4cU7VJsXBFckstfJdP2moaNhTHzVDkKQ`
* Ethereum (ETH): `0xc6a3C18cf11f5307bFa11F8BCBD51F355b6431cB`
* Litecoin (LTC): `MNNy3xBK8sM8t1YUA2iAwdi9wRvZp9yRoi`

Thank you for your support! 🙌

---

##### License

WP Admin Feedback Modal is released under [GNU General Public License v3.0](http://www.gnu.org/licenses/gpl-3.0.html).

##### Credits

WP Admin Feedback Modal is developed and maintained by [Sébastien Dumont](https://sebastiendumont.com/about/).

---

<p align="center">
	<img src="https://raw.githubusercontent.com/seb86/my-open-source-readme-template/master/a-sebastien-dumont-production.png" width="353">
</p>
