<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ExchangeRateService
{
    public function getExchangeRate($currency, $date)
    {
        $url = "https://olinda.bcb.gov.br/olinda/servico/PTAX/versao/v1/odata/CotacaoMoedaDia(moeda=@moeda,dataCotacao=@dataCotacao)?@moeda='$currency'&@dataCotacao='$date'&\$top=100&\$filter=tipoBoletim%20eq%20'Fechamento%20PTAX'&\$format=json";

        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['value'][0])) {
                return [
                    'cotacaoCompra' => $data['value'][0]['cotacaoCompra'],
                    'cotacaoVenda' => $data['value'][0]['cotacaoVenda']
                ];
            }
        }

        return null;
    }
}
