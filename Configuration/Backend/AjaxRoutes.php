<?php

/**
 * Definitions for routes provided by EXT:opendocs
 */
return [
    'allmailstome_enable' => [
        'path' => '/allmailstome/enable',
        'target' => \Lemming\AllMailsToMe\Controller\AllMailsToMeController::class . '::enable',
    ],
    'allmailstome_disable' => [
        'path' => '/allmailstome/disable',
        'target' => \Lemming\AllMailsToMe\Controller\AllMailsToMeController::class . '::disable',
    ],
];
