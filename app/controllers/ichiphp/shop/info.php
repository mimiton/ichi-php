<?php
/**
 * Created by PhpStorm.
 * User: xiaozheen
 * Date: 14/11/22
 * Time: 下午9:45
 */

namespace controllers\ichiphp\shop;

use models\shop;

class info {


    function _numeric( $id ) {


        \Model::load( '/shop' );
        $shop = new shop();

        $info = $shop->getInfoById( $id );

        \Response::writeXML($info[0]);
    }

}