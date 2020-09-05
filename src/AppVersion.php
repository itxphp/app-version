<?php
namespace Itx\Utilities ;

use \GuzzleHttp\Client;
use \Itx\Utilities\DomFetcher ;
use \Itx\Utilities\AppVersion\Exceptions\AppVersionException ;

class AppVersion
{
    private $stores = [
        "android" => "https://play.google.com/store/apps/details?id=%s" ,
        "ios" => "https://itunes.apple.com/lookup?%s=%s" 
    ] ;
    
    private function getAndroid($package)
    {
        $client = (new Client)->request('GET', sprintf($this->stores["android"] , $package), []);
        $data = DomFetcher::using((string) $client->getBody())->fetch([
            "version" => "//*[text()*=i'current version']/following-sibling::span[1]"
        ])  ;
        return $data["version"] ?? null ;
    }

    private function getIos($package)
    {
        $query = is_numeric($package) ? "id" : "bundleId" ;
        $client = (new Client)->request('GET', sprintf($this->stores["ios"] , $query , $package), []);
        $json = json_decode((string) $client->getBody() , true ) ;
        if(json_last_error() == JSON_ERROR_NONE ) {
            return $json["results"][0]["version"] ?? null ;
        } 

        return null ;
    }

    public static function get(array $packages) 
    {
        $data = []; 
        foreach($packages as $vender=>$package) {
            try {
                $data[$vender]  = (new self)->{'get'.ucfirst($vender)}($package) ;
            } catch(\Throwable $e) {
                throw new AppVersionException("{$vender} app [{$package}] doesn't exists") ;   
            }
        }
        return $data ;
    }

    public static function __callStatic($name, $arguments)
    {
        if(in_array($name , ["android" , "ios"])) {
            return static::get([
                $name => $arguments[0]
            ])[$name];
        }

        throw new AppVersionException("store not exists") ;
    }   
}