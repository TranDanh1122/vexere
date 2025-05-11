<?php

namespace DreamTeam\Base\Services\Interfaces;
use DreamTeam\Base\Services\Interfaces\CrudServiceInterface;

interface CurrencyServiceInterface extends CrudServiceInterface
{
	public function execute(array $currencies, array $deletedCurrencies): array;
}
