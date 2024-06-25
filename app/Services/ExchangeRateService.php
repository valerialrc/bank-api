<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class ExchangeRateService
{
    public function getExchangeRate($currency, $date)
    {
        $attempts = 0;
        $maxAttempts = 5;
        $response = null;

        while ($attempts < $maxAttempts) {
            $response = $this->fetchExchangeRate($currency, $date);

            if ($response && isset($response['value'][0]['cotacaoCompra'], $response['value'][0]['cotacaoVenda'])) {
                return [
                    'cotacaoCompra' => $response['value'][0]['cotacaoCompra'],
                    'cotacaoVenda' => $response['value'][0]['cotacaoVenda']
                ];
            } elseif ($response && empty($response['value'])) {
                $date = Carbon::createFromFormat('m-d-Y', $date)->subDay()->format('m-d-Y');
                $attempts++;
                continue;
            }

            $attempts++;
        }
        return null;
    }

    protected function fetchExchangeRate($currency, $date)
    {
        $url = "https://olinda.bcb.gov.br/olinda/servico/PTAX/versao/v1/odata/CotacaoMoedaDia(moeda=@moeda,dataCotacao=@dataCotacao)?@moeda='$currency'&@dataCotacao='$date'&\$top=100&\$filter=tipoBoletim%20eq%20'Fechamento%20PTAX'&\$format=json";

        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();

            return $data;
        } else {
            return null;
        }
    }
}
