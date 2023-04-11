<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Utils\DateTime;

class Order
{
	public function getName(): string
	{
		return $this->name;
	}


	public function setName(string $name): self
	{
		$this->name = $name;
		return $this;
	}


	public function getPhone(): string
	{
		return $this->phone;
	}


	public function setPhone(string $phone): self
	{
		$this->phone = $phone;
		return $this;
	}


	public function getEmail(): string
	{
		return $this->email;
	}


	public function setEmail(string $email): self
	{
		$this->email = $email;
		return $this;
	}


	public function getTotal(): float
	{
		return $this->total;
	}


	public function setTotal(float $total): self
	{
		$this->total = $total;
		return $this;
	}


	public function getProducts(): string
	{
		return $this->products;
	}


	/**
	 * @param array<int, string> $products
	 */
	public function setProducts(array $products): self
	{
		$this->products = json_encode($products) !== false ? json_encode($products) : '';
		return $this;
	}


	public function getDateOrdered(): DateTime
	{
		return DateTime::from($this->dateOrdered);
	}


	public function setDateOrdered(DateTime $dateOrdered): self
	{
		$this->dateOrdered = $dateOrdered->format(DATE_ATOM);
		return $this;
	}


	public function __construct(
		private string $name,
		private string $phone,
		private string $email,
		private float $total,
		private string $products,
		private string $dateOrdered,
	) {

	}
}
