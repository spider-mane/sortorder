<?php

return [

    /**
     *==========================================================================
     * Plugin Name
     *==========================================================================
     *
     * The name of your plugin, stylized to your liking.
     */
    'name' => 'Sort Order',

    /**
     *==========================================================================
     * Version
     *==========================================================================
     *
     * Current version of your plugin.
     */
    'version' => '0.1.0',

    /**
     *==========================================================================
     * Slug (Textdomain)
     *==========================================================================
     *
     * The plugin slug should ideally be identical to your plugin package name.
     * A slug is essentially a human-readable identifier and it is one way that
     * your plugin will be identified and referenced within WordPress including
     * but not limited to resolving the textdomain.
     *
     * @link https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#text-domains
     */
    'slug' => 'sortorder',

    /**
     *==========================================================================
     * Prefix
     *==========================================================================
     *
     * An abbreviated tag that you can use to prefix entries into the system
     * where naming collisions are a highly probable, such as database entries,
     * element ids, input names, etc.
     */
    'prefix' => 'srt',

    /**
     *==========================================================================
     * Description
     *==========================================================================
     *
     * Description of your plugin that an about page can display. Can be
     * different from (and more descriptive than) the one in the plugin header.
     */
    'description' => 'Wordpress library to sort display order of posts',

    /**
     *==========================================================================
     * Assets
     *==========================================================================
     *
     * The directory, relative to the root directory where plugin assets are
     * located.
     */
    'assets' => 'assets/dist/',

    /**
     *==========================================================================
     * Dependencies
     *==========================================================================
     *
     * A list of plugins that your own plugin depends on to properly function.
     * Use the textdomain/slug of any such plugin.
     */
    'dependencies' => [

        'leonidas',
    ],

    /**
     *==========================================================================
     * Development
     *==========================================================================
     *
     * A simple expression such as a single function call or ternary statement
     * that should return true if the plugin is in a development environment.
     */
    'dev' => defined('SORTORDER_DEVELOPMENT'),

];
