<?php

class Ct_Admin_Form
{

    const ID = 'ct-admin-forms';

    const NONCE_KEY = 'ct_admin';

    protected $views = array(
        'view0' => 'views/view0',
        'view1' => 'views/view1',
        'view2' => 'views/view2',
        'alerts' => 'views/alerts',
        'not-found' => 'views/not-found'
    );
    const WHITELISTED_KEYS = array(
        'ct-admin-cookie',
        'ct-admin-forgotten'
    );

    private $default_values = array();
    private $current_page = '';

    public function init()
    {
        add_action('admin_menu', array($this, 'add_menu_page'), 20);

        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

        add_action('admin_post_ct_admin_save', array($this, 'submit_save'));

    }

    public function get_id()
    {
        return self::ID;
    }

    public function get_nonce_key()
    {
        return self::NONCE_KEY;
    }

    public function get_whitelisted_keys()
    {
        return self::WHITELISTED_KEYS;
    }

    private function get_defaults()
    {
        $defaults = array();
        foreach ($this->get_whitelisted_keys() as $key => $val) {
            $defaults[$val] = get_option($val);
        }
        return $defaults;
    }


    public function add_menu_page()
    {

        add_menu_page(
            esc_html__('My menu section', 'ct-admin'),
            esc_html__('My menu section', 'ct-admin'),
            'manage_options',
            $this->get_id(),
            array(&$this, 'load_view'),
            'dashicons-admin-page'
        );

        add_submenu_page(
            $this->get_id(),
            esc_html__('Submenu', 'ct-admin'),
            esc_html__('Submenu', 'ct-admin'),
            'manage_options',
            $this->get_id() . '_view1',
            array(&$this, 'load_view')
        );


        add_submenu_page(
            $this->get_id(),
            esc_html__('Submenu2', 'ct-admin'),
            esc_html__('Submenu2', 'ct-admin'),
            'manage_options',
            $this->get_id() . '_view2',
            array(&$this, 'load_view')
        );


    }


    function load_view()
    {
        $this->default_values = $this->get_defaults();
        $this->current_page = ct_admin_current_view();
        
        $current_views = isset($this->views[$this->current_page]) ? $this->views[$this->current_page] : $this->views['not-found'];

        $step_data_func_name = $this->current_page . '_data';

        $args = [];
        /**
         * prepare data for view
         */
        if (method_exists($this, $step_data_func_name)) {
            $args = $this->$step_data_func_name();
        }
        /**
         * Default Admin Form Template
         */

        echo '<div class="ct-admin-forms ' . $this->current_page . '">';

        echo '<div class="container container1">';
        echo '<div class="inner">';

        $this->includeWithVariables(ct_admin_template_server_path('views/alerts', false));

        $this->includeWithVariables(ct_admin_template_server_path($current_views, false), $args);

        echo '</div>';
        echo '</div>';

        echo '</div> <!-- / ct-admin-forms -->';
    }


    function includeWithVariables($filePath, $variables = array(), $print = true)
    {
        $output = NULL;
        if (file_exists($filePath)) {
            // Extract the variables to a local namespace
            extract($variables);

            // Start output buffering
            ob_start();

            // Include the template file
            include $filePath;

            // End buffering and return its contents
            $output = ob_get_clean();
        }
        if ($print) {
            print $output;
        }
        return $output;

    }


