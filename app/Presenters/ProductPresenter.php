<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\ExchangeRateFacade;
use App\Model\LabelFacade;
use App\Model\OrderFacade;
use App\Model\ProductFacade;
use Nette;


final class ProductPresenter extends Nette\Application\UI\Presenter
{
	use Nette\SmartObject;

	public const COUNTRY_EMU = 'EMU';
	private Nette\Http\SessionSection $section;
	private ?float $emuCourse;


	public function __construct(
		private ProductFacade $productFacade,
		private LabelFacade $labelFacade,
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


	public function actionCreate(): void
	{
		$this->orderFacade->createData();
		$this->productFacade->createTable();
		$this->productFacade->createData();
		$this->labelFacade->createTable();
		$this->labelFacade->createData();
		$this->redirect('Product:list');
	}


	public function actionList(): void
	{
		$this->renderList();
	}


	public function actionShow(int $productId): void
	{
		$this->renderShow($productId);
	}


	public function renderList(): void
	{
		$products = $this->productFacade->getProducts();
		$count = count($this->section->get('products') ?? []);
		$price = $this->section->get('price') ?? 0;
		if (!empty($products)) {
			foreach ($products as $product) {
				$product->labels = $this->labelFacade->getLabelsForProduct($product->id);
				$product->emuPrice = $this->emuCourse !== null
					? round($product->price / $this->emuCourse, 1)
					: 0;

			}
		}
		$this->template->products = $products;
		$this->template->count = $count;
		$this->template->price = $price;
	}


	public function renderShow(int $productId): void
	{
		$product = $this->productFacade->getProduct($productId);
		$product->labels = $this->labelFacade->getLabelsForProduct($product->id);
		$product->emuPrice = $this->emuCourse !== null
			? round($product->price / $this->emuCourse, 1)
			: 0;
		$this->template->product = $product;
	}


	public function handleUpdate(int $id): void
	{
		$productsInCart = $this->section->get('products');
		$productsPricesInCart = $this->section->get('price');
		$productsInCart[] = $id;
		$price = $this->productFacade->getProductPrice($id);
		$productsPricesInCart += $price->price;
		$this->section->set('products', $productsInCart);
		$this->section->set('price', $productsPricesInCart);
		$this->redrawControl('nav');
	}
}
