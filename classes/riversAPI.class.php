<?php
class riversAPI extends API
{
    

    public function __construct($request, $origin) {
        parent::__construct($request);
        
        // Abstracted out for example
       
       //@todo: should create apiKey class to provide functionality to create apiKey

       /* if (!array_key_exists('apiKey', $this->request)) {
            throw new Exception('No API Key provided');
        } else if (!$APIKey->verifyKey($this->request['apiKey'], $origin)) {
            throw new Exception('Invalid API Key');
        }*/
    }

    /**
     * Example of an Endpoint
     */
     protected function river() {
         $river_arr = array();
        if ($this->method == 'GET') {
            $river = new riverModel((object)$this->request);
            $river_arr = $river->getResult();
           return $river_arr;
        } else {
           //@todo: shoud log debug message
        }
     }
 }