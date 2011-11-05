<?php
    /**
     * 
     * types:
     *      playerhead:
     *          position -> vector
     *          size -> integer
     *      image:
     *          source -> string (URI)
     *          metrics:
     *              from -> integer[2]
     *              to -> integer[2]
     *          position:
     *              from -> vector
     *              to -> vector
     *      file:
     *          source -> string (URI)
     *          position -> vector
     *          font -> string (path)
     *          size -> integer
     *          angle -> float
     *          color -> rgba
     *      text:
     *          text -> string
     *          position -> vector
     *          font -> string (path)
     *          size -> integer
     *          angle -> float
     *          color -> rgba
     *      playername:
     *          position -> vector
     *          font -> string (path)
     *          size -> integer
     *          angle -> float
     *          color -> rgba
     *      database:
     *          source:
     *              DSN -> string (PDO DSN)
     *              user -> string
     *              pass -> string
     *              query -> string
     *          default -> string
     *          required -> bool
     *          position -> vector
     *          font -> string (path)
     *          size -> integer
     *          angle -> float
     *          color -> rgba
     * 
     */

    return array(
        'basics' => array(
            'background'        => RESOURCE_PATH . DS . 'gfx' . DS . 'default.png',
            'alphablending'     => true,
            'savealpha'         => true,
            'quality'           => 0,
            'filters'           => PNG_NO_FILTER,
            'cache_lifetime'    => 60 * 20,
            'paidonly'          => true
        ),
        
        'data' => array(
            array(
                'type'      => 'playerhead',
                'position'  => array(14, 13),
                'size'      => 42,
            ),
            array(
                'type'      => 'playername',
                'position'  => array(70, 10 + 12),
                'font'      => RESOURCE_PATH . DS . 'fonts' . DS . 'georgiab.ttf',
                'size'      => 12,
                'angle'     => 0.0,
                'color'     => array(255, 255, 255, 0)
            ),
            array(
                'type'      => 'database',
                'source'    => array(
                    'DSN'       => 'mysql:host=localhost;port=3306;dbname=minecraft',
                    'user'      => 'root',
                    'pass'      => '',
                    'query'     => 'SELECT `level` FROM jobs WHERE `username`=? LIMIT 1'
                ),
                'default'   => 'n/a',
                'required'  => false,
                'position'  => array(145, 30 + 12),
                'font'      => RESOURCE_PATH . DS . 'fonts' . DS . 'georgia.ttf',
                'size'      => 12,
                'angle'     => 0.0,
                'color'     => array(255, 255, 255, 0),
            ),
            array(
                'type'      => 'database',
                'source'    => array(
                    'DSN'       => 'mysql:host=localhost;port=3306;dbname=minecraft',
                    'user'      => 'root',
                    'pass'      => '',
                    'query'     => 'SELECT `balance` FROM iConomy WHERE `username`=? LIMIT 1'
                ),
                'default'   => '0',
                'required'  => false,
                'position'  => array(118, 53 + 12),
                'font'      => RESOURCE_PATH . DS . 'fonts' . DS . 'georgia.ttf',
                'size'      => 12,
                'angle'     => 0.0,
                'color'     => array(255, 255, 255, 0),
            ),
        )
    );
?>
