<?php

namespace App\Support;

use Illuminate\Validation\Rule;

class CheckoutValidation
{
    public static function rules(bool $invoiceRequested = false): array
    {
        $rules = [
            'first_name' => ['required', 'string', 'min:2', 'max:50'],
            'last_name'  => ['required', 'string', 'min:2', 'max:50'],
            'email'      => ['required', 'email'],
            'phone'      => ['required', 'regex:/^[0-9\+\-\s]{7,20}$/'],
            'city'       => ['required', 'string', 'min:2', 'max:50'],
            'zip'        => ['required', 'string', 'min:4', 'max:10'],
            'street'     => ['required', 'string', 'min:3', 'max:100'],
            'terms_accepted' => ['accepted']
        ];

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
            'first_name.min' => 'Името трябва да съдържа поне 2 символа.',
            'first_name.max' => 'Името не може да надвишава 50 символа.',

            'last_name.required' => 'Моля, въведете вашата фамилия.',
            'last_name.min' => 'Фамилията трябва да съдържа поне 2 символа.',
            'last_name.max' => 'Фамилията не може да надвишава 50 символа.',

            'email.required' => 'Имейлът е задължителен.',
            'email.email' => 'Моля, въведете валиден имейл адрес.',

            'phone.required' => 'Моля, въведете телефонен номер.',
            'phone.regex' => 'Телефонният номер съдържа невалидни символи.',

            'city.required' => 'Моля, въведете град или село.',
            'zip.required' => 'Моля, въведете пощенски код.',
            'street.required' => 'Моля, въведете улица.',

            'companyName.required' => 'Моля, въведете името на фирмата.',
            'companyID.required' => 'Моля, въведете ЕИК/Булстат.',
            'companyAddress.required' => 'Моля, въведете адрес на фирмата.',
            'companyMol.required' => 'Моля, въведете МОЛ (представител).',

            // 'terms_accepted.required' => 'Трябва да се съгласите с Общите условия.',
            // 'terms_accepted.accepted' => 'Необходимо е да приемете Общите условия, за да продължите.',

        ];
    }
}
