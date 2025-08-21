<?php
return [
    'tables' => [
        'users' => [
            'match' => ['email'],
            'map' => [
                'name' => 'name',
                'email' => 'email',
                'password' => ['password','hash_if_plain'],
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
            ],
        ],
        'categories' => [
            'match' => ['name'],
            'map' => [
                'name' => 'name',
                'description' => 'description',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
            ],
        ],
        'products' => [
            'match' => ['name'],
            'map' => [
                'name' => 'name',
                'description' => 'description',
                'price' => 'price',
                'category_id' => ['category_id','passthrough'],
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
            ],
        ],
    ],
];
