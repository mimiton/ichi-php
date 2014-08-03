<?php
/**
 * @desc   模型调用类
 * @author xiaozheen
 *
 */
class Model extends Loader {
	
	/**
	 * @desc  载入数据模型
	 * @param unknown $modelPath
	 * @return unknown
	 */
	static function load( $modelPath, $autoInstantiate = false ) {

        // 模型根目录
        $pathRoot       = ICHI_MODELS_PATH;
        // 模型根命名空间
        $nameSpaceRoot  = ICHI_MODELS_NS;
        // 抽象目录，例如：`/foo/bar` => 模型目录下的`user/rank.php`模型文件
        $abstractPath   = $modelPath;

        return parent::load( $pathRoot, $nameSpaceRoot, $abstractPath, $autoInstantiate );
		
	}
}