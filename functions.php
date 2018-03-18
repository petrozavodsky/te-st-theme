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
        add_action('wp__ajax__TeStTheme_insert_post', [__CLASS__, 'ajax_insert_posts']);
    }


    public static function ajax_insert_posts()
    {
        $request = $_REQUEST;
        $request = array_map('trim', $request);
        unset($request['action']);

    }

}

TeStTheme::run();