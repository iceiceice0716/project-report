<?php

use PR\constant;

return [
    'router' => [
        'actionName' => 'a',
        'actionMap' => [
            constant::ACTION_HOME => '\PR\action\Home',
            constant::ACTION_UPLOAD => '\PR\action\Upload',
            constant::ACTION_SHOW => '\PR\action\Show',
        ],
    ],
];