<?php
/**
 * Created by PhpStorm.
 * User: xiaozheen
 * Date: 14-8-3
 * Time: 下午10:30
 */

class Loader {

    /**
     * @desc   载入文件
     */
    static function load( $realPathRoot, $nameSpaceRoot, $abstractPath, $autoInstatiate = false ) {

        // 文件真实路径
        $filePath  = $realPathRoot . $abstractPath . '.php';

        // 文件不存在，抛出500异常，由Router类抓取
        if( !file_exists($filePath) )
            throw new IchiStatusException( 500, 'File of abstract path: `'.$abstractPath.'` not found!' );

        // 载入文件
        require_once $filePath;

        // 非自动创建模式，返回
        if( !$autoInstatiate )
            return;

        // 模块命名空间
        $nameSpace = $nameSpaceRoot . str_replace( '/', '\\', $abstractPath );

        // 尝试使用命名空间创建实例
        if( class_exists($nameSpace) )
            $driver = new $nameSpace();
        // 降级，使用文件名创建实例
        // 从文filePath中获取文件名：\foo\bar\abc => new abc();
        else {
            $matches = explode( '\\', $abstractPath );
            $className = $matches[ count($matches)-1 ];
            $driver = new $className();
        }

        return $driver;

    }

}