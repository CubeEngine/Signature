<?php
    return array(
        'basics' => array(
            'background'        => 'gfx/default.png',
            //'cache_lifetime'    => 60 * 20,
            'paidonly'          => true,


            'format'            => array(
                'type'          => 'png',
                'alphablending' => true,
                'savealpha'     => true,
                //'quality'       => 100
            ),

            // the database connections
            'databases' => array(
                'minecraft' => array(
                    'dsn' => 'mysql:host=localhost;port=3306;dbname=test',
                    'user' => 'root',
                    'pass' => ''
                )
            ),

            // the fonts
            'fonts' => array(
                // absolute path => global resources folder, relative path => theme resources folder
                'georgia bold'  => '/font/georgiab.ttf',
                'minecraft'     => '/font/minecraft.ttf'
            ),

            // the single objects override/extend these values
            'defaults' => array(
                'size'      => 12,
                'angle'     => 0.0,
                'font'      => 'georgia bold',
                'database'  => 'minecraft',
                'color'     => new Color('#FFF')
            ),

            'params' => array(
                'player'
            )
        ),
        
        'objects' => array(
            array(
                'type'      => 'playerhead',
                'position'  => new Vector(14, 13),
                'size'      => 42,
            ),
            array(
                'type'      => 'text',
                'position'  => new Vector(70, 10 + 12),
                'text'      => '{player}'
            ),
            array(
                'type'      => 'dbcontent',
                'query'     => 'SELECT `level` FROM jobs WHERE `username`=? LIMIT 1',
                'default'   => 'n/a',
                'required'  => false,
                'position'  => new Vector(145, 30 + 12)
            ),
            array(
                'type'      => 'dbcontent',
                'query'     => 'SELECT `balance` FROM iConomy WHERE `username`=? LIMIT 1',
                'default'   => '0',
                'required'  => false,
                'position'  => new Vector(118, 53 + 12)
            ),
            array(
                'type'      => 'steve',
                'scale'     => 5,
                'position'  => new Vector(80, 0)
            )
        )
    );