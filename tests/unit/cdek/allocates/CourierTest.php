<?php

namespace cdek;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\allocates\courier as courierTest;



class cdekAllocatesCourierTest extends courierTest {

    protected function _before() {
        $this->namespace = 'cdek';
        $this->ClassName = '\\'.$this->namespace.'\\allocates';
    }

    protected function _mackInterface($obj) {

        $tmp = '<?xml version="1.0" encoding="UTF-8"?>response>
                    <Call Number="5296692"/>
                    <Call Msg="Добавлено заказов 1"/>
                </response>
                <';

        $tmp = json_encode(simplexml_load_string($tmp), JSON_UNESCAPED_UNICODE);
        $tmp = json_decode($tmp, true);

        return $tmp;

    }

}

?>
