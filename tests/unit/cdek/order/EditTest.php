<?php

namespace cdek;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\order\edit as editTest;



class cdekOrderEditTest extends editTest {

    protected function _before() {
        $this->namespace = 'cdek';
        $this->ClassName = '\\'.$this->namespace.'\\order';
    }

    protected function _mackInterface($obj) {

        $tmp = '<?xml version="1.0" encoding="UTF-8" ?>
        <ScheduleRequest Date="2010-10-14" Account="abc123" Secure="abcd1234" OrderCount="2">
            <Order Number="' . $obj['GCInvoiceNumber'] . '" DispatchNumber ="' . $obj['InvoiceNumber'] . '" Date="2010-10-14">
            <Attempt ID="4" Date="2010-10-16" TimeBeg="09:00:00" TimeEnd="13:00:00" 	RecipientName="Зимина Юлия 	Владимировна"
            Phone="79296071468">
            <Address Street="Просторная" House="д.9"  Flat="оф.10"  />
            </Attempt>
            </Order>
        </ScheduleRequest>';

        $tmp = json_encode(simplexml_load_string($tmp), JSON_UNESCAPED_UNICODE);
        $tmp = json_decode($tmp, true);

        return $tmp;

    }

}

?>
