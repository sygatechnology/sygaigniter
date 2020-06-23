<?php namespace App\API\v1\Services;

class ApiResultService
{

	public function set($rows, $total, $limit = 0, $numPage = 1)
	{
		return [
			'data' => $rows,
			'totalPages' => $this->getPageCount($total, $limit),
			'currentPage' => $numPage,
			'totalResult' => count($rows),
			'total' => $total
		];
	}

	public function setFromRows($rows, $limit = 0, $numPage = 1)
	{
		$total = count($rows);
		$result = $this->getLimitidedResultFromAllRows($rows, $limit, ($numPage-1));
		return [
			'data' => $result,
			'totalPages' => $this->getPageCount($total, $limit),
			'currentPage' => $numPage,
			'totalResult' => count($result),
			'total' => $total
		];
	}

	private function getLimitidedResultFromAllRows($rows, $limit, $numPage){
		return $limit > 0 ? array_slice($rows, $numPage*$limit, $limit) : $rows;
	}

	private function getPageCount($total, $limit){
		if($limit > 0){
			$mod = $total % $limit;
			$div = $total / $limit;
			$seg = explode('.', $div);
			$rest = ( $mod > 0 ) ? 1 : 0;
			return (count($seg) > 1) ? (int)$seg[0]+$rest : (int)$seg[0];
		}
		return 1;
	}
}
