<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

use PhpOffice\PhpSpreadsheet\Settings;
use Psr\Http\Client\ClientExceptionInterface;

class Web
{
    
    public static function WEBSERVICE(string $url)
    {
        $url = trim($url);
        if (strlen($url) > 2048) {
            return Functions::VALUE(); 
        }

        if (!preg_match('/^http[s]?:\/\
            return Functions::VALUE(); 
        }

        
        $client = Settings::getHttpClient();
        $requestFactory = Settings::getRequestFactory();
        $request = $requestFactory->createRequest('GET', $url);

        try {
            $response = $client->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            return Functions::VALUE(); 
        }

        if ($response->getStatusCode() != 200) {
            return Functions::VALUE(); 
        }

        $output = $response->getBody()->getContents();
        if (strlen($output) > 32767) {
            return Functions::VALUE(); 
        }

        return $output;
    }
}
