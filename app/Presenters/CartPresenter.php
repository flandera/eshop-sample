<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\ExchangeRateFacade;
use App\Model\Order;
use App\Model\OrderFacade;
use App\Model\ProductFacade;
use Nette;
use Nette\Application\UI\Form;
use Nette\Database\Row;
use Nette\Utils\ArrayHash;


final class CartPresenter extends Nette\Application\UI\Presenter
{
	use Nette\SmartObject;

	public const COUNTRY_EMU = 'EMU';
	private Nette\Http\SessionSection $section;
	private ?float $emuCourse;


	public function __construct(
		private ProductFacade $productFacade,
		private OrderFacade $orderFacade,
		private ExchangeRateFacade $exchangeRateFacade,
	) {
		parent::__construct();
	}


	public function startup()
	{
		parent::startup();
		$this->section = $this->session->getSection('cart');
		$this->emuCourse = $this->section->get('emucourse') ?? $this->exchangeRateFacade->getCourse(self::COUNTRY_EMU);
	}


	public function actionList(): void
	{
		$this->renderList();
	}


	public function renderList(): void
	{
		$products = $this->getOrderProducts();
		$total = $this->getTotalPrice($products);
		$this->template->products = $products;
		$this->template->total = $total;
	}


	/**
	 * @return array<int|string, Row|null>
	 */
	public function getOrderProducts(): array
	{
		$productsInCart = $this->section->get('products');
		$products = [];
		$total = 0;
		if (!empty($productsInCart)) {
			foreach ($productsInCart as $productId) {
				$product = $this->productFacade->getProduct($productId);
				$product->emuPrice = $this->emuCourse !== null
					? round($product->price / $this->emuCourse, 1)
					: 0;
				$product->count = isset($products[$productId]) ? $products[$productId]->count + 1 : 1;
				$products[$productId] = $product;
			}
		}
		return $products;
	}


	protected function createComponentOrderForm(): Form
	{
		$form = new Form;
		$form->addEmail('email', 'Email:')
			->setRequired()
			->setHtmlAttribute('placeholder', 'your@email.domain');
		$form->addText('name', 'Name:')
			->setRequired()
			->setHtmlAttribute('placeholder', 'Your Name');
		$form->addText('phone', 'Phone')
			->setRequired()
			->addRule(fn($value) => preg_match('/^(\+420)? ?[1-9][0-9]{2} ?[0-9]{3} ?[0-9]{3}$/', $value->getValue()), 'Invalid Phone number - must be in form of: +420 111 111 111')
			->setHtmlAttribute('placeholder', '111 111 111');
		$form->addSubmit('send', 'Order');
		$form->onSuccess[] = [$this, 'formSucceeded'];
		return $form;
	}


	public function formSucceeded(Form $form, arrayHash $data): void
	{
		$products = $this->getOrderProducts();
		$total = $this->getTotalPrice($products);
		$order = new Order(
			$data->name,
			$data->phone,
			$data->email,
			$total,
			json_encode($products) !== false ? json_encode($products) : '',
			(new Nette\Utils\DateTime())->format(DATE_ATOM),
		);
		$this->orderFacade->saveOrder($order);
		$this->section->set('products', null);
		$this->section->set('price', null);
		$this->flashMessage('Order succesfully created:-)');
		$this->redirect('Product:list');
	}


	/**
	 * @param array<int|string, Row|null> $products
	 */
	private function getTotalPrice(array $products): float
	{
		$total = 0;
		foreach ($products as $product) {
			$total += $product->price * $product->count;
		}
		return $total;
	}
}
