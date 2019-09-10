<?php

namespace abstracts;


trait infoTrait {

    public function getAddressInfo($address) {

        $parts = explode(',', trim($address));

        $tmp = explode(' ', trim($parts[0]));
        if(strlen($tmp[0]) == 6 && ctype_digit($tmp[0])) {

            $parts[] = $tmp[0];
            unset($tmp[0]);
            $parts[0] = join(' ', $tmp);

        }

        foreach($parts as $partId => $part) if(!$addressInfo['index'] && $index = $this->getIndexInfo($part)) {

            $addressInfo['index'] = $index;
            $elements[$partId] = 'index';
            unset($parts[$partId]);

        } elseif(!$addressInfo['country'] && $country = $this->getCountryInfo($part)) {

            $addressInfo['country'] = $country;
            $elements[$partId] = 'country';
            unset($parts[$partId]);

        } elseif(!$addressInfo['region'] && $region = $this->getRegionInfo($part)) {

            $addressInfo['region'] = $region;
            $elements[$partId] = 'region';
            unset($parts[$partId]);

        } elseif(!$addressInfo['settlement'] && $elements[$partId-1] != 'street' && $settlement = $this->getSettlementInfo($part)) {

            $addressInfo['settlement'] = $settlement;
            $elements[$partId] = 'settlement';
            unset($parts[$partId]);

        } elseif(!$addressInfo['street'] && $street = $this->getStreetInfo($part)) {

            $addressInfo['street'] = $street;
            $elements[$partId] = 'street';
            unset($parts[$partId]);

        } elseif(!$addressInfo['building'] && (($building = $this->getBuildingInfo($part)) || ($elements[$partId-1] == 'street' && $building = $this->getBuildingInfo('д '.$part)))) {

            $addressInfo['building'] = $building;
            $elements[$partId] = 'building';
            unset($parts[$partId]);

        } elseif(!$addressInfo['housing'] && $housing = $this->getHousingInfo($part)) {

            $addressInfo['housing'] = $housing;
            $elements[$partId] = 'housing';
            unset($parts[$partId]);

        } elseif(!$addressInfo['apartment'] && $apartment = $this->getApartmentInfo($part)) {

            $addressInfo['apartment'] = $apartment;
            $elements[$partId] = 'apartment';
            unset($parts[$partId]);

        }

        if($parts) foreach($parts as $partId => $part) if(!$addressInfo['settlement'] && preg_match("/[\w\d\s\-А-Яа-я]+/u", trim($part))) {

            $addressInfo['settlement'] = $this->getSettlementInfo('г '.$part);
            $elements[$partId] = 'settlement';
            unset($parts[$partId]);

        } elseif(!$addressInfo['street'] && preg_match("/[\w\d\s\-А-Яа-я0-9]+/u", trim($part))) {

            $addressInfo['street'] = $this->getStreetInfo('ул '.$part);
            $elements[$partId] = 'street';
            unset($parts[$partId]);

        } elseif(!$addressInfo['building'] && (ctype_digit(trim(str_ireplace('/', '', str_replace('/', '', $part)))) || $elements[$partId-1] == 'street')) {

            $addressInfo['building'] = $this->getBuildingInfo('д '.$part);
            $elements[$partId] = 'building';
            unset($parts[$partId]);

        }


        if(!$addressInfo['region'] && $addressInfo['index']) $addressInfo['region'] = $addressInfo['index']['region'];

        if(isset($addressInfo['index'])) $addressInfo['address'] .= $addressInfo['index']['index'];
        if(isset($addressInfo['country'])) {
            if(isset($addressInfo['address'])) $addressInfo['address'] .= ', ';
            $addressInfo['address'] .= $addressInfo['country']['name'];
        }
        if(isset($addressInfo['region'])) {
            if(isset($addressInfo['address'])) $addressInfo['address'] .= ', ';
            $addressInfo['address'] .= $addressInfo['region']['name'];
        }
        if(isset($addressInfo['settlement'])) {
            if(isset($addressInfo['address'])) $addressInfo['address'] .= ', ';
            $addressInfo['address'] .= $addressInfo['settlement']['type']['abbr'].'. '.$addressInfo['settlement']['name'];
        }
        if(isset($addressInfo['street'])) {
            if(isset($addressInfo['address'])) $addressInfo['address'] .= ', ';
            $addressInfo['address'] .= $addressInfo['street']['type']['abbr'].'. '.$addressInfo['street']['name'];
        }
        if(isset($addressInfo['building'])) {
            if(isset($addressInfo['address'])) $addressInfo['address'] .= ', ';
            $addressInfo['address'] .= $addressInfo['building']['type']['abbr'].'. '.$addressInfo['building']['name'];
        }
        if(isset($addressInfo['housing'])) {
            if(isset($addressInfo['address'])) $addressInfo['address'] .= ', ';
            $addressInfo['address'] .= $addressInfo['housing']['type']['abbr'].'. '.$addressInfo['housing']['name'];
        }
        if(isset($addressInfo['apartment'])) {
            if(isset($addressInfo['address'])) $addressInfo['address'] .= ', ';
            $addressInfo['address'] .= $addressInfo['apartment']['type']['abbr'].'. '.$addressInfo['apartment']['name'];
        }


        return $addressInfo;


    }
    public function getIndexInfo($index) {

        $index = trim($index);
        if(strlen($index) != 6) return false;
        if(!ctype_digit($index)) return false;
        $region = $this->getRegionInfo(null, null, null, null, $index);
        return ['index' => $index, 'region' => $region];

    }
    public function getCountryInfo($name='', $code='') {

        $country['AB'] = ['name' => 'Абхазия', 'phoneCode' => '7 840', 'code' => 'AB', 'fullCode' => 'ABH', 'number' => '895' , 'barcode' => ''];
        $country['AU'] = ['name' => 'Австралия', 'phoneCode' => '61', 'code' => 'AU', 'fullCode' => 'AUS', 'number' => '36' , 'barcode' => '930-939'];
        $country['AT'] = ['name' => 'Австрия', 'phoneCode' => '43', 'code' => 'AT', 'fullCode' => 'AUT', 'number' => '40' , 'barcode' => '900-919'];
        $country['AZ'] = ['name' => 'Азербайджан', 'phoneCode' => '994', 'code' => 'AZ', 'fullCode' => 'AZE', 'number' => '31' , 'barcode' => '476'];
        $country['AL'] = ['name' => 'Албания', 'phoneCode' => '355', 'code' => 'AL', 'fullCode' => 'ALB', 'number' => '8' , 'barcode' => '530'];
        $country['DZ'] = ['name' => 'Алжир', 'phoneCode' => '213', 'code' => 'DZ', 'fullCode' => 'DZA', 'number' => '12' , 'barcode' => '613'];
        $country['AO'] = ['name' => 'Ангола', 'phoneCode' => '244', 'code' => 'AO', 'fullCode' => 'AGO', 'number' => '24' , 'barcode' => ''];
        $country['AD'] = ['name' => 'Андорра', 'phoneCode' => '376', 'code' => 'AD', 'fullCode' => 'AND', 'number' => '20' , 'barcode' => ''];
        $country['AG'] = ['name' => 'Антигуа и Барбуда', 'phoneCode' => '1 268', 'code' => 'AG', 'fullCode' => 'ATG', 'number' => '28' , 'barcode' => ''];
        $country['AR'] = ['name' => 'Аргентина', 'phoneCode' => '54', 'code' => 'AR', 'fullCode' => 'ARG', 'number' => '32' , 'barcode' => '779'];
        $country['AM'] = ['name' => 'Армения', 'phoneCode' => '374', 'code' => 'AM', 'fullCode' => 'ARM', 'number' => '51' , 'barcode' => '485'];
        $country['AF'] = ['name' => 'Афганистан', 'phoneCode' => '93', 'code' => 'AF', 'fullCode' => 'AFG', 'number' => '4' , 'barcode' => ''];
        $country['BS'] = ['name' => 'Багамские Острова', 'phoneCode' => '1 242', 'code' => 'BS', 'fullCode' => 'BHS', 'number' => '44' , 'barcode' => ''];
        $country['BD'] = ['name' => 'Бангладеш', 'phoneCode' => '880', 'code' => 'BD', 'fullCode' => 'BGD', 'number' => '50' , 'barcode' => ''];
        $country['BB'] = ['name' => 'Барбадос', 'phoneCode' => '1 246', 'code' => 'BB', 'fullCode' => 'BRB', 'number' => '52' , 'barcode' => ''];
        $country['BH'] = ['name' => 'Бахрейн', 'phoneCode' => '973', 'code' => 'BH', 'fullCode' => 'BHR', 'number' => '48' , 'barcode' => '608'];
        $country['BZ'] = ['name' => 'Белиз', 'phoneCode' => '501', 'code' => 'BZ', 'fullCode' => 'BLZ', 'number' => '84' , 'barcode' => ''];
        $country['BY'] = ['name' => 'Белоруссия', 'phoneCode' => '375', 'code' => 'BY', 'fullCode' => 'BLR', 'number' => '112' , 'barcode' => '481'];
        $country['BE'] = ['name' => 'Бельгия', 'phoneCode' => '32', 'code' => 'BE', 'fullCode' => 'BEL', 'number' => '56' , 'barcode' => '540-549'];
        $country['BJ'] = ['name' => 'Бенин', 'phoneCode' => '229', 'code' => 'BJ', 'fullCode' => 'BEN', 'number' => '204' , 'barcode' => ''];
        $country['BG'] = ['name' => 'Болгария', 'phoneCode' => '359', 'code' => 'BG', 'fullCode' => 'BGR', 'number' => '100' , 'barcode' => '380'];
        $country['BO'] = ['name' => 'Боливия', 'phoneCode' => '591', 'code' => 'BO', 'fullCode' => 'BOL', 'number' => '68' , 'barcode' => '777'];
        $country['BA'] = ['name' => 'Босния и Герцеговина', 'phoneCode' => '387', 'code' => 'BA', 'fullCode' => 'BIH', 'number' => '70' , 'barcode' => '387'];
        $country['BW'] = ['name' => 'Ботсвана', 'phoneCode' => '267', 'code' => 'BW', 'fullCode' => 'BWA', 'number' => '72' , 'barcode' => ''];
        $country['BR'] = ['name' => 'Бразилия', 'phoneCode' => '55', 'code' => 'BR', 'fullCode' => 'BRA', 'number' => '76' , 'barcode' => '789-790'];
        $country['BN'] = ['name' => 'Бруней', 'phoneCode' => '673', 'code' => 'BN', 'fullCode' => 'BRN', 'number' => '96' , 'barcode' => ''];
        $country['BF'] = ['name' => 'Буркина-Фасо', 'phoneCode' => '226', 'code' => 'BF', 'fullCode' => 'BFA', 'number' => '854' , 'barcode' => ''];
        $country['BI'] = ['name' => 'Бурунди', 'phoneCode' => '257', 'code' => 'BI', 'fullCode' => 'BDI', 'number' => '108' , 'barcode' => ''];
        $country['BT'] = ['name' => 'Бутан', 'phoneCode' => '975', 'code' => 'BT', 'fullCode' => 'BTN', 'number' => '64' , 'barcode' => ''];
        $country['VU'] = ['name' => 'Вануату', 'phoneCode' => '678', 'code' => 'VU', 'fullCode' => 'VUT', 'number' => '548' , 'barcode' => ''];
        $country['VA'] = ['name' => 'Ватикан', 'phoneCode' => '379', 'code' => 'VA', 'fullCode' => 'VAT', 'number' => '336' , 'barcode' => ''];
        $country['GB'] = ['name' => 'Великобритания', 'phoneCode' => '44', 'code' => 'GB', 'fullCode' => 'GBR', 'number' => '826' , 'barcode' => '500-509'];
        $country['HU'] = ['name' => 'Венгрия', 'phoneCode' => '36', 'code' => 'HU', 'fullCode' => 'HUN', 'number' => '348' , 'barcode' => '599'];
        $country['VE'] = ['name' => 'Венесуэла', 'phoneCode' => '58', 'code' => 'VE', 'fullCode' => 'VEN', 'number' => '862' , 'barcode' => '759'];
        $country['TL'] = ['name' => 'Восточный Тимор', 'phoneCode' => '670', 'code' => 'TL', 'fullCode' => 'TLS', 'number' => '626' , 'barcode' => ''];
        $country['VN'] = ['name' => 'Вьетнам', 'phoneCode' => '84', 'code' => 'VN', 'fullCode' => 'VNM', 'number' => '704' , 'barcode' => '893'];
        $country['GA'] = ['name' => 'Габон', 'phoneCode' => '241', 'code' => 'GA', 'fullCode' => 'GAB', 'number' => '266' , 'barcode' => ''];
        $country['HT'] = ['name' => 'Гаити', 'phoneCode' => '509', 'code' => 'HT', 'fullCode' => 'HTI', 'number' => '332' , 'barcode' => ''];
        $country['GY'] = ['name' => 'Гайана', 'phoneCode' => '592', 'code' => 'GY', 'fullCode' => 'GUY', 'number' => '328' , 'barcode' => ''];
        $country['GM'] = ['name' => 'Гамбия', 'phoneCode' => '220', 'code' => 'GM', 'fullCode' => 'GMB', 'number' => '270' , 'barcode' => ''];
        $country['GH'] = ['name' => 'Гана', 'phoneCode' => '233', 'code' => 'GH', 'fullCode' => 'GHA', 'number' => '288' , 'barcode' => '603'];
        $country['GT'] = ['name' => 'Гватемала', 'phoneCode' => '502', 'code' => 'GT', 'fullCode' => 'GTM', 'number' => '320' , 'barcode' => '740'];
        $country['GN'] = ['name' => 'Гвинея', 'phoneCode' => '224', 'code' => 'GN', 'fullCode' => 'GIN', 'number' => '324' , 'barcode' => ''];
        $country['GW'] = ['name' => 'Гвинея-Бисау', 'phoneCode' => '245', 'code' => 'GW', 'fullCode' => 'GNB', 'number' => '624' , 'barcode' => ''];
        $country['DE'] = ['name' => 'Германия', 'phoneCode' => '49', 'code' => 'DE', 'fullCode' => 'DEU', 'number' => '276' , 'barcode' => '400-440'];
        $country['HN'] = ['name' => 'Гондурас', 'phoneCode' => '504', 'code' => 'HN', 'fullCode' => 'HND', 'number' => '340' , 'barcode' => '742'];
        $country['PS'] = ['name' => 'Государство Палестина', 'phoneCode' => '970', 'code' => 'PS', 'fullCode' => 'PSE', 'number' => '275' , 'barcode' => ''];
        $country['GD'] = ['name' => 'Гренада', 'phoneCode' => '1 473', 'code' => 'GD', 'fullCode' => 'GRD', 'number' => '308' , 'barcode' => ''];
        $country['GR'] = ['name' => 'Греция', 'phoneCode' => '30', 'code' => 'GR', 'fullCode' => 'GRC', 'number' => '300' , 'barcode' => '520'];
        $country['GE'] = ['name' => 'Грузия', 'phoneCode' => '995', 'code' => 'GE', 'fullCode' => 'GEO', 'number' => '268' , 'barcode' => '486'];
        $country['DK'] = ['name' => 'Дания', 'phoneCode' => '45', 'code' => 'DK', 'fullCode' => 'DNK', 'number' => '208' , 'barcode' => '570-579'];
        $country['DJ'] = ['name' => 'Джибути', 'phoneCode' => '253', 'code' => 'DJ', 'fullCode' => 'DJI', 'number' => '262' , 'barcode' => ''];
        $country['DM'] = ['name' => 'Доминика', 'phoneCode' => '1 767', 'code' => 'DM', 'fullCode' => 'DMA', 'number' => '212' , 'barcode' => ''];
        $country['DO'] = ['name' => 'Доминиканская Республика', 'phoneCode' => '1 809', 'code' => 'DO', 'fullCode' => 'DOM', 'number' => '214' , 'barcode' => '746'];
        $country['CD'] = ['name' => 'ДР Конго', 'phoneCode' => '243', 'code' => 'CD', 'fullCode' => 'COD', 'number' => '180' , 'barcode' => ''];
        $country['EG'] = ['name' => 'Египет', 'phoneCode' => '20', 'code' => 'EG', 'fullCode' => 'EGY', 'number' => '818' , 'barcode' => '622'];
        $country['ZM'] = ['name' => 'Замбия', 'phoneCode' => '260', 'code' => 'ZM', 'fullCode' => 'ZMB', 'number' => '894' , 'barcode' => ''];
        $country['ZW'] = ['name' => 'Зимбабве', 'phoneCode' => '263', 'code' => 'ZW', 'fullCode' => 'ZWE', 'number' => '716' , 'barcode' => ''];
        $country['IL'] = ['name' => 'Израиль', 'phoneCode' => '972', 'code' => 'IL', 'fullCode' => 'ISR', 'number' => '376' , 'barcode' => '729'];
        $country['IN'] = ['name' => 'Индия', 'phoneCode' => '91', 'code' => 'IN', 'fullCode' => 'IND', 'number' => '356' , 'barcode' => '890'];
        $country['ID'] = ['name' => 'Индонезия', 'phoneCode' => '62', 'code' => 'ID', 'fullCode' => 'IDN', 'number' => '360' , 'barcode' => '899'];
        $country['JO'] = ['name' => 'Иордания', 'phoneCode' => '962', 'code' => 'JO', 'fullCode' => 'JOR', 'number' => '400' , 'barcode' => '625'];
        $country['IQ'] = ['name' => 'Ирак', 'phoneCode' => '964', 'code' => 'IQ', 'fullCode' => 'IRQ', 'number' => '368' , 'barcode' => ''];
        $country['IR'] = ['name' => 'Иран', 'phoneCode' => '98', 'code' => 'IR', 'fullCode' => 'IRN', 'number' => '364' , 'barcode' => '626'];
        $country['IE'] = ['name' => 'Ирландия', 'phoneCode' => '353', 'code' => 'IE', 'fullCode' => 'IRL', 'number' => '372' , 'barcode' => '539'];
        $country['IS'] = ['name' => 'Исландия', 'phoneCode' => '354', 'code' => 'IS', 'fullCode' => 'ISL', 'number' => '352' , 'barcode' => '569'];
        $country['ES'] = ['name' => 'Испания', 'phoneCode' => '34', 'code' => 'ES', 'fullCode' => 'ESP', 'number' => '724' , 'barcode' => '840-849'];
        $country['IT'] = ['name' => 'Италия', 'phoneCode' => '39', 'code' => 'IT', 'fullCode' => 'ITA', 'number' => '380' , 'barcode' => '800-839'];
        $country['YE'] = ['name' => 'Йемен', 'phoneCode' => '967', 'code' => 'YE', 'fullCode' => 'YEM', 'number' => '887' , 'barcode' => ''];
        $country['CV'] = ['name' => 'Кабо-Верде', 'phoneCode' => '238', 'code' => 'CV', 'fullCode' => 'CPV', 'number' => '132' , 'barcode' => ''];
        $country['KZ'] = ['name' => 'Казахстан', 'phoneCode' => '+7 xxx', 'code' => 'KZ', 'fullCode' => 'KAZ', 'number' => '398' , 'barcode' => '487'];
        $country['KH'] = ['name' => 'Камбоджа', 'phoneCode' => '855', 'code' => 'KH', 'fullCode' => 'KHM', 'number' => '116' , 'barcode' => '884'];
        $country['CM'] = ['name' => 'Камерун', 'phoneCode' => '237', 'code' => 'CM', 'fullCode' => 'CMR', 'number' => '120' , 'barcode' => ''];
        $country['CA'] = ['name' => 'Канада', 'phoneCode' => '+1 xxx', 'code' => 'CA', 'fullCode' => 'CAN', 'number' => '124' , 'barcode' => '754-755'];
        $country['QA'] = ['name' => 'Катар', 'phoneCode' => '974', 'code' => 'QA', 'fullCode' => 'QAT', 'number' => '634' , 'barcode' => ''];
        $country['KE'] = ['name' => 'Кения', 'phoneCode' => '254', 'code' => 'KE', 'fullCode' => 'KEN', 'number' => '404' , 'barcode' => '616'];
        $country['CY'] = ['name' => 'Кипр', 'phoneCode' => '357', 'code' => 'CY', 'fullCode' => 'CYP', 'number' => '196' , 'barcode' => '529'];
        $country['KG'] = ['name' => 'Киргизия', 'phoneCode' => '996', 'code' => 'KG', 'fullCode' => 'KGZ', 'number' => '417' , 'barcode' => '470'];
        $country['KI'] = ['name' => 'Кирибати', 'phoneCode' => '686', 'code' => 'KI', 'fullCode' => 'KIR', 'number' => '296' , 'barcode' => ''];
        $country['CN'] = ['name' => 'Китай', 'phoneCode' => '86', 'code' => 'CN', 'fullCode' => 'CHN', 'number' => '156' , 'barcode' => '690-695'];
        $country['KP'] = ['name' => 'КНДР', 'phoneCode' => '850', 'code' => 'KP', 'fullCode' => 'PRK', 'number' => '408' , 'barcode' => '867'];
        $country['CO'] = ['name' => 'Колумбия', 'phoneCode' => '57', 'code' => 'CO', 'fullCode' => 'COL', 'number' => '170' , 'barcode' => '770'];
        $country['KM'] = ['name' => 'Коморские Острова', 'phoneCode' => '269', 'code' => 'KM', 'fullCode' => 'COM', 'number' => '174' , 'barcode' => ''];
        $country['CR'] = ['name' => 'Коста-Рика', 'phoneCode' => '506', 'code' => 'CR', 'fullCode' => 'CRI', 'number' => '188' , 'barcode' => '744'];
        $country['CI'] = ['name' => 'Кот-д\'Ивуар', 'phoneCode' => '225', 'code' => 'CI', 'fullCode' => 'CIV', 'number' => '384' , 'barcode' => '618'];
        $country['CU'] = ['name' => 'Куба', 'phoneCode' => '53', 'code' => 'CU', 'fullCode' => 'CUB', 'number' => '192' , 'barcode' => '850'];
        $country['KW'] = ['name' => 'Кувейт', 'phoneCode' => '965', 'code' => 'KW', 'fullCode' => 'KWT', 'number' => '414' , 'barcode' => '627'];
        $country['LA'] = ['name' => 'Лаос', 'phoneCode' => '856', 'code' => 'LA', 'fullCode' => 'LAO', 'number' => '418' , 'barcode' => ''];
        $country['LV'] = ['name' => 'Латвия', 'phoneCode' => '371', 'code' => 'LV', 'fullCode' => 'LVA', 'number' => '428' , 'barcode' => '475'];
        $country['LS'] = ['name' => 'Лесото', 'phoneCode' => '266', 'code' => 'LS', 'fullCode' => 'LSO', 'number' => '426' , 'barcode' => ''];
        $country['LR'] = ['name' => 'Либерия', 'phoneCode' => '231', 'code' => 'LR', 'fullCode' => 'LBR', 'number' => '430' , 'barcode' => ''];
        $country['LB'] = ['name' => 'Ливан', 'phoneCode' => '961', 'code' => 'LB', 'fullCode' => 'LBN', 'number' => '422' , 'barcode' => '528'];
        $country['LY'] = ['name' => 'Ливия', 'phoneCode' => '218', 'code' => 'LY', 'fullCode' => 'LBY', 'number' => '434' , 'barcode' => '624'];
        $country['LT'] = ['name' => 'Литва', 'phoneCode' => '370', 'code' => 'LT', 'fullCode' => 'LTU', 'number' => '440' , 'barcode' => '477'];
        $country['LI'] = ['name' => 'Лихтенштейн', 'phoneCode' => '423', 'code' => 'LI', 'fullCode' => 'LIE', 'number' => '438' , 'barcode' => ''];
        $country['LU'] = ['name' => 'Люксембург', 'phoneCode' => '352', 'code' => 'LU', 'fullCode' => 'LUX', 'number' => '442' , 'barcode' => '540-549'];
        $country['MU'] = ['name' => 'Маврикий', 'phoneCode' => '230', 'code' => 'MU', 'fullCode' => 'MUS', 'number' => '480' , 'barcode' => '609'];
        $country['MR'] = ['name' => 'Мавритания', 'phoneCode' => '222', 'code' => 'MR', 'fullCode' => 'MRT', 'number' => '478' , 'barcode' => ''];
        $country['MG'] = ['name' => 'Мадагаскар', 'phoneCode' => '261', 'code' => 'MG', 'fullCode' => 'MDG', 'number' => '450' , 'barcode' => ''];
        $country['MK'] = ['name' => 'Македония', 'phoneCode' => '389', 'code' => 'MK', 'fullCode' => 'MKD', 'number' => '807' , 'barcode' => '531'];
        $country['MW'] = ['name' => 'Малави', 'phoneCode' => '265', 'code' => 'MW', 'fullCode' => 'MWI', 'number' => '454' , 'barcode' => ''];
        $country['MY'] = ['name' => 'Малайзия', 'phoneCode' => '60', 'code' => 'MY', 'fullCode' => 'MYS', 'number' => '458' , 'barcode' => '955'];
        $country['ML'] = ['name' => 'Мали', 'phoneCode' => '223', 'code' => 'ML', 'fullCode' => 'MLI', 'number' => '466' , 'barcode' => ''];
        $country['MV'] = ['name' => 'Мальдивские Острова', 'phoneCode' => '960', 'code' => 'MV', 'fullCode' => 'MDV', 'number' => '462' , 'barcode' => ''];
        $country['MT'] = ['name' => 'Мальта', 'phoneCode' => '356', 'code' => 'MT', 'fullCode' => 'MLT', 'number' => '470' , 'barcode' => '535'];
        $country['MA'] = ['name' => 'Марокко', 'phoneCode' => '212', 'code' => 'MA', 'fullCode' => 'MAR', 'number' => '504' , 'barcode' => '611'];
        $country['MH'] = ['name' => 'Маршалловы Острова', 'phoneCode' => '692', 'code' => 'MH', 'fullCode' => 'MHL', 'number' => '584' , 'barcode' => ''];
        $country['MX'] = ['name' => 'Мексика', 'phoneCode' => '52', 'code' => 'MX', 'fullCode' => 'MEX', 'number' => '484' , 'barcode' => '750'];
        $country['MZ'] = ['name' => 'Мозамбик', 'phoneCode' => '258', 'code' => 'MZ', 'fullCode' => 'MOZ', 'number' => '508' , 'barcode' => ''];
        $country['MD'] = ['name' => 'Молдавия', 'phoneCode' => '373', 'code' => 'MD', 'fullCode' => 'MDA', 'number' => '498' , 'barcode' => '484'];
        $country['MC'] = ['name' => 'Монако', 'phoneCode' => '377', 'code' => 'MC', 'fullCode' => 'MCO', 'number' => '492' , 'barcode' => ''];
        $country['MN'] = ['name' => 'Монголия', 'phoneCode' => '976', 'code' => 'MN', 'fullCode' => 'MNG', 'number' => '496' , 'barcode' => '865'];
        $country['MM'] = ['name' => 'Мьянма', 'phoneCode' => '95', 'code' => 'MM', 'fullCode' => 'MMR', 'number' => '104' , 'barcode' => ''];
        $country['NA'] = ['name' => 'Намибия', 'phoneCode' => '264', 'code' => 'NA', 'fullCode' => 'NAM', 'number' => '516' , 'barcode' => ''];
        $country['NR'] = ['name' => 'Науру', 'phoneCode' => '674', 'code' => 'NR', 'fullCode' => 'NRU', 'number' => '520' , 'barcode' => ''];
        $country['NP'] = ['name' => 'Непал', 'phoneCode' => '977', 'code' => 'NP', 'fullCode' => 'NPL', 'number' => '524' , 'barcode' => ''];
        $country['NE'] = ['name' => 'Нигер', 'phoneCode' => '227', 'code' => 'NE', 'fullCode' => 'NER', 'number' => '562' , 'barcode' => ''];
        $country['NG'] = ['name' => 'Нигерия', 'phoneCode' => '234', 'code' => 'NG', 'fullCode' => 'NGA', 'number' => '566' , 'barcode' => ''];
        $country['NL'] = ['name' => 'Нидерланды', 'phoneCode' => '31', 'code' => 'NL', 'fullCode' => 'NLD', 'number' => '528' , 'barcode' => '870-879'];
        $country['NI'] = ['name' => 'Никарагуа', 'phoneCode' => '505', 'code' => 'NI', 'fullCode' => 'NIC', 'number' => '558' , 'barcode' => '743'];
        $country['NZ'] = ['name' => 'Новая Зеландия', 'phoneCode' => '64', 'code' => 'NZ', 'fullCode' => 'NZL', 'number' => '554' , 'barcode' => '940-949'];
        $country['NO'] = ['name' => 'Норвегия', 'phoneCode' => '47', 'code' => 'NO', 'fullCode' => 'NOR', 'number' => '578' , 'barcode' => '700-709'];
        $country['AE'] = ['name' => 'ОАЭ', 'phoneCode' => '971', 'code' => 'AE', 'fullCode' => 'ARE', 'number' => '784' , 'barcode' => '629'];
        $country['OM'] = ['name' => 'Оман', 'phoneCode' => '968', 'code' => 'OM', 'fullCode' => 'OMN', 'number' => '512' , 'barcode' => ''];
        $country['PK'] = ['name' => 'Пакистан', 'phoneCode' => '92', 'code' => 'PK', 'fullCode' => 'PAK', 'number' => '586' , 'barcode' => ''];
        $country['PW'] = ['name' => 'Палау', 'phoneCode' => '680', 'code' => 'PW', 'fullCode' => 'PLW', 'number' => '585' , 'barcode' => ''];
        $country['PA'] = ['name' => 'Панама', 'phoneCode' => '507', 'code' => 'PA', 'fullCode' => 'PAN', 'number' => '591' , 'barcode' => '745'];
        $country['PG'] = ['name' => 'Папуа - Новая Гвинея', 'phoneCode' => '675', 'code' => 'PG', 'fullCode' => 'PNG', 'number' => '598' , 'barcode' => ''];
        $country['PY'] = ['name' => 'Парагвай', 'phoneCode' => '595', 'code' => 'PY', 'fullCode' => 'PRY', 'number' => '600' , 'barcode' => '784'];
        $country['PE'] = ['name' => 'Перу', 'phoneCode' => '51', 'code' => 'PE', 'fullCode' => 'PER', 'number' => '604' , 'barcode' => '775'];
        $country['PL'] = ['name' => 'Польша', 'phoneCode' => '48', 'code' => 'PL', 'fullCode' => 'POL', 'number' => '616' , 'barcode' => '590'];
        $country['PT'] = ['name' => 'Португалия', 'phoneCode' => '351', 'code' => 'PT', 'fullCode' => 'PRT', 'number' => '620' , 'barcode' => '560'];
        $country['CG'] = ['name' => 'Республика Конго', 'phoneCode' => '242', 'code' => 'CG', 'fullCode' => 'COG', 'number' => '178' , 'barcode' => ''];
        $country['KR'] = ['name' => 'Республика Корея', 'phoneCode' => '82', 'code' => 'KR', 'fullCode' => 'KOR', 'number' => '410' , 'barcode' => '880'];
        $country['RU'] = ['name' => 'Россия', 'phoneCode' => '+7 xxx', 'code' => 'RU', 'fullCode' => 'RUS', 'number' => '643' , 'barcode' => '460-469'];
        $country['RW'] = ['name' => 'Руанда', 'phoneCode' => '250', 'code' => 'RW', 'fullCode' => 'RWA', 'number' => '646' , 'barcode' => ''];
        $country['RO'] = ['name' => 'Румыния', 'phoneCode' => '40', 'code' => 'RO', 'fullCode' => 'ROU', 'number' => '642' , 'barcode' => '594'];
        $country['SV'] = ['name' => 'Сальвадор', 'phoneCode' => '503', 'code' => 'SV', 'fullCode' => 'SLV', 'number' => '222' , 'barcode' => '741'];
        $country['WS'] = ['name' => 'Самоа', 'phoneCode' => '685', 'code' => 'WS', 'fullCode' => 'WSM', 'number' => '882' , 'barcode' => ''];
        $country['SM'] = ['name' => 'Сан-Марино', 'phoneCode' => '378', 'code' => 'SM', 'fullCode' => 'SMR', 'number' => '674' , 'barcode' => ''];
        $country['ST'] = ['name' => 'Сан-Томе и Принсипи', 'phoneCode' => '239', 'code' => 'ST', 'fullCode' => 'STP', 'number' => '678' , 'barcode' => ''];
        $country['SA'] = ['name' => 'Саудовская Аравия', 'phoneCode' => '966', 'code' => 'SA', 'fullCode' => 'SAU', 'number' => '682' , 'barcode' => '628'];
        $country['SC'] = ['name' => 'Сейшельские Острова', 'phoneCode' => '248', 'code' => 'SC', 'fullCode' => 'SYC', 'number' => '690' , 'barcode' => ''];
        $country['SN'] = ['name' => 'Сенегал', 'phoneCode' => '221', 'code' => 'SN', 'fullCode' => 'SEN', 'number' => '686' , 'barcode' => ''];
        $country['VC'] = ['name' => 'Сент-Винсент и Гренадины', 'phoneCode' => '1 784', 'code' => 'VC', 'fullCode' => 'VCT', 'number' => '670' , 'barcode' => ''];
        $country['KN'] = ['name' => 'Сент-Китс и Невис', 'phoneCode' => '1 869', 'code' => 'KN', 'fullCode' => 'KNA', 'number' => '659' , 'barcode' => ''];
        $country['LC'] = ['name' => 'Сент-Люсия', 'phoneCode' => '1 758', 'code' => 'LC', 'fullCode' => 'LCA', 'number' => '662' , 'barcode' => ''];
        $country['RS'] = ['name' => 'Сербия', 'phoneCode' => '381', 'code' => 'RS', 'fullCode' => 'SRB', 'number' => '688' , 'barcode' => '860'];
        $country['SG'] = ['name' => 'Сингапур', 'phoneCode' => '65', 'code' => 'SG', 'fullCode' => 'SGP', 'number' => '702' , 'barcode' => '888'];
        $country['SY'] = ['name' => 'Сирия', 'phoneCode' => '963', 'code' => 'SY', 'fullCode' => 'SYR', 'number' => '760' , 'barcode' => '621'];
        $country['SK'] = ['name' => 'Словакия', 'phoneCode' => '421', 'code' => 'SK', 'fullCode' => 'SVK', 'number' => '703' , 'barcode' => '858'];
        $country['SI'] = ['name' => 'Словения', 'phoneCode' => '386', 'code' => 'SI', 'fullCode' => 'SVN', 'number' => '705' , 'barcode' => '383'];
        $country['SB'] = ['name' => 'Соломоновы Острова', 'phoneCode' => '677', 'code' => 'SB', 'fullCode' => 'SLB', 'number' => '90' , 'barcode' => ''];
        $country['SO'] = ['name' => 'Сомали', 'phoneCode' => '252', 'code' => 'SO', 'fullCode' => 'SOM', 'number' => '706' , 'barcode' => ''];
        $country['SD'] = ['name' => 'Судан', 'phoneCode' => '249', 'code' => 'SD', 'fullCode' => 'SDN', 'number' => '729' , 'barcode' => ''];
        $country['SR'] = ['name' => 'Суринам', 'phoneCode' => '597', 'code' => 'SR', 'fullCode' => 'SUR', 'number' => '740' , 'barcode' => ''];
        $country['US'] = ['name' => 'США', 'phoneCode' => '+1 xxx', 'code' => 'US', 'fullCode' => 'USA', 'number' => '840' , 'barcode' => '000-139'];
        $country['SL'] = ['name' => 'Сьерра-Леоне', 'phoneCode' => '232', 'code' => 'SL', 'fullCode' => 'SLE', 'number' => '694' , 'barcode' => ''];
        $country['TJ'] = ['name' => 'Таджикистан', 'phoneCode' => '992', 'code' => 'TJ', 'fullCode' => 'TJK', 'number' => '762' , 'barcode' => '488'];
        $country['TH'] = ['name' => 'Таиланд', 'phoneCode' => '66', 'code' => 'TH', 'fullCode' => 'THA', 'number' => '764' , 'barcode' => '885'];
        $country['TZ'] = ['name' => 'Танзания', 'phoneCode' => '255', 'code' => 'TZ', 'fullCode' => 'TZA', 'number' => '834' , 'barcode' => ''];
        $country['TG'] = ['name' => 'Того', 'phoneCode' => '228', 'code' => 'TG', 'fullCode' => 'TGO', 'number' => '768' , 'barcode' => ''];
        $country['TO'] = ['name' => 'Тонга', 'phoneCode' => '676', 'code' => 'TO', 'fullCode' => 'TON', 'number' => '776' , 'barcode' => ''];
        $country['TT'] = ['name' => 'Тринидад и Тобаго', 'phoneCode' => '1 868', 'code' => 'TT', 'fullCode' => 'TTO', 'number' => '780' , 'barcode' => ''];
        $country['TV'] = ['name' => 'Тувалу', 'phoneCode' => '688', 'code' => 'TV', 'fullCode' => 'TUV', 'number' => '798' , 'barcode' => ''];
        $country['TN'] = ['name' => 'Тунис', 'phoneCode' => '216', 'code' => 'TN', 'fullCode' => 'TUN', 'number' => '788' , 'barcode' => '619'];
        $country['TM'] = ['name' => 'Туркмения', 'phoneCode' => '993', 'code' => 'TM', 'fullCode' => 'TKM', 'number' => '795' , 'barcode' => ''];
        $country['TR'] = ['name' => 'Турция', 'phoneCode' => '90', 'code' => 'TR', 'fullCode' => 'TUR', 'number' => '792' , 'barcode' => '869'];
        $country['UG'] = ['name' => 'Уганда', 'phoneCode' => '256', 'code' => 'UG', 'fullCode' => 'UGA', 'number' => '800' , 'barcode' => ''];
        $country['UZ'] = ['name' => 'Узбекистан', 'phoneCode' => '998', 'code' => 'UZ', 'fullCode' => 'UZB', 'number' => '860' , 'barcode' => '478'];
        $country['UA'] = ['name' => 'Украина', 'phoneCode' => '380', 'code' => 'UA', 'fullCode' => 'UKR', 'number' => '804' , 'barcode' => '482'];
        $country['UY'] = ['name' => 'Уругвай', 'phoneCode' => '598', 'code' => 'UY', 'fullCode' => 'URY', 'number' => '858' , 'barcode' => '773'];
        $country['FM'] = ['name' => 'Федеративные Штаты Микронезии', 'phoneCode' => '691', 'code' => 'FM', 'fullCode' => 'FSM', 'number' => '583' , 'barcode' => ''];
        $country['FJ'] = ['name' => 'Фиджи', 'phoneCode' => '679', 'code' => 'FJ', 'fullCode' => 'FJI', 'number' => '242' , 'barcode' => ''];
        $country['PH'] = ['name' => 'Филиппины', 'phoneCode' => '63', 'code' => 'PH', 'fullCode' => 'PHL', 'number' => '608' , 'barcode' => '480'];
        $country['FI'] = ['name' => 'Финляндия', 'phoneCode' => '358', 'code' => 'FI', 'fullCode' => 'FIN', 'number' => '246' , 'barcode' => '640-649'];
        $country['FR'] = ['name' => 'Франция', 'phoneCode' => '33', 'code' => 'FR', 'fullCode' => 'FRA', 'number' => '250' , 'barcode' => '300-379'];
        $country['HR'] = ['name' => 'Хорватия', 'phoneCode' => '385', 'code' => 'HR', 'fullCode' => 'HRV', 'number' => '191' , 'barcode' => '385'];
        $country['CF'] = ['name' => 'ЦАР', 'phoneCode' => '236', 'code' => 'CF', 'fullCode' => 'CAF', 'number' => '140' , 'barcode' => ''];
        $country['TD'] = ['name' => 'Чад', 'phoneCode' => '235', 'code' => 'TD', 'fullCode' => 'TCD', 'number' => '148' , 'barcode' => ''];
        $country['ME'] = ['name' => 'Черногория', 'phoneCode' => '382', 'code' => 'ME', 'fullCode' => 'MNE', 'number' => '499' , 'barcode' => '860'];
        $country['CZ'] = ['name' => 'Чехия', 'phoneCode' => '420', 'code' => 'CZ', 'fullCode' => 'CZE', 'number' => '203' , 'barcode' => '859'];
        $country['CL'] = ['name' => 'Чили', 'phoneCode' => '56', 'code' => 'CL', 'fullCode' => 'CHL', 'number' => '152' , 'barcode' => '780'];
        $country['CH'] = ['name' => 'Швейцария', 'phoneCode' => '41', 'code' => 'CH', 'fullCode' => 'CHE', 'number' => '756' , 'barcode' => '760-769'];
        $country['SE'] = ['name' => 'Швеция', 'phoneCode' => '46', 'code' => 'SE', 'fullCode' => 'SWE', 'number' => '752' , 'barcode' => '730-739'];
        $country['LK'] = ['name' => 'Шри-Ланка', 'phoneCode' => '94', 'code' => 'LK', 'fullCode' => 'LKA', 'number' => '144' , 'barcode' => '479'];
        $country['EC'] = ['name' => 'Эквадор', 'phoneCode' => '593', 'code' => 'EC', 'fullCode' => 'ECU', 'number' => '218' , 'barcode' => '786'];
        $country['GQ'] = ['name' => 'Экваториальная Гвинея', 'phoneCode' => '240', 'code' => 'GQ', 'fullCode' => 'GNQ', 'number' => '226' , 'barcode' => ''];
        $country['ER'] = ['name' => 'Эритрея', 'phoneCode' => '291', 'code' => 'ER', 'fullCode' => 'ERI', 'number' => '232' , 'barcode' => ''];
        $country['SZ'] = ['name' => 'Эсватини', 'phoneCode' => '268', 'code' => 'SZ', 'fullCode' => 'SWZ', 'number' => '748' , 'barcode' => ''];
        $country['EE'] = ['name' => 'Эстония', 'phoneCode' => '372', 'code' => 'EE', 'fullCode' => 'EST', 'number' => '233' , 'barcode' => '474'];
        $country['ET'] = ['name' => 'Эфиопия', 'phoneCode' => '251', 'code' => 'ET', 'fullCode' => 'ETH', 'number' => '231' , 'barcode' => ''];
        $country['ZA'] = ['name' => 'ЮАР', 'phoneCode' => '27', 'code' => 'ZA', 'fullCode' => 'ZAF', 'number' => '710' , 'barcode' => '600-601'];
        $country['OS'] = ['name' => 'Южная Осетия', 'phoneCode' => '+7 xxx', 'code' => 'OS', 'fullCode' => 'OST', 'number' => '896' , 'barcode' => ''];
        $country['SS'] = ['name' => 'Южный Судан', 'phoneCode' => '211', 'code' => 'SS', 'fullCode' => 'SSD', 'number' => '728' , 'barcode' => ''];
        $country['JM'] = ['name' => 'Ямайка', 'phoneCode' => '1 876', 'code' => 'JM', 'fullCode' => 'JAM', 'number' => '388' , 'barcode' => ''];
        $country['JP'] = ['name' => 'Япония', 'phoneCode' => '81', 'code' => 'JP', 'fullCode' => 'JPN', 'number' => '392' , 'barcode' => '490-499'];

        if($code) return $country[$code];

        if($name) {

            switch ($name) {

                case 'Абхазия': $countryId = 'AB'; break;
                case 'Австралия': $countryId = 'AU'; break;
                case 'Австрия': $countryId = 'AT'; break;
                case 'Азербайджан': $countryId = 'AZ'; break;
                case 'Албания': $countryId = 'AL'; break;
                case 'Алжир': $countryId = 'DZ'; break;
                case 'Ангола': $countryId = 'AO'; break;
                case 'Андорра': $countryId = 'AD'; break;
                case 'Антигуа и Барбуда': $countryId = 'AG'; break;
                case 'Аргентина': $countryId = 'AR'; break;
                case 'Армения': $countryId = 'AM'; break;
                case 'Афганистан': $countryId = 'AF'; break;
                case 'Багамские Острова': $countryId = 'BS'; break;
                case 'Бангладеш': $countryId = 'BD'; break;
                case 'Барбадос': $countryId = 'BB'; break;
                case 'Бахрейн': $countryId = 'BH'; break;
                case 'Белиз': $countryId = 'BZ'; break;
                case 'Белоруссия': $countryId = 'BY'; break;
                case 'Бельгия': $countryId = 'BE'; break;
                case 'Бенин': $countryId = 'BJ'; break;
                case 'Болгария': $countryId = 'BG'; break;
                case 'Боливия': $countryId = 'BO'; break;
                case 'Босния и Герцеговина': $countryId = 'BA'; break;
                case 'Ботсвана': $countryId = 'BW'; break;
                case 'Бразилия': $countryId = 'BR'; break;
                case 'Бруней': $countryId = 'BN'; break;
                case 'Буркина-Фасо': $countryId = 'BF'; break;
                case 'Бурунди': $countryId = 'BI'; break;
                case 'Бутан': $countryId = 'BT'; break;
                case 'Вануату': $countryId = 'VU'; break;
                case 'Ватикан': $countryId = 'VA'; break;
                case 'Великобритания': $countryId = 'GB'; break;
                case 'Венгрия': $countryId = 'HU'; break;
                case 'Венесуэла': $countryId = 'VE'; break;
                case 'Восточный Тимор': $countryId = 'TL'; break;
                case 'Вьетнам': $countryId = 'VN'; break;
                case 'Габон': $countryId = 'GA'; break;
                case 'Гаити': $countryId = 'HT'; break;
                case 'Гайана': $countryId = 'GY'; break;
                case 'Гамбия': $countryId = 'GM'; break;
                case 'Гана': $countryId = 'GH'; break;
                case 'Гватемала': $countryId = 'GT'; break;
                case 'Гвинея': $countryId = 'GN'; break;
                case 'Гвинея-Бисау': $countryId = 'GW'; break;
                case 'Германия': $countryId = 'DE'; break;
                case 'Гондурас': $countryId = 'HN'; break;
                case 'Государство Палестина': $countryId = 'PS'; break;
                case 'Гренада': $countryId = 'GD'; break;
                case 'Греция': $countryId = 'GR'; break;
                case 'Грузия': $countryId = 'GE'; break;
                case 'Дания': $countryId = 'DK'; break;
                case 'Джибути': $countryId = 'DJ'; break;
                case 'Доминика': $countryId = 'DM'; break;
                case 'Доминиканская Республика': $countryId = 'DO'; break;
                case 'ДР Конго': $countryId = 'CD'; break;
                case 'Египет': $countryId = 'EG'; break;
                case 'Замбия': $countryId = 'ZM'; break;
                case 'Зимбабве': $countryId = 'ZW'; break;
                case 'Израиль': $countryId = 'IL'; break;
                case 'Индия': $countryId = 'IN'; break;
                case 'Индонезия': $countryId = 'ID'; break;
                case 'Иордания': $countryId = 'JO'; break;
                case 'Ирак': $countryId = 'IQ'; break;
                case 'Иран': $countryId = 'IR'; break;
                case 'Ирландия': $countryId = 'IE'; break;
                case 'Исландия': $countryId = 'IS'; break;
                case 'Испания': $countryId = 'ES'; break;
                case 'Италия': $countryId = 'IT'; break;
                case 'Йемен': $countryId = 'YE'; break;
                case 'Кабо-Верде': $countryId = 'CV'; break;
                case 'Казахстан': $countryId = 'KZ'; break;
                case 'Камбоджа': $countryId = 'KH'; break;
                case 'Камерун': $countryId = 'CM'; break;
                case 'Канада': $countryId = 'CA'; break;
                case 'Катар': $countryId = 'QA'; break;
                case 'Кения': $countryId = 'KE'; break;
                case 'Кипр': $countryId = 'CY'; break;
                case 'Киргизия': $countryId = 'KG'; break;
                case 'Кирибати': $countryId = 'KI'; break;
                case 'Китай': $countryId = 'CN'; break;
                case 'КНДР': $countryId = 'KP'; break;
                case 'Колумбия': $countryId = 'CO'; break;
                case 'Коморские Острова': $countryId = 'KM'; break;
                case 'Коста-Рика': $countryId = 'CR'; break;
                case 'Кот-д\'Ивуар': $countryId = 'CI'; break;
                case 'Куба': $countryId = 'CU'; break;
                case 'Кувейт': $countryId = 'KW'; break;
                case 'Лаос': $countryId = 'LA'; break;
                case 'Латвия': $countryId = 'LV'; break;
                case 'Лесото': $countryId = 'LS'; break;
                case 'Либерия': $countryId = 'LR'; break;
                case 'Ливан': $countryId = 'LB'; break;
                case 'Ливия': $countryId = 'LY'; break;
                case 'Литва': $countryId = 'LT'; break;
                case 'Лихтенштейн': $countryId = 'LI'; break;
                case 'Люксембург': $countryId = 'LU'; break;
                case 'Маврикий': $countryId = 'MU'; break;
                case 'Мавритания': $countryId = 'MR'; break;
                case 'Мадагаскар': $countryId = 'MG'; break;
                case 'Македония': $countryId = 'MK'; break;
                case 'Малави': $countryId = 'MW'; break;
                case 'Малайзия': $countryId = 'MY'; break;
                case 'Мали': $countryId = 'ML'; break;
                case 'Мальдивские Острова': $countryId = 'MV'; break;
                case 'Мальта': $countryId = 'MT'; break;
                case 'Марокко': $countryId = 'MA'; break;
                case 'Маршалловы Острова': $countryId = 'MH'; break;
                case 'Мексика': $countryId = 'MX'; break;
                case 'Мозамбик': $countryId = 'MZ'; break;
                case 'Молдавия': $countryId = 'MD'; break;
                case 'Монако': $countryId = 'MC'; break;
                case 'Монголия': $countryId = 'MN'; break;
                case 'Мьянма': $countryId = 'MM'; break;
                case 'Намибия': $countryId = 'NA'; break;
                case 'Науру': $countryId = 'NR'; break;
                case 'Непал': $countryId = 'NP'; break;
                case 'Нигер': $countryId = 'NE'; break;
                case 'Нигерия': $countryId = 'NG'; break;
                case 'Нидерланды': $countryId = 'NL'; break;
                case 'Никарагуа': $countryId = 'NI'; break;
                case 'Новая Зеландия': $countryId = 'NZ'; break;
                case 'Норвегия': $countryId = 'NO'; break;
                case 'ОАЭ': $countryId = 'AE'; break;
                case 'Оман': $countryId = 'OM'; break;
                case 'Пакистан': $countryId = 'PK'; break;
                case 'Палау': $countryId = 'PW'; break;
                case 'Панама': $countryId = 'PA'; break;
                case 'Папуа - Новая Гвинея': $countryId = 'PG'; break;
                case 'Парагвай': $countryId = 'PY'; break;
                case 'Перу': $countryId = 'PE'; break;
                case 'Польша': $countryId = 'PL'; break;
                case 'Португалия': $countryId = 'PT'; break;
                case 'Республика Конго': $countryId = 'CG'; break;
                case 'Республика Корея': $countryId = 'KR'; break;
                case 'Россия': $countryId = 'RU'; break;
                case 'Руанда': $countryId = 'RW'; break;
                case 'Румыния': $countryId = 'RO'; break;
                case 'Сальвадор': $countryId = 'SV'; break;
                case 'Самоа': $countryId = 'WS'; break;
                case 'Сан-Марино': $countryId = 'SM'; break;
                case 'Сан-Томе и Принсипи': $countryId = 'ST'; break;
                case 'Саудовская Аравия': $countryId = 'SA'; break;
                case 'Сейшельские Острова': $countryId = 'SC'; break;
                case 'Сенегал': $countryId = 'SN'; break;
                case 'Сент-Винсент и Гренадины': $countryId = 'VC'; break;
                case 'Сент-Китс и Невис': $countryId = 'KN'; break;
                case 'Сент-Люсия': $countryId = 'LC'; break;
                case 'Сербия': $countryId = 'RS'; break;
                case 'Сингапур': $countryId = 'SG'; break;
                case 'Сирия': $countryId = 'SY'; break;
                case 'Словакия': $countryId = 'SK'; break;
                case 'Словения': $countryId = 'SI'; break;
                case 'Соломоновы Острова': $countryId = 'SB'; break;
                case 'Сомали': $countryId = 'SO'; break;
                case 'Судан': $countryId = 'SD'; break;
                case 'Суринам': $countryId = 'SR'; break;
                case 'США': $countryId = 'US'; break;
                case 'Сьерра-Леоне': $countryId = 'SL'; break;
                case 'Таджикистан': $countryId = 'TJ'; break;
                case 'Таиланд': $countryId = 'TH'; break;
                case 'Танзания': $countryId = 'TZ'; break;
                case 'Того': $countryId = 'TG'; break;
                case 'Тонга': $countryId = 'TO'; break;
                case 'Тринидад и Тобаго': $countryId = 'TT'; break;
                case 'Тувалу': $countryId = 'TV'; break;
                case 'Тунис': $countryId = 'TN'; break;
                case 'Туркмения': $countryId = 'TM'; break;
                case 'Турция': $countryId = 'TR'; break;
                case 'Уганда': $countryId = 'UG'; break;
                case 'Узбекистан': $countryId = 'UZ'; break;
                case 'Украина': $countryId = 'UA'; break;
                case 'Уругвай': $countryId = 'UY'; break;
                case 'Федеративные Штаты Микронезии': $countryId = 'FM'; break;
                case 'Фиджи': $countryId = 'FJ'; break;
                case 'Филиппины': $countryId = 'PH'; break;
                case 'Финляндия': $countryId = 'FI'; break;
                case 'Франция': $countryId = 'FR'; break;
                case 'Хорватия': $countryId = 'HR'; break;
                case 'ЦАР': $countryId = 'CF'; break;
                case 'Чад': $countryId = 'TD'; break;
                case 'Черногория': $countryId = 'ME'; break;
                case 'Чехия': $countryId = 'CZ'; break;
                case 'Чили': $countryId = 'CL'; break;
                case 'Швейцария': $countryId = 'CH'; break;
                case 'Швеция': $countryId = 'SE'; break;
                case 'Шри-Ланка': $countryId = 'LK'; break;
                case 'Эквадор': $countryId = 'EC'; break;
                case 'Экваториальная Гвинея': $countryId = 'GQ'; break;
                case 'Эритрея': $countryId = 'ER'; break;
                case 'Эсватини': $countryId = 'SZ'; break;
                case 'Эстония': $countryId = 'EE'; break;
                case 'Эфиопия': $countryId = 'ET'; break;
                case 'ЮАР': $countryId = 'ZA'; break;
                case 'Южная Осетия': $countryId = 'OS'; break;
                case 'Южный Судан': $countryId = 'SS'; break;
                case 'Ямайка': $countryId = 'JM'; break;
                case 'Япония': $countryId = 'JP'; break;

            }

            return $country[$countryId];
        }

    }
    public function getRegionInfo($name='', $code='', $fias='', $kladr='', $index='') {

        global $regions;

        if(!$regions) {

            $regions['01']['name'] = 'Респ. Адыгея';
            $regions['04']['name'] = 'Респ. Алтай';
            $regions['22']['name'] = 'Алтайский край';
            $regions['28']['name'] = 'Амурская обл.';
            $regions['29']['name'] = 'Архангельская обл.';
            $regions['30']['name'] = 'Астраханская обл.';
            $regions['99']['name'] = 'Байконур';
            $regions['02']['name'] = 'Респ. Башкортостан';
            $regions['31']['name'] = 'Белгородская обл.';
            $regions['32']['name'] = 'Брянская обл.';
            $regions['03']['name'] = 'Респ. Бурятия';
            $regions['33']['name'] = 'Владимирская обл.';
            $regions['34']['name'] = 'Волгоградская обл.';
            $regions['35']['name'] = 'Вологодская обл.';
            $regions['36']['name'] = 'Воронежская обл.';
            $regions['05']['name'] = 'Респ. Дагестан';
            $regions['79']['name'] = 'Аобл. Еврейская';
            $regions['75']['name'] = 'Забайкальский край';
            $regions['37']['name'] = 'Ивановская обл.';
            $regions['06']['name'] = 'Респ. Ингушетия';
            $regions['38']['name'] = 'Иркутская обл.';
            $regions['07']['name'] = 'Респ. Кабардино-Балкарская';
            $regions['39']['name'] = 'Калининградская обл.';
            $regions['08']['name'] = 'Респ. Калмыкия';
            $regions['40']['name'] = 'Калужская обл.';
            $regions['41']['name'] = 'Камчатский край';
            $regions['09']['name'] = 'Респ. Карачаево-Черкесская';
            $regions['10']['name'] = 'Респ. Карелия';
            $regions['42']['name'] = 'Кемеровская обл.';
            $regions['43']['name'] = 'Кировская обл.';
            $regions['11']['name'] = 'Респ. Коми';
            $regions['81']['name'] = 'АО Коми-Пермяцкий';
            $regions['82']['name'] = 'АО Корякский';
            $regions['44']['name'] = 'Костромская обл.';
            $regions['23']['name'] = 'Краснодарский край';
            $regions['24']['name'] = 'Красноярский край';
            $regions['91']['name'] = 'Респ. Крым';
            $regions['45']['name'] = 'Курганская обл.';
            $regions['46']['name'] = 'Курская обл.';
            $regions['47']['name'] = 'Ленинградская обл.';
            $regions['48']['name'] = 'Липецкая обл.';
            $regions['49']['name'] = 'Магаданская обл.';
            $regions['12']['name'] = 'Респ. Марий Эл';
            $regions['13']['name'] = 'Респ. Мордовия';
            $regions['77']['name'] = 'Москва';
            $regions['50']['name'] = 'Московская обл.';
            $regions['51']['name'] = 'Мурманская обл.';
            $regions['83']['name'] = 'АО Ненецкий';
            $regions['52']['name'] = 'Нижегородская обл.';
            $regions['53']['name'] = 'Новгородская обл.';
            $regions['54']['name'] = 'Новосибирская обл.';
            $regions['55']['name'] = 'Омская обл.';
            $regions['56']['name'] = 'Оренбургская обл.';
            $regions['57']['name'] = 'Орловская обл.';
            $regions['58']['name'] = 'Пензенская обл.';
            $regions['59']['name'] = 'Пермский край';
            $regions['25']['name'] = 'Приморский край';
            $regions['60']['name'] = 'Псковская обл.';
            $regions['61']['name'] = 'Ростовская обл.';
            $regions['62']['name'] = 'Рязанская обл.';
            $regions['63']['name'] = 'Самарская обл.';
            $regions['78']['name'] = 'Санкт-Петербург';
            $regions['64']['name'] = 'Саратовская обл.';
            $regions['14']['name'] = 'Респ. Саха (Якутия)';
            $regions['65']['name'] = 'Сахалинская обл.';
            $regions['66']['name'] = 'Свердловская обл.';
            $regions['92']['name'] = 'Севастополь';
            $regions['15']['name'] = 'Респ. Северная Осетия - Алания';
            $regions['67']['name'] = 'Смоленская обл.';
            $regions['26']['name'] = 'Ставропольский край';
            $regions['68']['name'] = 'Тамбовская обл.';
            $regions['16']['name'] = 'Респ. Татарстан';
            $regions['69']['name'] = 'Тверская обл.';
            $regions['70']['name'] = 'Томская обл.';
            $regions['71']['name'] = 'Тульская обл.';
            $regions['17']['name'] = 'Респ. Тыва';
            $regions['72']['name'] = 'Тюменская обл.';
            $regions['18']['name'] = 'Удмуртская Респ.';
            $regions['73']['name'] = 'Ульяновская обл.';
            $regions['27']['name'] = 'Хабаровский край';
            $regions['19']['name'] = 'Респ. Хакасия';
            $regions['86']['name'] = 'Ханты-Мансийский автономный округ - Югра';
            $regions['74']['name'] = 'Челябинская обл.';
            $regions['20']['name'] = 'Чеченская Респ.';
            $regions['21']['name'] = 'Чувашская Респ.';
            $regions['87']['name'] = 'АО Чукотский';
            $regions['88']['name'] = 'АО Эвенкийский';
            $regions['89']['name'] = 'АО Ямало-Ненецкий';
            $regions['76']['name'] = 'Ярославская обл.';

            $regions['01']['code'] = '01';
            $regions['04']['code'] = '04';
            $regions['22']['code'] = '22';
            $regions['28']['code'] = '28';
            $regions['29']['code'] = '29';
            $regions['30']['code'] = '30';
            $regions['99']['code'] = '99';
            $regions['02']['code'] = '02';
            $regions['31']['code'] = '31';
            $regions['32']['code'] = '32';
            $regions['03']['code'] = '03';
            $regions['33']['code'] = '33';
            $regions['34']['code'] = '34';
            $regions['35']['code'] = '35';
            $regions['36']['code'] = '36';
            $regions['05']['code'] = '05';
            $regions['79']['code'] = '79';
            $regions['75']['code'] = '75';
            $regions['80']['code'] = '80';
            $regions['37']['code'] = '37';
            $regions['06']['code'] = '06';
            $regions['38']['code'] = '38';
            $regions['85']['code'] = '85';
            $regions['07']['code'] = '07';
            $regions['39']['code'] = '39';
            $regions['08']['code'] = '08';
            $regions['40']['code'] = '40';
            $regions['41']['code'] = '41';
            $regions['09']['code'] = '09';
            $regions['10']['code'] = '10';
            $regions['42']['code'] = '42';
            $regions['43']['code'] = '43';
            $regions['11']['code'] = '11';
            $regions['81']['code'] = '81';
            $regions['82']['code'] = '82';
            $regions['44']['code'] = '44';
            $regions['23']['code'] = '23';
            $regions['24']['code'] = '24';
            $regions['91']['code'] = '91';
            $regions['45']['code'] = '45';
            $regions['46']['code'] = '46';
            $regions['47']['code'] = '47';
            $regions['48']['code'] = '48';
            $regions['49']['code'] = '49';
            $regions['12']['code'] = '12';
            $regions['13']['code'] = '13';
            $regions['77']['code'] = '77';
            $regions['50']['code'] = '50';
            $regions['51']['code'] = '51';
            $regions['83']['code'] = '83';
            $regions['52']['code'] = '52';
            $regions['53']['code'] = '53';
            $regions['54']['code'] = '54';
            $regions['55']['code'] = '55';
            $regions['56']['code'] = '56';
            $regions['57']['code'] = '57';
            $regions['58']['code'] = '58';
            $regions['59']['code'] = '59';
            $regions['25']['code'] = '25';
            $regions['60']['code'] = '60';
            $regions['61']['code'] = '61';
            $regions['62']['code'] = '62';
            $regions['63']['code'] = '63';
            $regions['78']['code'] = '78';
            $regions['64']['code'] = '64';
            $regions['14']['code'] = '14';
            $regions['65']['code'] = '65';
            $regions['66']['code'] = '66';
            $regions['92']['code'] = '92';
            $regions['15']['code'] = '15';
            $regions['67']['code'] = '67';
            $regions['26']['code'] = '26';
            $regions['84']['code'] = '84';
            $regions['68']['code'] = '68';
            $regions['16']['code'] = '16';
            $regions['69']['code'] = '69';
            $regions['70']['code'] = '70';
            $regions['71']['code'] = '71';
            $regions['17']['code'] = '17';
            $regions['72']['code'] = '72';
            $regions['18']['code'] = '18';
            $regions['73']['code'] = '73';
            $regions['27']['code'] = '27';
            $regions['19']['code'] = '19';
            $regions['86']['code'] = '86';
            $regions['74']['code'] = '74';
            $regions['20']['code'] = '20';
            $regions['21']['code'] = '21';
            $regions['87']['code'] = '87';
            $regions['88']['code'] = '88';
            $regions['89']['code'] = '89';
            $regions['76']['code'] = '76';

            $regions['01']['fias'] = 'd8327a56-80de-4df2-815c-4f6ab1224c50';
            $regions['04']['fias'] = '5c48611f-5de6-4771-9695-7e36a4e7529d';
            $regions['22']['fias'] = '8276c6a1-1a86-4f0d-8920-aba34d4cc34a';
            $regions['28']['fias'] = '844a80d6-5e31-4017-b422-4d9c01e9942c';
            $regions['29']['fias'] = '294277aa-e25d-428c-95ad-46719c4ddb44';
            $regions['30']['fias'] = '83009239-25cb-4561-af8e-7ee111b1cb73';
            $regions['99']['fias'] = '63ed1a35-4be6-4564-a1ec-0c51f7383314';
            $regions['02']['fias'] = '6f2cbfd8-692a-4ee4-9b16-067210bde3fc';
            $regions['31']['fias'] = '639efe9d-3fc8-4438-8e70-ec4f2321f2a7';
            $regions['32']['fias'] = 'f5807226-8be0-4ea8-91fc-39d053aec1e2';
            $regions['03']['fias'] = 'a84ebed3-153d-4ba9-8532-8bdf879e1f5a';
            $regions['33']['fias'] = 'b8837188-39ee-4ff9-bc91-fcc9ed451bb3';
            $regions['34']['fias'] = 'da051ec8-da2e-4a66-b542-473b8d221ab4';
            $regions['35']['fias'] = 'ed36085a-b2f5-454f-b9a9-1c9a678ee618';
            $regions['36']['fias'] = 'b756fe6b-bbd3-44d5-9302-5bfcc740f46e';
            $regions['05']['fias'] = '0bb7fa19-736d-49cf-ad0e-9774c4dae09b';
            $regions['79']['fias'] = '1b507b09-48c9-434f-bf6f-65066211c73e';
            $regions['75']['fias'] = 'b6ba5716-eb48-401b-8443-b197c9578734';
            $regions['80']['fias'] = '53ec9705-ec3e-4cbf-921f-229968e10aeb';
            $regions['37']['fias'] = '0824434f-4098-4467-af72-d4f702fed335';
            $regions['06']['fias'] = 'b2d8cd20-cabc-4deb-afad-f3c4b4d55821';
            $regions['38']['fias'] = '6466c988-7ce3-45e5-8b97-90ae16cb1249';
            $regions['85']['fias'] = 'c2f0810a-d71e-4af6-85f1-e0bbd81b3e4d';
            $regions['07']['fias'] = '1781f74e-be4a-4697-9c6b-493057c94818';
            $regions['39']['fias'] = '90c7181e-724f-41b3-b6c6-bd3ec7ae3f30';
            $regions['08']['fias'] = '491cde9d-9d76-4591-ab46-ea93c079e686';
            $regions['40']['fias'] = '18133adf-90c2-438e-88c4-62c41656de70';
            $regions['41']['fias'] = 'd02f30fc-83bf-4c0f-ac2b-5729a866a207';
            $regions['09']['fias'] = '61b95807-388a-4cb1-9bee-889f7cf811c8';
            $regions['10']['fias'] = '248d8071-06e1-425e-a1cf-d1ff4c4a14a8';
            $regions['42']['fias'] = '393aeccb-89ef-4a7e-ae42-08d5cebc2e30';
            $regions['43']['fias'] = '0b940b96-103f-4248-850c-26b6c7296728';
            $regions['11']['fias'] = 'c20180d9-ad9c-46d1-9eff-d60bc424592a';
            $regions['81']['fias'] = 'e3d95b95-cc2d-440d-95c6-65577fae076e';
            $regions['82']['fias'] = '1e97910c-34fb-428a-a113-13a0024684ae';
            $regions['44']['fias'] = '15784a67-8cea-425b-834a-6afe0e3ed61c';
            $regions['23']['fias'] = 'd00e1013-16bd-4c09-b3d5-3cb09fc54bd8';
            $regions['24']['fias'] = 'db9c4f8b-b706-40e2-b2b4-d31b98dcd3d1';
            $regions['91']['fias'] = 'bd8e6511-e4b9-4841-90de-6bbc231a789e';
            $regions['45']['fias'] = '4a3d970f-520e-46b9-b16c-50d4ca7535a8';
            $regions['46']['fias'] = 'ee594d5e-30a9-40dc-b9f2-0add1be44ba1';
            $regions['47']['fias'] = '6d1ebb35-70c6-4129-bd55-da3969658f5d';
            $regions['48']['fias'] = '1490490e-49c5-421c-9572-5673ba5d80c8';
            $regions['49']['fias'] = '9c05e812-8679-4710-b8cb-5e8bd43cdf48';
            $regions['12']['fias'] = 'de2cbfdf-9662-44a4-a4a4-8ad237ae4a3e';
            $regions['13']['fias'] = '37a0c60a-9240-48b5-a87f-0d8c86cdb6e1';
            $regions['77']['fias'] = '0c5b2444-70a0-4932-980c-b4dc0d3f02b5';
            $regions['50']['fias'] = '29251dcf-00a1-4e34-98d4-5c47484a36d4';
            $regions['51']['fias'] = '1c727518-c96a-4f34-9ae6-fd510da3be03';
            $regions['83']['fias'] = '89db3198-6803-4106-9463-cbf781eff0b8';
            $regions['52']['fias'] = '88cd27e2-6a8a-4421-9718-719a28a0a088';
            $regions['53']['fias'] = 'e5a84b81-8ea1-49e3-b3c4-0528651be129';
            $regions['54']['fias'] = '1ac46b49-3209-4814-b7bf-a509ea1aecd9';
            $regions['55']['fias'] = '05426864-466d-41a3-82c4-11e61cdc98ce';
            $regions['56']['fias'] = '8bcec9d6-05bc-4e53-b45c-ba0c6f3a5c44';
            $regions['57']['fias'] = '5e465691-de23-4c4e-9f46-f35a125b5970';
            $regions['58']['fias'] = 'c99e7924-0428-4107-a302-4fd7c0cca3ff';
            $regions['59']['fias'] = '4f8b1a21-e4bb-422f-9087-d3cbf4bebc14';
            $regions['25']['fias'] = '43909681-d6e1-432d-b61f-ddac393cb5da';
            $regions['60']['fias'] = 'f6e148a1-c9d0-4141-a608-93e3bd95e6c4';
            $regions['61']['fias'] = 'f10763dc-63e3-48db-83e1-9c566fe3092b';
            $regions['62']['fias'] = '963073ee-4dfc-48bd-9a70-d2dfc6bd1f31';
            $regions['63']['fias'] = 'df3d7359-afa9-4aaa-8ff9-197e73906b1c';
            $regions['78']['fias'] = 'c2deb16a-0330-4f05-821f-1d09c93331e6';
            $regions['64']['fias'] = 'df594e0e-a935-4664-9d26-0bae13f904fe';
            $regions['14']['fias'] = 'c225d3db-1db6-4063-ace0-b3fe9ea3805f';
            $regions['65']['fias'] = 'aea6280f-4648-460f-b8be-c2bc18923191';
            $regions['66']['fias'] = '92b30014-4d52-4e2e-892d-928142b924bf';
            $regions['92']['fias'] = '6fdecb78-893a-4e3f-a5ba-aa062459463b';
            $regions['15']['fias'] = 'de459e9c-2933-4923-83d1-9c64cfd7a817';
            $regions['67']['fias'] = 'e8502180-6d08-431b-83ea-c7038f0df905';
            $regions['26']['fias'] = '327a060b-878c-4fb4-8dc4-d5595871a3d8';
            $regions['84']['fias'] = '29a47e2d-7606-481d-98d5-444e755dec34';
            $regions['68']['fias'] = 'a9a71961-9363-44ba-91b5-ddf0463aebc2';
            $regions['16']['fias'] = '0c089b04-099e-4e0e-955a-6bf1ce525f1a';
            $regions['69']['fias'] = '61723327-1c20-42fe-8dfa-402638d9b396';
            $regions['70']['fias'] = '889b1f3a-98aa-40fc-9d3d-0f41192758ab';
            $regions['71']['fias'] = 'd028ec4f-f6da-4843-ada6-b68b3e0efa3d';
            $regions['17']['fias'] = '026bc56f-3731-48e9-8245-655331f596c0';
            $regions['72']['fias'] = '54049357-326d-4b8f-b224-3c6dc25d6dd3';
            $regions['18']['fias'] = '52618b9c-bcbb-47e7-8957-95c63f0b17cc';
            $regions['73']['fias'] = 'fee76045-fe22-43a4-ad58-ad99e903bd58';
            $regions['27']['fias'] = '7d468b39-1afa-41ec-8c4f-97a8603cb3d4';
            $regions['19']['fias'] = '8d3f1d35-f0f4-41b5-b5b7-e7cadf3e7bd7';
            $regions['86']['fias'] = 'd66e5325-3a25-4d29-ba86-4ca351d9704b';
            $regions['74']['fias'] = '27eb7c10-a234-44da-a59c-8b1f864966de';
            $regions['20']['fias'] = 'de67dc49-b9ba-48a3-a4cc-c2ebfeca6c5e';
            $regions['21']['fias'] = '878fc621-3708-46c7-a97f-5a13a4176b3e';
            $regions['87']['fias'] = 'f136159b-404a-4f1f-8d8d-d169e1374d5c';
            $regions['88']['fias'] = '2d7468f0-bec1-4571-85f3-bdefd62b3d50';
            $regions['89']['fias'] = '826fa834-3ee8-404f-bdbc-13a5221cfb6e';
            $regions['76']['fias'] = 'a84b2ef4-db03-474b-b552-6229e801ae9b';

            $regions['01']['kladr'] = '0100000000000';
            $regions['04']['kladr'] = '0400000000000';
            $regions['22']['kladr'] = '2200000000000';
            $regions['28']['kladr'] = '2800000000000';
            $regions['29']['kladr'] = '2900000000000';
            $regions['30']['kladr'] = '3000000000000';
            $regions['99']['kladr'] = '9900000000000';
            $regions['02']['kladr'] = '0200000000000';
            $regions['31']['kladr'] = '3100000000000';
            $regions['32']['kladr'] = '3200000000000';
            $regions['03']['kladr'] = '0300000000000';
            $regions['33']['kladr'] = '3300000000000';
            $regions['34']['kladr'] = '3400000000000';
            $regions['35']['kladr'] = '3500000000000';
            $regions['36']['kladr'] = '3600000000000';
            $regions['05']['kladr'] = '0500000000000';
            $regions['79']['kladr'] = '7900000000000';
            $regions['75']['kladr'] = '7500000000000';
            $regions['80']['kladr'] = '8000000000000';
            $regions['37']['kladr'] = '3700000000000';
            $regions['06']['kladr'] = '0600000000000';
            $regions['38']['kladr'] = '3800000000000';
            $regions['85']['kladr'] = '8500000000000';
            $regions['07']['kladr'] = '0700000000000';
            $regions['39']['kladr'] = '3900000000000';
            $regions['08']['kladr'] = '0800000000000';
            $regions['40']['kladr'] = '4000000000000';
            $regions['41']['kladr'] = '4100000000000';
            $regions['09']['kladr'] = '0900000000000';
            $regions['10']['kladr'] = '1000000000000';
            $regions['42']['kladr'] = '4200000000000';
            $regions['43']['kladr'] = '4300000000000';
            $regions['11']['kladr'] = '1100000000000';
            $regions['81']['kladr'] = '8100000000000';
            $regions['82']['kladr'] = '8200000000000';
            $regions['44']['kladr'] = '4400000000000';
            $regions['23']['kladr'] = '2300000000000';
            $regions['24']['kladr'] = '2400000000000';
            $regions['91']['kladr'] = '9100000000000';
            $regions['45']['kladr'] = '4500000000000';
            $regions['46']['kladr'] = '4600000000000';
            $regions['47']['kladr'] = '4700000000000';
            $regions['48']['kladr'] = '4800000000000';
            $regions['49']['kladr'] = '4900000000000';
            $regions['12']['kladr'] = '1200000000000';
            $regions['13']['kladr'] = '1300000000000';
            $regions['77']['kladr'] = '7700000000000';
            $regions['50']['kladr'] = '5000000000000';
            $regions['51']['kladr'] = '5100000000000';
            $regions['83']['kladr'] = '8300000000000';
            $regions['52']['kladr'] = '5200000000000';
            $regions['53']['kladr'] = '5300000000000';
            $regions['54']['kladr'] = '5400000000000';
            $regions['55']['kladr'] = '5500000000000';
            $regions['56']['kladr'] = '5600000000000';
            $regions['57']['kladr'] = '5700000000000';
            $regions['58']['kladr'] = '5800000000000';
            $regions['59']['kladr'] = '5900000000000';
            $regions['25']['kladr'] = '2500000000000';
            $regions['60']['kladr'] = '6000000000000';
            $regions['61']['kladr'] = '6100000000000';
            $regions['62']['kladr'] = '6200000000000';
            $regions['63']['kladr'] = '6300000000000';
            $regions['78']['kladr'] = '7800000000000';
            $regions['64']['kladr'] = '6400000000000';
            $regions['14']['kladr'] = '1400000000000';
            $regions['65']['kladr'] = '6500000000000';
            $regions['66']['kladr'] = '6600000000000';
            $regions['92']['kladr'] = '9200000000000';
            $regions['15']['kladr'] = '1500000000000';
            $regions['67']['kladr'] = '6700000000000';
            $regions['26']['kladr'] = '2600000000000';
            $regions['84']['kladr'] = '8400000000000';
            $regions['68']['kladr'] = '6800000000000';
            $regions['16']['kladr'] = '1600000000000';
            $regions['69']['kladr'] = '6900000000000';
            $regions['70']['kladr'] = '7000000000000';
            $regions['71']['kladr'] = '7100000000000';
            $regions['17']['kladr'] = '1700000000000';
            $regions['72']['kladr'] = '7200000000000';
            $regions['18']['kladr'] = '1800000000000';
            $regions['73']['kladr'] = '7300000000000';
            $regions['27']['kladr'] = '2700000000000';
            $regions['19']['kladr'] = '1900000000000';
            $regions['86']['kladr'] = '8600000000000';
            $regions['74']['kladr'] = '7400000000000';
            $regions['20']['kladr'] = '2000000000000';
            $regions['21']['kladr'] = '2100000000000';
            $regions['87']['kladr'] = '8700000000000';
            $regions['88']['kladr'] = '8800000000000';
            $regions['89']['kladr'] = '8900000000000';
            $regions['76']['kladr'] = '7600000000000';

        }


        if($code) return $regions[$code];
        if($kladr) return $regions[substr($kladr, 0, 2)];
        if($fias) {
            switch ($fias) {

                case 'd8327a56-80de-4df2-815c-4f6ab1224c50':
                    $regionId = '01';
                    break;
                case '5c48611f-5de6-4771-9695-7e36a4e7529d':
                    $regionId = '04';
                    break;
                case '8276c6a1-1a86-4f0d-8920-aba34d4cc34a':
                    $regionId = '22';
                    break;
                case '844a80d6-5e31-4017-b422-4d9c01e9942c':
                    $regionId = '28';
                    break;
                case '294277aa-e25d-428c-95ad-46719c4ddb44':
                    $regionId = '29';
                    break;
                case '83009239-25cb-4561-af8e-7ee111b1cb73':
                    $regionId = '30';
                    break;
                case '63ed1a35-4be6-4564-a1ec-0c51f7383314':
                    $regionId = '99';
                    break;
                case '6f2cbfd8-692a-4ee4-9b16-067210bde3fc':
                    $regionId = '02';
                    break;
                case '639efe9d-3fc8-4438-8e70-ec4f2321f2a7':
                    $regionId = '31';
                    break;
                case 'f5807226-8be0-4ea8-91fc-39d053aec1e2':
                    $regionId = '32';
                    break;
                case 'a84ebed3-153d-4ba9-8532-8bdf879e1f5a':
                    $regionId = '03';
                    break;
                case 'b8837188-39ee-4ff9-bc91-fcc9ed451bb3':
                    $regionId = '33';
                    break;
                case 'da051ec8-da2e-4a66-b542-473b8d221ab4':
                    $regionId = '34';
                    break;
                case 'ed36085a-b2f5-454f-b9a9-1c9a678ee618':
                    $regionId = '35';
                    break;
                case 'b756fe6b-bbd3-44d5-9302-5bfcc740f46e':
                    $regionId = '36';
                    break;
                case '0bb7fa19-736d-49cf-ad0e-9774c4dae09b':
                    $regionId = '05';
                    break;
                case '1b507b09-48c9-434f-bf6f-65066211c73e':
                    $regionId = '79';
                    break;
                case 'b6ba5716-eb48-401b-8443-b197c9578734':
                    $regionId = '75';
                    break;
                case '53ec9705-ec3e-4cbf-921f-229968e10aeb':
                    $regionId = '80';
                    break;
                case '0824434f-4098-4467-af72-d4f702fed335':
                    $regionId = '37';
                    break;
                case 'b2d8cd20-cabc-4deb-afad-f3c4b4d55821':
                    $regionId = '06';
                    break;
                case '6466c988-7ce3-45e5-8b97-90ae16cb1249':
                    $regionId = '38';
                    break;
                case 'c2f0810a-d71e-4af6-85f1-e0bbd81b3e4d':
                    $regionId = '85';
                    break;
                case '1781f74e-be4a-4697-9c6b-493057c94818':
                    $regionId = '07';
                    break;
                case '90c7181e-724f-41b3-b6c6-bd3ec7ae3f30':
                    $regionId = '39';
                    break;
                case '491cde9d-9d76-4591-ab46-ea93c079e686':
                    $regionId = '08';
                    break;
                case '18133adf-90c2-438e-88c4-62c41656de70':
                    $regionId = '40';
                    break;
                case 'd02f30fc-83bf-4c0f-ac2b-5729a866a207':
                    $regionId = '41';
                    break;
                case '61b95807-388a-4cb1-9bee-889f7cf811c8':
                    $regionId = '09';
                    break;
                case '248d8071-06e1-425e-a1cf-d1ff4c4a14a8':
                    $regionId = '10';
                    break;
                case '393aeccb-89ef-4a7e-ae42-08d5cebc2e30':
                    $regionId = '42';
                    break;
                case '0b940b96-103f-4248-850c-26b6c7296728':
                    $regionId = '43';
                    break;
                case 'c20180d9-ad9c-46d1-9eff-d60bc424592a':
                    $regionId = '11';
                    break;
                case 'e3d95b95-cc2d-440d-95c6-65577fae076e':
                    $regionId = '81';
                    break;
                case '1e97910c-34fb-428a-a113-13a0024684ae':
                    $regionId = '82';
                    break;
                case '15784a67-8cea-425b-834a-6afe0e3ed61c':
                    $regionId = '44';
                    break;
                case 'd00e1013-16bd-4c09-b3d5-3cb09fc54bd8':
                    $regionId = '23';
                    break;
                case 'db9c4f8b-b706-40e2-b2b4-d31b98dcd3d1':
                    $regionId = '24';
                    break;
                case 'bd8e6511-e4b9-4841-90de-6bbc231a789e':
                    $regionId = '91';
                    break;
                case '4a3d970f-520e-46b9-b16c-50d4ca7535a8':
                    $regionId = '45';
                    break;
                case 'ee594d5e-30a9-40dc-b9f2-0add1be44ba1':
                    $regionId = '46';
                    break;
                case '6d1ebb35-70c6-4129-bd55-da3969658f5d':
                    $regionId = '47';
                    break;
                case '1490490e-49c5-421c-9572-5673ba5d80c8':
                    $regionId = '48';
                    break;
                case '9c05e812-8679-4710-b8cb-5e8bd43cdf48':
                    $regionId = '49';
                    break;
                case 'de2cbfdf-9662-44a4-a4a4-8ad237ae4a3e':
                    $regionId = '12';
                    break;
                case '37a0c60a-9240-48b5-a87f-0d8c86cdb6e1':
                    $regionId = '13';
                    break;
                case '0c5b2444-70a0-4932-980c-b4dc0d3f02b5':
                    $regionId = '77';
                    break;
                case '29251dcf-00a1-4e34-98d4-5c47484a36d4':
                    $regionId = '50';
                    break;
                case '1c727518-c96a-4f34-9ae6-fd510da3be03':
                    $regionId = '51';
                    break;
                case '89db3198-6803-4106-9463-cbf781eff0b8':
                    $regionId = '83';
                    break;
                case '88cd27e2-6a8a-4421-9718-719a28a0a088':
                    $regionId = '52';
                    break;
                case 'e5a84b81-8ea1-49e3-b3c4-0528651be129':
                    $regionId = '53';
                    break;
                case '1ac46b49-3209-4814-b7bf-a509ea1aecd9':
                    $regionId = '54';
                    break;
                case '05426864-466d-41a3-82c4-11e61cdc98ce':
                    $regionId = '55';
                    break;
                case '8bcec9d6-05bc-4e53-b45c-ba0c6f3a5c44':
                    $regionId = '56';
                    break;
                case '5e465691-de23-4c4e-9f46-f35a125b5970':
                    $regionId = '57';
                    break;
                case 'c99e7924-0428-4107-a302-4fd7c0cca3ff':
                    $regionId = '58';
                    break;
                case '4f8b1a21-e4bb-422f-9087-d3cbf4bebc14':
                    $regionId = '59';
                    break;
                case '43909681-d6e1-432d-b61f-ddac393cb5da':
                    $regionId = '25';
                    break;
                case 'f6e148a1-c9d0-4141-a608-93e3bd95e6c4':
                    $regionId = '60';
                    break;
                case 'f10763dc-63e3-48db-83e1-9c566fe3092b':
                    $regionId = '61';
                    break;
                case '963073ee-4dfc-48bd-9a70-d2dfc6bd1f31':
                    $regionId = '62';
                    break;
                case 'df3d7359-afa9-4aaa-8ff9-197e73906b1c':
                    $regionId = '63';
                    break;
                case 'c2deb16a-0330-4f05-821f-1d09c93331e6':
                    $regionId = '78';
                    break;
                case 'df594e0e-a935-4664-9d26-0bae13f904fe':
                    $regionId = '64';
                    break;
                case 'c225d3db-1db6-4063-ace0-b3fe9ea3805f':
                    $regionId = '14';
                    break;
                case 'aea6280f-4648-460f-b8be-c2bc18923191':
                    $regionId = '65';
                    break;
                case '92b30014-4d52-4e2e-892d-928142b924bf':
                    $regionId = '66';
                    break;
                case '6fdecb78-893a-4e3f-a5ba-aa062459463b':
                    $regionId = '92';
                    break;
                case 'de459e9c-2933-4923-83d1-9c64cfd7a817':
                    $regionId = '15';
                    break;
                case 'e8502180-6d08-431b-83ea-c7038f0df905':
                    $regionId = '67';
                    break;
                case '327a060b-878c-4fb4-8dc4-d5595871a3d8':
                    $regionId = '26';
                    break;
                case '29a47e2d-7606-481d-98d5-444e755dec34':
                    $regionId = '84';
                    break;
                case 'a9a71961-9363-44ba-91b5-ddf0463aebc2':
                    $regionId = '68';
                    break;
                case '0c089b04-099e-4e0e-955a-6bf1ce525f1a':
                    $regionId = '16';
                    break;
                case '61723327-1c20-42fe-8dfa-402638d9b396':
                    $regionId = '69';
                    break;
                case '889b1f3a-98aa-40fc-9d3d-0f41192758ab':
                    $regionId = '70';
                    break;
                case 'd028ec4f-f6da-4843-ada6-b68b3e0efa3d':
                    $regionId = '71';
                    break;
                case '026bc56f-3731-48e9-8245-655331f596c0':
                    $regionId = '17';
                    break;
                case '54049357-326d-4b8f-b224-3c6dc25d6dd3':
                    $regionId = '72';
                    break;
                case '52618b9c-bcbb-47e7-8957-95c63f0b17cc':
                    $regionId = '18';
                    break;
                case 'fee76045-fe22-43a4-ad58-ad99e903bd58':
                    $regionId = '73';
                    break;
                case '7d468b39-1afa-41ec-8c4f-97a8603cb3d4':
                    $regionId = '27';
                    break;
                case '8d3f1d35-f0f4-41b5-b5b7-e7cadf3e7bd7':
                    $regionId = '19';
                    break;
                case 'd66e5325-3a25-4d29-ba86-4ca351d9704b':
                    $regionId = '86';
                    break;
                case '27eb7c10-a234-44da-a59c-8b1f864966de':
                    $regionId = '74';
                    break;
                case 'de67dc49-b9ba-48a3-a4cc-c2ebfeca6c5e':
                    $regionId = '20';
                    break;
                case '878fc621-3708-46c7-a97f-5a13a4176b3e':
                    $regionId = '21';
                    break;
                case 'f136159b-404a-4f1f-8d8d-d169e1374d5c':
                    $regionId = '87';
                    break;
                case '2d7468f0-bec1-4571-85f3-bdefd62b3d50':
                    $regionId = '88';
                    break;
                case '826fa834-3ee8-404f-bdbc-13a5221cfb6e':
                    $regionId = '89';
                    break;
                case 'a84b2ef4-db03-474b-b552-6229e801ae9b':
                    $regionId = '76';
                    break;
            }

            return $regions[$regionId];
        }
        if($index) {
            switch (substr($index, 0, 3)) {


                case '385': $regionId = '01'; break;
                case '649': $regionId = '04'; break;
                case '656': $regionId = '22'; break;
                case '675': $regionId = '28'; break;
                case '163': $regionId = '29'; break;
                case '414': $regionId = '30'; break;
                case '468': $regionId = '99'; break;
                case '450': $regionId = '02'; break;
                case '452': $regionId = '02'; break;
                case '453': $regionId = '02'; break;
                case '309': $regionId = '31'; break;
                case '241': $regionId = '32'; break;
                case '670': $regionId = '03'; break;
                case '671': $regionId = '03'; break;
                case '600': $regionId = '33'; break;
                case '601': $regionId = '33'; break;
                case '602': $regionId = '33'; break;
                case '400': $regionId = '34'; break;
                case '403': $regionId = '34'; break;
                case '404': $regionId = '34'; break;
                case '160': $regionId = '35'; break;
                case '161': $regionId = '35'; break;
                case '162': $regionId = '35'; break;
                case '394': $regionId = '36'; break;
                case '396': $regionId = '36'; break;
                case '397': $regionId = '36'; break;
                case '367': $regionId = '05'; break;
                case '368': $regionId = '05'; break;
                case '679': $regionId = '79'; break;
                case '672': $regionId = '75'; break;
                case '153': $regionId = '37'; break;
                case '386': $regionId = '06'; break;
                case '664': $regionId = '38'; break;
                case '665': $regionId = '38'; break;
                case '360': $regionId = '07'; break;
                case '361': $regionId = '07'; break;
                case '236': $regionId = '39'; break;
                case '358': $regionId = '08'; break;
                case '359': $regionId = '08'; break;
                case '248': $regionId = '40'; break;
                case '683': $regionId = '41'; break;
                case '369': $regionId = '09'; break;
                case '185': $regionId = '10'; break;
                case '186': $regionId = '10'; break;
                case '650': $regionId = '42'; break;
                case '610': $regionId = '43'; break;
                case '167': $regionId = '11'; break;
                case '168': $regionId = '11'; break;
                case '169': $regionId = '11'; break;
                case '619': $regionId = '81'; break;
                case '688': $regionId = '82'; break;
                case '156': $regionId = '44'; break;
                case '350': $regionId = '23'; break;
                case '352': $regionId = '23'; break;
                case '353': $regionId = '23'; break;
                case '354': $regionId = '23'; break;
                case '660': $regionId = '24'; break;
                case '296': $regionId = '91'; break;
                case '297': $regionId = '91'; break;
                case '298': $regionId = '91'; break;
                case '640': $regionId = '45'; break;
                case '305': $regionId = '46'; break;
                case '187': $regionId = '47'; break;
                case '188': $regionId = '47'; break;
                case '398': $regionId = '48'; break;
                case '685': $regionId = '49'; break;
                case '424': $regionId = '12'; break;
                case '425': $regionId = '12'; break;
                case '430': $regionId = '13'; break;
                case '431': $regionId = '13'; break;
                case '101': $regionId = '77'; break;
                case '102': $regionId = '77'; break;
                case '103': $regionId = '77'; break;
                case '104': $regionId = '77'; break;
                case '105': $regionId = '77'; break;
                case '106': $regionId = '77'; break;
                case '107': $regionId = '77'; break;
                case '108': $regionId = '77'; break;
                case '109': $regionId = '77'; break;
                case '110': $regionId = '77'; break;
                case '111': $regionId = '77'; break;
                case '112': $regionId = '77'; break;
                case '113': $regionId = '77'; break;
                case '114': $regionId = '77'; break;
                case '115': $regionId = '77'; break;
                case '116': $regionId = '77'; break;
                case '117': $regionId = '77'; break;
                case '118': $regionId = '77'; break;
                case '119': $regionId = '77'; break;
                case '120': $regionId = '77'; break;
                case '121': $regionId = '77'; break;
                case '122': $regionId = '77'; break;
                case '123': $regionId = '77'; break;
                case '124': $regionId = '77'; break;
                case '125': $regionId = '77'; break;
                case '126': $regionId = '77'; break;
                case '127': $regionId = '77'; break;
                case '128': $regionId = '77'; break;
                case '129': $regionId = '77'; break;
                case '140': $regionId = '50'; break;
                case '141': $regionId = '50'; break;
                case '142': $regionId = '50'; break;
                case '143': $regionId = '50'; break;
                case '144': $regionId = '50'; break;
                case '183': $regionId = '51'; break;
                case '184': $regionId = '51'; break;
                case '166': $regionId = '83'; break;
                case '603': $regionId = '52'; break;
                case '606': $regionId = '52'; break;
                case '607': $regionId = '52'; break;
                case '173': $regionId = '53'; break;
                case '630': $regionId = '54'; break;
                case '644': $regionId = '55'; break;
                case '460': $regionId = '56'; break;
                case '461': $regionId = '56'; break;
                case '462': $regionId = '56'; break;
                case '302': $regionId = '57'; break;
                case '440': $regionId = '58'; break;
                case '614': $regionId = '59'; break;
                case '690': $regionId = '25'; break;
                case '180': $regionId = '60'; break;
                case '344': $regionId = '61'; break;
                case '346': $regionId = '61'; break;
                case '347': $regionId = '61'; break;
                case '390': $regionId = '62'; break;
                case '443': $regionId = '63'; break;
                case '445': $regionId = '63'; break;
                case '190': $regionId = '78'; break;
                case '191': $regionId = '78'; break;
                case '192': $regionId = '78'; break;
                case '193': $regionId = '78'; break;
                case '194': $regionId = '78'; break;
                case '195': $regionId = '78'; break;
                case '196': $regionId = '78'; break;
                case '197': $regionId = '78'; break;
                case '198': $regionId = '78'; break;
                case '199': $regionId = '78'; break;
                case '410': $regionId = '64'; break;
                case '677': $regionId = '14'; break;
                case '678': $regionId = '14'; break;
                case '693': $regionId = '65'; break;
                case '620': $regionId = '66'; break;
                case '299': $regionId = '92'; break;
                case '362': $regionId = '15'; break;
                case '363': $regionId = '15'; break;
                case '214': $regionId = '67'; break;
                case '355': $regionId = '26'; break;
                case '392': $regionId = '68'; break;
                case '420': $regionId = '16'; break;
                case '421': $regionId = '16'; break;
                case '422': $regionId = '16'; break;
                case '423': $regionId = '16'; break;
                case '170': $regionId = '69'; break;
                case '634': $regionId = '70'; break;
                case '300': $regionId = '71'; break;
                case '301': $regionId = '71'; break;
                case '667': $regionId = '17'; break;
                case '625': $regionId = '72'; break;
                case '426': $regionId = '18'; break;
                case '432': $regionId = '73'; break;
                case '680': $regionId = '27'; break;
                case '655': $regionId = '19'; break;
                case '628': $regionId = '86'; break;
                case '454': $regionId = '74'; break;
                case '455': $regionId = '74'; break;
                case '456': $regionId = '74'; break;
                case '457': $regionId = '74'; break;
                case '364': $regionId = '20'; break;
                case '428': $regionId = '21'; break;
                case '689': $regionId = '87'; break;
                case '648': $regionId = '88'; break;
                case '629': $regionId = '89'; break;
                case '150': $regionId = '76'; break;
                case '152': $regionId = '76'; break;


            }

            return $regions[$regionId];
        }

        if($name) {

            $objectType['обл'] = ['name' => 'Область', 'abbr' => 'обл'];
            $objectType['респ'] = ['name' => 'Республика', 'abbr' => 'Респ'];
            $objectType['край'] = ['name' => 'Край', 'abbr' => 'край'];
            $objectType['г'] = ['name' => 'Город', 'abbr' => 'г'];
            $objectType['аобл'] = ['name' => 'Автономная область', 'abbr' => 'Аобл'];
            $objectType['ао'] = ['name' => 'Автономный округ', 'abbr' => 'АО'];
            $objectType['округ'] = ['name' => 'Автономный округ', 'abbr' => 'АО'];

            foreach ($objectType as $objectId => $object) $objectType[mb_strtolower($object['name'])] = $objectType[$objectId];

            $part = explode(' ', trim($name));

            $partId = 0;
            $type = mb_strtolower($part[$partId]);
            if (!($typeInfo = $objectType[$type])) {

                $type = (substr($type, -1) == '.') ? substr($type, 0, -1) : $type . '.';
                if (!($typeInfo = $objectType[$type])) {

                    $partId = sizeof($part) - 1;
                    $type = mb_strtolower($part[$partId]);
                    if (!($typeInfo = $objectType[$type])) {

                        $type = (substr($type, -1) == '.') ? substr($type, 0, -1) : $type . '.';
                        $typeInfo = $objectType[$type];
                    }
                }
            }

            if ($typeInfo) {
                $part[$partId] = '';
                $name = trim(join(' ', $part));
            }

            switch (mb_strtolower(trim($name))) {

                case 'адыгея':
                    $regionId = '01';
                    break;
                case 'алтай':
                    $regionId = '04';
                    break;
                case 'алтайский':
                    $regionId = '22';
                    break;
                case 'амурская':
                    $regionId = '28';
                    break;
                case 'архангельская':
                    $regionId = '29';
                    break;
                case 'астраханская':
                    $regionId = '30';
                    break;
                case 'байконур':
                    $regionId = '99';
                    break;
                case 'башкортостан':
                    $regionId = '02';
                    break;
                case 'белгородская':
                    $regionId = '31';
                    break;
                case 'брянская':
                    $regionId = '32';
                    break;
                case 'бурятия':
                    $regionId = '03';
                    break;
                case 'владимирская':
                    $regionId = '33';
                    break;
                case 'волгоградская':
                    $regionId = '34';
                    break;
                case 'вологодская':
                    $regionId = '35';
                    break;
                case 'воронежская':
                    $regionId = '36';
                    break;
                case 'дагестан':
                    $regionId = '05';
                    break;
                case 'еврейская':
                    $regionId = '79';
                    break;
                case 'забайкальский':
                    $regionId = '75';
                    break;
                case 'ивановская':
                    $regionId = '37';
                    break;
                case 'ингушетия':
                    $regionId = '06';
                    break;
                case 'иркутская':
                    $regionId = '38';
                    break;
                case 'кабардино-балкарская':
                    $regionId = '07';
                    break;
                case 'кабардино-балкария':
                    $regionId = '07';
                    break;
                case 'калининградская':
                    $regionId = '39';
                    break;
                case 'калмыкия':
                    $regionId = '08';
                    break;
                case 'калужская':
                    $regionId = '40';
                    break;
                case 'камчатский':
                    $regionId = '41';
                    break;
                case 'карачаево-черкесская':
                    $regionId = '09';
                    break;
                case 'карачаево-черкессия':
                    $regionId = '09';
                    break;
                case 'карелия':
                    $regionId = '10';
                    break;
                case 'кемеровская':
                    $regionId = '42';
                    break;
                case 'кировская':
                    $regionId = '43';
                    break;
                case 'коми':
                    $regionId = '11';
                    break;
                case 'коми-пермяцкий':
                    $regionId = '81';
                    break;
                case 'корякский':
                    $regionId = '82';
                    break;
                case 'костромская':
                    $regionId = '44';
                    break;
                case 'краснодарский':
                    $regionId = '23';
                    break;
                case 'красноярский':
                    $regionId = '24';
                    break;
                case 'крым':
                    $regionId = '91';
                    break;
                case 'курганская':
                    $regionId = '45';
                    break;
                case 'курская':
                    $regionId = '46';
                    break;
                case 'ленинградская':
                    $regionId = '47';
                    break;
                case 'липецкая':
                    $regionId = '48';
                    break;
                case 'магаданская':
                    $regionId = '49';
                    break;
                case 'марий эл':
                    $regionId = '12';
                    break;
                case 'мордовия':
                    $regionId = '13';
                    break;
                case 'москва':
                    $regionId = '77';
                    break;
                case 'московская':
                    $regionId = '50';
                    break;
                case 'мурманская':
                    $regionId = '51';
                    break;
                case 'ненецкий':
                    $regionId = '83';
                    break;
                case 'нижегородская':
                    $regionId = '52';
                    break;
                case 'новгородская':
                    $regionId = '53';
                    break;
                case 'новосибирская':
                    $regionId = '54';
                    break;
                case 'омская':
                    $regionId = '55';
                    break;
                case 'оренбургская':
                    $regionId = '56';
                    break;
                case 'орловская':
                    $regionId = '57';
                    break;
                case 'пензенская':
                    $regionId = '58';
                    break;
                case 'пермский':
                    $regionId = '59';
                    break;
                case 'приморский':
                    $regionId = '25';
                    break;
                case 'псковская':
                    $regionId = '60';
                    break;
                case 'ростовская':
                    $regionId = '61';
                    break;
                case 'рязанская':
                    $regionId = '62';
                    break;
                case 'самарская':
                    $regionId = '63';
                    break;
                case 'санкт-петербург':
                    $regionId = '78';
                    break;
                case 'саратовская':
                    $regionId = '64';
                    break;
                case 'саха (якутия)':
                    $regionId = '14';
                    break;
                case 'саха /якутия/':
                    $regionId = '14';
                    break;
                case 'саха':
                    $regionId = '14';
                    break;
                case 'якутия':
                    $regionId = '14';
                    break;
                case 'сахалинская':
                    $regionId = '65';
                    break;
                case 'свердловская':
                    $regionId = '66';
                    break;
                case 'севастополь':
                    $regionId = '92';
                    break;
                case 'северная осетия - алания':
                    $regionId = '15';
                    break;
                case 'алания':
                    $regionId = '15';
                    break;
                case 'северная осетия':
                    $regionId = '15';
                    break;
                case 'смоленская':
                    $regionId = '67';
                    break;
                case 'ставропольский':
                    $regionId = '26';
                    break;
                case 'тамбовская':
                    $regionId = '68';
                    break;
                case 'татарстан':
                    $regionId = '16';
                    break;
                case 'тверская':
                    $regionId = '69';
                    break;
                case 'томская':
                    $regionId = '70';
                    break;
                case 'тульская':
                    $regionId = '71';
                    break;
                case 'тыва':
                    $regionId = '17';
                    break;
                case 'тюменская':
                    $regionId = '72';
                    break;
                case 'удмуртская':
                    $regionId = '18';
                    break;
                case 'ульяновская':
                    $regionId = '73';
                    break;
                case 'хабаровский':
                    $regionId = '27';
                    break;
                case 'хакасия':
                    $regionId = '19';
                    break;
                case 'ханты-мансийский автономный округ - югра':
                    $regionId = '86';
                    break;
                case 'ханты-мансийский':
                    $regionId = '86';
                    break;
                case 'югра':
                    $regionId = '86';
                    break;
                case 'челябинская':
                    $regionId = '74';
                    break;
                case 'чеченская':
                    $regionId = '20';
                    break;
                case 'чечня':
                    $regionId = '20';
                    break;
                case 'чувашия':
                    $regionId = '21';
                    break;
                case 'чувашская':
                    $regionId = '21';
                    break;
                case 'чукотский':
                    $regionId = '87';
                    break;
                case 'эвенкийский':
                    $regionId = '88';
                    break;
                case 'ямало-ненецкий':
                    $regionId = '89';
                    break;
                case 'ярославская':
                    $regionId = '76';
                    break;
            }

            if(!$regions[$regionId]) return false;

            $regions[$regionId]['type'] = $typeInfo;

            return $regions[$regionId];

        }

    }
    public function getSettlementInfo($settlement) {

        $objectType['волость'] = ['name' => 'Волость', 'abbr' => 'волость'];
        $objectType['г'] = ['name' => 'Город', 'abbr' => 'г'];
        $objectType['дп'] = ['name' => 'Дачный поселок', 'abbr' => 'дп'];
        $objectType['массив'] = ['name' => 'Массив', 'abbr' => 'массив'];
        $objectType['п/о'] = ['name' => 'Почтовое отделение', 'abbr' => 'П/О'];
        $objectType['пгт'] = ['name' => 'Поселок городского типа', 'abbr' => 'пгт'];
        $objectType['рп'] = ['name' => 'Рабочий поселок', 'abbr' => 'рп'];
        $objectType['с/а'] = ['name' => 'Сельская администрация', 'abbr' => 'С/А'];
        $objectType['с/о'] = ['name' => 'Сельский округ', 'abbr' => 'С/О'];
        $objectType['с/п'] = ['name' => 'Сельское поселение', 'abbr' => 'С/П'];
        $objectType['с/с'] = ['name' => 'Сельсовет', 'abbr' => 'С/С'];
        $objectType['тер'] = ['name' => 'Территория', 'abbr' => 'тер'];
        $objectType['аал'] = ['name' => 'Аал', 'abbr' => 'аал'];
        $objectType['автодорога'] = ['name' => 'Автодорога', 'abbr' => 'автодорога'];
        $objectType['арбан'] = ['name' => 'Арбан', 'abbr' => 'арбан'];
        $objectType['аул'] = ['name' => 'Аул', 'abbr' => 'аул'];
        $objectType['волость'] = ['name' => 'Волость', 'abbr' => 'волость'];
        $objectType['высел'] = ['name' => 'Выселки(ок)', 'abbr' => 'высел'];
        $objectType['г'] = ['name' => 'Город', 'abbr' => 'г'];
        $objectType['городок'] = ['name' => 'Городок', 'abbr' => 'городок'];
        $objectType['д'] = ['name' => 'Деревня', 'abbr' => 'д'];
        $objectType['дп'] = ['name' => 'Дачный поселок', 'abbr' => 'ДП'];
        $objectType['ж/д_будка'] = ['name' => 'Железнодорожная будка', 'abbr' => 'Ж/Д_БУДКА'];
        $objectType['ж/д_казарм'] = ['name' => 'Железнодорожная казарма', 'abbr' => 'Ж/Д_КАЗАРМ'];
        $objectType['ж/д_оп'] = ['name' => 'Ж/д останов, (обгонный) пункт', 'abbr' => 'Ж/Д_ОП'];
        $objectType['ж/д_платф'] = ['name' => 'Железнодорожная платформа', 'abbr' => 'Ж/Д_ПЛАТФ'];
        $objectType['ж/д_пост'] = ['name' => 'Железнодорожный пост', 'abbr' => 'Ж/Д_ПОСТ'];
        $objectType['ж/д_рзд'] = ['name' => 'Железнодорожный разъезд', 'abbr' => 'Ж/Д_РЗД'];
        $objectType['ж/д_ст'] = ['name' => 'Железнодорожная станция', 'abbr' => 'Ж/Д_СТ'];
        $objectType['жилрайон'] = ['name' => 'Жилой район', 'abbr' => 'жилрайон'];
        $objectType['заимка'] = ['name' => 'Заимка', 'abbr' => 'заимка'];
        $objectType['казарма'] = ['name' => 'Казарма', 'abbr' => 'казарма'];
        $objectType['кв-л'] = ['name' => 'Квартал', 'abbr' => 'кв-л'];
        $objectType['кордон'] = ['name' => 'Кордон', 'abbr' => 'кордон'];
        $objectType['кп'] = ['name' => 'Курортный поселок', 'abbr' => 'кп'];
        $objectType['лпх'] = ['name' => 'Леспромхоз', 'abbr' => 'лпх'];
        $objectType['м'] = ['name' => 'Местечко', 'abbr' => 'м'];
        $objectType['мкр'] = ['name' => 'Микрорайон', 'abbr' => 'мкр'];
        $objectType['нп'] = ['name' => 'Населенный пункт', 'abbr' => 'нп'];
        $objectType['остров'] = ['name' => 'Остров', 'abbr' => 'остров'];
        $objectType['п'] = ['name' => 'Поселок', 'abbr' => 'п'];
        $objectType['п/о'] = ['name' => 'Почтовое отделение', 'abbr' => 'П/О'];
        $objectType['п/р'] = ['name' => 'Планировочный район', 'abbr' => 'П/Р'];
        $objectType['п/ст'] = ['name' => 'Поселок и (при) станция(и)', 'abbr' => 'П/СТ'];
        $objectType['пгт'] = ['name' => 'Поселок городского типа', 'abbr' => 'пгт'];
        $objectType['погост'] = ['name' => 'Погост', 'abbr' => 'погост'];
        $objectType['починок'] = ['name' => 'Починок', 'abbr' => 'починок'];
        $objectType['промзона'] = ['name' => 'Промышленная зона', 'abbr' => 'промзона'];
        $objectType['рзд'] = ['name' => 'Разъезд', 'abbr' => 'рзд'];
        $objectType['рп'] = ['name' => 'Рабочий поселок', 'abbr' => 'рп'];
        $objectType['с'] = ['name' => 'Село', 'abbr' => 'с'];
        $objectType['сл'] = ['name' => 'Слобода', 'abbr' => 'сл'];
        $objectType['снт'] = ['name' => 'Садовое некоммерческое товарищество', 'abbr' => 'снт'];
        $objectType['ст'] = ['name' => 'Станция', 'abbr' => 'ст'];
        $objectType['ст-ца'] = ['name' => 'Станица', 'abbr' => 'ст-ца'];
        $objectType['тер'] = ['name' => 'Территория', 'abbr' => 'тер'];
        $objectType['у'] = ['name' => 'Улус', 'abbr' => 'у'];
        $objectType['х'] = ['name' => 'Хутор', 'abbr' => 'х'];

        foreach ($objectType as $objectId => $object) $objectType[mb_strtolower($object['name'])] = $objectType[$objectId];


        $part = explode(' ', trim($settlement));

        $partId = 0;
        $type = mb_strtolower($part[$partId]);
        if(!($typeInfo = $objectType[$type])) {

            $type = (substr($type, -1) == '.') ? substr($type, 0, -1) : $type . '.';
            if(!($typeInfo = $objectType[$type])) {

                $partId = sizeof($part)-1;
                $type = mb_strtolower($part[$partId]);
                if(!($typeInfo = $objectType[$type])) {

                    $type = (substr($type, -1) == '.') ? substr($type, 0, -1) : $type . '.';
                    $typeInfo = $objectType[$type];

                }

            }
        }

        if(!$typeInfo) return false;
        else {

            $part[$partId] = '';
            $name = trim(join(' ', $part));
            if(ctype_digit(str_replace('/', '', $name))) return false;
            return ['name' => $name, 'type' => $typeInfo];

        }

    }
    public function getStreetInfo($street) {

        $objectType['аал'] = ['name' => 'Аал', 'abbr' => 'аал'];
        $objectType['аллея'] = ['name' => 'Аллея', 'abbr' => 'аллея'];
        $objectType['аул'] = ['name' => 'Аул', 'abbr' => 'аул'];
        $objectType['б-р'] = ['name' => 'Бульвар', 'abbr' => 'б-р'];
        $objectType['вал'] = ['name' => 'Вал', 'abbr' => 'вал'];
        $objectType['въезд'] = ['name' => 'Въезд', 'abbr' => 'въезд'];
        $objectType['высел'] = ['name' => 'Выселки(ок)', 'abbr' => 'высел'];
        $objectType['городок'] = ['name' => 'Городок', 'abbr' => 'городок'];
        $objectType['гск'] = ['name' => 'Гаражно-строительный кооператив', 'abbr' => 'гск'];
        $objectType['д'] = ['name' => 'Деревня', 'abbr' => 'д'];
        $objectType['дор'] = ['name' => 'Дорога', 'abbr' => 'дор'];
        $objectType['ж/д_будка'] = ['name' => 'Железнодорожная будка', 'abbr' => 'Ж/Д_БУДКА'];
        $objectType['ж/д_казарм'] = ['name' => 'Железнодорожная казарма', 'abbr' => 'Ж/Д_КАЗАРМ'];
        $objectType['ж/д_оп'] = ['name' => 'Ж/д останов, (обгонный) пункт', 'abbr' => 'Ж/Д_ОП'];
        $objectType['ж/д_платф'] = ['name' => 'Железнодорожная платформа', 'abbr' => 'Ж/Д_ПЛАТФ'];
        $objectType['ж/д_пост'] = ['name' => 'Железнодорожный пост', 'abbr' => 'Ж/Д_ПОСТ'];
        $objectType['ж/д_рзд'] = ['name' => 'Железнодорожный разъезд', 'abbr' => 'Ж/Д_РЗД'];
        $objectType['ж/д_ст'] = ['name' => 'Железнодорожная станция', 'abbr' => 'Ж/Д_СТ'];
        $objectType['жт'] = ['name' => 'Животноводческая точка', 'abbr' => 'ЖТ'];
        $objectType['заезд'] = ['name' => 'Заезд', 'abbr' => 'заезд'];
        $objectType['казарма'] = ['name' => 'Казарма', 'abbr' => 'казарма'];
        $objectType['канал'] = ['name' => 'Канал', 'abbr' => 'канал'];
        $objectType['кв-л'] = ['name' => 'Квартал', 'abbr' => 'кв-л'];
        $objectType['км'] = ['name' => 'Километр', 'abbr' => 'км'];
        $objectType['коса'] = ['name' => 'Коса', 'abbr' => 'коса'];
        $objectType['линия'] = ['name' => 'Линия', 'abbr' => 'линия'];
        $objectType['лпх'] = ['name' => 'Леспромхоз', 'abbr' => 'лпх'];
        $objectType['м'] = ['name' => 'Местечко', 'abbr' => 'м'];
        $objectType['мкр'] = ['name' => 'Микрорайон', 'abbr' => 'мкр'];
        $objectType['мост'] = ['name' => 'Мост', 'abbr' => 'мост'];
        $objectType['наб'] = ['name' => 'Набережная', 'abbr' => 'наб'];
        $objectType['нп'] = ['name' => 'Населенный пункт', 'abbr' => 'нп'];
        $objectType['остров'] = ['name' => 'Остров', 'abbr' => 'остров'];
        $objectType['п'] = ['name' => 'Поселок', 'abbr' => 'п'];
        $objectType['п/о'] = ['name' => 'Почтовое отделение', 'abbr' => 'П/О'];
        $objectType['п/р'] = ['name' => 'Планировочный район', 'abbr' => 'П/Р'];
        $objectType['п/ст'] = ['name' => 'Поселок и (при) станция(и)', 'abbr' => 'П/СТ'];
        $objectType['парк'] = ['name' => 'Парк', 'abbr' => 'парк'];
        $objectType['пер'] = ['name' => 'Переулок', 'abbr' => 'пер'];
        $objectType['пр-д'] = ['name' => 'Проезд', 'abbr' => 'Пр-д'];
        $objectType['пр'] = ['name' => 'Проезд', 'abbr' => 'Пр-д'];
        $objectType['переезд'] = ['name' => 'Переезд', 'abbr' => 'переезд'];
        $objectType['пл'] = ['name' => 'Площадь', 'abbr' => 'пл'];
        $objectType['платф'] = ['name' => 'Платформа', 'abbr' => 'платф'];
        $objectType['пл-ка'] = ['name' => 'Площадка', 'abbr' => 'пл-ка'];
        $objectType['полустанок'] = ['name' => 'Полустанок', 'abbr' => 'полустанок'];
        $objectType['пр-кт'] = ['name' => 'Проспект', 'abbr' => 'пр-кт'];
        $objectType['пр-т'] = ['name' => 'Проспект', 'abbr' => 'пр-кт'];
        $objectType['проезд'] = ['name' => 'Проезд', 'abbr' => 'проезд'];
        $objectType['просек'] = ['name' => 'Просек', 'abbr' => 'просек'];
        $objectType['проселок'] = ['name' => 'Проселок', 'abbr' => 'проселок'];
        $objectType['проток'] = ['name' => 'Проток', 'abbr' => 'проток'];
        $objectType['проулок'] = ['name' => 'Проулок', 'abbr' => 'проулок'];
        $objectType['рзд'] = ['name' => 'Разъезд', 'abbr' => 'рзд'];
        $objectType['ряды'] = ['name' => 'Ряды', 'abbr' => 'ряды'];
        $objectType['с'] = ['name' => 'Село', 'abbr' => 'с'];
        $objectType['сад'] = ['name' => 'Сад', 'abbr' => 'сад'];
        $objectType['сквер'] = ['name' => 'Сквер', 'abbr' => 'сквер'];
        $objectType['сл'] = ['name' => 'Слобода', 'abbr' => 'сл'];
        $objectType['снт'] = ['name' => 'Садовое некоммерческое товарищество', 'abbr' => 'снт'];
        $objectType['ст'] = ['name' => 'Станция', 'abbr' => 'ст'];
        $objectType['стр'] = ['name' => 'Строение', 'abbr' => 'стр'];
        $objectType['тер'] = ['name' => 'Территория', 'abbr' => 'тер'];
        $objectType['тракт'] = ['name' => 'Тракт', 'abbr' => 'тракт'];
        $objectType['туп'] = ['name' => 'Тупик', 'abbr' => 'туп'];
        $objectType['ул'] = ['name' => 'Улица', 'abbr' => 'ул'];
        $objectType['уч-к'] = ['name' => 'Участок', 'abbr' => 'уч-к'];
        $objectType['ферма'] = ['name' => 'Ферма', 'abbr' => 'ферма'];
        $objectType['х'] = ['name' => 'Хутор', 'abbr' => 'х'];
        $objectType['ш'] = ['name' => 'Шоссе', 'abbr' => 'ш'];

        foreach ($objectType as $objectId => $object) $objectType[mb_strtolower($object['name'])] = $objectType[$objectId];


        $part = explode(' ', trim($street));

        $partId = 0;
        $type = mb_strtolower($part[$partId]);
        if(!($typeInfo = $objectType[$type])) {

            $type = (substr($type, -1) == '.') ? substr($type, 0, -1) : $type . '.';
            if(!($typeInfo = $objectType[$type])) {

                $partId = sizeof($part)-1;
                $type = mb_strtolower($part[$partId]);
                if(!($typeInfo = $objectType[$type])) {

                    $type = (substr($type, -1) == '.') ? substr($type, 0, -1) : $type . '.';
                    $typeInfo = $objectType[$type];

                }

            }
        }

        if(!$typeInfo) return false;
        else {

            $part[$partId] = '';
            return ['name' => trim(join(' ', $part)), 'type' => $typeInfo];

        }

    }
    public function getBuildingInfo($building) {

        $objectType['д'] = ['name' => 'Дом', 'abbr' => 'д'];
        $objectType['вл'] = ['name' => 'Владение', 'abbr' => 'вл'];
        $objectType['дв'] = ['name' => 'Домовладение', 'abbr' => 'дв'];
        $objectType['соор'] = ['name' => 'Сооружение', 'abbr' => 'соор'];

        foreach ($objectType as $objectId => $object) $objectType[mb_strtolower($object['name'])] = $objectType[$objectId];


        $part = explode(' ', trim($building));
        if(sizeof($part) == 1) $part = explode('.', trim($building));

        $partId = 0;
        $type = mb_strtolower($part[$partId]);
        if(!($typeInfo = $objectType[$type])) {

            $type = (substr($type, -1) == '.') ? substr($type, 0, -1) : $type . '.';
            if(!($typeInfo = $objectType[$type])) {

                $partId = sizeof($part)-1;
                $type = mb_strtolower($part[$partId]);
                if(!($typeInfo = $objectType[$type])) {

                    $type = (substr($type, -1) == '.') ? substr($type, 0, -1) : $type . '.';
                    $typeInfo = $objectType[$type];

                }

            }
        }

        if(!$typeInfo) return false;
        else {

            $part[$partId] = '';
            return ['name' => trim(join(' ', $part)), 'type' => $typeInfo];

        }

    }
    public function getHousingInfo($housing) {

        $objectType['стр'] = ['name' => 'Строение', 'abbr' => 'стр'];
        $objectType['с'] = ['name' => 'Строение', 'abbr' => 'стр'];
        $objectType['корп'] = ['name' => 'Корпус', 'abbr' => 'корп'];
        $objectType['к'] = ['name' => 'Корпус', 'abbr' => 'корп'];
        $objectType['лит'] = ['name' => 'литера', 'abbr' => 'лит'];


        foreach ($objectType as $objectId => $object) $objectType[mb_strtolower($object['name'])] = $objectType[$objectId];


        $part = explode(' ', trim($housing));

        $partId = 0;
        $type = mb_strtolower($part[$partId]);
        if(!($typeInfo = $objectType[$type])) {

            $type = (substr($type, -1) == '.') ? substr($type, 0, -1) : $type . '.';
            if(!($typeInfo = $objectType[$type])) {

                $partId = sizeof($part)-1;
                $type = mb_strtolower($part[$partId]);
                if(!($typeInfo = $objectType[$type])) {

                    $type = (substr($type, -1) == '.') ? substr($type, 0, -1) : $type . '.';
                    $typeInfo = $objectType[$type];

                }

            }
        }

        if(!$typeInfo) return false;
        else {

            $part[$partId] = '';
            return ['name' => trim(join(' ', $part)), 'type' => $typeInfo];

        }

    }
    public function getApartmentInfo($apartment) {

        $objectType['оф'] = ['name' => 'Офис', 'abbr' => 'оф'];
        $objectType['кв'] = ['name' => 'Квартира', 'abbr' => 'кв'];
        $objectType['пом'] = ['name' => 'Помещение', 'abbr' => 'Пом'];
        $objectType['пав-н'] = ['name' => 'Павильон', 'abbr' => 'Пав-н'];
        $objectType['пав'] = ['name' => 'Павильон', 'abbr' => 'Пав-н'];

        foreach ($objectType as $objectId => $object) $objectType[mb_strtolower($object['name'])] = $objectType[$objectId];


        $part = explode(' ', trim($apartment));

        $partId = 0;
        $type = mb_strtolower($part[$partId]);
        if(!($typeInfo = $objectType[$type])) {

            $type = (substr($type, -1) == '.') ? substr($type, 0, -1) : $type . '.';
            if(!($typeInfo = $objectType[$type])) {

                $partId = sizeof($part)-1;
                $type = mb_strtolower($part[$partId]);
                if(!($typeInfo = $objectType[$type])) {

                    $type = (substr($type, -1) == '.') ? substr($type, 0, -1) : $type . '.';
                    $typeInfo = $objectType[$type];

                }

            }
        }

        if(!$typeInfo) return false;
        else {

            $part[$partId] = '';
            return ['name' => trim(join(' ', $part)), 'type' => $typeInfo];

        }

    }

