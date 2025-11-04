<?php

namespace App\Services\Shipping;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Gdinko\Econt\Models\CarrierEcontCity as EcontCity;
use Gdinko\Econt\Models\CarrierEcontOffice as EcontOffice;
use Gdinko\Econt\Models\CarrierEcontStreet as EcontStreet;

class EcontDirectoryService
{
    /**
     * Search Bulgarian cities (BGR) in the Econt directory.
     * Returns a lightweight collection suitable for autocomplete dropdowns.
     */
    public function searchCities(?string $q = null, int $limit = 50): Collection
    {
        $cities = EcontCity::query()
            ->where('country_code3', 'BGR')
            ->select(['id', 'name', 'region_name', 'post_code'])
            ->when($q && trim($q) !== '', fn($qq) => $qq->where('name', 'LIKE', '%' . trim($q) . '%'))
            ->orderBy('name')
            ->limit($limit)
            ->get();

        return $cities->map(fn($c) => [
            'id'        => (int) $c->id,
            'name'      => $c->name,
            'post_code' => $c->post_code,
            'label'     => $this->formatCityLabel($c->name, $c->region_name, $c->post_code),
        ]);
    }

    /**
     * List Econt offices for a given city (by internal city table PK "id").
     * Optional $q filters by office name or office code.
     */
    public function officesByCity(int $cityId, ?string $q = null, int $limit = 200): Collection
    {
        $officesTable = (new EcontOffice)->getTable(); // carrier_econt_offices
        $citiesTable  = (new EcontCity)->getTable();   // carrier_econt_cities

        $query = EcontOffice::query()
            ->join("{$citiesTable} as c", 'c.econt_city_id', '=', "{$officesTable}.econt_city_id")
            ->where('c.id', $cityId)
            ->where('c.country_code3', 'BGR')
            ->where("{$officesTable}.country_code3", 'BGR')
            ->select([
                "{$officesTable}.id   as id",
                "{$officesTable}.code as code",
                "{$officesTable}.name as office_name",
                DB::raw("c.name as city_name"),
            ])
            ->when($q && trim($q) !== '', function ($w) use ($officesTable, $q) {
                $needle = trim($q);
                $w->where(function ($x) use ($officesTable, $needle) {
                    $x->where("{$officesTable}.name", 'LIKE', "%{$needle}%")
                        ->orWhere("{$officesTable}.code", 'LIKE', "{$needle}%");
                });
            })
            ->orderBy("{$officesTable}.name")
            ->limit($limit);

        return $query->get()->map(function ($row) {
            return [
                'id'      => (int) $row->id,
                'code'    => $row->code,
                'city'    => $row->city_name,
                'name'    => $row->office_name,
                'address' => null,
                'label'   => $this->formatOfficeLabel($row->city_name, $row->office_name, null, $row->code),
                'value'   => $row->code,
            ];
        });
    }

    /**
     * Build a concise label for an office like: "Sofia — Mall of Sofia [1127]".
     * Removes leading city prefixes from office names (e.g. "гр. София", "София -", etc.).
     */
    private function labelForOffice(string $city, ?string $officeName, string $code): string
    {
        $name = trim((string) $officeName);

        // Strip leading city prefix variants: "гр. София", "София -", "София –/—", "София,", extra spaces
        $pattern = '/^(гр\.\s*)?' . preg_quote($city, '/') . '(\s*[-–—,]|\s+)?/ui';
        $name = preg_replace($pattern, '', $name) ?? $name;
        $name = trim(preg_replace('/\s+/', ' ', $name));

        if ($name === '') {
            $name = 'Офис'; // fallback; you can change to 'Office' if you prefer all-English
        }

        return sprintf('%s — %s [%s]', $city, $name, $code);
    }

