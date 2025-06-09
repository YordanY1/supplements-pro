<?php

namespace App\Services;

use Gdinko\Econt\Facades\Econt;
use Gdinko\Econt\Hydrators\Label;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

class EcontLabelService
{
    public static function createLabel(Order $order, float $amount): ?array
    {
        $sender = config('econt.sender');
        $receiverName = "{$order->first_name} {$order->last_name}";

        preg_match('/^(.*?)[\s,]+(\d+)$/u', $order->street, $matches);

        $streetName = $matches[1] ?? $order->street;
        $streetNum  = (int)($matches[2] ?? 1);


        $services = [
            'smsNotification' => true,
        ];

        if ($order->payment_method === 'cash') {
            $services['cdAmount']   = number_format($amount, 2, '.', '');
            $services['cdType']     = 'get';
            $services['cdCurrency'] = 'BGN';
        }

        $labelData = [
            'senderClient' => [
                'name'   => $sender['name'],
                'phones' => [$sender['phone']],
            ],
            'senderAddress' => [
                'city' => [
                    'country'  => ['code3' => 'BGR'],
                    'name'     => $sender['city'],
                    'postCode' => (int) $sender['zip'],
                ],
                'street' => $sender['street'],
                'num'    => (int) $sender['num'],
            ],
            'receiverClient' => [
                'name'   => $receiverName,
                'phones' => [$order->phone],
            ],
            'receiverAddress' => [
                'city' => [
                    'country'  => ['code3' => 'BGR'],
                    'name'     => $order->city,
                    'postCode' => (int) $order->zip,
                ],
                'street' => $streetName,
                'num'    => $streetNum,
            ],
            'packCount'           => 1,
            'shipmentType'        => 'pack',
            'weight'              => 1.0,
            'shipmentDescription' => 'Поръчка от онлайн магазин',
            'services'            => $services,
            'payAfterAccept'      => false,
            'payAfterTest'        => false,
            'holidayDeliveryDay'  => 'workday',
        ];

        Log::debug('📦 LabelData изпращано към Econt', $labelData);


        try {
            $label = new Label($labelData, 'create');
            $response = Econt::createLabel($label);
            Log::info('📦 Товарителницата е създадена успешно', ['response' => $response]);

            return $response;
        } catch (\Throwable $e) {
            Log::error('❌ Грешка при създаване на товарителница: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
}
