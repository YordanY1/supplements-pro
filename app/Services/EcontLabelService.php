<?php

namespace App\Services;

use Gdinko\Econt\Facades\Econt;
use Gdinko\Econt\Hydrators\Label;
use App\Models\Order;
use App\Data\ShippingData;
use Illuminate\Support\Facades\Log;

class EcontLabelService
{
    /**
     * Calculate shipping price based on user-provided data (not saved Order).
     * This uses Econt's 'calculate' API method to simulate the delivery cost.
     *
     * @param ShippingData $data
     * @return float|null
     */
    public static function calculateShipping(ShippingData $data): ?float
    {
        Log::debug('📥 calculateShipping input', [
            'city' => $data->city,
            'zip' => $data->zip,
            'street' => $data->street,
            'weight' => $data->weight,
        ]);

        try {
            // Prepare label data without cash-on-delivery
            $labelData = self::buildLabelData($data);
            Log::debug('📤 Sending calculate request to Econt', $labelData);

            // Use 'calculate' method to simulate shipping cost
            $label = new Label($labelData, 'calculate');
            $response = Econt::createLabel($label);

            Log::debug('📬 Econt response (calculate)', $response);

            $amount = $response['label']['totalPrice'] ?? null;
            Log::info('✅ Calculated shipping price', ['amount' => $amount]);

            return $amount;
        } catch (\Throwable $e) {
            if (method_exists($e, 'getResponse')) {
                $json = json_decode($e->getResponse()?->getBody(), true);
                Log::error('📨 Econt error response', [
                    'type' => $json['type'] ?? 'unknown',
                    'message' => $json['message'] ?? '',
                    'fields' => $json['fields'] ?? [],
                    'innerErrors' => $json['innerErrors'] ?? [],
                ]);
            }

            Log::error('❌ Error in calculateShipping', [
                'message'  => $e->getMessage(),
                'trace'    => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    /**
     * Create a shipping label using Econt API after a successful order.
     * This actually registers the shipment.
     *
     * @param Order $order
     * @param float $amount Total price including products and shipping
     * @return array|null
     */
    public static function createLabel(Order $order, float $amount): ?array
    {
        Log::debug('📥 createLabel input', [
            'order_id' => $order->id,
            'amount' => $amount,
        ]);

        try {
            // Prepare data for actual label creation
            $labelData = self::buildLabelData($order, $amount);
            Log::debug('📤 Sending create request to Econt', $labelData);

            // Use 'create' method to generate a label
            $label = new Label($labelData, 'create');
            $response = Econt::createLabel($label);

            Log::info('✅ Label successfully created', [
                'order_id' => $order->id,
                'response' => $response,
            ]);

            return $response;
        } catch (\Throwable $e) {
            Log::error('❌ Error in createLabel', [
                'order_id' => $order->id,
                'message'  => $e->getMessage(),
                'trace'    => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Builds the full data array needed to calculate or create a shipment label via Econt.
     * This data includes sender and receiver info, address formatting, and optional COD setup.
     *
     * @param Order|ShippingData $data  The data instance (Order or DTO) containing shipping info.
     * @param float $amount The total amount to be collected on delivery (used only if payment is cash).
     * @return array        The structured data to send to Econt API.
     */
    protected static function buildLabelData($data, float $amount = 0): array
    {

        if (!isset($data->weight) || $data->weight <= 0) {
            Log::warning('⚠️ Weight not properly set in Econt label data', [
                'source' => $data instanceof Order ? 'Order' : 'ShippingData',
                'weight' => $data->weight ?? null,
            ]);
        }

        $sender = config('econt.sender');

        // Detect type: Order or ShippingData
        if ($data instanceof Order) {
            $receiverName = "{$data->first_name} {$data->last_name}";
            $paymentMethod = $data->payment_method;
        } else {
            $receiverName = $data->receiver_name ?? 'Клиент';
            $paymentMethod = 'card'; // default or from DTO in бъдеще
        }

        $streetName = $data->street;
        $streetNum = 1;

        if (preg_match('/^(.*?)[\s,]+(\d+)$/u', $data->street, $matches)) {
            $streetName = $matches[1];
            $streetNum = (int) $matches[2];
        } else {
            Log::warning('⚠️ Failed to parse street', ['street' => $data->street]);
        }

        $services = ['smsNotification' => true];

        if ($paymentMethod === 'cash') {
            $services['cdAmount'] = number_format($amount, 2, '.', '');
            $services['cdType'] = 'get';
            $services['cdCurrency'] = 'BGN';
        }

        return [
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
                'phones' => [$data->phone],
            ],
            'receiverAddress' => [
                'city' => [
                    'country'  => ['code3' => 'BGR'],
                    'name'     => $data->city,
                    'postCode' => (int) $data->zip,
                ],
                'street' => $streetName,
                'num'    => $streetNum,
            ],
            'packCount'           => 1,
            'shipmentType'        => 'PACK',
            'weight' => $data->weight,
            'shipmentDescription' => 'Поръчка от онлайн магазин',
            'services'            => $services,
            'payAfterAccept'      => false,
            'payAfterTest'        => false,
            'holidayDeliveryDay'  => 'workday',
        ];
    }
}