    public function admin_enqueue_scripts($hook_suffix)
    {
        if (strpos($hook_suffix, $this->get_id()) === false) {
            return;
        }

        wp_enqueue_style('ct-admin-form-bs', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css', CT_WP_ADMIN_VERSION);

        wp_enqueue_script('ct-admin-form-bs', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js',
            array('jquery'),
            CT_WP_ADMIN_VERSION,
            true
        );


        wp_enqueue_style('ct-admin-form', ct_admin_url('assets/style.css'), CT_WP_ADMIN_VERSION);

        wp_enqueue_script('ct-admin-form-js', ct_admin_url('assets/custom.js'),
            array('jquery'),
            CT_WP_ADMIN_VERSION,
            true
        );
    }


    public function submit_save()
    {

        $nonce = sanitize_text_field($_POST[$this->get_nonce_key()]);
        $action = sanitize_text_field($_POST['action']);

        if (!isset($nonce) || !wp_verify_nonce($nonce, $action)) {
            print 'Sorry, your nonce did not verify.';
            exit;
        }
        if (!current_user_can('manage_options')) {
            print 'You can\'t manage options';
            exit;
        }
        /**
         * whitelist keys that can be updated
         */
        $whitelisted_keys = $this->get_whitelisted_keys();

        $fields_to_update = [];

        foreach ($whitelisted_keys as $key) {
            if (array_key_exists($key, $_POST)) {
                $fields_to_update[$key] = $_POST[$key];
            }
        }

        /**
         * Loop through form fields keys and update data in DB (wp_options)
         */

        $this->db_update_options($fields_to_update);

        $redirect_to = $_POST['redirectToUrl'];

        if ($redirect_to) {
            add_settings_error('ct_msg', 'ct_msg_option', __("Changes saved."), 'success');
            set_transient('settings_errors', get_settings_errors(), 30);
            wp_safe_redirect($redirect_to);
            exit;
        }
    }

    private function db_update_options($group)
    {
        foreach ($group as $key => $fields) {
            $db_opts = get_option($key);
            $db_opts = ($db_opts === '') ? array() : $db_opts;

            if(!$db_opts){
                $db_opts = array();
            }

            $updated = array_merge($db_opts, $fields);
            update_option($key, $updated);


        }
    }


    /**
     * Form elements outputs
     */


    private function render_input($group, $key, $required = false)
    {
        $inputValue = isset($this->default_values[$group][$key]) ? stripslashes($this->default_values[$group][$key]) : '';
        $requiredAttr = ($required) ? "required" : '';

        return '<input type="text" id="' . $key . '" name="' . $group . '[' . $key . ']" class="form-control" value="' . $inputValue . '" ' . $requiredAttr . '>';
    }

    private function render_textarea($group, $key)
    {
        $defaultValue = isset($this->default_values[$group][$key]) ? stripslashes($this->default_values[$group][$key]) : '';

        return '<textarea class="form-control" rows="6" autocomplete="off" id="' . $key . '" name="' . $group . '[' . $key . ']">' . $defaultValue . '</textarea>';
    }

    private function render_select($group, $key, $options)
    {
        $selectedVal = isset($this->default_values[$group][$key]) ? $this->default_values[$group][$key] : '';

        $html = '';
        $html .= '<select class="form-control" id="' . $key . '" name="' . $group . '[' . $key . ']">';
        $html .= ($selectedVal == '') ? '<option value=""></option>' : '';
        foreach ($options as $key => $opt) {
            $selectedOpt = '';
            if ($selectedVal == $key) {
                $selectedOpt = 'selected="selected"';
            }
            $html .= '<option value="' . $key . '" ' . $selectedOpt . '>' . $opt . '</option>';
        }
        $html .= '</select>';
        return $html;
    }

    private function render_checkbox($group, $key)
    {
        $checkedVal = isset($this->default_values[$group][$key]) ? $this->default_values[$group][$key] : '';

        $checkedAttr = "";
        if ($checkedVal != '') {
            $checkedAttr = "checked";
        }
        $html = '';

        $html .= '
        <input type="hidden" name="' . $group . '[' . $key . ']" value="">
        <input class="form-check-input" type="checkbox" value="on" id="' . $key . '" name="' . $group . '[' . $key . ']" ' . $checkedAttr . '>';

        return $html;
    }

    /**
     * Prepare data for views
     */

    private function view0_data()
    {
        $args = [];

        $values = array(
            '' => esc_html__('Select', 'ct-admin'),
            'cs' => 'Čeština',
            'de' => 'Deutsch',
            'en' => 'English',
            'es' => 'Español',
            'fr' => 'Français',
            'hr' => 'Hrvatski',
            'hu' => 'Magyar',
            'no' => 'Norwegian',
            'it' => 'Italiano',
            'nl' => 'Nederlands',
            'pl' => 'Polski',
            'pt' => 'Português',
            'ro' => 'Română',
            'ru' => 'Русский',
            'sk' => 'Slovenčina',
            'dk' => 'Danish',
            'bg' => 'Bulgarian',
            'sv' => 'Swedish'
        );
        $args['cookie_content_language'] = $this->render_select('ct-admin-cookie', 'cookie_content_language', $values);
        $args['cookie_content'] = $this->render_textarea('ct-admin-cookie', 'cookie_content');
        $args['cookie_popup_label_accept'] = $this->render_input('ct-admin-cookie', 'cookie_popup_label_accept');

        $args['forgotten_automated_forget'] = $this->render_checkbox('ct-admin-forgotten', 'forgotten_automated_forget');


        return $args;
    }

    private function view1_data()
    {

        $services_args = array(
            'post_type'        => 'any',
            'numberposts'      => - 1,
            'suppress_filters' => false,
        );

        $blog_posts = get_posts($services_args);



        $args = [];
        $args['posts'] = $blog_posts;

        // add options
        $values = array(
            'manual'                     => __( 'Never', 'ct-admin' ),
            'ct-admin-weekly'    => __( 'Weekly', 'ct-admin' ),
            'ct-admin-monthly'   => __( 'Monthly', 'ct-admin' ),
            'ct-admin-quarterly' => __( 'Quarterly', 'ct-admin' )
        );
        $args['cookie_scan_period'] = $this->render_select('ct-admin-cookie', 'cookie_scan_period', $values);


        return $args;


    }

    private function view2_data()
    {
        $args = [];

        $values = array(
            '' => esc_html__('Select', 'ct-admin'),
            'cs' => 'Čeština',
            'de' => 'Deutsch',
            'en' => 'English',
            'es' => 'Español',
            'fr' => 'Français',
            'hr' => 'Hrvatski',
            'hu' => 'Magyar',
            'no' => 'Norwegian',
            'it' => 'Italiano',
            'nl' => 'Nederlands',
            'pl' => 'Polski',
            'pt' => 'Português',
            'ro' => 'Română',
            'ru' => 'Русский',
            'sk' => 'Slovenčina',
            'dk' => 'Danish',
            'bg' => 'Bulgarian',
            'sv' => 'Swedish'
        );
        $args['cookie_content_language2'] = $this->render_select('ct-admin-cookie', 'cookie_content_language2', $values);
        $args['cookie_content2'] = $this->render_textarea('ct-admin-cookie', 'cookie_content2');
        $args['cookie_popup_label_accept2'] = $this->render_input('ct-admin-cookie', 'cookie_popup_label_accept2');

        $args['forgotten_automated_forget2'] = $this->render_checkbox('ct-admin-forgotten', 'forgotten_automated_forget2');


        return $args;
    }

}