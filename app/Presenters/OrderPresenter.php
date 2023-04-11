<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\OrderFacade;
use Nette;
use Nette\Application\Responses\JsonResponse;


final class OrderPresenter extends Nette\Application\UI\Presenter
{
	use Nette\SmartObject;

	public function __construct(
		private OrderFacade $orderFacade,
	) {
		parent::__construct();
	}


	public function actionList(): JsonResponse
	{
		$this->sendJson($this->orderFacade->getOrders());
	}
}
