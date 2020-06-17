<?

IncludeModuleLangFile(__FILE__);
use \Bitrix\Main\ModuleManager;
use Bitrix\Main\EventManager;

Class Sk_Locations extends CModule
{

    var $MODULE_ID = "sk.locations";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $errors;

    function __construct()
    {
        $this->MODULE_VERSION = "1.0.0";
        $this->MODULE_VERSION_DATE = "20.05.2020";
        $this->MODULE_NAME = "Геолокация пользователей";
        $this->MODULE_DESCRIPTION = "Модуль для хранения геолокации пользователя в highload инфоблок UserLocations";
    }

    function DoInstall()
    {
        $this->InstallDB();
        $this->InstallEvents();
        $this->InstallFiles();
        \Bitrix\Main\ModuleManager::RegisterModule("sk.locations");
		
		//добавление hl и полей
			$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList(
				array("filter" => array(
					'NAME' => 'UserLocations'
				))
			)->fetch();
			
			if (!isset($hlblock['ID']))
			{
				$result = \Bitrix\Highloadblock\HighloadBlockTable::add(array(
					'NAME' => 'UserLocations',
					'TABLE_NAME' => 'user_location',
				));
				if (!$result->isSuccess()) {
					$errors = $result->getErrorMessages();
				} else {
					$id = $result->getId();
				}

				$oUserTypeEntity    = new CUserTypeEntity();		 
				$aUserFields    = array(
					'ENTITY_ID'         => 'HLBLOCK_'.$id,
					'USER_TYPE_ID'      => 'string',
					'SORT'              => 500,
					'MULTIPLE'          => 'N',
					'MANDATORY'         => 'N',
					'SHOW_FILTER'       => 'N',
					'SHOW_IN_LIST'      => '',
					'EDIT_IN_LIST'      => '',
					'IS_SEARCHABLE'     => 'N',
					'SETTINGS'          => array(
							'DEFAULT_VALUE' => '',
							'SIZE'          => '20',
							'ROWS'          => '1',
							'MIN_LENGTH'    => '0',
							'MAX_LENGTH'    => '0',
							'REGEXP'        => '',
					),
				);

				$arUfProp = array("IP", "COUNTRY", "CITY", "SESSION");
				foreach($arUfProp as $prop){
					$aUserFieldsProp = $aUserFields;
					$aUserFieldsProp['FIELD_NAME'] = 'UF_'.$prop;
					$aUserFieldsProp['XML_ID'] = 'XML_ID_'.$prop.'_FIELD';
					$iUserFieldId =  $oUserTypeEntity -> Add( $aUserFieldsProp ); 
				}
			}
		  
		
        return true;
    }

    function DoUninstall()
    {
        $this->UnInstallDB();
        $this->UnInstallEvents();
        $this->UnInstallFiles();
        \Bitrix\Main\ModuleManager::UnRegisterModule("sk.locations");
			$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList(
			array("filter" => array(
				'NAME' => 'UserLocations'
			))
		)->fetch();
		if (isset($hlblock['ID']))
		{
			$result = \Bitrix\Highloadblock\HighloadBlockTable::delete(
				$hlblock['ID']
			);
			
		}
        return true;
    }

    function InstallDB()
    {
		return false;
    }

    function UnInstallDB()
    {
        return false;
    }

    function InstallEvents()
    {
      	EventManager::getInstance()->registerEventHandler(
			"main",
			"OnBeforeEndBufferContent",
			$this->MODULE_ID,
			"Sk\Locations\Main",
			"appendUserLocations"
		);

		return false;
    }

    function UnInstallEvents()
    {
        return true;
    }

    function InstallFiles()
    {
        return true;
    }

    function UnInstallFiles()
    {
        return true;
    }
}