    public function methodsListPreProcessing($get_defined_vars) {

        extract($get_defined_vars);


        $status[0]['method'] = "info/methodsList";
        $status[0]['active'] = false;
        $status[1]['method'] = "info/cityList";
        $status[1]['active'] = false;
        $status[2]['method'] = "info/parcelShopsList";
        $status[2]['active'] = false;
        $status[3]['method'] = "info/tariffList";
        $status[3]['active'] = false;
        $status[4]['method'] = "info/statusList";
        $status[4]['active'] = false;
        $status[5]['method'] = "info/errorList";
        $status[5]['active'] = false;
        $status[6]['method'] = "order/getCost";
        $status[6]['active'] = false;
        $status[7]['method'] = "order/create";
        $status[7]['active'] = false;
        $status[8]['method'] = "order/getInfo";
        $status[8]['active'] = false;
        $status[9]['method'] = "order/edit";
        $status[9]['active'] = false;
        $status[10]['method'] = "order/cancel";
        $status[10]['active'] = false;
        $status[11]['method'] = "batches/getOrderList";
        $status[11]['active'] = false;
        $status[12]['method'] = "batches/getLabel";
        $status[12]['active'] = false;
        $status[13]['method'] = "batches/create";
        $status[13]['active'] = false;
        $status[14]['method'] = "batches/getInfo";
        $status[14]['active'] = false;
        $status[15]['method'] = "batches/removeOrder";
        $status[15]['active'] = false;
        $status[16]['method'] = "allocates/courier";
        $status[16]['active'] = false;
        $status[17]['method'] = "allocates/cancel";
        $status[17]['active'] = false;
        $status[18]['method'] = "returns/create";
        $status[18]['active'] = false;
        $status[19]['method'] = "returns/getReturnsList";
        $status[19]['active'] = false;
        $status[20]['method'] = "returns/getInfo";
        $status[20]['active'] = false;


        return get_defined_vars();

    }
    public function methodsListPostProcessing($get_defined_vars) {

        extract($get_defined_vars);


        $status = array_values($status);


        return get_defined_vars();

    }


