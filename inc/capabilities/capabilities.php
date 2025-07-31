<?php

$wp_caps = [
    'manage_clients',
    'manage_services',
];


tr_roles()->updateRolesCapabilities('administrator', $wp_caps);
