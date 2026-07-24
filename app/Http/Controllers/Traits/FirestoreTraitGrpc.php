<?php

namespace App\Http\Controllers\Traits;

use App\Models\Modern\Item;
use App\Services\FirestoreService;

trait FirestoreTrait
{
    /**
     * Lazily resolve the FirestoreService from the container.
     */
    protected function firestore(): FirestoreService
    {
        static $service;

        if (! $service) {
            $service = app(FirestoreService::class);
        }

        return $service;
    }

    /**
     * Get all documents in a Firestore collection.
     *
     * @return \Google\Cloud\Firestore\DocumentSnapshot[]
     */
    public function getCollection(string $collection)
    {
        return $this->firestore()->getCollection($collection);
    }

    /**
     * Add a new document to a Firestore collection.
     *
     * @return \Google\Cloud\Firestore\DocumentReference
     */
    public function addDocument(string $collection, array $data)
    {
        return $this->firestore()->addDocument($collection, $data);
    }

    public function getDocument(string $collection, string $documentId): ?array
    {
        try {
            $docData = $this->firestore()->getDocument($collection, $documentId);

            return ! empty($docData) ? $docData : null;
        } catch (\Throwable $e) {
            // log error if needed
            return null; // safely return null if any exception occurs
        }
    }

    public function updateDocument(string $collection, string $documentId, array $data): void
    {
        $this->firestore()->updateDocument($collection, $documentId, $data);
    }

    public function generateDriverFirestoreData($customer)
    {

        $latestItem = Item::with(['item_Type', 'itemVehicle', 'vehicleMake'])
            ->where('userid_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->first();

        return [
            'completed_rides' => [],
            'docApprovedStatus' => 'pending',
            'driverImageUrl' => '',
            'driverId' => $customer->id,
            'driverName' => $customer->first_name,
            'driverNumber' => $customer->phone_country.$customer->phone,
            'driverRating' => '',
            'driverStatus' => 'pending',
            'geo' => [
                'geohash' => '',
                'geopoint' => [28.535515, 77.391025],
            ],
            'itemId' => $latestItem?->id ?? '',
            'itemTypeId' => $latestItem?->item_type_id ?? '',
            'itemTypeName' => $latestItem?->item_Type?->name ?? '',
            'rejected_rides' => [],
            'rideStatus' => 'available',
            'ride_request' => [],
            'timestamp' => now(),
            'vehicleMake' => $latestItem?->vehicleMake?->name ?? '',
            'vehicleModel' => $latestItem?->model ?? '',
            'vehicleNumber' => $latestItem?->registration_number ?? '',
            'vehiclecolor' => $latestItem?->itemVehicle?->color ?? '',
            'vehicleyear' => $latestItem?->itemVehicle?->year ?? '',
        ];
    }

    public function storeDriverInFirestore(array $firestoreData)
    {

        return $this->addDocument('drivers', $firestoreData);
    }

    public function deleteFirestoreDriver($firestoreId)
    {
        try {
            $this->firestore()->deleteDocument('drivers', $firestoreId);
        } catch (\Exception $e) {
            \Log::error('Failed to delete Firestore driver: '.$e->getMessage());
        }
    }
}
