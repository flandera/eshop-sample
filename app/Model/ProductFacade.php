<?php

declare(strict_types=1);

namespace App\Model;

use Nette;
use Nette\Database\Row;

final class ProductFacade
{
	public function __construct(
		private Nette\Database\Explorer $database,
	) {
	}


	/**
	 * @return array<Row>
	 */
	public function getProducts(): array
	{
		return $this->database
			->query('
            SELECT p.id, p.name as name, p.price as price
            FROM products p
            ')->fetchAll();
	}


	public function createTable(): void
	{
		$this->database->query('CREATE TABLE IF NOT EXISTS products ("id" INTEGER NOT NULL PRIMARY KEY, "name" VARCHAR, "price" VARCHAR)');
		$this->database->query('DELETE FROM products');
	}


	public function createData(): void
	{
		$data = [['id' => 1, 'name' => 'jablko', 'price' => '24'], ['id' => 2, 'name' => 'hruška', 'price' => '25']];
		$this->database
			->table('products')
			->insert($data);
	}


	public function getProductPrice(int $productId): ?Row
	{
		return $this->database
			->query('
            SELECT p.price as price
            FROM products p
            WHERE p.id = ?
            ', $productId)->fetch();
	}


	public function getProduct(int $productId): ?Row
	{
		return $this->database
			->query('
            SELECT *
            FROM products p
            WHERE p.id = ?
            ', $productId)->fetch();
	}
}
