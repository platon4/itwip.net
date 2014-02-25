<?php

return array(
    'guest' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Guest',
        'bizRule' => null,
        'data' => null
    ),
    'user' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'User',
        'children' => array(
            'guest', // унаследуемся от гостя
        ),
        'bizRule' => null,
        'data' => null
    ),    
	'trusted' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Trusted',
        'children' => array(
            'user', // унаследуемся от пользователя
        ),
        'bizRule' => null,
        'data' => null
    ),
    'tester' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Tester',
        'children' => array(
            'trusted',
        ),
        'bizRule' => null,
        'data' => null
    ),
    'moderator' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Moderator',
        'children' => array(
            'tester',
        ),
        'bizRule' => null,
        'data' => null
    ),
    'admin' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Administrator',
        'children' => array(
            'moderator',         // позволим админу всё, что позволено модератору
        ),
        'bizRule' => null,
        'data' => null
    ),	
);