<?php
/**
 * Created by PhpStorm.
 * User: xiaozheen
 * Date: 14-8-3
 * Time: 下午10:30
 */

/**
 * @desc 文件加载类Loader
 */
class Loader {

    /**
     * @desc   载入文件
     */
    static function load( $realPathRoot, $nameSpaceRoot, $abstractPath, $autoInstatiate = false ) {

        // 模块命名空间
        $nameSpace = $nameSpaceRoot . str_replace( '/', '\\', $abstractPath );

        // 若命名空间下模块已存在，则跳过文件加载
        if( !class_exists($nameSpace) ) {

            // 文件真实路径
            $filePath  = $realPathRoot . $abstractPath . '.php';

            // 文件不存在，抛出500异常，由Router类抓取
            if( !file_exists($filePath) )
                throw new IchiStatusException( 500, 'Loader::load(): File of abstract path `'.$abstractPath.'` not found!' );

            // 载入文件
            require_once $filePath;

        }

        // 非自动创建模式，返回
        if( !$autoInstatiate )
            return;

        // 返回创建的实例

        return new $nameSpace();

    }

}