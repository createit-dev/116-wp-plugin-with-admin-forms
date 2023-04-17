<?php
/** @var string $cookie_content_language2 */
/** @var string $cookie_content2 */
/** @var string $cookie_popup_label_accept2 */
/** @var string $forgotten_automated_forget2 */
?>

<h1><?php echo esc_html__('Main title', 'ct-admin'); ?></h1>
<p><?php echo esc_html__('Lorem ipsum dolor sit amet', 'ct-admin'); ?></p>
<form method="POST" action="<?php echo esc_html(admin_url('admin-post.php')); ?>">
    <input type="hidden" name="action" value="ct_admin_save">
    <?php wp_nonce_field('ct_admin_save', 'ct_admin'); ?>
    <input type="hidden" name="redirectToUrl" value="<?php echo ct_admin_view_pagename('view2'); ?>">

    <div class="row g-5">
        <div class="col-md-6">
            <fieldset class="mt-3">
                <legend class="mb-3"><?php echo esc_html__('Section 1', 'ct-admin') ?></legend>

                <div class="mb-3 row">
                    <div class="col-md-4">
                        <label for="cookie_content_language2"
                               class="form-label"><?php echo esc_html__('Option 1', 'ct-admin') ?></label>
                    </div>
                    <div class="col-md-8">
                        <?php echo $cookie_content_language2; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="cookie_content2"
                           class="form-label"><?php echo esc_html__('Option 2', 'ct-admin') ?></label>
                    <?php echo $cookie_content2; ?>
                </div>
            </fieldset>

        </div>
        <div class="col-md-6">
            <fieldset class="mt-3">
                <legend class="mb-3"><?php echo esc_html__('Section 2', 'ct-admin') ?></legend>

                <div class="mb-3">
                    <label for="cookie_popup_label_accept2"
                           class="form-label"><?php echo esc_html__("Option 3", 'ct-admin') ?></label>
                    <?php echo $cookie_popup_label_accept2; ?>
                </div>

                <div class="mb-3 form-checkbox">
                    <?php echo $forgotten_automated_forget2; ?>
                    <label for="forgotten_automated_forget2"
                           class="form-check-label"><?php echo esc_html__("Option 4", 'ct-admin') ?></label>
                </div>

            </fieldset>
        </div>

    </div>
    <!-- / row -->

    <?php ct_admin_submit(esc_html__('Submit')); ?>

</form>