    public function cityListPreProcessing($get_defined_vars) {

        extract($get_defined_vars);


        $cacheKey = $_SERVER['HTTP_HOST'].$namespace."cityList";

        if($this->memcache) $citylist = $this->memcache->get($cacheKey);


        return get_defined_vars();

    }
    public function cityListPostProcessing($get_defined_vars) {

        extract($get_defined_vars);


        $citylist = array_values($citylist);

        if($kladr && !$cityName) {

            $kladrRequest = json_decode(file_get_contents("https://kladr-api.ru/api.php?contentType=city&cityId=".$kladr), true);
            $cityName = $kladrRequest['result'][0]['name'];

        }

        foreach($citylist as $cityId => $city) {
            switch ($city['name']) {
                case 'Екатеринбург':
                    $citylist[$cityId]['kladr'] = '6600000100000';
                    $citylist[$cityId]['RegionName'] = 'Свердловская обл.';
                    break;
            }
        }

        //Получение КЛАДР
        if($kladr) foreach($citylist as $cityId => $city) {

            unset($citykladr);

            if (($kladr && !$citylist[$cityId]['kladr'] && $city['name'] == $cityName) || ($regionName && !$citylist[$cityId]['RegionName'])) {

                $cacheKey = "cityListgetkladr" . $city['name'];

                if($this->memcache) $citykladr = $this->memcache->get($cacheKey);

                if(empty($citykladr)) {

                    $citykladr = json_decode(file_get_contents("https://kladr-api.ru/api.php?contentType=city&query=" . $city['name'] . "&withParent=1&limit=1"), true);

                    //Ограничитель для тестов
                    //if($this->memcache instanceof \memcache) $citykladr=array_slice($citykladr,0,99);

                    if($this->memcache) $this->memcache->set($cacheKey, $citykladr);

                }

                if ($citykladr['result'][0]['id']) $citylist[$cityId]['kladr'] = $citykladr['result'][0]['id'];

                if (array_key_exists(0, $citykladr['result'][0]['parents']) && !$citylist[$cityId]['RegionName']) {

                    if ($citykladr['result'][0]['parents'][0]['name'] . " " . $citykladr['result'][0]['parents'][0]['typeShort'] . ".") {

                        $citylist[$cityId]['RegionName'] = $citykladr['result'][0]['parents'][0]['name'] . " " . $citykladr['result'][0]['parents'][0]['typeShort'] . ".";
                        $citylist[$cityId]['RegionId'] = $citykladr['result'][0]['parents'][0]['id'];

                    }

                }

            }

        }

        foreach($citylist as $cityId => $city) {

            if(strlen($city['kladr']) == 11) $citylist[$cityId]['kladr'] .= '00';

        }

        $regionNameInfo = $this->getRegionInfo($regionName);
        $cityNameInfo = $this->getSettlementInfo($cityName);

        if($cityName) foreach($citylist as $cityId => $city) {

            //$cityInfo = $this->getSettlementInfo($city['name']);
            if($city['name'] != ($cityNameInfo['name'] ?: $cityName)) unset($citylist[$cityId]);

        }

        if($kladr) foreach($citylist as $cityId => $city) {

            if ($citylist[$cityId]['kladr'] != $kladr) unset($citylist[$cityId]);

        }


        //if($regionNameInfo['name'] == $cityNameInfo['name']) unset($regionName);

        if($regionName) foreach($citylist as $cityId => $city) {

            if($citylist[$cityId]['RegionId']) $regionInfo = $this->getRegionInfo(null, null, null, $citylist[$cityId]['RegionId']);
            else $regionInfo = $this->getRegionInfo($citylist[$cityId]['RegionName'] ?: $citylist[$cityId]['name']);

            if($regionNameInfo['code'] != $regionInfo['code']) unset($citylist[$cityId]);

        }

        foreach($citylist as $cityId => $city) if(!$city['name']) {

            unset($citylist[$cityId]);
            $errorInfo[] = 'Получен населенный пункт без имени: ' . print_r($city, true);

        }

        if(!sizeof($citylist)) {

            $answer['errors']['error']['code'] = "_2";
            $answer['errors']['error']['message'] = "Не удалось получить данные ни одного подходящего населенного пункта.";
            if(isset($errorInfo)) $answer['errors']['error']['info'] = $errorInfo;

            return $answer;

        }

        $citylist = array_values($citylist);


        return get_defined_vars();

    }


