<?php
define('PREFIX','b_');
CModule::AddAutoloadClasses(
	"estelife",
	array(
		// compability classes
		'core\database\VSpecialQuery' => "classes/core/database/interface.VSpecialQuery.php",
		'core\database\VQuery' => "classes/core/database/interface.VQuery.php",
		'core\database\VResult' => "classes/core/database/interface.VResult.php",
		'core\database\VJoin' => "classes/core/database/interface.VJoin.php",
		'core\database\VFilter' => "classes/core/database/interface.VFilter.php",
		'core\database\VDriver' => "classes/core/database/interface.VDriver.php",
		'core\database\VQueryBuilder' => "classes/core/database/class.VQueryBuilder.php",
		'core\database\VListQueries' => "classes/core/database/class.VListQueries.php",
		'core\database\VDatabase' => "classes/core/database/class.VDatabase.php",
		'core\database\VFunction' => "classes/core/database/interface.VFunction.php",

		// mysql
		'core\database\mysql\VDriver' => "classes/core/database/mysql/class.VDriver.php",
		'core\database\mysql\VFilter' => "classes/core/database/mysql/class.VFilter.php",
		'core\database\mysql\VJoin' => "classes/core/database/mysql/class.VJoin.php",
		'core\database\mysql\VQuery' => "classes/core/database/mysql/class.VQuery.php",
		'core\database\mysql\VQueryBuilder' => "classes/core/database/mysql/class.VQueryBuilder.php",
		'core\database\mysql\VResult' => "classes/core/database/mysql/class.VResult.php",
		'core\database\mysql\VSpecialQuery' => "classes/core/database/mysql/class.VSpecialQuery.php",
		'core\database\mysql\VFunction' => "classes/core/database/mysql/class.VFunction.php",
		
		// exceptions
		'core\database\exceptions\VCollectionException' => "classes/core/database/exceptions/class.VCollectionException.php",
		'core\database\exceptions\VDatabaseException' => "classes/core/database/exceptions/class.VDatabaseException.php",
		'core\database\exceptions\VJoinException' => "classes/core/database/exceptions/class.VJoinException.php",
		'core\database\exceptions\VQueryBuildException' => "classes/core/database/exceptions/class.VQueryBuildException.php",
		'core\database\exceptions\VQueryException' => "classes/core/database/exceptions/class.VQueryException.php",
		'core\database\exceptions\VResultException' => "classes/core/database/exceptions/class.VResultException.php",

		// collections
		'core\database\collections\VCollection' => "classes/core/database/collections/class.VCollection.php",
		'core\database\collections\VListRecords' => "classes/core/database/collections/class.VListRecords.php",
		'core\database\collections\VManager' => "classes/core/database/collections/class.VManager.php",
		'core\database\collections\VMeta' => "classes/core/database/collections/class.VMeta.php",
		'core\database\collections\VRecord' => "classes/core/database/collections/class.VRecord.php",
		'core\database\collections\VRules' => "classes/core/database/collections/class.VRules.php",

		// exceptions
		'core\exceptions\VException' => "classes/core/exceptions/class.VException.php",
		'core\exceptions\VFormException' => "classes/core/exceptions/class.VFormException.php",

		// companies
		'companies\VCompanies' => "classes/companies/class.VCompanies.php",
		
		// geo
		'geo\VGeo' => "classes/geo/class.VGeo.php",

		// bitrix
		'core\bx\VDatabaseAdapter' => "classes/core/bx/class.VDatabaseAdapter.php",

		// types
		'core\types\VArray' => "classes/core/types/class.VArray.php",
		'core\types\VString' => "classes/core/types/class.VString.php",
		'core\types\VDate' => "classes/core/types/class.VDate.php",
		'core\http\VHttp' => "classes/core/http/class.VHttp.php",

		//bitrix
		'bitrix\VNavigation'=>"classes/bitrix/class.VNavigation.php",
		'bitrix\ERESULT'=>"classes/bitrix/class.ERESULT.php",

		// orm
		'orm\items\VItem' => "classes/orm/items/class.VItem.php",
		'orm\items\VClinic' => 'classes/orm/items/class.VClinic.php',
		'orm\VEntities' => 'classes/orm/class.VEntities.php'
	)
);