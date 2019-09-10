<?php

namespace easyway;

use \Codeception\Stub\Expected;

use \tests\unit\abstracts\order\create as createTest;



class easywayOrderCreateTest extends createTest {

    protected $steper;
    protected $stepcontent;

    protected function _before() {
        $this->namespace = 'easyway';
        $this->ClassName = '\\'.$this->namespace.'\\order';
        $this->steper = [];
        $this->stepcontent = [];
    }

    protected function _mackInterface($obj) {

        $step = md5(print_r($obj, true));
        $i = intval($this->steper[$step]);
        
        $tmp = '{
                "data": {';

        for($j=0; $j < sizeof($this->stepcontent[$step]); $j++) {

            $tmp .= $this->stepcontent[$step][$j].',';

        }

        $this->stepcontent[$step][$i] = '"id": "'.$obj['CreatedSendings'][$i]['InvoiceNumber'].'"';

        $tmp .= $this->stepcontent[$step][$i];

        $tmp .= '}
                }';


        $this->steper[$step] = $i + 1;


        return json_decode($tmp, true);

    }

    public function testReturns() {}

}

?>