    public function parcelShopsListPreProcessing($get_defined_vars) {

        extract($get_defined_vars);


        $cacheKey = $_SERVER['HTTP_HOST'].$namespace."parcelShopsList";

        if($this->memcache) $parcelShopsList = $this->memcache->get($cacheKey);


        return get_defined_vars();

    }
    public function parcelShopsListPostProcessing($get_defined_vars) {

        extract($get_defined_vars);


        if($cityName) foreach($parcelShopsList as $parcelShopId => $parcelShop) {

            if($parcelShop["CitiName"] != $cityName) unset($parcelShopsList[$parcelShopId]);

        }

        if($regionName == $cityName) unset($regionName);

        if($regionName) foreach($parcelShopsList as $parcelShopId => $parcelShop) {

            $region1 = $this->getRegionInfo($regionName);
            $region2 = $this->getRegionInfo($parcelShop['Region']);

            if($region1['code'] != $region2['code']) unset($parcelShopsList[$parcelShopId]);

        }

        if($number) foreach($parcelShopsList as $parcelShopId => $parcelShop) {

            if($parcelShop["Number"] != $number) unset($parcelShopsList[$parcelShopId]);

        }

        if($tariff && $parcelShop["tariffs"]) foreach($parcelShopsList as $parcelShopId => $parcelShop) {

            if(!in_array($tariff, $parcelShop["tariffs"])) unset($parcelShopsList[$parcelShopId]);

        }

        foreach($parcelShopsList as $parcelShopId => $parcelShop) {

            if ($parcelShopsList[$parcelShopId]['MaxWeight'] && $weight && ($weight > $parcelShopsList[$parcelShopId]['MaxWeight'])) unset($parcelShopsList[$parcelShopId]);
            if ($parcelShop['MaxSize'] && $size && ($size > $parcelShopsList[$parcelShopId]['MaxSize'])) unset($parcelShopsList[$parcelShopId]);
            if ($payment == 'card' && !$parcelShop['Card']) unset($parcelShopsList[$parcelShopId]);

        }

        $parcelShopsListReturn = [];

        foreach($parcelShopsList as $parcelShopId => $parcelShop) if($parcelShop['Number']) {

            $parcelShopsListReturn[$parcelShopId] = $parcelShop;


        } else $errorInfo[] = 'Получен населенный пункт без имени: ' . print_r($parcelShop, true);

        if(!$parcelShopsListReturn) {

            $answer['errors']['error']['code'] = "_2";
            $answer['errors']['error']['message'] = "Не удалось получить данные ни одного ПВЗ.";
            if(isset($errorInfo)) $answer['errors']['error']['info'] = $errorInfo;

            return $answer;

        }

        $parcelShopsListReturn = array_values($parcelShopsListReturn);


        return get_defined_vars();

    }


