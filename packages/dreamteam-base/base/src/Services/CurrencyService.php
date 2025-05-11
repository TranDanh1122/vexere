<?php

namespace DreamTeam\Base\Services;

use DreamTeam\Base\Repositories\Interfaces\CurrencyRepositoryInterface;
use DreamTeam\Base\Services\Interfaces\CurrencyServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use DreamTeam\Base\Services\CrudService;
use CurrencyHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CurrencyService extends CrudService implements CurrencyServiceInterface
{

    public function __construct(
        CurrencyRepositoryInterface $repository,
    )
    {
        $this->repository = $repository;
    }

    public function execute(array $currencies, array $deletedCurrencies): array
    {
        $validated = Validator::make(
            $currencies,
            [
                '*.title' => 'required|string|' . Rule::in(CurrencyHelper::currencyCodes()),
                '*.symbol' => 'required|string',
            ],
            [
                '*.title.in' => trans('Ecommerce::currency.invalid_currency_name', [
                    'currencies' => implode(', ', CurrencyHelper::currencyCodes()),
                ]),
            ],
            [
                '*.title' => trans('Ecommerce::currency.invalid_currency_name'),
                '*.symbol' => trans('Ecommerce::currency.symbol'),
            ]
        );

        if ($validated->fails()) {
            return [
                'error' => true,
                'message' => $validated->getMessageBag()->first(),
            ];
        }

        if ($deletedCurrencies) {
            $this->repository->deleteMultipleFromPrimaries($deletedCurrencies);
        }

        foreach ($currencies as $item) {
            if (! $item['title'] || ! $item['symbol']) {
                continue;
            }

            $item['title'] = substr(strtoupper($item['title']), 0, 3);
            $item['decimals'] = (int)$item['decimals'];
            $item['decimals'] = $item['decimals'] < 10 ? $item['decimals'] : 2;

            if (count($currencies) == 1) {
                $item['is_default'] = 1;
            }

            $currency = $this->repository->findOneByPrimary($item['id'], false);

            if (! $currency) {
                $this->repository->createOrUpdateFromArray($item);
            } else {
                $currency->fill($item);
                $this->repository->createOrUpdateFromArray($currency->toArray());
            }
        }

        return [
            'error' => false,
        ];
    }
}