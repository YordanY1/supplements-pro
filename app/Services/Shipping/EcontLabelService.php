<?php

namespace App\Services\Shipping;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Gdinko\Econt\Facades\Econt;
use Gdinko\Econt\Models\CarrierEcontCity as EcontCity;
use Gdinko\Econt\Hydrators\Label;
use Gdinko\Econt\Enums\LabelMode;
use Gdinko\Econt\Enums\ShipmentType;
use Throwable;
use Illuminate\Http\Client\RequestException;


class EcontLabelService
{
    /**
     * Build Econt label payload (no courier time windows).
     * Adds detailed logs for debugging end-to-end.
     */
    public function buildLabelData(array $input): array
    {
        Log::debug('ECONT_LABEL_BUILD:start', [
            'input' => $input,
        ]);

        // Resolve receiver city (if provided by our PK)
        $receiverCityName = null;
        $receiverPostCode = null;

        if (!empty($input['receiver']['city_id'])) {
            $cityId = (int) $input['receiver']['city_id'];

            Log::debug('ECONT_LABEL_BUILD:resolve_city', [
                'city_id' => $cityId,
            ]);

            $city = EcontCity::query()
                ->select(['name', 'post_code'])
                ->find($cityId);

            if ($city) {
                $receiverCityName = $city->name;
                $receiverPostCode = $city->post_code;

                Log::debug('ECONT_LABEL_BUILD:city_resolved', [
                    'name' => $receiverCityName,
                    'post_code' => $receiverPostCode,
                ]);
            } else {
                Log::warning('ECONT_LABEL_BUILD:city_not_found', [
                    'city_id' => $cityId,
                ]);
            }
        } else {
            Log::debug('ECONT_LABEL_BUILD:no_city_pk_provided');
        }

        // Sender client
        $senderClient = [
            'name'   => (string) ($input['sender']['name'] ?? ''),
            'phones' => [(string) ($input['sender']['phone'] ?? '')],
        ];

        Log::debug('ECONT_LABEL_BUILD:sender_client', $senderClient);

        // Sender: office or address
        $senderOfficeCode = $input['sender']['office_code'] ?? null;
        $senderAddress = null;

        if ($senderOfficeCode) {
            Log::debug('ECONT_LABEL_BUILD:sender_office', [
                'office_code' => $senderOfficeCode,
            ]);
        } else {
            $senderAddress = [
                'city' => [
                    'country'  => ['code3' => 'BGR'],
                    'name'     => (string) ($input['sender']['city_name'] ?? ''),
                    'postCode' => (string) ($input['sender']['post_code'] ?? ''),
                ],
            ];
            if (!empty($input['sender']['street'])) {
                $senderAddress['street'] = (string) $input['sender']['street'];
            }
            if (!empty($input['sender']['num'])) {
                $senderAddress['num'] = (string) $input['sender']['num'];
            }

            Log::debug('ECONT_LABEL_BUILD:sender_address', $senderAddress);
        }

        // Receiver client
        $receiverClient = [
            'name'   => (string) ($input['receiver']['name'] ?? ''),
            'phones' => [(string) ($input['receiver']['phone'] ?? '')],
        ];

        Log::debug('ECONT_LABEL_BUILD:receiver_client', $receiverClient);

        // Receiver: office or address
        $receiverOfficeCode = $input['receiver']['office_code'] ?? null;
        $receiverAddress = null;

        if ($receiverOfficeCode) {
            Log::debug('ECONT_LABEL_BUILD:receiver_office', [
                'office_code' => $receiverOfficeCode,
            ]);
        } else {
            $receiverAddress = [
                'city' => [
                    'country'  => ['code3' => 'BGR'],
                    'name'     => (string) $receiverCityName,
                    'postCode' => (string) $receiverPostCode,
                ],
            ];
            if (!empty($input['receiver']['street_label'])) {
                $receiverAddress['street'] = (string) $input['receiver']['street_label'];
            }
            if (!empty($input['receiver']['street_num'])) {
                $receiverAddress['num'] = (string) $input['receiver']['street_num'];
            }

            Log::debug('ECONT_LABEL_BUILD:receiver_address', $receiverAddress);
        }

        // Optional services (COD, SMS)
        $services = [];
        if (!empty($input['cod'])) {
            $services['cdAmount']   = (string) $input['cod']['amount'];
            $services['cdType']     = (string) ($input['cod']['type'] ?? 'get');
            $services['cdCurrency'] = (string) ($input['cod']['currency'] ?? 'BGN');

            Log::debug('ECONT_LABEL_BUILD:service_cod', [
                'cdAmount' => $services['cdAmount'],
                'cdType' => $services['cdType'],
                'cdCurrency' => $services['cdCurrency'],
            ]);
        }
        if (!empty($input['sms'])) {
            $services['smsNotification'] = (bool) $input['sms'];

            Log::debug('ECONT_LABEL_BUILD:service_sms', [
                'smsNotification' => $services['smsNotification'],
            ]);
        }

        // Core payload
        $data = [
            'senderClient'         => $senderClient,
            'receiverClient'       => $receiverClient,
            'packCount'            => (int) ($input['pack_count'] ?? 1),
            'shipmentType'         => ShipmentType::PACK,
            'weight'               => (float) ($input['weight'] ?? 1.0),
            'shipmentDescription'  => (string) ($input['description'] ?? 'Артикули'),
            'payAfterAccept'       => (bool) ($input['pay_after_accept'] ?? false),
            'payAfterTest'         => (bool) ($input['pay_after_test'] ?? false),
            'holidayDeliveryDay'   => (string) ($input['holiday_day'] ?? 'workday'),
        ];

        // Attach sender/receiver endpoints
        if ($senderOfficeCode) {
            $data['senderOfficeCode'] = (string) $senderOfficeCode;
        } elseif ($senderAddress) {
            $data['senderAddress'] = $senderAddress;
        }

        if ($receiverOfficeCode) {
            $data['receiverOfficeCode'] = (string) $receiverOfficeCode;
        } elseif ($receiverAddress) {
            $data['receiverAddress'] = $receiverAddress;
        }

        if (!empty($services)) {
            $data['services'] = $services;
        }

        if (!empty($input['receiver']['address_note'])) {
            $data['shipmentDescription'] .= ' — ' . $input['receiver']['address_note'];
        }

        Log::debug('ECONT_LABEL_BUILD:payload_ready', [
            'payload' => $data,
        ]);

        return $data;
    }

