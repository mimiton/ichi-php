<?php
/**
 * Created by PhpStorm.
 * User: xiaozheen
 * Date: 14-8-1
 * Time: 上午8:41
 */

/**
 * @desc   链式操作工具，配置域名->应用的映射，以及特殊路由规则
 * @author xiaozheen
 */
class DomainConfiger {

    // 请求方法常量
    const REQ_METHOD_ALL = 'ALL';
    const REQ_METHOD_GET = 'GET';
    const REQ_METHOD_POST= 'POST';
    const REQ_METHOD_PUT = 'PUT';
    const REQ_METHOD_DEL = 'DELETE';

    // 域名配置
    static $domainConfigs = array();

    // 操作域名
    var $domain,
        // 获取匹配域名配置时的缓存
        $configMatchCache;

    /**
     * @desc  构造方法，传入要配置的目标域名
     * @param unknown $domain
     */
    function __construct( $domain ) {
        $this->domain = $domain;
    }

    /**
     * @desc   映射到指定应用
     * @param  unknown $appName
     * @return DomainConfigSetter
     */
    function toApp( $appName ) {

        $domain = $this->domain;

        self::$domainConfigs[ $domain ]['appName'] = $appName;

        return $this;

    }

    /**
     * @desc  添加特殊路由
     * @param unknown $method
     * @param unknown $uriRegex
     * @param unknown $reRouteUri
     * @return DomainConfigSetter
     */
    private function specialRoute( $method, $uriRegex, $reRouteUri ) {

        $domain = $this->domain;

        self::$domainConfigs[ $domain ]['specialRoute'][ $method ][ $uriRegex ] = $reRouteUri;

        return $this;

    }

    /**
     * @desc  添加处理所有（ALL）请求类型的特殊路由
     * @param unknown $uriRegex
     * @param unknown $reRouteUri
     */
    function all( $uriRegex, $reRouteUri ) {

        return $this->specialRoute( self::REQ_METHOD_ALL, $uriRegex, $reRouteUri );

    }

    /**
     * @desc  添加处理（GET）请求类型的特殊路由
     * @param unknown $uriRegex
     * @param unknown $reRouteUri
     */
    function get( $uriRegex, $reRouteUri ) {

        return $this->specialRoute( self::REQ_METHOD_GET, $uriRegex, $reRouteUri );

    }

    /**
     * @desc  添加处理（POST）请求类型的特殊路由
     * @param unknown $uriRegex
     * @param unknown $reRouteUri
     */
    function post( $uriRegex, $reRouteUri ) {

        return $this->specialRoute( self::REQ_METHOD_POST, $uriRegex, $reRouteUri );

    }

    /**
     * @desc  添加处理（PUT）请求类型的特殊路由
     * @param unknown $uriRegex
     * @param unknown $reRouteUri
     */
    function put( $uriRegex, $reRouteUri ) {

        return $this->specialRoute( self::REQ_METHOD_PUT, $uriRegex, $reRouteUri );

    }

    /**
     * @desc  添加处理（DELETE）请求类型的特殊路由
     * @param unknown $uriRegex
     * @param unknown $reRouteUri
     */
    function delete( $uriRegex, $reRouteUri ) {

        return $this->specialRoute( self::REQ_METHOD_DELETE, $uriRegex, $reRouteUri );

    }


    /**
     * @desc   以正则匹配方式获取域名对应应用的路由配置
     * @param  unknown $domain
     * @return unknown|string
     */
    private function getConfigMatched() {

        // 优先从缓存中返回
        if( isset($this->configMatchCache) )
            return $this->configMatchCache;

        $domain = $this->domain;

        // 遍历匹配
        foreach ( self::$domainConfigs as $pattern => $config )
            if( preg_match( '/^' . $pattern . '$/i', $domain ) ) {

                // 结果存进缓存
                $this->configMatchCache = $config;

                return $config;

            }

        return NULL;

    }

    /**
     * @desc   获取匹配应用名
     * @return mixed
     */
    function getAppName() {

        $config = $this->getConfigMatched();

        return $config['appName'];

    }

    /**
     * @desc   获取特殊路由规则
     * @return mixed
     */
    function getSpecialRoute() {

        $config = $this->getConfigMatched();

        return $config['specialRoute'];

    }

}