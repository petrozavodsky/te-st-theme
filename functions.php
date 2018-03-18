<?php

class TeStTheme {
    public static $version = '1.0.0';

    private function __construct() {}

     public static function run() {
        add_action( 'after_setup_theme', array( __CLASS__, 'after_setup_theme' ) );
        do_action( 'TeStTheme_setup' );
    }

}

TeStTheme::run();