<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Http\Session;

class ExchangeRateFacade
{
	private string $day;

	/** @var array <int, array<int, string>> */
	private array $courseList;
	private string $fileName;


	public function __construct(
		private string $url,
		private ?string $appDir,
		private Session $session,
	) {
		$this->day = (new \DateTime())->format('Y-m-d');
        /** @codingStandardsIgnoreStart */
		$this->fileName = $this->appDir . '/../temp/files/' . $this->day . '-' . 'courses.txt';
        /** @codingStandardsIgnoreEnd */
	}


	private function getCourseList(): void
	{
		if (@file_get_contents($this->fileName) === false) {
			$this->downloadCourses();
		}
		$this->parseList($this->fileName);
	}


	public function getCourse(string $country): ?float
	{
		$this->getCourseList();
		foreach ($this->courseList as $courseCountry) {
			if ($courseCountry[0] === $country) {
				$stringPrice = preg_replace('/\n/', '', $courseCountry[4]);
				$stringPrice = preg_replace('/,/', '.', $stringPrice);
				if (is_numeric($stringPrice)) {
					$this->session->getSection('cart')->set('emucourse', (float) $stringPrice);
					return (float) $stringPrice;
				}
			}
		}
		return null;
	}


	private function parseList(string $inputFile): void
	{
		$courseList = [];
		$lines = file($inputFile);
		if ($lines) {
			foreach ($lines as $line) {
				$countryCourse = explode('|', $line);
				$courseList[] = $countryCourse;
			}
			$this->courseList = $courseList;
		}
	}


	private function downloadCourses(): void
	{
		file_put_contents($this->fileName, @file_get_contents($this->url));
	}
}
