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
		'core\exceptions\VHttpEx' => "classes/core/exceptions/class.VHttpEx.php",

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

		// promotions
		'promotions\VPromotions' => "classes/promotions/class.VPromotions.php",
		
		// geo
		'geo\VGeo' => "classes/geo/class.VGeo.php",

		// likes
		'like\VLike' => "classes/likes/class.VLike.php",

		//notice
		'notice\VNotice' => "classes/notice/class.VNotice.php",

		//search
		'search\VSearch' => "classes/search/class.VSearch.php",
		'SphinxClient' => "classes/search/sphinxapi.php",

		//comments
		'comments\VComment' => "classes/comments/class.VComment.php",
		//comments
		'core\validate\VValidate' => "classes/core/validate/class.VValidate.php",

		// bitrix
		'core\bx\VDatabaseAdapter' => "classes/core/bx/class.VDatabaseAdapter.php",

		// types
		'core\types\VArray' => "classes/core/types/class.VArray.php",
		'core\types\VString' => "classes/core/types/class.VString.php",
		'core\types\VDate' => "classes/core/types/class.VDate.php",
		'core\http\VHttp' => "classes/core/http/class.VHttp.php",

		// forms
		'core\utils\forms\VForm' => "classes/core/utils/forms/class.VForm.php",
		'core\utils\forms\VField' => "classes/core/utils/forms/class.VField.php",
		'core\utils\forms\VSubmit' => "classes/core/utils/forms/class.VSubmit.php",
		'core\utils\forms\VText' => "classes/core/utils/forms/class.VText.php",
		'core\utils\forms\VTextarea' => "classes/core/utils/forms/class.VTextarea.php",
		'core\utils\forms\VHidden' => "classes/core/utils/forms/class.VHidden.php",
		'core\utils\forms\VToken' => "classes/core/utils/forms/class.VToken.php",

		//bitrix
		'bitrix\VNavigation'=>"classes/bitrix/class.VNavigation.php",
		'bitrix\VNavigationArray'=>"classes/bitrix/class.VNavigationArray.php",
		'bitrix\ERESULT'=>"classes/bitrix/class.ERESULT.php",

		//request
		'request\VRequest'=>"classes/request/class.VRequest.php",
		'request\VCompany'=>"classes/request/class.VCompany.php",
		'request\VUser'=>"classes/request/class.VUser.php",
		'request\exceptions\VRequest'=>"classes/request/exceptions/class.VRequest.php",

		//filters
		'filters\decorators\VDecorator'=>'classes/filters/decorators/class.VDecorator.php',
		'filters\VFilter'=>'classes/filters/interface.VFilter.php',
		'filters\VChangeable'=>'classes/filters/interface.VChangeable.php',
		'filters\VSession'=>'classes/filters/class.VSession.php',
		'filters\VQuery'=>'classes/filters/class.VQuery.php',
		'filters\VCreator'=>'classes/filters/class.VCreator.php',
		'filters\decorators\VClinic'=>'classes/filters/decorators/class.VClinic.php',
		'filters\decorators\VPromotions'=>'classes/filters/decorators/class.VPromotions.php',
		'filters\decorators\VSponsors'=>'classes/filters/decorators/class.VSponsors.php',
		'filters\decorators\VEvents'=>'classes/filters/decorators/class.VEvents.php',
		'filters\decorators\VTrainingsCenters'=>'classes/filters/decorators/class.VTrainingsCenters.php',
		'filters\decorators\VTrainings'=>'classes/filters/decorators/class.VTrainings.php',
		'filters\decorators\VPreparationsMakers'=>'classes/filters/decorators/class.VPreparationsMakers.php',
		'filters\decorators\VApparatuses'=>'classes/filters/decorators/class.VApparatuses.php',
		'filters\decorators\VApparatusesMakers'=>'classes/filters/decorators/class.VApparatusesMakers.php',
		'filters\decorators\VPreparations'=>'classes/filters/decorators/class.VPreparations.php',
		'filters\decorators\VThreads'=>'classes/filters/decorators/class.VThreads.php',
		'filters\decorators\VImplants'=>'classes/filters/decorators/class.VImplants.php',
		'filters\decorators\VProfessionals'=>'classes/filters/decorators/class.VProfessionals.php',
		'filters\decorators\VThreadsMakers'=>'classes/filters/decorators/class.VThreadsMakers.php',

		//subscribe
		'subscribe\owners\VOwner'=>'classes/subscribe/owners/class.VOwner.php',
		'subscribe\owners\VCreator'=>'classes/subscribe/owners/class.VCreator.php',
		'subscribe\owners\VOwnerCollection'=>'classes/subscribe/owners/class.VOwnerCollection.php',
		'subscribe\owners\VOwnerEvents'=>'classes/subscribe/owners/interface.VOwnerEvents.php',
		'subscribe\mailers\VMailer'=>'classes/subscribe/mailers/class.VMailer.php',
		'subscribe\mailers\VPromotions'=>'classes/subscribe/mailers/class.VPromotions.php',
		'subscribe\mailers\VTrainings'=>'classes/subscribe/mailers/class.VTrainings.php',
		'subscribe\factories\VFactory'=>'classes/subscribe/factories/interface.VFactory.php',
		'subscribe\factories\VPromotions'=>'classes/subscribe/factories/class.VPromotions.php',
		'subscribe\factories\VTrainings'=>'classes/subscribe/factories/class.VTrainings.php',
		'subscribe\exceptions\VEventEx'=>'classes/subscribe/exceptions/class.VEventEx.php',
		'subscribe\exceptions\VFactoryEx'=>'classes/subscribe/exceptions/class.VFactoryEx.php',
		'subscribe\exceptions\VOwnerCreatorEx'=>'classes/subscribe/exceptions/class.VOwnerCreatorEx.php',
		'subscribe\exceptions\VOwnerEx'=>'classes/subscribe/exceptions/class.VOwnerEx.php',
		'subscribe\events\VAggregator'=>'classes/subscribe/events/interface.VAggregator.php',
		'subscribe\events\VPromotions'=>'classes/subscribe/events/class.VPromotions.php',
		'subscribe\events\VTrainings'=>'classes/subscribe/events/class.VTrainings.php',
		'subscribe\events\VEvent'=>'classes/subscribe/events/class.VEvent.php',

		// pay
		'pay\VProtocol'=>'classes/pay/class.VProtocol.php',
		'pay\VReceipt'=>'classes/pay/class.VReceipt.php',
		'pay\VReceiptEx'=>'classes/pay/class.VReceiptEx.php',
		'pay\VSecure'=>'classes/pay/class.VSecure.php',

	)
);