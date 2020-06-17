<?php

namespace Sk\Locations;

use Sk\Locations\DataTable;

use Bitrix\Main\Loader; 
Loader::includeModule("highloadblock"); 
use Bitrix\Highloadblock as HL; 
use Bitrix\Main\Entity;

class Main{

	public function appendUserLocations(){

		if(!$_SESSION["sk_loc_added"] && !defined("ADMIN_SECTION") && $ADMIN_SECTION !== true){

			// добавление записи
			$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList(
				array("filter" => array(
					'NAME' => 'UserLocations'
				))
			)->fetch();
			if (isset($hlblock['ID'])){
				$hb = HL\HighloadBlockTable::getById($hlblock['ID'])->fetch(); 

				$entity = HL\HighloadBlockTable::compileEntity($hb); 
				$entity_data_class = $entity->getDataClass(); 

				$data = array(
				  "UF_IP"=> \Bitrix\Main\Service\GeoIp\Manager::getRealIp(),
				  "UF_SESSION"=>$_SESSION['fixed_session_id'],
				  "UF_COUNTRY"=> \Bitrix\Main\Service\GeoIp\Manager::getCountryName(),
				  "UF_CITY"=>\Bitrix\Main\Service\GeoIp\Manager::getCityName()
			   );

			   $result = $entity_data_class::add($data);
			}
			$_SESSION["sk_loc_added"] = "Y";
		}

		return false;
	}
}