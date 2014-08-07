<?php
/**
 * Created by PhpStorm.
 * User: xiaozheen
 * Date: 14-8-7
 * Time: 下午10:22
 */

namespace extend\utils\secure\validate;


class validate_code {

    private $charset = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ123456789'; //随机因子
    private $code; //验证码
    private $img; //图形资源句柄
    private $font; //指定的字体
    private $fontcolor; //指定字体颜色

    /**
     * @desc  构造方法
     * @param int $w 图片宽度
     * @param int $h 高度
     * @param int $l 字符数量
     * @param int $fontSize 字体大小
     */
    function __construct( $width = 130, $height = 50, $length = 4, $fontSize = 20 ) {

        $this->font     = dirname( __FILE__ ) . '/Trebuchet MS Italic.ttf';
        $this->codelen  = $length;
        $this->width    = $width;
        $this->height   = $height;
        $this->fontsize = $fontSize;

    }

    /**
     * @desc  创建并输出验证码图片
     */
    function make() {

        $this->createBg();
        $this->createCode();
        $this->createLine();
        $this->createFont();
        $this->outPut();
        return strtolower($this->code);

    }

    /**
     * @desc  创建随机码
     */
    private function createCode() {

        $_len = strlen($this->charset) - 1;

        for ( $i = 0; $i < $this->codelen; $i++ )
            $this->code .= $this->charset[ mt_rand(0, $_len) ];

    }

    /**
     * @desc  创建图片背景
     */
    private function createBg() {

        $this->img = imagecreatetruecolor( $this->width, $this->height );

        $color = imagecolorallocate( $this->img, mt_rand(157, 255), mt_rand(157, 255), mt_rand(157, 255) );

        imagefilledrectangle( $this->img, 0, $this->height, $this->width, 0, $color );

    }


    /**
     * @desc  创建图片中文字
     */
    private function createFont() {

        $charWidth = $this->width / $this->codelen;

        for ( $i = 0; $i < $this->codelen; $i++ ) {

            $this->fontcolor = imagecolorallocate( $this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156) );

            imagettftext( $this->img, $this->fontsize, mt_rand(-30, 30), $charWidth * $i + mt_rand(1, 5), $this->height / 1.4, $this->fontcolor, $this->font, $this->code[$i] );

        }
    }


    /**
     * @desc  添加干扰图案
     */
    private function createLine() {
        //线条
        for ($i = 0; $i < 6; $i++) {
            $color = imagecolorallocate($this->img, mt_rand(0, 156) , mt_rand(0, 156) , mt_rand(0, 156));
            imageline($this->img, mt_rand(0, $this->width) , mt_rand(0, $this->height) , mt_rand(0, $this->width) , mt_rand(0, $this->height) , $color);
        }
        //雪花
        for ($i = 0; $i < 100; $i++) {
            $color = imagecolorallocate($this->img, mt_rand(200, 255) , mt_rand(200, 255) , mt_rand(200, 255));
            imagestring($this->img, mt_rand(1, 5) , mt_rand(0, $this->width) , mt_rand(0, $this->height) , '*', $color);
        }
    }

    /**
     * @desc  输出
     */
    private function outPut() {
        header('Content-type:image/png');
        imagepng($this->img);
        imagedestroy($this->img);
    }

} 