<?php
/**
 * Created by PhpStorm.
 * User: xiaozheen
 * Date: 14-8-19
 * Time: 下午2:02
 */

namespace extend\utils\http;


class curl {

    /**
     * @desc  发送GET请求
     * @param $url
     * @param Array $data
     * @param int $timeout
     * @param String $UA
     * @param String $proxy
     * @return Bool|String
     */
    static function get( $url, $data = NULL, $timeout = 10, $UA = NULL, $proxy = NULL ) {

        if( empty($url) )
            return false;

        // 序列化参数
        if( is_array($data) )
            $url .= '?' . self::serialize($data);

        $curl = self::makeBasicCURL( $url, $UA, $timeout, $proxy );

        // 执行
        $result = curl_exec($curl);
        curl_close($curl);

        return $result;

    }

    /**
     * @desc  发送POST请求
     * @param $url
     * @param Array $data
     * @param int $timeout
     * @param String $UA
     * @param String $proxy
     * @param String $customerMethod 自定义请求方法，PUT/DELETE等
     * @return Bool|String
     */
    static function post( $url, $data = NULL, $timeout = 10, $UA = NULL, $proxy = NULL, $customerMethod = NULL ) {

        if( empty($url) )
            return false;

        $curl = self::makeBasicCURL( $url, $UA, $timeout, $proxy );

        // 自定义请求method，PUT/DELETE等
        if( $customerMethod )
            curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, $customerMethod );
        else
            // 常规的POST请求
            curl_setopt( $curl, CURLOPT_POST, true );

        // 序列化参数
        $params = is_array($data)? self::serialize($data) : $data;
        // POST提交的数据包
        curl_setopt( $curl, CURLOPT_POSTFIELDS, $params );


        // 执行
        $result = curl_exec($curl);
        curl_close($curl);

        return $result;

    }

    /**
     * @desc  PUT请求
     * @param $url
     * @param null $data
     * @param int $timeout
     * @param null $UA
     * @param null $proxy
     * @return Bool|String
     */
    static function put( $url, $data = NULL, $timeout = 10, $UA = NULL, $proxy = NULL ) {
        return self::post( $url, $data, $timeout, $UA, $proxy, 'PUT' );
    }

    /**
     * @desc   DELETE请求
     * @param $url
     * @param $data
     * @param int $timeout
     * @param null $UA
     * @param null $proxy
     * @return Bool|String
     */
    static function delete( $url, $data, $timeout = 10, $UA = NULL, $proxy = NULL ) {
        return self::post( $url, $data, $timeout, $UA, $proxy, 'DELETE' );
    }

    /**
     * @desc  序列化请求参数
     * @param $params
     * @return string
     */
    static function serialize( $params ) {

        $str = '';
        foreach( $params as $key => $value ) {
            $str .= $key .'='. $value .'&';
        }

        return substr( $str, 0, -1 );

    }

    /**
     * @desc  构造一个基础的curl对象，供上面各种请求方法使用
     * @param $url
     * @param $UA
     * @param $timeout
     * @param $proxy
     * @return resource
     */
    private static function makeBasicCURL( $url, $UA, $timeout, $proxy ) {

        $isSSL = substr($url, 0, 8) == 'https://';

        $curl = curl_init();

        // 代理
        if( !is_null($proxy) )
            curl_setopt( $curl, CURLOPT_PROXY, $proxy );

        // URL
        curl_setopt( $curl, CURLOPT_URL, $url );

        // https
        if($isSSL) {
            curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, 0 );  // 对认证证书来源的检查
            curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, 1 );  // 从证书中检查SSL加密算法是否存在
        }

        // COOKIE
        $cookiePath = self::getCookiePath();
        // cookie保存路径
        curl_setopt( $curl, CURLOPT_COOKIEJAR,  $cookiePath );
        // cookie读取路径
        curl_setopt( $curl, CURLOPT_COOKIEFILE, $cookiePath );

        // UA信息
        if( !is_null($UA) )
            curl_setopt( $curl, CURLOPT_USERAGENT, $UA );

        // 启用时会将头文件的信息作为数据流输出。
        curl_setopt( $curl, CURLOPT_HEADER, 0 );
        // 启用时会将服务器服务器返回的"Location: "放在header中递归的返回给服务器，使用CURLOPT_MAXREDIRS可以限定递归返回的数量。
        curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, 1 );
        // 文件流形式
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
        // 设置cURL允许执行的最长秒数。
        curl_setopt( $curl, CURLOPT_TIMEOUT, $timeout );

        return $curl;

    }

    /**
     * @desc   cookie读写路径
     * @return string
     */
    static private function getCookiePath() {
        return ICHI_CWD . '/.tmp/cookie';
    }
} 