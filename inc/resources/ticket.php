<?php

// Main Ticket resource
$ticket = tr_resource_pages('Ticket@\MakerMaker\Controllers\Web\TicketController', 'Tickets')
    ->setIcon('dashicons-editor-help')
    ->setPosition(2);

$ticketAddons = tr_resource_pages('TicketAddons@\MakerMaker\Controllers\Web\TicketAddonController', 'Addons');

// Add child pages to main ticket resource
$ticket->addPage($ticketAddons);
