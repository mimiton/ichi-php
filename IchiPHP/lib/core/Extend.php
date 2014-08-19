<?php
/**
 * Created by PhpStorm.
 * User: xiaozheen
 * Date: 14-8-19
 * Time: 下午2:08
 */

/**
 * @desc  扩展文件载入类
 */
class Extend extends Loader {

    /**
     * @desc  载入扩展
     */
    static function load( $abstractPath, $autoInstantiate = false ) {

        // 根目录
        $pathRoot       = ICHI_EXTEND_PATH;
        // 根命名空间
        $nameSpaceRoot  = ICHI_EXTEND_NS;
        // 抽象目录，例如：`/utils/http/curl` => 扩展目录下的`utils/http/curl.php`文件

        return parent::load( $pathRoot, $nameSpaceRoot, $abstractPath, $autoInstantiate );

    }

}