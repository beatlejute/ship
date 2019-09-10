<?php

namespace cdek;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\order\cancel as cancelTest;



class cdekOrderCancelTest extends cancelTest {

    protected function _before() {
        $this->namespace = 'cdek';
        $this->ClassName = '\\'.$this->namespace.'\\order';
    }

    protected function _mackInterface($obj) {

        $tmp = '<?xml version="1.0" encoding="UTF-8"?>
                <response>
                    <DeleteRequest Msg="Удалено заказов:1" >
                        <Order Number="number-8ZSO90" Msg="Заказ удален" />
                    </DeleteRequest>
                </response>';

        $tmp = json_encode(simplexml_load_string($tmp), JSON_UNESCAPED_UNICODE);
        $tmp = json_decode($tmp, true);

        return $tmp;

    }

}

?>
