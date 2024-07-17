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

//========================For Every User Api =========================//
Route::middleware('auth:api')->group(function () {
    Route::get('/user', [UserController::class, 'user']);
    Route::post('/logout', [UserController::class, 'logoutUser']);
    Route::post('/update-password', [UserController::class, 'updatePassword']);
    Route::post('/profile-update/request/{id}', [UserController::class, 'post_update_profile']);

});

// ================================== Admin ========================== //
Route::middleware(['admin', 'auth:api'])->group(function () {    
    Route::post('/profile-update', [UserController::class, 'edit_profile_update']);
    Route::get('/user-delete/{id}', [UserController::class, 'delete_user']);
    Route::resource('covered', CoveredController::class);    

    //=========================== About ===============================//
    Route::post('/about-store', [AboutController::class, 'store']);
    Route::get('/single-about/{id}', [AboutController::class, 'show']);
    Route::post('/about-update/{id}', [AboutController::class, 'update']);

    //============================== Privacy ============================//
    Route::post('/privacy-store', [PrivacyPolicyController::class, 'store']);
    Route::get('/single-privacy/{id}', [PrivacyPolicyController::class, 'show']);
    Route::post('/privacy-update/{id}', [PrivacyPolicyController::class, 'update']);

    //====================== Terms & conditions =========================//
    Route::post('/terems-store', [TermsConditionsController::class, 'store']);
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

    //============================ Client Document =====================//
    Route::get('/user-document', [DocumentControler::class, 'show_user_documet']);
    Route::get('/singel_user_documet/{id}', [DocumentControler::class, 'singel_user_documet']);
    Route::post('/client-document-status', [DocumentControler::class, 'client_document_status']);

    //========================= User management ========================//
    Route::get('/user-management', [UserController::class, 'update_profile_all_user']);
    Route::get('/singel-user-management/{id}', [UserController::class, 'singel_user_update_profile_data']);
    Route::post('/user-status', [UserController::class, 'update_user_status']);

    //======================= Admin management =========================//
    Route::get('/admin-management', [UserController::class, 'admin_user']);

    //========================= User account create ====================//
    Route::get('/create-all-user', [UserController::class, 'all_user']);

    //============================ My Team  ============================//
    Route::get('/my-team', [MyteamController::class, 'show_all_team']);
    Route::get('/singel-team/{id}', [MyteamController::class, 'singel_team_member']);
    Route::post('/teame-status', [MyteamController::class, 'update_team_status']);

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

    //========================== Tier ==================================//    
    Route::post('/tiear-store', [TierController::class, 'storeTier']);
    Route::post('/update-tier/{id}', [TierController::class, 'updateTier']);

});

//========================= Client dashboard ===========================//
Route::middleware(['user', 'auth:api'])->group(function () {

    Route::post('/upload-document', [DocumentControler::class, 'store_document']);
    Route::post('/billing-send-mail', [DocumentControler::class, 'billing']);
    Route::post('/upload-document', [DocumentControler::class, 'store_document']);
    Route::post('/store-teme', [MyteamController::class, 'store_teame']);
    Route::get('/my-teame', [MyteamController::class, 'show_my_team']);
    Route::get('/singel-teame/{id}', [MyteamController::class, 'single_team']);
    Route::get('/delete-teame/{id}', [MyteamController::class, 'delete_team']);
    Route::post('/confirm-order', [VendorController::class, 'confirmOrder']);
});

//======================== Admin and User Both Access ====================//
Route::middleware(['user_admin', 'auth:api'])->group(function () {
    Route::get('/show-tiear', [TierController::class, 'show_tiear']);
    Route::get('/vendor', [VendorController::class, 'AdminVendor_index']);
    Route::get('/ehr', [EHRController::class, 'EHR_index']);
    Route::get('/faith', [FaithExamController::class, 'FithExam_index']);
});

//=============== Website Api with out login access this api =============//
Route::post('/login', [UserController::class, 'loginUser']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/email-verify', [UserController::class, 'emailVerified']);
Route::post('/forget-password', [UserController::class, 'forgetPassword']);
Route::post('/reset-password', [UserController::class, 'resetPassword']);
Route::post('/resend-otp', [UserController::class, 'resendOtp']);
Route::post('/contact', [ContactController::class, 'contact_mail']);
Route::post('/trial', [ContactController::class, 'coustom_trial']);
Route::post('/parsonal-info', [IntekInformationController::class, 'parsonal_info']);
Route::post('/buisness-info', [IntekInformationController::class, 'buisness_info']);
Route::post('/appoinment-info', [IntekInformationController::class, 'appoinment_info']);
Route::get('/about', [AboutController::class, 'about_index']);
Route::get('/privacy', [PrivacyPolicyController::class, 'privacy_index']);
Route::get('/terems', [TermsConditionsController::class, 'terms_index']);
Route::get('/faq', [FAqController::class, 'faq_index']);
Route::get('/show-covered', [CoveredController::class, 'index']);