<?php

return [
    'credentials' => [
        'file' => storage_path('app/firebase/credentials.json'),
    ],
    'project_id' => env('FIREBASE_PROJECT_ID'),
    'storage' => [
        'bucket' => env('FIREBASE_STORAGE_BUCKET')
    ]
];