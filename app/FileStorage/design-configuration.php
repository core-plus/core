<?php

return [
    // default-filesystem
    'default-filesystem' => 'local',
    'filesystems' => [
        // filesystems.local
        'local' => [
            'disk' => 'local',
            'timeout' => 3360,
        ],
        // filesystems.aliyun-oss
        'aliyun-oss' => [
            'bucket' => null,
            'access-key-id' => null,
            'access-key-secret' => null,
            'domain' => null,
            'inside-domain' => null, // 内部请求域名
            'timeout' => 3360,
            'acl' => 'public-read', // public-read-write、public-read、private
        ],
    ],
    'channels' => [
        // channels.public
        'public' => [
            'filesystem' => '',
        ],
        // channels.protected
        'protected' => [
            'filesystem' => '',
        ],
        // channels.private
        'private' => [
            'filesystem' => '',
        ],
    ],
    // task-create-validate
    'task-create-validate' => [
        'image-min-width' => 0,
        'image-max-width' => 2800,
        'image-min-height' => 0,
        'image-max-height' => 2800,
        'file-min-size' => 2048, // 2KB
        'file-mix-size' => 2097152, // 2MB
        'file-mime-types' => [],
    ],
];
