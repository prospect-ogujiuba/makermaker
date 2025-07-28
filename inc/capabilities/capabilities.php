<?php

$wp_caps = [
    'manage_clients',
    'manage_services',
];

tr_roles()->remove(
    'b2bcnc_admin',
    [],
    'B2BCNC Admin'
);


tr_roles()->updateRolesCapabilities('administrator', $wp_caps);