    /**
     * Global office search:
     * - If $q matches a city (including variants like "гр. {city}", "{city} - ..."), return all offices for that city.
     * - Otherwise search by office name/code/city name across BG.
     */
    public function searchOffices(?string $q = null, int $limit = 50): Collection
    {
        $officesTable = (new EcontOffice)->getTable();
        $citiesTable  = (new EcontCity)->getTable();

        $q = trim((string) $q);
        if ($q === '') {
            return collect();
        }

        // Escape for MySQL REGEXP so that user input doesn't break the pattern
        $quoted = $this->escapeForMysqlRegex($q);

        // Example: ^(гр\. )?София([[:space:]]*[-–—,]|[[:space:]]+).*$
        $regex = '^(гр\\.\\s*)?' . $quoted . '([[:space:]]*[-–—,]|[[:space:]]+).*$';

        // Try to resolve possible city matches (BGR only)
        $cityIds = EcontCity::query()
            ->where('country_code3', 'BGR')
            ->where(function ($w) use ($q, $regex) {
                $w->where('name', '=', $q)
                    ->orWhere('name', 'LIKE', $q . ' %')
                    ->orWhere('name', 'LIKE', $q . '-%')
                    ->orWhere('name', 'LIKE', 'гр. ' . $q . '%')
                    ->orWhereRaw('name REGEXP ?', [$regex]);
            })
            ->pluck('econt_city_id')
            ->filter()
            ->unique()
            ->values();

        // Base query for BG offices
        $query = EcontOffice::query()
            ->join("{$citiesTable} as c", 'c.econt_city_id', '=', "{$officesTable}.econt_city_id")
            ->where('c.country_code3', 'BGR')
            ->where("{$officesTable}.country_code3", 'BGR')
            ->select([
                "{$officesTable}.id   as id",
                "{$officesTable}.code as code",
                "{$officesTable}.name as office_name",
                DB::raw("c.name as city_name"),
            ]);

        if ($cityIds->isNotEmpty()) {
            // City match: return all offices for these econt_city_id(s)
            $effectiveLimit = max($limit, 200); // Sofia ~131 offices, so bump the cap

            $query->whereIn("{$officesTable}.econt_city_id", $cityIds)
                ->orderBy('c.name')
                ->orderBy("{$officesTable}.name")
                ->limit($effectiveLimit);
        } else {
            // Global search by office name/code/city name
            $needle = $q;
            $query->where(function ($x) use ($officesTable, $needle) {
                $x->where("{$officesTable}.name", 'LIKE', "%{$needle}%")
                    ->orWhere("{$officesTable}.code", 'LIKE', "{$needle}%")
                    ->orWhere('c.name', 'LIKE', "%{$needle}%");
            })
                ->orderBy('c.name')
                ->orderBy("{$officesTable}.name")
                ->limit($limit);
        }

        return $query->get()->map(function ($row) {
            return [
                'id'    => (int) $row->id,
                'code'  => $row->code,
                'label' => $this->labelForOffice($row->city_name, $row->office_name, $row->code),
                'value' => $row->code,
            ];
        });
    }

    /**
     * Format a human-readable label for a city (e.g. "Sofia (Sofia region, 1000)").
     */
    private function formatCityLabel(?string $name, ?string $region, ?string $postCode): string
    {
        $extras = array_filter([$region, $postCode]);
        return $extras ? sprintf('%s (%s)', $name, implode(', ', $extras)) : (string) $name;
    }

    /**
     * Format a human-readable label for an office.
     */
    private function formatOfficeLabel(?string $city, ?string $name, ?string $address, ?string $code): string
    {
        $office = trim(preg_replace('/\s+/', ' ', trim($name ?? '')));
        if ($address) {
            $office = trim($office . ' ' . trim($address));
        }
        return sprintf('%s ( %s [%s])', $city, $office, $code);
    }

    /**
     * Escape user input string for MySQL REGEXP (no delimiters needed).
     */
    private function escapeForMysqlRegex(string $value): string
    {
        // preg_quote is good enough for MySQL REGEXP as well.
        $escaped = preg_quote($value, '/');
        return $escaped;
    }

    /**
     * List streets for a given city (by Econt's econt_city_id, NOT by our PK "id").
     * Optional $q filters by street name; returns a compact structure for autocomplete.
     */
    public function streetsByCity(int $cityId, ?string $q = null, int $limit = 100): Collection
    {
        $q = trim((string) $q);

        $rows = EcontStreet::query()
            ->where('econt_city_id', $cityId) // IMPORTANT: this is econt_city_id, not the internal PK
            ->when(
                $q !== '',
                fn($w) =>
                $w->where('name', 'LIKE', "%{$q}%")
                    ->orWhere('name_en', 'LIKE', "%{$q}%")
            )
            ->orderBy('name')
            ->limit($limit)
            ->get(['id', 'econt_street_id', 'name']);

        return $rows->map(fn($s) => [
            'id'    => (int) $s->id,               // internal PK
            'code'  => (int) $s->econt_street_id,  // Econt street code
            'label' => $s->name,                   // e.g. "бул. Цар Борис III"
            'value' => (int) $s->econt_street_id,
        ]);
    }
}
