<?php

function get_countries_GET(Web $w): void
{
    $w->setLayout(null);

    $client = new GuzzleHttp\Client([
        'base_uri' => 'https://raw.githubusercontent.com',
        'timeout' => 5.0,
    ]);

    // Fetch the list of countries.
    try {
        $response = $client->request('GET', '/2pisoftware/countries/master/dist/countries.json');

        if ($response->getStatusCode() !== 200) {
            throw new Exception('Unexpected status code returned: ' . $response->getStatusCode());
        }
    } catch (Throwable $t) {
        LogService::getInstance($w)->setLogger('ADMIN')->error('API request failed: ' . $t->getMessage());
        echo 'API request failed: ' . $t->getMessage();
    }

    // Decode the JSON into objects.
    $countries = [];
    try {
        $countries = json_decode($response->getBody());
        // var_dump($countries);
        // die();
        if (empty($countries)) {
            throw new Exception('Response body is empty');
        }
    } catch (Throwable $t) {
        LogService::getInstance($w)->setLogger('ADMIN')->error('Failed to decode response body: ' . $t->getMessage());
        echo 'Failed to decode response body: ' . $t->getMessage();
    }

    // Loop over the objects.
    foreach ($countries as $c) {
        // Check if a country already exists under that name.
        $country = AdminService::getInstance($w)->getCountryWhere([
            'name' => $c->name->common,
        ]);
        // If not, create a new one.
        if (empty($country)) {
            $country = new Country($w);
        }

        try {
            AdminService::getInstance($w)->startTransaction();

            // Set the country's properties and insert/update it.
            $country->name = $c->name->common;
            $country->alpha_2_code = $c->cca2;
            $country->alpha_3_code = $c->cca3;
            $country->capital = $c->capital ? $c->capital[0] : '';
            $country->region = $c->region;
            $country->subregion = $c->subregion;
            $country->demonym = property_exists($c, 'demonym') && property_exists($c->demonym, 'eng') ? $c->demonym->eng->m : print_r($c->demonym);

            if (!$country->insertOrUpdate()) {
                throw new Exception('Failed to insert or update country with name: ' . $country->name);
                continue;
            }

            // Loop over the country's languages.
            foreach ($c->languages as $key => $lang) {
                // Check if the language already exists under that name.
                $language = AdminService::getInstance($w)->getLanguageWhere([
                    'name' => $lang,
                ]);
                // If not, create a new one and insert it.
                if (empty($language)) {
                    $language = new Language($w);
                    $language->name = $lang;
                    $language->native_name = $lang;
                    $language->iso_639_1 = $key;
                    $language->iso_639_2 = $key;

                    if (!$language->insert()) {
                        LogService::getInstance($w)->setLogger('ADMIN')->error('Failed to insert or update language with name: ' . $language->name);
                        continue;
                    }
                }

                // Check if a country language exists for that country and language. If not, create one and insert it.
                $country_language = AdminService::getInstance($w)->getCountryLanguage($country->id, $language->id);
                if (empty($country_language)) {
                    $country_language = new CountryLanguage($w);
                    $country_language->country_id = $country->id;
                    $country_language->language_id = $language->id;

                    if (!$country_language->insert()) {
                        LogService::getInstance($w)->setLogger('ADMIN')->error('Failed to insert or update country language with name: ' . $language->name);
                    }
                }
            }

            AdminService::getInstance($w)->commitTransaction();
        } catch (Throwable $t) {
            BridgeService::getInstance($w)->rollbackTransaction();
            LogService::getInstance($w)->setLogger('BRIDGE')->error($t->getMessage());
            continue;
        }
    }
}
