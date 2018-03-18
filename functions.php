<?php

class TeStTheme
{
    public static $version = '1.0.0';

    private function __construct()
    {
    }

    public static function run()
    {
        add_action('after_setup_theme', array(__CLASS__, 'after_setup_theme'));
        do_action('TeStTheme_setup');
        add_action('wp_ajax_TeStTheme_insert_post', [__CLASS__, 'ajax_insert_posts']);
        add_action('wp_enqueue_scripts', [__CLASS__, 'add_js_css']);
    }

    public static function add_js_css()
    {


        wp_enqueue_style(
            'bootstrap',
            'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css',
            [],
            '4.0.0'
        );

        wp_enqueue_style(
            'TeStTheme-stylesheet',
            get_stylesheet_uri(),
            ['bootstrap'],
            self::$version
        );

    }

    public static function ajax_insert_posts()
    {
        $request = $_REQUEST;
        $request = array_map('trim', $request);
        unset($request['action']);
    }


    public static function after_setup_theme()
    {


    }

}

TeStTheme::run();