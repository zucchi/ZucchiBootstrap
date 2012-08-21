<?php
return array(
    'controller_plugins' => array(
        'invokables' => array(
            'bootstrapMessenger' => 'ZucchiBootstrap\Controller\Plugin\BootstrapMessenger',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'bootstrapForm' => 'ZucchiBootstrap\Form\View\Helper\BootstrapForm',
            'bootstrapRow' => 'ZucchiBootstrap\Form\View\Helper\BootstrapRow',
            'bootstrapCollection' => 'ZucchiBootstrap\Form\View\Helper\BootstrapCollection',
            'bootstrapNavbar' => 'ZucchiBootstrap\Navigation\View\Helper\Navbar',
            'bootstrapAlert' => 'ZucchiBootstrap\View\Helper\Alert',
        ),
    ),
);