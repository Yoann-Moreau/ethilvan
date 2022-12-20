<?php

namespace App\Service;

class PaginationService {

	public function getPages(int $number_of_elements, int $elements_per_page, int $current_page): array {
		$number_of_pages = (int)ceil($number_of_elements / $elements_per_page);

		$pages = [];
		for ($i = 1; $i <= $number_of_pages; $i++) {
			// Keep only first page, last page, and pages close to current page
			if ($i === 1 || $i === $number_of_pages || ($i > $current_page - 3 && $i < $current_page + 3)) {
				$pages[] = $i;
			}
		}

		return $pages;
	}

}
