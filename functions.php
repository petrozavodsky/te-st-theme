<?php

class TeStTheme
{
    public static $version = '1.0.0';
    public static $post_type = 'book';
    public static $ajax_action = 'TeStTheme_insert_post';

    private function __construct()
    {
    }

    public static function run()
    {
        do_action('TeStTheme_setup');
        add_action('wp_ajax_' . self::$ajax_action, [__CLASS__, 'ajax_insert_posts']);
        add_action('wp_enqueue_scripts', [__CLASS__, 'add_js_css']);
        add_action('wp_head', [__CLASS__, 'add_viewport']);
        add_action('init', [__CLASS__, 'post_type_books']);
        add_action('TeStTheme__theme-content-area-before', [__CLASS__, 'ajax_form']);
    }

    public static function post_type_books()
    {
        register_post_type(
            self::$post_type,
            [
                'labels' =>
                    [
                        'name' => __('Books', 'TeStTheme'),
                        'singular_name' => __('Book', 'TeStTheme'),
                        'add_new' => __('Add Book', 'TeStTheme'),
                        'add_new_item' => __('Add new book', 'TeStTheme'),
                        'edit_item' => __('Edit book', 'TeStTheme'),
                        'new_item' => __('New book', 'TeStTheme'),
                        'view_item' => __('View book', 'TeStTheme'),
                        'search_items' => __('Search book', 'TeStTheme'),
                        'not_found' => __('Books not found', 'TeStTheme'),
                        'menu_name' => __('Books', 'TeStTheme')
                    ],
                'menu_position' => 5,
                'menu_icon' => 'dashicons-book',
                'hierarchical' => false,
                'public' => false,
                'show_ui' => true,
                'show_in_menu' => true,
                'rewrite' => false,
                'supports' => [
                    'title',
                    'excerpt',
                    'author'
                ]
            ]
        );

        add_action('pre_get_posts', [__CLASS__, 'pre_get_posts']);


    }


    public static function pre_get_posts($query)
    {
        if ($query->is_front_page() && $query->is_main_query()) {
            $query->set('post_type', self::$post_type);
        }
    }

    public static function add_viewport()
    {
        echo "<meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>\r\n";
    }

    public static function add_js_css()
    {


        wp_enqueue_style(
            'bootstrap',
            get_template_directory_uri() . '/css/vendor/bootstrap/css/bootstrap.min.css',
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


    public static function ajax_form()
    {
        $action = add_query_arg(['action' => self::$ajax_action], admin_url('wp-admin.php'));
        ?>

        <form class="form__ajax-insert" action="<?php echo $action; ?>">

            <div class="md-form">
                <label for="title">
                    <?php _e('Title', 'TeStTheme'); ?>
                    <input name="title" required="required" class="form-control" type="text" placeholder="<?php _e('Enter title', 'TeStTheme'); ?>"/>
                </label>
            </div>

            <div class="md-form">
                <label for="description">
                    <?php _e('Description', 'TeStTheme'); ?>
                    <textarea name="description" required="required" class="form-control md-textarea"></textarea>
                </label>
            </div>

            <button type="submit" class="btn btn-primary">
                <?php _e('Submit', 'TeStTheme'); ?>
            </button>

        </form>

        <?php
    }

}

TeStTheme::run();