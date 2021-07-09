<?php

function get_countries_GET(Web $w): void
{
    $w->setLayout(null);

    $client = new GuzzleHttp\Client([
        'base_uri' => 'https://restcountries.eu',
        'timeout' => 5.0,
    ]);

    try {
        $response = $client->request('GET', '/rest/v2/all');

        if ($response->getStatusCode() !== 200) {
            throw new Exception('Unexpected status code returned: ' . $response->getStatusCode());
        }
    } catch (Throwable $t) {
        LogService::getInstance($w)->setLogger('ADMIN')->error('API request failed: ' . $t->getMessage());
        echo 'API request failed: ' . $t->getMessage();
    }

    $data = null;

    try {
        $data = json_decode($response->getBody());
        if (empty($data)) {
            throw new Exception('Data is empty: ' . $data);
        }
    } catch (Throwable $t) {
        LogService::getInstance($w)->setLogger('ADMIN')->error('Failed to decode response body: ' . $t->getMessage());
        echo 'Failed to decode response body: ' . $t->getMessage();
    }

    $new = 0;
    $updated = 0;
    $failures = 0;

    foreach ($data as $d) {
        $is_new = false;
        $country = AdminService::getInstance($w)->getCountry([
            'name' => $d->name,
        ]);
        if (empty($country)) {
            $is_new = true;
            $country = new Country($w);
        }

        $country->name = $d->name;
        $country->alpha_2_Code = $d->alpha2Code;
        $country->alpha_3_Code = $d->alpha3Code;
        $country->capital = $d->capital;
        $country->region = $d->region;
        $country->subregion = $d->subregion;
        $country->demonym = $d->demonym;

        if (!$country->insertOrUpdate()) {
            LogService::getInstance($w)->setLogger('ADMIN')->error('Failed to insert or update country with name: ' . $country->name);
            $failures++;
            continue;
        }

        if ($is_new) {
            $new++;
        } else {
            $updated++;
        }
    }

    echo '<p>New Countries: ' . $new . '</p>';
    echo '<p>Updated Countries: ' . $updated . '</p>';
    echo '<p>Failures (Check logs for details): ' . $failures . '</p>';
}
