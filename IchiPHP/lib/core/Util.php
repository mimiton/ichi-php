<?php
/**
 * Created by PhpStorm.
 * User: xiaozheen
 * Date: 14-8-19
 * Time: 下午3:51
 */

class Util {

    static function unescape( $str ) {

        $str = str_replace('%u','%5Cu',$str);

        $str = rawurldecode($str);

        preg_match_all('/(%u.{4})/',$str,$r);

        $ar = $r[0];
        $table_change = array();

        foreach( $ar as $k => $v ) {

            if(substr($v,0,2) == '%u' && strlen($v) == 6) {

                $ar[$k] = iconv('UCS-2','utf-8',pack('H4',substr($v,-4)));
                $table_change = array( $v => $ar[$k] );

            }

            $str = strtr( $str, $table_change );

        }

        return $str;

    }

    static function JSON2Array( $str ) {

        $str = self::unescape(stripcslashes($str));

        return json_decode( $str, true );

    }

    static function array2JSON( $arr ) {
        return json_encode( $arr );
    }

    static function array2XML( $arr, $containerTag = 'response' ) {

        $xml = simplexml_load_string('<'.$containerTag.' />');

        self::createXML( $arr, $xml );

        return $xml->saveXML();
    }

    private static function createXML( $arr, $xml ) {

        foreach( $arr as $k => $v ) {

            if( is_array($v) ) {

                $c_xml = $xml->addChild($k);
                self::createXML( $v, $c_xml );

            }

            else $xml->addChild( $k, $v );

        }

    }

}