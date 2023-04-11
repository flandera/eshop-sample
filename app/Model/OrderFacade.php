<?php

declare(strict_types=1);

namespace App\Model;

use Nette;

final class OrderFacade
{
	public function __construct(
		private Nette\Database\Explorer $database,
	) {
	}


	public function saveOrder(Order $order): bool
	{
		return is_numeric($this->database
			->table('orders')
			->insert([
				'name' => $order->getName(),
				'phone' => $order->getPhone(),
				'email' => $order->getEmail(),
				'total' => $order->getTotal(),
				'products' => $order->getProducts(),
				'dateOrdered' => $order->getDateOrdered(),
			]));
	}


	/**
	 * @return array<int, string>
	 */
	public function getOrders(): array
	{
		return $this->database->fetchAll('SELECT * FROM orders');
	}


	public function createData(): void
	{
		$this->database->query('CREATE table IF NOT EXISTS orders ("id" INTEGER NOT NULL PRIMARY KEY, "name" VARCHAR, "phone" VARCHAR, "email" VARCHAR, "total" FLOAT, "products" TEXT, "dateOrdered" TEXT )');
	}
}
