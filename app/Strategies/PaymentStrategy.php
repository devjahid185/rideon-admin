<?php

namespace App\Strategies;

interface PaymentStrategy
{
    public function process($bookingId, $bookingData, Request $request);

    public function cancel($bookingId, $bookingData);

    public function refund($bookingId, $bookingData);

    public function return($bookingId, $requestData);

    public function callback($bookingId, $requestData);
}
