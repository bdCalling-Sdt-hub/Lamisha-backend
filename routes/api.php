<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\UserController;
use App\Http\Controllers\Api\About\ContactController;
use App\Http\Controllers\Api\About\IntekInformationController;
use App\Http\Controllers\Api\About\CoveredController;
use App\Http\Controllers\Api\About\AboutController;
use App\Http\Controllers\Api\About\PrivacyPolicyController;
use App\Http\Controllers\Api\About\TermsConditionsController;
use App\Http\Controllers\Api\About\FAqController;
use App\Http\Controllers\Api\Admin\AdminintextInfoController;
use App\Http\Controllers\Api\Client\DocumentControler;
use App\Http\Controllers\Api\Client\MyteamController;
use App\Http\Controllers\Api\About\FaithExamController;
use App\Http\Controllers\Api\About\EHRController;
use App\Http\Controllers\Api\About\VendorController;
use App\Http\Controllers\Api\About\TierController;
use App\Http\Controllers\Api\Admin\PricingController;
use App\Http\Middleware\Cors;
use Illuminate\Support\Facades\Http;

//========================For Every User Api =========================//
Route::middleware('auth:api')->group(function () {
    Route::get('/user', [UserController::class, 'user']);
    Route::post('/logout', [UserController::class, 'logoutUser']);
    Route::post('/update-password', [UserController::class, 'updatePassword']);
    Route::post('/profile-update/request', [UserController::class, 'post_update_profile']);
    Route::put('/profile-image-update', [UserController::class, 'profile_image_update']);
    Route::get('/profile-image-get', [UserController::class, 'profile_image_get']);
    Route::delete('/admin-user-delete', [UserController::class, 'adminUserDelete']); // super-admin

});

Route::post('/register', [UserController::class, 'register']);

// ================================== Admin ========================== //
Route::middleware(['admin', 'auth:api'])->group(function () {

    Route::post('/profile-update', [UserController::class, 'edit_profile_update']);
    Route::post('/admin-update/{id}', [UserController::class, 'adminUpdate']);
    Route::delete('/user-delete/{id}', [UserController::class, 'delete_user']);
    Route::resource('covered', CoveredController::class);

    //=========================== About ===============================//
    Route::post('/about-store-or-update', [AboutController::class, 'storeOrUpdate']);
    Route::get('/single-about/{id}', [AboutController::class, 'show']);
    Route::post('/about-update/{id}', [AboutController::class, 'update']);

    //============================== Privacy ============================//
    Route::post('/privacy-store', [PrivacyPolicyController::class, 'store']);
    Route::get('/single-privacy/{id}', [PrivacyPolicyController::class, 'show']);
    Route::post('/privacy-update/{id}', [PrivacyPolicyController::class, 'update']);

    //====================== Terms & conditions =========================//
    Route::post('/terems-store', [TermsConditionsController::class, 'storeOrUpdate']);
    Route::get('/single-terems/{id}', [TermsConditionsController::class, 'show']);
    Route::post('/terens-update/{id}', [TermsConditionsController::class, 'update']);

    //=============================== FAq ==============================//
    Route::post('/faq-store', [FAqController::class, 'store']);
    Route::get('/single-faq/{id}', [FAqController::class, 'show']);
    Route::post('/faq-update/{id}', [FAqController::class, 'update']);
    Route::get('/faq-delete/{id}', [FAqController::class, 'destroy']);

    //=================== Deshboard Intek information ==================//
    Route::get('/intekinfo', [AdminintextInfoController::class, 'intekInof']);
    Route::get('/singel/itekinfo/{id}', [AdminintextInfoController::class, 'singleIntek_info']);
    Route::post('/parsonal-status', [AdminintextInfoController::class, 'update_parsonal_status']);
    Route::post('/bisness-status', [AdminintextInfoController::class, 'update_buisness_status']);
    Route::post('/appoinment-status', [AdminintextInfoController::class, 'update_appoinment_status']);
    Route::get('/notification', [AdminintextInfoController::class, 'getUserNotifications']);
    Route::get('/update-notification/{id}', [AdminintextInfoController::class, 'updateNotification']);

    //============================ Client Document =====================//
    Route::get('/user-document', [DocumentControler::class, 'show_user_documet']);
    Route::get('/singel_user_documet/{id}', [DocumentControler::class, 'singel_user_documet']);
    Route::post('/client-document-status', [DocumentControler::class, 'client_document_status']);


    //========================= User management ========================//
    Route::get('/user-management', [UserController::class, 'update_profile_all_user']);
    Route::get('/singel-user-management/{id}', [UserController::class, 'singel_user_update_profile_data']);
    Route::post('/user-status', [UserController::class, 'update_user_status']);
    Route::get('/all-user', [UserController::class, 'all_user']);
    Route::post('/update-profile-status', [UserController::class, 'updateProfileStatus']);
    Route::post('/client-type-update/{id}', [UserController::class, 'updatClientType']);
    Route::get('/update-user/{id}', [UserController::class,'updateUser']);

    //======================= Admin management =========================//
    Route::get('/admin-management', [UserController::class, 'admin_user']);

    //========================= User account create ====================//
    Route::get('/create-all-user', [UserController::class, 'allCreateUser']);

    //============================ My Team  ============================//
    Route::get('/my-team', [MyteamController::class, 'show_all_team']);
    Route::get('/singel-team/{id}', [MyteamController::class, 'singel_team_member']);
    Route::post('/teame-status', [MyteamController::class, 'update_team_status']);

    //======================== Add Tear or Pricing ====================//
    Route::post('/add-tiear', [PricingController::class, 'store']);
    Route::post('/update-tiear/{id}', [PricingController::class, 'update']);

    //========================= Fith examin ===========================//
    Route::post('/faith-store', [FaithExamController::class, 'store']);
    Route::get('/single-faith/{id}', [FaithExamController::class, 'show']);
    Route::post('/faith-update/{id}', [FaithExamController::class, 'update']);
    Route::get('/faith-delete/{id}', [FaithExamController::class, 'destroy']);

    //============================= EHR ================================//
    Route::post('/ehr-store', [EHRController::class, 'store']);
    Route::get('/single-ehr/{id}', [EHRController::class, 'show']);
    Route::post('/ehr-update/{id}', [EHRController::class, 'update']);
    Route::get('/ehr-delete/{id}', [EHRController::class, 'destroy']);

    //========================= Vendor =================================//
    Route::post('/vendor-store', [VendorController::class, 'store']);
    Route::get('/vendor-delete/{id}', [VendorController::class, 'destroy']);

    //========================== QA ====================================//
    Route::get('/qa-index', [VendorController::class, 'qaIndex']);
    Route::get('/single-qa/{id}', [VendorController::class, 'singelQa']);
    Route::post('/qa-store', [VendorController::class, 'qaStore']);
    Route::get('/qa-delete/{id}', [VendorController::class, 'qaDestroy']);


    //========================== Tier ==================================//
    Route::post('/tiear-update', [TierController::class, 'updateTier']);
    Route::post('/update-tier/{id}', [TierController::class, 'updateTier']);

});

