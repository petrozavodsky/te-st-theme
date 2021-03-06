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
        add_action('wp', [__CLASS__, 'ajax_form']);
        add_action('TeStTheme__theme-main-before', [__CLASS__, 'meta_description']);
        add_action('admin_init', [__CLASS__, 'user_restriction'], 1);
        add_action('wp', [__CLASS__, 'hide_admin_bar']);
        add_action('after_setup_theme', [__CLASS__, 'theme_textdomain']);

        add_action('TeStTheme__theme-register-link', function () {
            if (!is_user_logged_in()) {
                $url = site_url('/wp-login.php?action=register');
                echo "<p>  <a href='{$url}'> " . __('To register on the site', 'TeStTheme') . " </a> </p>";
            } else {
                $url = wp_logout_url(site_url('/'));
                echo "<p>  <a href='{$url}'> " . __('Logout', 'TeStTheme') . " </a> </p>";
            }
        });
    }

    public static function theme_textdomain()
    {
        load_theme_textdomain('TeStTheme',get_template_directory() . '/languages');
    }


    public static function hide_admin_bar()
    {

        if (!current_user_can('unfiltered_html')) {
            show_admin_bar(false);
        }
    }

    public static function user_restriction()
    {
        if (true != DOING_AJAX && !current_user_can('unfiltered_html')) {
            wp_redirect(site_url('/'));
            exit;
        }
    }

    public function meta_description()
    {
        global $wp_query;

        if (0 < $wp_query->post_count):
            ?>
            <h1 class="meta__description display-3 text-center">
                <?php _e('List of books', 'TeStTheme'); ?>
            </h1>
        <?php
        endif;
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

        $request = array_filter($request, function ($elem) {
            return !empty($elem);
        });

        if (!wp_verify_nonce($_POST['books_ajax_form'], __FILE__)) {
            wp_die(__("Cheatin&#8217; uh?", 'TeStTheme'));
        }

        $out = [
            'message' => __('Book saved in draft', 'TeStTheme')
        ];

        if (!in_array('title', array_keys($request))) {
            $out['message'] = __('Enter title', 'TeStTheme');

            wp_send_json_error(self::ajax_insert_posts_alerts_helper($out, 'info'));
        }

        if (!in_array('description', array_keys($request))) {
            $out['message'] = __('Enter description pls.', 'TeStTheme');
            wp_send_json_error(self::ajax_insert_posts_alerts_helper($out, 'warning'));
        }

        $post_id = self::insert_book($request['title'], $request['description']);

        if (is_wp_error($post_id)) {
            $out['message'] = __('An error occurred while saving. Please try again later.', 'TeStTheme');
            wp_send_json_error(self::ajax_insert_posts_alerts_helper($out, 'danger'));
        }

        wp_send_json_success(self::ajax_insert_posts_alerts_helper($out, 'success'));

    }

    public static function insert_book($title, $excerpt)
    {
        $data = [
            'post_type' => self::$post_type,
            'post_title' => wp_strip_all_tags($title),
            'post_excerpt' => wp_strip_all_tags($excerpt),
            'post_status' => 'draft',
            'post_author' => get_current_user_id()
        ];

        return wp_insert_post($data, true);


    }

    public static function ajax_insert_posts_alerts_helper($array, $type = 'warning')
    {

        if (array_key_exists('message', $array)) {
            $array['message'] = trim("
            <div class='alert alert-{$type}' role='alert'>{$array['message']}</div>
            ");
        }

        return $array;
    }

    public static function ajax_form()
    {

        if (is_user_logged_in()) {
            wp_enqueue_script(
                'TeStTheme-ajax-insert-form',
                get_template_directory_uri() . '/js/form-script.min.js',
                ['jquery'],
                self::$version,
                true
            );


            add_action('TeStTheme__theme-content-area-before', function () {

                $action = add_query_arg(['action' => self::$ajax_action], admin_url('admin-ajax.php'));

                ?>
                <form class="form__ajax-insert" action="<?php echo $action; ?>" method="post">

                    <div class="md-form">
                        <label for="title">
                            <?php _e('Title', 'TeStTheme'); ?>
                            <input name="title" class="form-control" type="text"
                                   placeholder="<?php _e('Enter title', 'TeStTheme'); ?>"/>
                        </label>
                    </div>

                    <div class="md-form">
                        <label for="description">
                            <?php _e('Description', 'TeStTheme'); ?>
                            <textarea name="description" class="form-control md-textarea"></textarea>
                        </label>
                    </div>

                    <div class="form__ajax-insert-button-wrap">
                        <button type="submit" class="btn btn-primary">
                            <?php _e('Submit', 'TeStTheme'); ?>
                        </button>

                        <div class="form__ajax-insert-button-preload loader"></div>

                    </div>
                    <input type="hidden" name="books_ajax_form" value="<?php echo wp_create_nonce(__FILE__); ?>"/>
                </form>

                <?php

            });
        }
    }

}

TeStTheme::run();
