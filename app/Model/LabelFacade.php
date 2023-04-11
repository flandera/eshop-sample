<?php

declare(strict_types=1);

namespace App\Model;

use Nette;
use Nette\Database\Row;
use Nette\Database\Table\ActiveRow;

final class LabelFacade
{
	public function __construct(
		private Nette\Database\Explorer $database,
	) {
	}


	/**
	 * @return array<ActiveRow>
	 */
	public function getProducts(): array
	{
		return $this->database
			->table('labels')
			->order('name ASC')
			->fetchAll();
	}


	public function createTable(): void
	{
		$this->database->query('CREATE TABLE IF NOT EXISTS labels ("id" INTEGER NOT NULL PRIMARY KEY, "name" VARCHAR)');
		$this->database->query(
			'CREATE table IF NOT EXISTS products_labels
                ("id" INTEGER NOT NULL PRIMARY KEY, "labelId" INT, "productId" INT,
                FOREIGN KEY(labelId) REFERENCES labels(id),
                FOREIGN KEY(productId) REFERENCES products(id))',
		);
	}


	public function createData(): void
	{
		$data = [['id' => 1, 'name' => 'čerstvé'], ['id' => 2, 'name' => 'ovoce'], ['id' => 3, 'name' => 'šťavnaté']];
		$this->database
			->table('labels')
			->insert($data);

		$joinData = [['id' => 1, 'labelId' => 1, 'productId' => 1], ['id' => 2, 'labelId' => 2, 'productId' => 1], ['id' => 3, 'labelId' => 2, 'productId' => 2], ['id' => 4, 'labelId' => 3, 'productId' => 2]];
		$this->database
			->table('products_labels')
			->insert($joinData);
	}


	/**
	 * @return array<Row>
	 */
	public function getLabelsForProduct(int $id): array
	{
		return $this->database->query(
			'SELECT l.name from labels l
            LEFT JOIN products_labels pl ON pl.labelId = l.id
            WHERE pl.productId = ?',
			$id,
		)->fetchAll();
	}
}
