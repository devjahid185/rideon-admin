<?php

namespace App\Http\Controllers\Traits;

use App\Models\Modern\Item;
use App\Services\FirestoreService;
use Illuminate\Support\Facades\Log;

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
     */
    public function getCollection(string $collection): ?array
    {
        return $this->firestore()->getCollection($collection);
    }

    /**
     * Add a new document to a Firestore collection.
     *
     * @return array|null
     */
    public function addDocument(string $collection, array $data): ?string
    {
        return $this->firestore()->addDocument($collection, $data);
    }

    /**
     * Update a document in Firestore.
     */
    public function updateDocument(string $collection, string $documentId, array $data): ?array
    {
        return $this->firestore()->updateDocument($collection, $documentId, $data);
    }

    /**
     * Delete a Firestore driver document safely.
     */
    public function deleteFirestoreDriver(string $firestoreId): void
    {
        try {
            $this->firestore()->deleteDocument('drivers', $firestoreId);
        } catch (\Exception $e) {
            Log::error('Failed to delete Firestore driver: '.$e->getMessage());
        }
    }

    public function getDocument(string $collection, string $documentId): ?array
    {
        try {
            $documentPath = "{$collection}/{$documentId}";
            $docData = $this->firestore()->getDocument($documentPath);

            return ! empty($docData) ? $docData : null;
        } catch (\Throwable $e) {
            Log::error('FirestoreTrait getDocument failed: '.$e->getMessage(), [
                'collection' => $collection,
                'documentId' => $documentId,
            ]);

            return null;
        }
    }

    /**
     * Generate structured Firestore data for a driver.
     *
     * @param  mixed  $customer
     * @return array
     */
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

    /**
     * Store a driver in Firestore.
     *
     * @return array|null
     */
    public function storeDriverInFirestore(array $firestoreData): ?string
    {
        return $this->addDocument('drivers', $firestoreData);
    }
}
