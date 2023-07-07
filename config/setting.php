<?php

return [
    'webTitle'                     => 'Bookstore',
    'on'                           => 1,
    'off'                          => 0,
    'memberZeroFillMax'            => 3,
    'normalCacheTime'              => 604800, // (seconds) 7 days
    'fileType'                     => [
        ['id' => 1, 'code' => 'image'],
        ['id' => 2, 'code' => 'file'],
    ],
    'fileMaxSize'                       => 5120,
    'imageAllowExtension'               => ['png', 'jpeg'],
    'fileAllowExtension'                => ['pdf', 'pptx', 'ppt', 'doc', 'docx', 'csv', 'xls', 'xlsx']
];
