<?php

use App\Http\Controllers\Api\Auth\TokenController;
use App\Http\Controllers\Api\V1\Admin\RideRequestController;
use App\Http\Controllers\Api\V1\Admin\RestaurantOwnerApiController;
use App\Http\Controllers\Api\V1\Admin\SliderApiController;
use App\Http\Controllers\Api\V1\Admin\FoodDeliveryApiController;
use App\Http\Controllers\Front\PaymentFrontController;
use App\Strategies\PaypalStrategy;
use Illuminate\Support\Facades\Route;

// paypal
Route::post('/paypal/ipn', [PaymentFrontController::class, 'handlePaypalIPN'])
    ->name('paypal.ipn');
Route::post('/paypal/webhook', [PaypalStrategy::class, 'handleWebhook'])
    ->name('paypal.webhook');

Route::post('/generateToken', [TokenController::class, 'issueSanctumToken'])
    ->name('token.generate');
// 'auth:sanctum'

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin', 'middleware' => ['auth:sanctum']], function () {
    // Slider
    Route::post('/ride-requests', [RideRequestController::class, 'createRide']);
    Route::get('/ride-requests', [RideRequestController::class, 'getRides']);
    Route::patch('/ride-requests/{id}/status', [RideRequestController::class, 'updateRideStatus']);
    Route::get('/sliders', [SliderApiController::class, 'sliders']);
    Route::get('/food/restaurants/nearby', [FoodDeliveryApiController::class, 'nearbyRestaurants']);
    Route::get('/food/restaurants/{restaurantId}/menu', [FoodDeliveryApiController::class, 'restaurantMenu']);
    Route::post('/food/order/create', [FoodDeliveryApiController::class, 'createFoodOrder']);
    Route::get('/food/order/{orderId}', [FoodDeliveryApiController::class, 'orderDetails']);
    Route::get('/food/orders/my', [FoodDeliveryApiController::class, 'listUserOrders']);
    Route::get('/food/orders/available', [FoodDeliveryApiController::class, 'listDriverAvailableOrders']);
    Route::post('/food/order/{orderId}/accept', [FoodDeliveryApiController::class, 'assignDriver']);
    Route::post('/food/order/{orderId}/reject-offer', [FoodDeliveryApiController::class, 'rejectDriverOffer']);
    Route::post('/food/order/{orderId}/status', [FoodDeliveryApiController::class, 'updateOrderStatus']);
    Route::get('/food/order/{orderId}/timeline', [FoodDeliveryApiController::class, 'orderStatusTimeline']);
    Route::post('/food/order/{orderId}/location', [FoodDeliveryApiController::class, 'storeOrderLocation']);
    Route::get('/food/order/{orderId}/tracking', [FoodDeliveryApiController::class, 'orderTracking']);

    Route::post('/restaurant-owner/register', [RestaurantOwnerApiController::class, 'register']);
    Route::post('/restaurant-owner/login', [RestaurantOwnerApiController::class, 'login']);
    Route::get('/restaurant-owner/me', [RestaurantOwnerApiController::class, 'me']);
    Route::get('/restaurant-owner/dashboard', [RestaurantOwnerApiController::class, 'dashboard']);
    Route::post('/restaurant-owner/restaurant/onboard', [RestaurantOwnerApiController::class, 'onboardRestaurant']);
    Route::post('/restaurant-owner/categories', [RestaurantOwnerApiController::class, 'saveCategory']);
    Route::post('/restaurant-owner/categories/{id}', [RestaurantOwnerApiController::class, 'saveCategory']);
    Route::post('/restaurant-owner/items', [RestaurantOwnerApiController::class, 'saveItem']);
    Route::post('/restaurant-owner/items/{id}', [RestaurantOwnerApiController::class, 'saveItem']);
    Route::post('/restaurant-owner/branches', [RestaurantOwnerApiController::class, 'saveBranch']);
    Route::post('/restaurant-owner/branches/{branchId}', [RestaurantOwnerApiController::class, 'saveBranch']);
    Route::post('/restaurant-owner/branches/{branchId}/availability', [RestaurantOwnerApiController::class, 'updateBranchAvailability']);
    Route::post('/restaurant-owner/branches/{branchId}/delete', [RestaurantOwnerApiController::class, 'deleteBranch']);
    Route::get('/restaurant-owner/orders', [RestaurantOwnerApiController::class, 'orders']);
    Route::post('/restaurant-owner/orders/{orderId}/status', [RestaurantOwnerApiController::class, 'updateOrderStatus']);
    Route::get('/restaurant-owner/wallet', [RestaurantOwnerApiController::class, 'wallet']);
    Route::post('/restaurant-owner/wallet/withdraw', [RestaurantOwnerApiController::class, 'requestWithdrawal']);

    // App Users
    Route::post('/userRegister', 'AppUsersApiController@userRegister')->name('userRegister');
    Route::post('/otpVerification', 'AppUsersApiController@otpVerification');
    Route::post('/userLogin', 'AppUsersApiController@userLogin');
    Route::post('/userLogout', 'AppUsersApiController@userLogout');
    Route::post('/puthostRequest', 'AppUsersApiController@puthostRequest');
    Route::post('/gethostStatus', 'AppUsersApiController@gethostStatus');
    Route::post('/socialLogin', 'AppUsersApiController@socialLogin');
    Route::post('/userEmailLogin', 'AppUsersApiController@userEmailLogin')->name('userEmailLogin');
    Route::post('/forgotPassword', 'AppUsersApiController@forgotPassword');
    Route::post('/verifyResetToken', 'AppUsersApiController@verifyResetToken');
    Route::post('/ResendTokenEmailChange', 'AppUsersApiController@ResendTokenEmailChange');
    Route::post('/sendMobileLoginOtp', 'AppUsersApiController@sendMobileLoginOtp');
    Route::post('/userMobileLogin', 'AppUsersApiController@userMobileLogin');

    Route::post('/sendOnlyEmailLoginOtp', 'AppUsersApiController@sendOnlyEmailLoginOtp');
    Route::post('/userOnlyEmailLogin', 'AppUsersApiController@userOnlyEmailLogin');

    Route::post('/resetPassword', 'AppUsersApiController@resetPassword');
    Route::post('/emailcheck', 'AppUsersApiController@emailcheck');
    Route::post('/mobilecheck', 'AppUsersApiController@mobilecheck');
    Route::post('/ResendOtp ', 'AppUsersApiController@ResendOtp');
    Route::post('/ResendToken ', 'AppUsersApiController@ResendToken');
    Route::post('/updatePassword ', 'AppUsersApiController@updatePassword');
    Route::post('/getUserWallet ', 'AppUsersApiController@getUserWallet');
    Route::post('/getUserWalletTransactions ', 'AppUsersApiController@getUserWalletTransactions');

    Route::post('/getVendorWallet ', 'AppUsersApiController@getVendorWallet');
    Route::post('/getVendorWalletTransactions ', 'AppUsersApiController@getVendorWalletTransactions');
    Route::post('/insertPayout ', 'AppUsersApiController@insertPayout');
    Route::post('/getPayoutTransactions ', 'AppUsersApiController@getPayoutTransactions');

    // payoutMethod
    Route::post('/update-payout-method', 'PayoutMethodApiController@updatePayoutMethod');
    Route::post('/get-payout-methods', 'PayoutMethodApiController@getPayoutMethods');
    Route::get('/get-payout-types', 'PayoutMethodApiController@getPayoutTypes');

    Route::post('/getDriverEarings ', 'DriverFinanceApiController@getDriverEarings');

    Route::post('/deleteAccount ', 'AppUsersApiController@deleteAccount');
    Route::post('/addEditVerificationDocuments', 'AppUsersApiController@addEditVerificationDocuments');
    Route::post('/getVerificationDocuments', 'AppUsersApiController@getVerificationDocuments');

    // UserProfile
    Route::post('/getUserProfile ', 'UserProfileController@getUserProfile');
    Route::post('/getUseritems ', 'UserProfileController@getUseritems');
    Route::post('/getVendorItemReviews ', 'UserProfileController@getVendorItemReviews');

    // Cities
    Route::get('/yourLocations', 'CitiesApiController@yourLocations');
    Route::post('/searchCities', 'CitiesApiController@searchCities');
    // Item Type
    Route::get('/getAllCategories', 'ItemTypeApiController@getAllCategories');
    Route::get('service-types', 'ItemTypeApiController@getServiceTypes');
    Route::get('item-types/by-service-type', 'ItemTypeApiController@getItemTypesByServiceType');

    Route::post('/getItemsByItemType', 'ItemTypeApiController@getItemsByItemType');
    Route::post('/editItem', 'ItemsApiController@editItem');
    Route::post('/myItems', 'ItemsApiController@myItems');
    Route::post('/addEditItemImage', 'ItemsApiController@addEditItemImage');
    Route::post('/addEditItemImages', 'ItemsApiController@addEditItemImages');
    // Item Rules
    Route::get('/getItemRules', 'RentalItemRuleApiController@getItemRules');
    // Item Rules
    Route::get('/getMakes', 'MakeApiController@getMakes');
    Route::get('/getMakesModel', 'MakeApiController@getMakesModel');

    // Cancellations rasons
    Route::get('/getCancelReasons', 'CancellationReasonController@getCancelReasons');
    Route::get('/getCancellationPolicies', 'BookingApiController@getCancellationPolicies');

    // Review
    Route::post('/getItemReviews', 'ReviewApiController@getItemReviews');
    Route::post('/giveReviewByUser', 'ReviewApiController@giveReviewByUser');
    Route::post('/giveReviewByHost', 'ReviewApiController@giveReviewByHost');

    // Booking
    Route::post('/getItemPrices', 'BookingApiController@getItemPrices');
    Route::post('/bookItem', 'BookingApiController@bookItem'); // Need to change
    Route::post('/bookingRecord', 'BookingApiController@bookingRecord');
    Route::post('/vendorbookingRecord', 'BookingApiController@vendorBookingRecord');
    Route::post('/confirmBookingByHost', 'BookingApiController@confirmBookingByHost');
    Route::post('/updateBookingStatusByDriver', 'BookingApiController@updateBookingStatusByDriver');
    Route::post('/updatePaymentStatusByDriver', 'BookingApiController@updatePaymentStatusByDriver');
    Route::post('/updatePaymentStatusByUser', 'BookingApiController@updatePaymentStatusByUser');
    Route::post('/updateBookingStatusByUser', 'BookingApiController@updateBookingStatusByUser');

    Route::post('/editProfile', 'MyAccountController@editProfile');
    Route::post('/uploadProfileImage', 'MyAccountController@uploadProfileImage');
    Route::post('/checkMobileNumber', 'MyAccountController@checkMobileNumber');
    Route::post('/changeMobileNumber', 'MyAccountController@changeMobileNumber');
    Route::post('/checkEmail', 'MyAccountController@checkEmail');
    Route::post('/changeEmail', 'MyAccountController@changeEmail');
    Route::post('/getDriverDashboardStats', 'MyAccountController@getDriverDashboardStats');

    // Static Pages
    Route::post('static-pages/media', 'StaticPagesApiController@storeMedia')->name('static-pages.storeMedia');
    Route::apiResource('static-pages', 'StaticPagesApiController');
    Route::get('StaticPage', 'StaticPagesApiController@StaticPage');

    // All Packages
    Route::post('all-packages/media', 'AllPackagesApiController@storeMedia')->name('all-packages.storeMedia');
    Route::apiResource('all-packages', 'AllPackagesApiController', ['except' => ['destroy']]);

    // General Setting
    Route::get('getgeneralSettings', 'GeneralSettingApiController@getgeneralSettings')->name('getSettings');

    // Payout
    Route::post('/getTotalPayoutAmount', 'PayoutApiController@getTotalPayoutAmount');

    Route::post('fcmUpdate', 'AppUsersApiController@fcmUpdate');

    // email sms push notification
    Route::post('emailSmsNotification', 'AppUsersApiController@emailSmsNotification');
    // currency
    Route::post('/getCurrencyDetails', 'CurrencyApiController@index')->name('getCurrencyDetails');
    Route::get('/updateRates', 'CurrencyApiController@updateRates')->name('updateRates');

    // SOS
    Route::get('/sos', 'SOSController@index')->name('sos.index');
});