    public function tariffListPreProcessing($get_defined_vars) {

        extract($get_defined_vars);





        return get_defined_vars();

    }
    public function tariffListPostProcessing($get_defined_vars) {

        extract($get_defined_vars);


        foreach($tariffList as $tariffId => $tariff) if(!$tariff['name']) {

            unset($tariffList[$tariffId]);
            $errorInfo[] = 'Получен тариф без имени: ' . print_r($tariff, true);

        }

        if(!$tariffList) {

            $answer['error']['code'] = "_2";
            $answer['error']['message'] = "Не удалось получить данные ни одного тарифа.";
            if(isset($errorInfo)) $answer['error']['info'] = $errorInfo;

            return $answer;

        }

        $tariffList = array_values($tariffList);


        return get_defined_vars();

    }


    public function errorListPreProcessing($get_defined_vars) {

        extract($get_defined_vars);





        return get_defined_vars();

    }
    public function errorListPostProcessing($get_defined_vars) {

        extract($get_defined_vars);


        $selferrors[0]['Error'] = "_0";
        $selferrors[0]['ErrorText'] = "Вызываемый метод не существует.";

        $selferrors[1]['Error'] = "_1";
        $selferrors[1]['ErrorText'] = "Не удалось получить данные от сервиса ТК.";

        $selferrors[2]['Error'] = "_2";
        $selferrors[2]['ErrorText'] = "Сервис ТК вернул некорректный ответ.";

        $selferrors[3]['Error'] = "_3";
        $selferrors[3]['ErrorText'] = "Некорректные входные данные.";


        $errors = array_merge($selferrors, $errors);
        $errors = array_values($errors);


        return get_defined_vars();

    }


    public function statusListPreProcessing($get_defined_vars) {

        extract($get_defined_vars);





        return get_defined_vars();

    }
    public function statusListPostProcessing($get_defined_vars) {

        extract($get_defined_vars);

        foreach($statusList as $statusId => $status) if(!$status['StateText']) {

            unset($statusList[$statusId]);
            $errorInfo[] = 'Получен статус пункт без описания: ' . print_r($statusId, true);

        }

        if(!$statusList) {

            $answer['error']['code'] = "_2";
            $answer['error']['message'] = "Не удалось получить данные ни одного статуса.";
            if(isset($errorInfo)) $answer['error']['info'] = $errorInfo;

            return $answer;

        }

        $statusList = array_values($statusList);


        return get_defined_vars();

    }

}