<?php

namespace cdek;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\order\create as createTest;



class cdekOrderCreateTest extends createTest {

    protected function _before() {
        $this->namespace = 'cdek';
        $this->ClassName = '\\'.$this->namespace.'\\order';
    }

    protected function _mackInterface($obj) {

        $tmp = '<?xml version="1.0" encoding="UTF-8"?>
                <response>';
        foreach ($obj['CreatedSendings'] as $id => $val) {
            if ($id) $tmp .= ',';
            $tmp .= '<Order DispatchNumber="'.$val['InvoiceNumber'].'" Number="'.$val['orderNumber'].'"/>';
        }

        $tmp .= '<Order Msg="Добавлено заказов 2"/>
                </response>';


        $tmp = json_encode(simplexml_load_string($tmp), JSON_UNESCAPED_UNICODE);
        $tmp = json_decode($tmp, true);

        return $tmp;

    }

}

?>
