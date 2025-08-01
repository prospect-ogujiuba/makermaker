<?php

$wp_caps = [
    'manage_services',
    'edit_services',
    'delete_services',
    'view_services',
    'publish_services',
];


tr_roles()->updateRolesCapabilities('administrator', $wp_caps);
