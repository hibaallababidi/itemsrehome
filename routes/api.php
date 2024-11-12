<?php

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DisplayOrdersController;
use App\Http\Controllers\EditProductController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\FiltersController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OperationAdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RespondToOrdersController;
use App\Http\Controllers\RespondToSubmissionsController;
use App\Http\Controllers\SavedController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SubmissionsController;
use App\Http\Controllers\SuggestedCategoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();

Route::group([
    'middleware' => 'localization',
], function () {
    Route::post('register', [RegisterController::class, 'register']);

    Route::group([
        'controller' => LoginController::class
    ], function () {
        Route::post('user_login', 'userLogin');
        Route::post('admin_login', 'adminLogin');
    });

    Route::group([
        'controller' => EmailVerificationController::class
    ],
        function () {
            Route::post('verify_email', 'verifyEmail');
            Route::post('resend_verification_code', 'resendVerificationCode');
        }
    );

    Route::group([
        'controller' => ForgotPasswordController::class
    ],
        function () {
            /*
             * admin
             */
            Route::post('admin_forgot_password', 'adminForgotPassword');
            Route::post('admin_verify_password_email', 'verifyAdminPasswordEmail');
            Route::post('admin_update_password', 'adminUpdatePassword');
            /*
             * user
             */
            Route::post('user_forgot_password', 'userForgotPassword');
            Route::post('user_verify_password_email', 'verifyUserPasswordEmail');
            Route::post('user_update_password', 'userUpdatePassword');
        }
    );

    /*
     * GUEST
     */

    //Helez
    Route::post('addFeedback', [FeedbackController::class, 'add_feedback']);
    Route::get('DisplayProfile', [ProfileController::class, 'DisplayProfile']);


    //Hiba
    Route::group([
        'controller' => CategoryController::class
    ], function () {
        Route::get('display_categories', 'displayCategoriesUser');
        Route::get('display_sub_categories', 'displaySubCategories');
    });
    Route::group([
        'controller' => ProductController::class
    ], function () {
        Route::get('display_products', 'displayProducts');
        Route::get('display_free_products', 'displayFreeProducts');
        Route::get('display_products_by_location', 'displayProductsByLocation');
        Route::get('display_product_details', 'displayProductDetails');
        Route::get('display_user_products', 'displayUserProducts');
    });
    Route::post('filter_products', [FiltersController::class, 'filterProducts']);
    Route::post('search_product', [SearchController::class, 'searchProduct']);

    Route::get('cities', [LocationController::class, 'displayCities']);
    Route::get('locations', [LocationController::class, 'displayLocations']);

    /*
     * ADMIN
     */
    Route::group([
        'prefix' => 'admin',
        'middleware' => ['assign.guard:admins', 'jwt.auth']
    ],
        function () {
            Route::post('reset_password', [ResetPasswordController::class, 'resetPassword']);
            Route::post('logout', [LogoutController::class, 'logout']);

            //Helez
            Route::get('BestSaller', [OperationAdminController::class, 'BestSaller']);
            Route::get('BestBuyers', [OperationAdminController::class, 'BestBuyers']);
            Route::get('display_users_block', [OperationAdminController::class, 'DisplayBlocksUsersForAdmin']);
            Route::get('DisplayUsersForAdmin', [OperationAdminController::class, 'DisplayUsersForAdmin']);
            Route::get('displayFeedback', [FeedbackController::class, 'display_feedback']);
            Route::get('countsuggestedcategory', [SuggestedCategoryController::class, 'count_suggested_category']);
            Route::get('DisplaySuggestedCategory', [SuggestedCategoryController::class, 'show_suggested_category']);
            Route::get('ShowReports', [ReportController::class, 'show_reports']);
            Route::get('DisplayProfile', [ProfileController::class, 'DisplayProfile']);
            Route::post('RemoveBlock', [BlockController::class, 'delete_my_Blocks_List']);
            Route::get('displayPurchaseOperationsForAdmin', [PurchaseController::class, 'display_order_purchase']);
//            Route::get('display_operation_completed', [PurchaseController::class, 'display_operation_completed']);
//            Route::get('display_operation_accepted', [PurchaseController::class, 'display_operation_accepted']);
//            Route::get('display_operation_Pending', [PurchaseController::class, 'display_operation_Pending']);
            Route::get('display_users_block', [OperationAdminController::class, 'DisplayBlocksUsersForAdmin']);
            Route::post('addBlock', [BlockController::class, 'add_block']);

            //Hiba
            Route::group([
                'controller' => CategoryController::class
            ], function () {
                Route::get('display_categories', 'displayCategoriesAdmin');
                Route::get('display_sub_categories', 'displaySubCategoriesAdmin');
                Route::post('add_category', 'addCategory');
                Route::post('add_sub_category', 'addSubCategory');
                Route::post('edit_category', 'editCategory');
                Route::post('edit_sub_category', 'editSubCategory');
                Route::delete('delete_category', [CategoryController::class, 'deleteCategory']);
                Route::delete('delete_sub_category', [CategoryController::class, 'deleteSubCategory']);
            });
            Route::get('display_submissions', [SubmissionsController::class, 'displaySubmissions']);
            Route::get('get_submissions_count', [SubmissionsController::class, 'getSubmissionsCount']);
            Route::get('display_submission_details', [ProductController::class, 'displayProductDetails']);
            Route::get('display_user_products', [ProductController::class, 'displayUserProductsForAdmin']);
            Route::get('display_product_details', [ProductController::class, 'displayProductDetails']);
            Route::group([
                'controller' => RespondToSubmissionsController::class
            ], function () {
                Route::post('accept_submission', 'acceptSubmission');
                Route::post('reject_submission', 'rejectSubmission');
            });
            Route::post('search_user', [SearchController::class, 'searchUser']);
        }
    );

    /*
     * USER
     */
    Route::group([
        'prefix' => 'user',
        'middleware' => ['assign.guard:users', 'jwt.auth', 'force-logged-out']
    ],
        function () {
            Route::post('reset_password', [ResetPasswordController::class, 'resetPassword']);
            Route::post('logout', [LogoutController::class, 'logout']);

            Route::get('get_notification_count', [NotificationController::class, 'getNotificationCount']);
            Route::get('notification_list', [NotificationController::class, 'notificationList']);

            //Helez
            Route::get('DisplayMyProfile', [ProfileController::class, 'DisplayMyProfile']);
            Route::post('AddOrderPurchase', [PurchaseController::class, 'add_order_purchase']);

            Route::post('evaluation', [EvaluationController::class, 'evaluation']);
            Route::delete('deleteMyOrderPurchase', [PurchaseController::class, 'delete_my_order_purchase']);
            Route::get('displayMyOrderPurchase', [PurchaseController::class, 'display_my_order_purchase']);
            Route::post('updateProfile', [ProfileController::class, 'updated_profile']);
            Route::post('addReport', [ReportController::class, 'add_report']);
            Route::post('add_suggested_category', [SuggestedCategoryController::class, 'add_suggested_category']);
            Route::post('update_email', [ProfileController::class, 'updated_email']);
            Route::post('verifyEmailUpdate', [ProfileController::class, 'verifyEmailUpdate']);


            Route::post('savedproduct', [SavedController::class, 'add_saved_product']);
            Route::delete('deletsavedproduct', [SavedController::class, 'delete_my_saved_product']);
            Route::get('ShowSavedProduct', [SavedController::class, 'show_saved_product']);

            //Hiba
            Route::group([
                'controller' => DisplayOrdersController::class
            ], function () {
                Route::get('display_pending_orders', 'displayPendingOrders');
                Route::get('display_accepted_orders', 'displayAcceptedOrders');
                Route::get('display_completed_orders', 'displayCompletedOrders');
                Route::get('display_order_details', 'displayOrderDetails');
            });
            Route::post('set_favourite_category', [CategoryController::class, 'setFavouriteCategory']);
            Route::get('display_my_products', [ProductController::class, 'displayMyProducts']);
            Route::post('edit_product', [EditProductController::class, 'editProduct']);

            Route::post('submit_product', [SubmissionsController::class, 'submitProduct']);
            Route::group([
                'controller' => RespondToOrdersController::class
            ], function () {
                Route::post('accept_order', 'acceptOrder');
                Route::post('reject_order', 'rejectOrder');
                Route::post('complete_order', 'completeOrder');
            });
        }
    );
});