//========================= Client dashboard ===========================//
Route::middleware(['user', 'auth:api'])->group(function () {

    Route::post('/upload-document', [DocumentControler::class, 'store_document']);
    Route::post('/billing-send-mail', [DocumentControler::class, 'billing']);
    Route::get('/get-billing', [DocumentControler::class, 'get_billing']);
    Route::post('/update-aggriment', [DocumentControler::class, 'update_document']);
    Route::post('/update-document-appoinment', [DocumentControler::class, 'updateDocumentAppoinment']);
    Route::post('/store-teme', [MyteamController::class, 'store_teame']);
    Route::get('/my-teame', [MyteamController::class, 'show_my_team']);
    Route::get('/singel-teame/{id}', [MyteamController::class, 'single_team']);
    Route::delete('/delete-teame/{id}', [MyteamController::class, 'delete_team']);
    Route::post('/confirm-order', [VendorController::class, 'confirmOrder']);
    Route::get('/qa-client', [VendorController::class, 'clentQa']);
    Route::get('/check-document-status', [DocumentControler::class, 'checkStatus']);
    Route::get('/auth-user-document', [DocumentControler::class, 'show_auth_user_documet']);
});

//======================== Admin and User Both Access ====================//
Route::middleware(['user_admin', 'auth:api'])->group(function () {
    Route::get('/vendor', [VendorController::class, 'AdminVendor_index']);
    Route::get('/ehr', [EHRController::class, 'EHR_index']);
    Route::get('/faith', [FaithExamController::class, 'FithExam_index']);
    Route::get('/show-tiear', [TierController::class, 'show_tiear']);
    Route::get('/client-tiear', [TierController::class, 'client_tier']);
});


//=============== Website Api with out login access this api =============//
Route::get('/proxy-api', function () {
    $response = Http::get('http://159.65.14.5:8000/api/buisness-info');
    return $response->body();
});
Route::post('/login', [UserController::class, 'loginUser']);
Route::post('/email-verify', [UserController::class, 'emailVerified']);
Route::post('/forget-password', [UserController::class, 'forgetPassword']);
Route::post('/reset-password', [UserController::class, 'resetPassword']);
Route::post('/resend-otp', [UserController::class, 'resendOtp']);
Route::post('/contact', [ContactController::class, 'contact_mail']);
Route::post('/trial', [ContactController::class, 'coustom_trial']);
Route::post('/parsonal-info', [IntekInformationController::class, 'parsonal_info']);
Route::post('/buisness-info', [IntekInformationController::class, 'buisness_info']);
Route::post('/appoinment-info', [IntekInformationController::class, 'appointment_info']);
Route::get('/about', [AboutController::class, 'about_index']);
Route::get('/privacy', [PrivacyPolicyController::class, 'privacy_index']);
Route::get('/terems', [TermsConditionsController::class, 'terms_index']);
Route::get('/faq', [FAqController::class, 'faq_index']);
Route::get('/show-covered', [CoveredController::class, 'index']);
Route::get('/pricing', [PricingController::class, 'Index']);
Route::get('/show-tiear', [TierController::class, 'show_tiear']);
Route::get('/show-tiear-price', [TierController::class, 'showTiearPricing']);
