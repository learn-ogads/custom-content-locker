<?php

use Detection\MobileDetect;
require_once "MobileDetect.php";

class Offers
{
    private string $API_KEY;
    private string $AFF_SUB4;

    public function __construct(string $apiKey, string $affSub4)
    {
        $this->API_KEY = $apiKey;
        $this->AFF_SUB4 = $affSub4;
    }

    /**
     * @throws Exception The exception can vastly differ. We want a generic catch-all.
     */
    public function fetchOffers()
    {
        $ch = $this->createCURL();
        $content = curl_exec($ch);
        // Check for an error
        if (!$content) {
            throw new Exception(curl_error($ch));
        }
        curl_close($ch);
        try {
            return $this->parseResponse($content);
        } catch (JsonException $e) {
            throw new Exception("Unable to fetch required data, please try again");
        }
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    private function parseResponse(string $resp) {
        $content = json_decode($resp, null, 512, JSON_THROW_ON_ERROR);
        if (!$content->success) {
            throw new Exception($content->error);
        }
        return $content->offers;
    }

    /** @noinspection PhpMissingReturnTypeInspection */
    private function createCURL()
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->createUrl(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $this->API_KEY
            ]
        ]);
        return $ch;
    }

    private function createUrl(): string
    {
        return getenv("OFFERS_ENDPOINT") . "?" . http_build_query($this->buildData());
    }

    public static function isMobileOrTablet(): bool
    {
        $detect = new MobileDetect();
        if ($detect->isMobile() || $detect->isTablet()) return true;
        return false;
    }

    private function buildData(): array
    {
        return [
            "ip" => Server::getIpAddress(),
            "user_agent" => Server::getUserAgent(),
            "ctype" => Offers::isMobileOrTablet() ? 1 : null,
            "aff_sub4" => $this->AFF_SUB4
        ];
    }

}