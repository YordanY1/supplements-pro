<?php

namespace App\Services;

use Gdinko\Econt\Facades\Econt;
use Gdinko\Econt\Hydrators\Label;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

class EcontLabelService
{
    /**
     * Calculate shipping price based on order details without creating a shipping label.
     * This uses Econt's 'calculate' API method to simulate the delivery cost.
     *
     * @param Order $order
     * @return float|null
     */
    public static function calculateShipping(Order $order): ?float
    {
        Log::debug('📥 calculateShipping input', [
            'city' => $order->city,
            'zip' => $order->zip,
            'street' => $order->street,
        ]);

        try {
            // Prepare label data without cash-on-delivery
            $labelData = self::buildLabelData($order);
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
     * @param Order $order  The order instance containing user-provided shipping info.
     * @param float $amount The total amount to be collected on delivery (used only if payment is cash).
     * @return array        The structured data to send to Econt API.
     */
    protected static function buildLabelData(Order $order, float $amount = 0): array
    {
        // Load sender data from config (e.g. name, phone, city, zip, street, num)
        $sender = config('econt.sender');

        // Format receiver full name from first and last names
        $receiverName = "{$order->first_name} {$order->last_name}";

        // Attempt to split the street into name and number
        $streetName = $order->street;
        $streetNum = 1;

        // Try to extract street name and number (e.g. "Mesta 12" => "Mesta", 12)
        if (preg_match('/^(.*?)[\s,]+(\d+)$/u', $order->street, $matches)) {
            $streetName = $matches[1];
            $streetNum = (int) $matches[2];
        } else {
            // Log if the address couldn't be parsed (still proceeds with defaults)
            Log::warning('⚠️ Failed to parse street', ['street' => $order->street]);
        }

        // Define base services - SMS notification is always enabled
        $services = ['smsNotification' => true];

        // If the order is cash on delivery, add COD-specific fields
        if ($order->payment_method === 'cash') {
            $services['cdAmount'] = number_format($amount, 2, '.', '');  // Amount to collect
            $services['cdType'] = 'get';                                  // Receiver pays
            $services['cdCurrency'] = 'BGN';                              // Currency is Bulgarian lev
        }

        // Return the full label data array, structured for Econt API
        return [
            'senderClient' => [
                'name'   => $sender['name'],                  // Sender company or person name
                'phones' => [$sender['phone']],               // Sender phone number as array
            ],
            'senderAddress' => [
                'city' => [
                    'country'  => ['code3' => 'BGR'],         // 3-letter ISO code for Bulgaria
                    'name'     => $sender['city'],            // Sender city name
                    'postCode' => (int) $sender['zip'],       // Sender postal code
                ],
                'street' => $sender['street'],                // Sender street name
                'num'    => (int) $sender['num'],             // Sender street number
            ],
            'receiverClient' => [
                'name'   => $receiverName,                    // Receiver full name
                'phones' => [$order->phone],                  // Receiver phone number as array
            ],
            'receiverAddress' => [
                'city' => [
                    'country'  => ['code3' => 'BGR'],         // Receiver country (always Bulgaria)
                    'name'     => $order->city,               // Receiver city
                    'postCode' => (int) $order->zip,          // Receiver ZIP code
                ],
                'street' => $streetName,                      // Extracted street name
                'num'    => $streetNum,                       // Extracted or fallback street number
            ],
            'packCount'           => 1,                       // Always sending one package
            'shipmentType'        => 'PACK',                  // Standard package type
            'weight'              => 1.0,                     // Default package weight in kg
            'shipmentDescription' => 'Поръчка от онлайн магазин',  // Short shipment note
            'services'            => $services,               // Econt services (SMS, COD)
            'payAfterAccept'      => false,                   // Do not use "Pay after accept" service
            'payAfterTest'        => false,                   // Do not allow test before pay
            'holidayDeliveryDay'  => 'workday',               // Allow delivery on working days only
        ];
    }
}
