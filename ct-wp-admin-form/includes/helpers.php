<?php

/**
 * helpers
 */

function ct_admin_current_view()
{
    $current_step = isset($_GET['page']) ? $_GET['page'] : 'view0';

    if (strpos($current_step, '_') === false) {
        return 'view0';
    }

    return str_replace("ct-admin-forms_", "", $current_step);

}

function ct_admin_template_server_path($file_path, $include = true, $options = array())
{
    $my_plugin_dir = WP_PLUGIN_DIR . "/" . CT_WP_ADMIN_DIR . "/";

    if ( is_dir( $my_plugin_dir ) ) {
        $path_to_file = $my_plugin_dir . $file_path . '.php';
    }

    // Check if theme includes views template file (/themes/mytheme/ct-admin/). If yes, use it instead plugin view.

    /*
    $extension = '.php';
    $name = basename($file_path, $extension);

    // view options
    $options = apply_filters('ct_admin_locate_template_options', $options, $name);

    $include_dir_path = rtrim(get_stylesheet_directory(), '/')."/ct-admin";
    $path_to_file     = rtrim($include_dir_path, '/')."/$name.php";

    if (!is_readable($path_to_file)) {
        // theme not includes views, use plugin directory
        $include_dir_path =  $my_plugin_dir ."/views";
    }

    $include_dir_path = apply_filters('ct_admin_locate_template_path', $include_dir_path, $name);
    $path_to_file     = rtrim($include_dir_path, '/')."/$name.php";

    var_dump($path_to_file);
    */


    if ($include) {
        include $path_to_file;
    }

    return $path_to_file;
}
function ct_admin_url($append = '')
{
    return plugins_url($append, __DIR__);
}

function ct_admin_view_pagename($step)
{
    $view_url_part = '';
    if($step){
        $view_url_part = '_' . $step;
    }

    return admin_url('admin.php?page=ct-admin-forms' . $view_url_part);
}
function ct_admin_submit($submit_text, $hide_class = "sr-only"){ ?>
    <div class="form__submit <?php echo $hide_class ?>">
        <p class="submit">
            <input type="submit" name="submit5" id="submit5" class="button" value="<?php echo $submit_text; ?>">
        </p>
    </div>
<?php }

/**
 * @param $message
 * @param $msg_type
 * @return void
 * warning, info, success
 */
function ct_admin_message($message, $msg_type = 'info') {
    return "<div id='message' class='alert alert-$msg_type'>$message</div>";
}