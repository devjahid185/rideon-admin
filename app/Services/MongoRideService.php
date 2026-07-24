<?php

namespace App\Services;

use MongoDB\Client;

class MongoRideService
{
    protected $collection;

    public function __construct()
    {
        $client = new Client(env('MONGO_DSN')); // MongoDB Atlas DSN
        $db = $client->selectDatabase(env('MONGO_DB', '3rees'));
        $this->collection = $db->selectCollection('rides');
    }

    // Create a ride
    public function createRide(array $data)
    {
        $data['requested_at'] = new \MongoDB\BSON\UTCDateTime;
        $result = $this->collection->insertOne($data);

        return $this->collection->findOne(['_id' => $result->getInsertedId()]);
    }

    // Get all rides
    public function getRides()
    {
        return $this->collection->find()->toArray();
    }
}
