<?php
$form_errors = get_transient("settings_errors");
delete_transient("settings_errors");

if(!empty($form_errors)){
    foreach($form_errors as $error){
        echo ct_admin_message($error['message'], $error['type']);
    }
}