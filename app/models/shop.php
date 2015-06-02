<?php
/**
 * Created by PhpStorm.
 * User: xiaozheen
 * Date: 14/11/22
 * Time: 下午10:53
 */

namespace models;


class shop {

    function getInfoById( $id ) {
        $db = \Driver::useDefault('mysql');
        return $db->getData( 'SELECT * FROM tb_shop WHERE id='.$id );
    }

} 