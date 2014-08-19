<?php
/**
 * @desc   驱动调用类
 * @author xiaozheen
 *
 */
class Driver extends Loader {

    static $defaultDrivers    = array();
    static $defaultDriverCfgs = array();

    /**
     * @desc  配置预设驱动等
     * @param unknown $props
     */
    static function config( $props ) {

        // 储存预设驱动配置信息，以alias别名为索引
        foreach ( $props['defaultDrivers'] as $alias => $cfg ) {
            self::$defaultDriverCfgs[ $alias ] = $cfg;
        }

    }

    /**
     * @desc  使用预设驱动
     * @param unknown $alias
     */
    static function useDefault( $alias ) {

        // 驱动实例存在，直接返回
        if( isset( self::$defaultDrivers[ $alias ] ) )
            return self::$defaultDrivers[ $alias ];

        // 驱动配置信息存在，使用它进行初始化
        if( isset( self::$defaultDriverCfgs[ $alias ] ) ) {

            // 驱动配置信息
            $cfg = self::$defaultDriverCfgs[ $alias ];

            // 载入驱动，获取创建好的实例
            self::$defaultDrivers[ $alias ] = self::load( $cfg['path'], true, $cfg );

            // 返回实例
            return self::$defaultDrivers[ $alias ];

        }

    }

    /**
     * @desc  载入驱动
     * @param $driverPath
     * @param bool $autoInstantiate
     * @return mixed
     */
    static function load( $driverPath, $autoInstantiate = false, $configs = NULL ) {

        // 驱动根目录
        $pathRoot       = ICHI_DRIVERS_PATH;
        // 驱动根命名空间
        $nameSpaceRoot  = ICHI_DRIVERS_NS;
        // 抽象目录，例如：`/database/mysql` => 驱动目录下的`database/mysql.php`驱动文件
        $abstractPath   = $driverPath;

        $instance = parent::load( $pathRoot, $nameSpaceRoot, $abstractPath, $autoInstantiate );

        // 自动调用驱动初始化方法
        if( $autoInstantiate && method_exists( $instance, 'init' ) )
            $instance->init( $configs );


        return $instance;

    }

}