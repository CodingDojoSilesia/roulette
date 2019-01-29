<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Api extends \Codeception\Module
{
    public function grabHashname()
    {
        $url = $this->getModule('REST')->_getConfig('url');
        $endpoint = $url . '/players';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $rawOutput = curl_exec($ch);
        if (! curl_errno($ch)) {
            $info = curl_getinfo($ch);
            if ($info['http_code'] === 201) {
                $jsonOutput = json_decode($rawOutput, true);
                return $jsonOutput['hashname'];
            }
        }
        return null;
    }
}
