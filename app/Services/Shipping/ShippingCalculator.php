<?php

namespace App\Services\Shipping;

use Gdinko\Econt\Enums\LabelMode;

class ShippingCalculator
{
    public function __construct(
        protected EcontLabelService $labelService
    ) {}

    /**
     * Calculate shipping price from Econt by given input
     */
    public function calculate(array $labelInput): float
    {
        $result = $this->labelService->submit($labelInput, LabelMode::CALCULATE);

        // Correctly extract the total price from the nested Econt response
        $price =
            $result['label']['totalPrice'] ??
            $result['label']['senderDueAmount'] ??
            $result['label']['services'][0]['price'] ??
            0.00;

        return (float) $price;
    }
}
