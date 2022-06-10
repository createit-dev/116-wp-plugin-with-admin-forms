<?php
/** @var string $cookie_scan_period */
/** @var array $posts */

?>

<h1><?php echo esc_html__('Some title', 'ct-admin'); ?></h1>
<p><?php echo esc_html__('Lorem ipsum dolor sit amet'); ?></p>
<form method="POST" action="<?php echo esc_html(admin_url('admin-post.php')); ?>">
    <input type="hidden" name="action" value="ct_admin_save">
    <?php wp_nonce_field('ct_admin_save', 'ct_admin'); ?>
    <input type="hidden" name="redirectToUrl" value="<?php echo ct_admin_view_pagename('view1'); ?>">

    <div class="row g-5">
        <div class="col-md-6">
            <fieldset class="mt-3">
                <legend><?php echo esc_html__('Section 3', 'ct-admin') ?></legend>

                <div class="mb-3 row">
                    <div class="col-md-4">
                        <label for="cookie_content_language"
                               class="form-label"><?php echo esc_html__('Option 14', 'ct-admin') ?></label>
                    </div>
                    <div class="col-md-8">
                        <?php echo $cookie_scan_period; ?>
                    </div>
                </div>

                <div class="mb-3 row">
                    <div class="col-md-4">
                        <label for="cookie_content_language"
                               class="form-label"><?php echo esc_html__('List', 'ct-admin') ?></label>
                    </div>
                    <div class="col-md-8">

                        <ul>
                            <?php foreach ($posts as $post): ?>
                                <li><?php echo $post->post_title; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

            </fieldset>
        </div>
    </div>
    <!-- / row -->

    <?php ct_admin_submit(esc_html__('Submit')); ?>

</form>


