<?php

namespace App\Support;

class CheckoutValidation
{
    public static function rules(string $shippingMethod, bool $invoiceRequested = false): array
    {
        $rules = [
            'first_name' => ['required', 'string', 'min:2', 'max:50'],
            'last_name'  => ['required', 'string', 'min:2', 'max:50'],
            'email'      => ['required', 'email'],
            'phone'      => ['required', 'regex:/^[0-9\+\-\s]{7,20}$/'],
            'terms_accepted' => ['accepted'],
            'shipping_method' => ['required', 'in:address,econt_office'],
        ];

        if ($shippingMethod === 'address') {
            $rules += [
                'cityId'     => ['required', 'integer'],
                'streetCode' => ['required', 'integer'],
                'streetNum'  => ['required', 'string', 'max:20'],
            ];
        }

        if ($shippingMethod === 'econt_office') {
            $rules += [
                'officeCode' => ['required', 'string'],
            ];
        }

        if ($invoiceRequested) {
            $rules += [
                'companyName'      => ['required', 'string', 'min:2', 'max:100'],
                'companyID'        => ['required', 'string', 'min:4', 'max:20'],
                'companyAddress'   => ['required', 'string', 'min:5', 'max:150'],
                'companyMol'       => ['required', 'string', 'min:3', 'max:100'],
                'companyTaxNumber' => ['nullable', 'string', 'max:30'],
            ];
        }

        return $rules;
    }


    public static function messages(): array
    {
        return [
            'first_name.required' => 'Моля, въведете вашето име.',
            'last_name.required' => 'Моля, въведете вашата фамилия.',
            'email.required' => 'Имейлът е задължителен.',
            'email.email' => 'Моля, въведете валиден имейл адрес.',
            'phone.required' => 'Моля, въведете телефонен номер.',
            'phone.regex' => 'Телефонният номер съдържа невалидни символи.',

            'shipping_method.required' => 'Моля, изберете метод на доставка.',

            // Address
            'cityId.required' => 'Моля, изберете населено място.',
            'streetCode.required' => 'Моля, изберете улица.',
            'streetNum.required' => 'Моля, въведете номер.',

            // Office
            'officeCode.required' => 'Моля, изберете офис на Еконт.',

            // Invoice
            'companyName.required' => 'Моля, въведете името на фирмата.',
            'companyID.required' => 'Моля, въведете ЕИК/Булстат.',
            'companyAddress.required' => 'Моля, въведете адрес на фирмата.',
            'companyMol.required' => 'Моля, въведете МОЛ.',
        ];
    }
}