    /**
     * Submit label (create/calculate/validate).
     * $mode is a string from Gdinko\Econt\Enums\LabelMode::*
     */
    public function submit(array $input, string $mode = LabelMode::CREATE): array
    {
        $traceId = (string) Str::uuid();

        Log::info('ECONT_LABEL_SUBMIT:received', [
            'trace_id' => $traceId,
            'mode'     => $mode,
            'raw_input' => $input,
        ]);

        $payload = $this->buildLabelData($input);

        Log::info('ECONT_LABEL_SUBMIT:payload', [
            'trace_id' => $traceId,
            'mode'     => $mode,
            'payload'  => $payload,
        ]);

        try {
            // Build hydrator (do NOT include courier request time windows anywhere)
            $label  = new Label($payload, $mode);

            Log::debug('ECONT_LABEL_SUBMIT:calling_econt', [
                'trace_id' => $traceId,
                'mode'     => $mode,
            ]);

            // According to package, createLabel handles CALCULATE/VALIDATE/CREATE by mode
            $result = Econt::createLabel($label);

            Log::info('ECONT_LABEL_SUBMIT:success', [
                'trace_id' => $traceId,
                'mode'     => $mode,
                'result'   => $result,
            ]);

            return is_array($result) ? $result : json_decode(json_encode($result), true);
        } catch (Throwable $e) {
            $raw = null;
            $json = null;

            // Laravel Http Client (пакетът gdinko/econt ползва него)
            if ($e instanceof RequestException && $e->response) {
                $raw = $e->response->body();
                try {
                    $json = $e->response->json();
                } catch (\Throwable $t) {
                    // ignore
                }
            }

            // Fallback ако минем през Guzzle (рядко)
            if (!$raw && method_exists($e, 'getResponse') && $e->getResponse()) {
                $raw = (string) $e->getResponse()->getBody();
            }

            $message = $e->getMessage();
            $parsedFromMessage = $this->tryExtractJson($message) ?? $this->tryExtractJson((string) $e);

            Log::error('ECONT_LABEL_SUBMIT:error', [
                'trace_id' => $traceId,
                'mode'     => $mode,
                'message'  => $message,
                'raw'      => $raw,   // <- тук ще видиш суровото тяло от Еконт
                'json'     => $json,  // <- ако е JSON, още по-добре
                'parsed'   => $parsedFromMessage,
                'exception_class' => get_class($e),
                'file'     => $e->getFile(),
                'line'     => $e->getLine(),
            ]);

            throw $e;
        }
    }

    public function validateThenCreate(array $input): array
    {
        // 1) VALIDATE
        try {
            $this->submit($input, LabelMode::VALIDATE);
        } catch (\Throwable $e) {
            throw new \RuntimeException('Econt validation failed. Please check account permissions / services and payload. ' . $e->getMessage(), previous: $e);
        }

        // 2) CREATE
        return $this->submit($input, LabelMode::CREATE);
    }


    /**
     * Tries to extract a JSON blob from a string (e.g., exception message)
     * to help surface Econt "innerErrors".
     */
    private function tryExtractJson(?string $text): ?array
    {
        if (!$text) return null;
        $start = strpos($text, '{');
        $end   = strrpos($text, '}');
        if ($start === false || $end === false || $end <= $start) {
            return null;
        }
        $json = substr($text, $start, $end - $start + 1);
        $arr  = json_decode($json, true);
        return json_last_error() === JSON_ERROR_NONE ? $arr : null;
    }
}
