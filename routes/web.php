<?php

use App\Http\Controllers\AuditUserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroupdescriptionController;
use App\Http\Controllers\GroupmemberandroleController;
use App\Http\Controllers\RequesterController;
use App\Http\Controllers\TasklistController;
use App\Http\Controllers\ProcurementController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AuditlogController;
use App\Http\Controllers\BranchcodeController;
use App\Http\Controllers\BudgetcodeController;
use App\Http\Controllers\ActivedirectoryController;
use App\Http\Controllers\AdvanceFormController;
use App\Http\Controllers\AdvanceRecordController;
use App\Http\Controllers\BankPaymentController;
use App\Http\Controllers\BankPaymentVoucherController;
use App\Http\Controllers\ClearAdvanceFormController;
use App\Http\Controllers\DeleterecordController;
use App\Http\Controllers\DEUploadController;
use App\Http\Controllers\GeneralLedgerController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RealBranchController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SegmentController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TAXCodeController;
use App\Http\Controllers\BankReceiptController;
use App\Http\Controllers\CashPaymentController;
use App\Http\Controllers\CashReceiptVoucherController;
use App\Models\GeneralLedgerCode;
use App\Models\PaymentMethod;
use App\Http\Controllers\ReportAdvanceController;
use App\Http\Controllers\ReportClearAdvanceController;
use App\Http\Controllers\BlockFormController;
use App\Http\Controllers\JournalVoucherController;
use App\Http\Controllers\BankVoucherController;
use App\Http\Controllers\ExportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



// Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
//     return view('dashboard');
// })->name('dashboard');
Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('report/listing/{status}', [DashboardController::class, 'reportListing'])->name('report/listing');
    Route::get('get-dashboard-data', [DashboardController::class, 'getDashboardListing'])->name('get-dashboard-data');
    // Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('group/listing', [GroupdescriptionController::class, 'groupListing'])->name('group/listing');
    Route::get('specialgroup/listing', [GroupdescriptionController::class, 'specialGroupListing'])->name('specialgroup/listing');
    Route::get('get-group-listing-data', [GroupdescriptionController::class, 'getGroupListingData'])->name('get-group-listing-data');
    Route::get('get-special-group-listing-data', [GroupdescriptionController::class, 'getSpecialListingData'])->name('get-special-group-listing-data');

    Route::post('group/save', [GroupdescriptionController::class, 'saveGroupListing'])->name('group/save');
    Route::get('group/member/listing/{group_id}', [GroupmemberandroleController::class, 'groupListingFilter']);
    Route::post('group/member/save', [GroupmemberandroleController::class, 'saveGroupListing'])->name('group/member/save');
    Route::get('listing/users', [GroupmemberandroleController::class, 'listingUsers'])->name('listing/users');
    Route::get('listing/users-data', [GroupmemberandroleController::class, 'listingUserData'])->name('listing/users-data');

    // Branch Code ??
    Route::get('branchcode/listing', [BranchcodeController::class, 'index'])->name('branchcode/listing');
    Route::post('branchcode/save', [BranchcodeController::class, 'saveBranchCode'])->name('branchcode/save');
    Route::get('get-branch-code-listing-data', [BranchcodeController::class, 'getBranchCodeData'])->name('get-branch-code-listing-data');
    // End Brach Code


    Route::get('tasklist', [TasklistController::class,'index'])->name('tasklist');
    Route::get('get-task-listing-data', [TasklistController::class, 'getTaskListingData'])->name('get-task-listing-data');

    // Start Budget Code
    Route::get('budgetcode/listing', [BudgetcodeController::class, 'index'])->name('budgetcode/listing');
    Route::get('budgetcode/detail/{id}', [BudgetcodeController::class, 'detail'])->name('budgetcode/detail');
    Route::post('budgetcode/upload', [BudgetcodeController::class, 'uploadFile'])->name('budgetcode/upload');
    Route::post('budgetcode/save', [BudgetcodeController::class, 'saveBudget'])->name('budgetcode/save');
    Route::get('get-budget-code-listing-data', [BudgetcodeController::class, 'getBudgetCodeData'])->name('get-budget-code-listing-data');
    //End Budget Code

    Route::get('auditlog/listing', [AuditlogController::class, 'index'])->name('auditlog/listing');
    Route::get('get-auditlog-listing-data', [AuditlogController::class, 'getAuditLogListingData'])->name('get-auditlog-listing-data');
    // ***** Start Procurement **** //
    Route::get('form/procurement/new', [ProcurementController::class, 'index'])->name('form/procurement/new');
    Route::get('form/procurement/listing', [ProcurementController::class, 'listing'])->name('form/procurement/listing');
    Route::post('form/procurement/save', [ProcurementController::class,'procurementSave'])->name('form/procurement/save');
    Route::get('form/procurement/detail/{req_recid}', [ProcurementController::class, 'procurementDetail'])->name('form/procurement/detail'); 
    Route::post('form/procurement/action', [ProcurementController::class, 'actionRequest'])->name('form/procurement/action');
    Route::post('form/procurement/editrow', [ProcurementController::class, 'editRow'])->name('form/procurement/editrow');
    Route::post('form/procurement/resubmit', [ProcurementController::class, 'resubmit'])->name('form/procurement/resubmit');
    Route::get('form/procurement/list', [ProcurementController::class, 'listAuthRequest'])->name('procurement_request.list');
    Route::get('form/procurement/pdf/{req_recid}', [ProcurementController::class, 'generatePDF'])->name('form/procurement/pdf');
    Route::get('download/procurement-excel', [ProcurementController::class, 'downloadExcel'])->name('download/procurement-excel');
    Route::get('get-procurement-listing-data', [ProcurementController::class, 'getProcurementListingData'])->name('get-procurement-listing-data');
    Route::post('update-bid', [ProcurementController::class, 'updateBid'])->name('update-bid');
    Route::post('form/procurement/detail/update_vendor',[ProcurementController::class, 'updateVendor'])->name('form/procurement/detail/update_vendor');
    Route::post('form/procurement/detail/update_vat', [ProcurementController::class, 'updateCheckBox'])->name('form/procurement/detail/update_vat');
    Route::get('get-procurement-request-listing-data', [ProcurementController::class, 'getProcurementRequestListingData'])->name('get-procurement-request-listing-data');
    // ***** End Procurement **** //
    // ***** Start Payment Request *****//
    Route::get('form/payment/new/ref/{id}/{pr_id}', [PaymentController::class, 'fromProcurementNew']);
    Route::get('form/payment/new', [PaymentController::class, 'index'])->name('form/payment/new')->middleware('block.form');
    Route::post('form/payment/save', [PaymentController::class,'paymentSave'])->name('form/payment/save');
    Route::get('form/payment/detail/{req_recid}', [PaymentController::class, 'paymentDetail'])->name('form/payment/detail');
    Route::post('form/payment/action', [PaymentController::class, 'actionRequest'])->name('form/payment/action');
    Route::post('form/payment/editrow', [PaymentController::class, 'editRow'])->name('form/payment/editrow');
    Route::post('form/payment/resubmit', [PaymentController::class, 'resubmit'])->name('form/payment/resubmit');
    Route::get('form/payment/list', [PaymentController::class, 'listAuthRequest'])->name('payment_request.list_auth')->middleware('block.form');
    Route::get('form/payment/pdf/{req_recid}', [PaymentController::class, 'generatePDF'])->name('form/payment/pdf');
    Route::get('download/payment-excel', [PaymentController::class, 'downloadExcel'])->name('download/payment-excel');
    Route::get('get-payment-listing-data', [PaymentController::class, 'getPaymentListingData'])->name('get-payment-listing-data');
    
    // ***** End Payment Request **** //
    Route::post('form/delete', [DeleterecordController::class,'deleteRecord']);

    Route::get('download/{uuid}', [DeleterecordController::class,'download']);
    Route::get('requestlog/listing', [DeleterecordController::class,'requestLog'])->name('requestlog/listing');
    Route::post('requestlog/filter-listing', [DeleterecordController::class,'filterRequestLog'])->name('requestlog/filter-listing');
    Route::get('get-requestlog-listing-data', [DeleterecordController::class, 'getRequestLogData'])->name('get-requestlog-listing-data');

    Route::get('user-tracking', [AuditUserController::class,'index'])->name('user_log.index');
    Route::get('get-user-tracking-data', [AuditUserController::class,'getUserTrackingData'])->name('get-user-tracking-data');
    Route::get('budget-code-tracking/{track_id}', [AuditUserController::class,'budgetCodeTracking'])->name('budget-code-tracking');
    /** reports */
    Route::get('reports/advance-clear-procurement-tracking-request', [ReportController::class,'advanceClearProcurementRequestTracking'])->name('reports/advance-clear-procurement-tracking-request');
    Route::get('reports/payment-procurement-tracking-request', [ReportController::class,'paymentProcurementRequestTracking'])->name('reports/payment-procurement-tracking-request');
    Route::get('reports/payment-tracking-request', [ReportController::class,'paymentRequestTracking'])->name('reports/payment-tracking-request');
    Route::get('reports/accounting-voucher-tracking-request', [ReportController::class,'accountingVoucherRequestTracking'])->name('reports/accounting-voucher-tracking-request');
    Route::get('reports/accounting-voucher-tracking-request-search', [ReportController::class,'accountingVoucherRequestTrackingSearch'])->name('reports/accounting-voucher-tracking-request-search');
    Route::get('reports/procurement-tracking-request', [ReportController::class,'procurementRequestTracking'])->name('reports/procurement-tracking-request');
    Route::get('reports/budget-tracking-request', [ReportController::class,'budgetRequestTracking'])->name('reports/budget-tracking-request');
    Route::get('reports/journal-tracking-request-search', [ReportController::class,'journalVoucherRequestTrackingSearch'])->name('reports/journal-tracking-request-search');
    Route::get('reports/bank-receipt-tracking-request-search', [ReportController::class,'bankReceiptVoucherRequestTrackingSearch'])->name('reports/bank-receipt-tracking-request-search');
    Route::get('reports/cash-payment-tracking-request-search', [ReportController::class,'cashPaymentVoucherRequestTrackingSearch'])->name('reports/cash-payment-tracking-request-search');
    Route::get('reports/cash-receipt-tracking-request-search', [ReportController::class,'cashReceiptVoucherRequestTrackingSearch'])->name('reports/cash-receipt-tracking-request-search');
    Route::get('reports/bank-voucher-tracking-request-search', [ReportController::class,'BankVoucherRequestTrackingSearch'])->name('reports/bank-voucher-tracking-request-search');

    Route::post('reports/filter-payment-tracking-request', [ReportController::class,'filterPaymentReportTracking'])->name('reports/filter-payment-tracking-request');
    
    Route::get('get-payment-procurement-tracking-data', [ReportController::class,'getPaymentAndProcurementRequestTrackingData'])->name('get-payment-procurement-tracking-data');
    Route::get('get-clear-advance-procurement-tracking-data', [ReportController::class,'getClearAdvanceAndProcurementRequestTrackingData'])->name('get-clear-advance-procurement-tracking-data');
    Route::get('get-payment-tracking-data', [ReportController::class,'getPaymentRequestTrackingData'])->name('get-payment-tracking-data');
    Route::get('get-accounting-voucher-tracking-data', [ReportController::class,'getAccountingVoucherRequestTrackingData'])->name('get-accounting-voucher-tracking-data');
    Route::get('get-cash-payment-voucher-tracking-data', [ReportController::class,'getCashPaymentVoucherRequestTrackingData'])->name('get-cash-payment-voucher-tracking-data');
    Route::get('get-cash-receipt-voucher-tracking-data', [ReportController::class,'getCashReceiptVoucherRequestTrackingData'])->name('get-cash-receipt-voucher-tracking-data');
    Route::get('get-bank-receipt-voucher-tracking-data', [ReportController::class,'getBankREceiptVoucherRequestTrackingData'])->name('get-bank-receipt-voucher-tracking-data');
    Route::get('get-bank-voucher-tracking-data', [ReportController::class,'getBankVoucherRequestTrackingData'])->name('get-bank-voucher-tracking-data');
    Route::get('get-procurement-tracking-data', [ReportController::class,'getProcurementRequestTrackingData'])->name('get-procurement-tracking-data');
    Route::get('get-budget-tracking-data', [ReportController::class,'getBudgetRequestTrackingData'])->name('get-budget-tracking-data');
    Route::get('get-journal-voucher-tracking-data', [ReportController::class,'getJournalVoucherRequestTrackingData'])->name('get-journal-voucher-tracking-data');

    /** records */

    /** reports advance */
     Route::get('reports/advance-tracking-request', [ReportAdvanceController::class,'advanceRequestTracking'])->name('reports/advance-tracking-request');
     Route::post('reports/filter-advance-tracking-request', [ReportAdvanceController::class,'filterAdvanceReportTracking'])->name('reports/filter-advance-tracking-request');
     Route::get('get-advance-tracking-data', [ReportAdvanceController::class,'getAdvanceRequestTrackingData'])->name('get-advance-tracking-data');
    /** records advance */

    /** reports clear advance */
    Route::get('reports/clear-advance-tracking-request', [ReportClearAdvanceController::class,'clearAdvanceRequestTracking'])->name('reports/clear-advance-tracking-request');
    Route::post('reports/filter-clear-advance-tracking-request', [ReportClearAdvanceController::class,'filterClearAdvanceReportTracking'])->name('reports/filter-clear-advance-tracking-request');
    Route::get('get-clear-advance-tracking-data', [ReportClearAdvanceController::class,'getClearAdvanceRequestTrackingData'])->name('get-clear-advance-tracking-data');
   /** reports clear advance */
    Route::get('lists/payment-record', [PaymentController::class, 'listPaymentRecord'])->name('lists/payment-record');

    Route::post('payment/ytd-expense/preview', [PaymentController::class, 'previewPaymentYTDExpense'])->name('payment/ytd-expense/preview');

    /** advance form */
    Route::get('form/advances', [AdvanceFormController::class, 'index'])->name('form/advances')->middleware('block.form');
    Route::get('form/advances/create', [AdvanceFormController::class, 'create'])->name('form/advances/create')->middleware('block.form');
    Route::get('form/advances/edit/{cryptedString}', [AdvanceFormController::class, 'edit'])->name('form/advances/edit');
    Route::get('form/advances/detail/{cryptedString}', [AdvanceFormController::class, 'detail'])->name('form/advances/detail');
    Route::get('form/advances/show-for-approval/{cryptedString}', [AdvanceFormController::class, 'showForApproval'])->name('form/advances/show-for-approval');
    Route::get('form/advances/show-for-resubmitting/{cryptedString}', [AdvanceFormController::class, 'showReSubmitForm'])->name('form/advances/show-for-resubmitting');
    Route::get('form/advances/show-for-query/{cryptedString}', [AdvanceFormController::class, 'showForQuery'])->name('form/advances/show-for-query');
    Route::get('form/advances/export-to-pdf/{cryptedString}', [AdvanceFormController::class, 'exportFormToPDF'])->name('form/advances/export-to-pdf');
    Route::get('form/advances/save-procurement-references/{cryptedString}/{references}', [AdvanceFormController::class, 'showWithProcurmentReferences'])->name('form/advances/save-procurement-references');
    Route::get('form/advances/download-template-excel', [AdvanceFormController::class, 'downloadExcel'])->name('form/advances/download-template-excel');

    Route::post('form/advances/save-draft', [AdvanceFormController::class, 'saveDraft'])->name('form/advances/save-draft');
    Route::post('form/advances/items/delete', [AdvanceFormController::class, 'deleteItem'])->name('form/advances/items/delete');
    Route::post('form/advances/items/update', [AdvanceFormController::class, 'updateItem'])->name('form/advances/items/update');
    Route::post('form/advances/items/add-new', [AdvanceFormController::class, 'addNewItem'])->name('form/advances/items/add-new');
    Route::post('form/advances/items/submit', [AdvanceFormController::class, 'submitRequest'])->name('form/advances/items/submit');
    Route::post('form/advances/ytd-expense/preview', [AdvanceFormController::class, 'previewYTDExpense'])->name('form/advances/ytd-expense/preview');
    Route::post('form/advances/delete-request', [AdvanceFormController::class, 'deleteRequest'])->name('form/advances/delete-request');
    Route::post('form/advances/approve-request', [AdvanceFormController::class, 'approveRequest'])->name('form/advances/approve-request');
    Route::post('form/advances/re-submit', [AdvanceFormController::class, 'resubmitForm'])->name('form/advances/re-submit');
    Route::post('form/advances/items/query-back-to-approver', [AdvanceFormController::class, 'queryBackToApprover'])->name('form/advances/items/query-back-to-approver');

    /** Clear advance form */
    Route::get('form/clear-advances', [ClearAdvanceFormController::class, 'index'])->name('form/clear-advances')->middleware('block.form');
    Route::get('form/clear-advances/create', [ClearAdvanceFormController::class, 'create'])->name('form/clear-advances/create')->middleware('block.form');
    Route::get('form/clear-advances/save-advance-references/{cryptedString}/{references}', [ClearAdvanceFormController::class, 'showWithAdvanceReferences'])->name('form/clear-advances/save-advance-references');
    Route::get('form/clear-advances/edit/{cryptedString}', [ClearAdvanceFormController::class, 'edit'])->name('form/clear-advances/edit');
    Route::get('form/clear-advances/detail/{cryptedString}', [ClearAdvanceFormController::class, 'detail'])->name('form/clear-advances/detail');
    Route::get('form/clear-advances/show-for-approval/{cryptedString}', [ClearAdvanceFormController::class, 'showForApproval'])->name('form/advances/show-for-approval');
    Route::get('form/clear-advances/show-for-resubmitting/{cryptedString}', [ClearAdvanceFormController::class, 'showReSubmitForm'])->name('form/advances/show-for-resubmitting');
    Route::get('form/clear-advances/show-for-query/{cryptedString}', [ClearAdvanceFormController::class, 'showForQuery'])->name('form/clear-advances/show-for-query');
    Route::get('form/clear-advances/export-to-pdf/{cryptedString}', [ClearAdvanceFormController::class, 'exportFormToPDF'])->name('form/advances/export-to-pdf');
    Route::get('form/clear-advances/download-template-excel', [ClearAdvanceFormController::class, 'downloadExcel'])->name('form/clear-advances/download-template-excel');

    Route::post('form/clear-advances/save-draft', [ClearAdvanceFormController::class, 'saveDraft'])->name('form/clear-advances/save-draft');
    Route::post('form/clear-advances/items/delete', [ClearAdvanceFormController::class, 'deleteItem'])->name('form/clear-advances/items/delete');
    Route::post('form/clear-advances/items/update', [ClearAdvanceFormController::class, 'updateItem'])->name('form/clear-advances/items/update');
    Route::post('form/clear-advances/items/add-new', [ClearAdvanceFormController::class, 'addNewItem'])->name('form/clear-advances/items/add-new');
    Route::post('form/clear-advances/items/submit', [ClearAdvanceFormController::class, 'submitRequest'])->name('form/clear-advances/items/submit');
    Route::post('form/clear-advances/ytd-expense/preview', [ClearAdvanceFormController::class, 'previewYTDExpense'])->name('form/clear-advances/ytd-expense/preview');
    Route::post('form/clear-advances/delete-request', [ClearAdvanceFormController::class, 'deleteRequest'])->name('form/clear-advances/delete-request');
    Route::post('form/clear-advances/approve-request', [ClearAdvanceFormController::class, 'approveRequest'])->name('form/clear-advances/approve-request');
    Route::post('form/clear-advances/re-submit', [ClearAdvanceFormController::class, 'resubmitForm'])->name('form/clear-advances/re-submit');
    Route::post('form/clear-advances/items/query-back-to-approver', [ClearAdvanceFormController::class, 'queryBackToApprover'])->name('form/clear-advances/items/query-back-to-approver');

    /** Bank payment vourcher request */
    Route::get('form/bank-payment-vouchers', [BankPaymentVoucherController::class, 'index'])->name('form/bank-payment-vouchers');
    Route::get('form/bank-payment-vouchers/create', [BankPaymentVoucherController::class, 'create'])->name('form/bank-payment-vouchers/create');
    Route::get('form/bank-payment-vouchers/edit/{cryptedString}', [BankPaymentVoucherController::class, 'edit'])->name('form/bank-payment-vouchers/edit');
    Route::get('form/bank-payment-vouchers/detail/{cryptedString}', [BankPaymentVoucherController::class, 'detail'])->name('form/bank-payment-vouchers/detail');
    Route::get('form/bank-payment-vouchers/download-template-excel', [BankPaymentVoucherController::class, 'downloadExcel'])->name('form/bank-payment-vouchers/download-template-excel');
    Route::get('form/bank-payment-vouchers/show-for-approval/{cryptedString}', [BankPaymentVoucherController::class, 'showForApproval'])->name('form/bank-payment-vouchers/show-for-approval');
    Route::get('form/bank-payment-vouchers/show-for-resubmitting/{cryptedString}', [BankPaymentVoucherController::class, 'showReSubmitForm'])->name('form/bank-payment-vouchers/show-for-resubmitting');
    Route::get('form/bank-payment-vouchers/show-for-query/{cryptedString}', [BankPaymentVoucherController::class, 'showForQuery'])->name('form/bank-payment-vouchers/show-for-query');
    Route::get('form/bank-payment-vouchers/export-to-pdf/{cryptedString}', [BankPaymentVoucherController::class, 'exportFormToPDF'])->name('form/bank-payment-vouchers/export-to-pdf');
    Route::get('form/bank-payment-vouchers/get-rquest-info/{cryptedString}/{references}', [BankPaymentVoucherController::class, 'getRequestInfo'])->name('form/bank-payment-vouchers/get-rquest-info');
    Route::get('form/bank-payment-vouchers/export-to-excel/{cryptedString}', [BankPaymentVoucherController::class, 'exportFormToExcel'])->name('form/bank-payment-vouchers/export-to-excel');
    
    Route::post('form/bank-payment-vouchers/save-draft', [BankPaymentVoucherController::class, 'saveDraft'])->name('form/bank-payment-vouchers/save-draft');
    Route::post('form/bank-payment-vouchers/items/add-new', [BankPaymentVoucherController::class, 'addNewItem'])->name('form/bank-payment-vouchers/items/add-new');
    Route::post('form/bank-payment-vouchers/items/update', [BankPaymentVoucherController::class, 'updateItem'])->name('form/bank-payment-vouchers/items/update');
    Route::post('form/bank-payment-vouchers/delete', [BankPaymentVoucherController::class, 'delete'])->name('form/bank-payment-vouchers/delete');
    Route::post('form/bank-payment-vouchers/update-exchange-rate', [BankPaymentVoucherController::class, 'updateExchangeRate'])->name('form/bank-payment-vouchers/update-exchange-rate');
    Route::post('form/bank-payment-vouchers/submit', [BankPaymentVoucherController::class, 'submitRequest'])->name('form/bank-payment-vouchers/submit');
    Route::post('form/bank-payment-vouchers/approve-request', [BankPaymentVoucherController::class, 'approveRequest'])->name('form/bank-payment-vouchers/approve-request');
    Route::post('form/bank-payment-vouchers/re-submit', [BankPaymentVoucherController::class, 'resubmitForm'])->name('form/bank-payment-vouchers/re-submit');
    Route::post('form/bank-payment-vouchers/items/query-back-to-approver', [BankPaymentVoucherController::class, 'queryBackToApprover'])->name('form/bank-payment-vouchers/items/query-back-to-approver');
    Route::post('form/bank-payment-vouchers/update-status-request', [BankPaymentVoucherController::class, 'updateStatus'])->name('form/bank-payment-vouchers/update-status-request');

    // Journal vourcher request
    Route::get('form/journal-vouchers', [JournalVoucherController::class, 'index'])->name('form/journal-vouchers');
    Route::get('form/journal-vouchers/create', [JournalVoucherController::class, 'create'])->name('form/journal-vouchers/create');
    Route::get('form/journal-vouchers/edit/{cryptedString}', [JournalVoucherController::class, 'edit'])->name('form/journal-vouchers/edit');
    Route::post('form/journal-vouchers/save-draft', [JournalVoucherController::class, 'saveDraft'])->name('form/journal-vouchers/save-draft');
    Route::get('form/journal-vouchers/download-template-excel', [JournalVoucherController::class, 'downloadExcel'])->name('form/journal-vouchers/download-template-excel');
    Route::post('form/journal-vouchers/submit', [JournalVoucherController::class, 'submitRequest'])->name('form/journal-vouchers/submit');
    Route::post('form/journal-vouchers/items/add-new', [JournalVoucherController::class, 'addNewItem'])->name('form/journal-vouchers/items/add-new');
    Route::post('form/journal-vouchers/items/update', [JournalVoucherController::class, 'updateItem'])->name('form/journal-vouchers/items/update');
    Route::post('form/journal-vouchers/delete', [JournalVoucherController::class, 'delete'])->name('form/journal-vouchers/delete');
    Route::post('form/journal-vouchers/update-exchange-rate', [JournalVoucherController::class, 'updateExchangeRate'])->name('form/journal-vouchers/update-exchange-rate');
    Route::get('form/journal-vouchers/detail/{cryptedString}', [JournalVoucherController::class, 'detail'])->name('form/journal-vouchers/detail/');
    Route::get('form/journal-vouchers/export-to-pdf/{cryptedString}', [JournalVoucherController::class, 'exportFormToPDF'])->name('form/journal-vouchers/export-to-pdf');
    Route::get('form/journal-vouchers/show-for-approval/{cryptedString}', [JournalVoucherController::class, 'showForApproval'])->name('form/journal-vouchers/show-for-approval');
    Route::get('form/journal-vouchers/show-for-resubmitting/{cryptedString}', [JournalVoucherController::class, 'showReSubmitForm'])->name('form/journal-vouchers/show-for-resubmitting');
    Route::get('form/journal-vouchers/show-for-query/{cryptedString}', [JournalVoucherController::class, 'showForQuery'])->name('form/journal-vouchers/show-for-query');
    Route::post('form/journal-vouchers/approve-request', [JournalVoucherController::class, 'approveRequest'])->name('form/journal-vouchers/approve-request');
    Route::post('form/journal-vouchers/re-submit', [JournalVoucherController::class, 'resubmitForm'])->name('journal-vouchers/re-submit');
    Route::post('form/journal-vouchers/items/query-back-to-approver', [JournalVoucherController::class, 'queryBackToApprover'])->name('form/journal-vouchers/items/query-back-to-approver');
    Route::get('form/journal-vouchers/get-rquest-info/{cryptedString}/{references}', [JournalVoucherController::class, 'getRequestInfo'])->name('form/journal-vouchers/get-rquest-info');
    Route::get('removeattach/{attachid}',[JournalVoucherController::class,'removeattach'])->name('removeattach');
    Route::post('form/bank-journal-vouchers/update-status-request', [JournalVoucherController::class, 'updateStatus'])->name('form/bank-journal-vouchers/update-status-request');
    Route::get('form/journal-vouchers/export-to-excel/{cryptedString}', [JournalVoucherController::class, 'exportFormToExcel'])->name('form/journal-vouchers/export-to-excel');

    // Bank Receipt vourcher request
    Route::get('form/bank-receipt-vouchers', [BankReceiptController::class, 'index'])->name('form/bank-receipt-vouchers');
    Route::get('form/bank-receipt-vouchers/create', [BankReceiptController::class, 'create'])->name('form/bank-receipt-vouchers/create');
    Route::get('form/bank-receipt-vouchers/edit/{cryptedString}', [BankReceiptController::class, 'edit'])->name('form/bank-receipt-vouchers/edit');
    Route::post('form/bank-receipt-vouchers/save-draft', [BankReceiptController::class, 'saveDraft'])->name('form/bank-receipt-vouchers/save-draft');
    Route::get('form/bank-receipt-vouchers/download-template-excel', [BankReceiptController::class, 'downloadExcel'])->name('form/bank-receipt-vouchers/download-template-excel');
    Route::post('form/bank-receipt-vouchers/submit', [BankReceiptController::class, 'submitRequest'])->name('form/bank-receipt-vouchers/submit');
    Route::post('form/bank-receipt-vouchers/items/add-new', [BankReceiptController::class, 'addNewItem'])->name('form/bank-receipt-vouchers/items/add-new');
    Route::post('form/bank-receipt-vouchers/items/update', [BankReceiptController::class, 'updateItem'])->name('form/bank-receipt-vouchers/items/update');
    Route::post('form/bank-receipt-vouchers/delete', [BankReceiptController::class, 'delete'])->name('form/bank-receipt-vouchers/delete');
    Route::post('form/bank-receipt-vouchers/update-exchange-rate', [BankReceiptController::class, 'updateExchangeRate'])->name('form/bank-receipt-vouchers/update-exchange-rate');
    Route::get('form/bank-receipt-vouchers/detail/{cryptedString}', [BankReceiptController::class, 'detail'])->name('form/bank-receipt-vouchers/detail/');
    Route::get('form/bank-receipt-vouchers/export-to-pdf/{cryptedString}', [BankReceiptController::class, 'exportFormToPDF'])->name('form/bank-receipt-vouchers/export-to-pdf');
    Route::get('form/bank-receipt-vouchers/show-for-approval/{cryptedString}', [BankReceiptController::class, 'showForApproval'])->name('form/bank-receipt-vouchers/show-for-approval');
    Route::get('form/bank-receipt-vouchers/show-for-resubmitting/{cryptedString}', [BankReceiptController::class, 'showReSubmitForm'])->name('form/bank-receipt-vouchers/show-for-resubmitting');
    Route::get('form/bank-receipt-vouchers/show-for-query/{cryptedString}', [BankReceiptController::class, 'showForQuery'])->name('form/bank-receipt-vouchers/show-for-query');
    Route::post('form/bank-receipt-vouchers/approve-request', [BankReceiptController::class, 'approveRequest'])->name('form/bank-receipt-vouchers/approve-request');
    Route::post('form/bank-receipt-vouchers/re-submit', [BankReceiptController::class, 'resubmitForm'])->name('form/bank-receipt-vouchers/re-submit');
    Route::post('form/bank-receipt-vouchers/items/query-back-to-approver', [BankReceiptController::class, 'queryBackToApprover'])->name('form/bank-receipt-vouchers/items/query-back-to-approver');
    Route::get('form/bank-receipt-vouchers/get-rquest-info/{cryptedString}/{references}', [BankReceiptController::class, 'getRequestInfo'])->name('form/bank-receipt-vouchers/get-rquest-info');
    Route::post('form/bank-receipt-vouchers/update-status-request', [BankReceiptController::class, 'updateStatus'])->name('form/bank-receipt-vouchers/update-status-request');
    Route::get('form/bank-receipt-vouchers/export-to-excel/{cryptedString}', [BankReceiptController::class, 'exportFormToExcel'])->name('form/bank-receipt-vouchers/export-to-excel');

    // Cash Payment vourcher request
    Route::get('form/cash-payment-vouchers', [CashPaymentController::class, 'index'])->name('form/cash-payment-vouchers');
    Route::get('form/cash-payment-vouchers/create', [CashPaymentController::class, 'create'])->name('form/cash-payment-vouchers/create');
    Route::get('form/cash-payment-vouchers/edit/{cryptedString}', [CashPaymentController::class, 'edit'])->name('form/cash-payment-vouchers/edit');
    Route::post('form/cash-payment-vouchers/save-draft', [CashPaymentController::class, 'saveDraft'])->name('form/cash-payment-vouchers/save-draft');
    Route::get('form/cash-payment-vouchers/download-template-excel', [CashPaymentController::class, 'downloadExcel'])->name('form/cash-payment-vouchers/download-template-excel');
    Route::post('form/cash-payment-vouchers/submit', [CashPaymentController::class, 'submitRequest'])->name('form/cash-payment-vouchers/submit');
    Route::post('form/cash-payment-vouchers/items/add-new', [CashPaymentController::class, 'addNewItem'])->name('form/cash-payment-vouchers/items/add-new');
    Route::post('form/cash-payment-vouchers/items/update', [CashPaymentController::class, 'updateItem'])->name('form/cash-payment-vouchers/items/update');
    Route::post('form/cash-payment-vouchers/delete', [CashPaymentController::class, 'delete'])->name('form/cash-payment-vouchers/delete');
    Route::post('form/cash-payment-vouchers/update-exchange-rate', [CashPaymentController::class, 'updateExchangeRate'])->name('form/cash-payment-vouchers/update-exchange-rate');
    Route::get('form/cash-payment-vouchers/detail/{cryptedString}', [CashPaymentController::class, 'detail'])->name('form/cash-payment-vouchers/detail/');
    Route::get('form/cash-payment-vouchers/export-to-pdf/{cryptedString}', [CashPaymentController::class, 'exportFormToPDF'])->name('form/cash-payment-vouchers/export-to-pdf');
    Route::get('form/cash-payment-vouchers/show-for-approval/{cryptedString}', [CashPaymentController::class, 'showForApproval'])->name('form/cash-payment-vouchers/show-for-approval');
    Route::get('form/cash-payment-vouchers/show-for-resubmitting/{cryptedString}', [CashPaymentController::class, 'showReSubmitForm'])->name('form/cash-payment-vouchers/show-for-resubmitting');
    Route::get('form/cash-payment-vouchers/show-for-query/{cryptedString}', [CashPaymentController::class, 'showForQuery'])->name('form/cash-payment-vouchers/show-for-query');
    Route::post('form/cash-payment-vouchers/approve-request', [CashPaymentController::class, 'approveRequest'])->name('form/cash-payment-vouchers/approve-request');
    Route::post('form/cash-payment-vouchers/re-submit', [CashPaymentController::class, 'resubmitForm'])->name('form/cash-payment-vouchers/re-submit');
    Route::post('form/cash-payment-vouchers/items/query-back-to-approver', [CashPaymentController::class, 'queryBackToApprover'])->name('form/cash-payment-vouchers/items/query-back-to-approver');
    Route::get('form/cash-payment-vouchers/get-rquest-info/{cryptedString}/{references}', [CashPaymentController::class, 'getRequestInfo'])->name('form/cash-payment-vouchers/get-rquest-info');
    Route::get('form/cash-payment-vouchers/export-to-excel/{cryptedString}', [CashPaymentController::class, 'exportFormToExcel'])->name('form/cash-payment-vouchers/export-to-excel');

    // Cash Receipt vourcher request
    Route::get('form/cash-receipt-vouchers', [CashReceiptVoucherController::class, 'index'])->name('form/cash-receipt-vouchers');
    Route::get('form/cash-receipt-vouchers/create', [CashReceiptVoucherController::class, 'create'])->name('form/cash-receipt-vouchers/create');
    Route::get('form/cash-receipt-vouchers/edit/{cryptedString}', [CashReceiptVoucherController::class, 'edit'])->name('form/cash-receipt-vouchers/edit');
    Route::post('form/cash-receipt-vouchers/save-draft', [CashReceiptVoucherController::class, 'saveDraft'])->name('form/cash-receipt-vouchers/save-draft');
    Route::get('form/cash-receipt-vouchers/download-template-excel', [CashReceiptVoucherController::class, 'downloadExcel'])->name('form/cash-receipt-vouchers/download-template-excel');
    Route::post('form/cash-receipt-vouchers/submit', [CashReceiptVoucherController::class, 'submitRequest'])->name('form/cash-receipt-vouchers/submit');
    Route::post('form/cash-receipt-vouchers/items/add-new', [CashReceiptVoucherController::class, 'addNewItem'])->name('form/cash-receipt-vouchers/items/add-new');
    Route::post('form/cash-receipt-vouchers/items/update', [CashReceiptVoucherController::class, 'updateItem'])->name('form/cash-receipt-vouchers/items/update');
    Route::post('form/cash-receipt-vouchers/delete', [CashReceiptVoucherController::class, 'delete'])->name('form/cash-receipt-vouchers/delete');
    Route::post('form/cash-receipt-vouchers/update-exchange-rate', [CashReceiptVoucherController::class, 'updateExchangeRate'])->name('form/cash-receipt-vouchers/update-exchange-rate');
    Route::get('form/cash-receipt-vouchers/detail/{cryptedString}', [CashReceiptVoucherController::class, 'detail'])->name('form/cash-receipt-vouchers/detail/');
    Route::get('form/cash-receipt-vouchers/export-to-pdf/{cryptedString}', [CashReceiptVoucherController::class, 'exportFormToPDF'])->name('form/cash-receipt-vouchers/export-to-pdf');
    Route::get('form/cash-receipt-vouchers/show-for-approval/{cryptedString}', [CashReceiptVoucherController::class, 'showForApproval'])->name('form/cash-receipt-vouchers/show-for-approval');
    Route::get('form/cash-receipt-vouchers/show-for-resubmitting/{cryptedString}', [CashReceiptVoucherController::class, 'showReSubmitForm'])->name('form/cash-receipt-vouchers/show-for-resubmitting');
    Route::get('form/cash-receipt-vouchers/show-for-query/{cryptedString}', [CashReceiptVoucherController::class, 'showForQuery'])->name('form/cash-receipt-vouchers/show-for-query');
    Route::post('form/cash-receipt-vouchers/approve-request', [CashReceiptVoucherController::class, 'approveRequest'])->name('form/cash-receipt-vouchers/approve-request');
    Route::post('form/cash-receipt-vouchers/re-submit', [CashReceiptVoucherController::class, 'resubmitForm'])->name('form/cash-receipt-vouchers/re-submit');
    Route::post('form/cash-receipt-vouchers/items/query-back-to-approver', [CashReceiptVoucherController::class, 'queryBackToApprover'])->name('form/cash-receipt-vouchers/items/query-back-to-approver');
    Route::get('form/cash-receipt-vouchers/get-rquest-info/{cryptedString}/{references}', [CashReceiptVoucherController::class, 'getRequestInfo'])->name('form/cash-receipt-vouchers/get-rquest-info');
    Route::get('form/cash-receipt-vouchers/export-to-excel/{cryptedString}', [CashReceiptVoucherController::class, 'exportFormToExcel'])->name('form/cash-receipt-vouchers/export-to-excel');

    // Bank vourcher request
    Route::get('form/bank-vouchers', [BankVoucherController::class, 'index'])->name('form/bank-vouchers');
    Route::get('form/bank-vouchers/create', [BankVoucherController::class, 'create'])->name('form/bank-vouchers/create');
    Route::get('form/bank-vouchers/edit/{cryptedString}', [BankVoucherController::class, 'edit'])->name('form/bank-vouchers/edit');
    Route::post('form/bank-vouchers/save-draft', [BankVoucherController::class, 'saveDraft'])->name('form/bank-vouchers/save-draft');
    Route::get('form/bank-vouchers/download-template-excel', [BankVoucherController::class, 'downloadExcel'])->name('form/bank-vouchers/download-template-excel');
    Route::post('form/bank-vouchers/submit', [BankVoucherController::class, 'submitRequest'])->name('form/bank-vouchers/submit');
    Route::post('form/bank-vouchers/items/add-new', [BankVoucherController::class, 'addNewItem'])->name('form/bank-vouchers/items/add-new');
    Route::post('form/bank-vouchers/items/update', [BankVoucherController::class, 'updateItem'])->name('form/bank-vouchers/items/update');
    Route::post('form/bank-vouchers/delete', [BankVoucherController::class, 'delete'])->name('form/bank-vouchers/delete');
    Route::post('form/bank-vouchers/update-exchange-rate', [BankVoucherController::class, 'updateExchangeRate'])->name('form/bank-vouchers/update-exchange-rate');
    Route::get('form/bank-vouchers/detail/{cryptedString}', [BankVoucherController::class, 'detail'])->name('form/bank-vouchers/detail/');
    Route::get('form/bank-vouchers/export-to-pdf/{cryptedString}', [BankVoucherController::class, 'exportFormToPDF'])->name('form/bank-vouchers/export-to-pdf');
    Route::get('form/bank-vouchers/show-for-approval/{cryptedString}', [BankVoucherController::class, 'showForApproval'])->name('form/bank-vouchers/show-for-approval');
    Route::get('form/bank-vouchers/show-for-resubmitting/{cryptedString}', [BankVoucherController::class, 'showReSubmitForm'])->name('form/bank-vouchers/show-for-resubmitting');
    Route::get('form/bank-vouchers/show-for-query/{cryptedString}', [BankVoucherController::class, 'showForQuery'])->name('form/bank-vouchers/show-for-query');
    Route::post('form/bank-vouchers/approve-request', [BankVoucherController::class, 'approveRequest'])->name('form/bank-vouchers/approve-request');
    Route::post('form/bank-vouchers/re-submit', [BankVoucherController::class, 'resubmitForm'])->name('form/bank-vouchers/re-submit');
    Route::post('form/bank-vouchers/items/query-back-to-approver', [BankVoucherController::class, 'queryBackToApprover'])->name('form/bank-vouchers/items/query-back-to-approver');
    Route::get('form/bank-vouchers/get-rquest-info/{cryptedString}/{references}', [BankVoucherController::class, 'getRequestInfo'])->name('form/bank-vouchers/get-rquest-info');

    /**advance record */
    Route::get('reports/advance-records', [AdvanceRecordController::class, 'index'])->name('reports/advance-records');
    Route::get('reports/advance-records/render-pagination', [AdvanceRecordController::class, 'renderPagination'])->name('reports/advance-records/render-pagination');
    Route::post('reports/advance-records/update-payment', [AdvanceRecordController::class, 'updatePayment'])->name('reports/advance-records/update-payment');

    /** general ledger */
    Route::get('general-ledger-codes', [GeneralLedgerController::class, 'index'])->name('general-ledger-codes.index');
    Route::get('general-ledger-codes/listing', [GeneralLedgerController::class, 'listPagination'])->name('general-ledger-codes/listing');
    Route::get('general-ledger-codes/download-template', [GeneralLedgerController::class, 'downloadTemplate'])->name('general-ledger-codes/download-template');

    Route::post('general-ledger-codes/create', [GeneralLedgerController::class, 'store'])->name('general-ledger-codes/create');
    Route::post('general-ledger-codes/update', [GeneralLedgerController::class, 'update'])->name('general-ledger-codes/update');
    Route::post('general-ledger-codes/import-excel-data', [GeneralLedgerController::class, 'importExcelData'])->name('general-ledger-codes/import-excel-data');

    /** Tax code */
    Route::get('tax-codes', [TAXCodeController::class, 'index'])->name('tax-codes');
    Route::get('tax-codes/listing', [TAXCodeController::class, 'listPagination'])->name('tax-codes/listing');
    Route::get('tax-codes/download-template', [TAXCodeController::class, 'downloadTemplate'])->name('tax-codes/download-template');

    Route::post('tax-codes/create', [TAXCodeController::class, 'store'])->name('tax-codes/create');
    Route::post('tax-codes/update', [TAXCodeController::class, 'update'])->name('tax-codes/update');
    Route::post('tax-codes/import-excel-data', [TAXCodeController::class, 'importExcelData'])->name('tax-codes/import-excel-data');

    /** product code */
    Route::get('product-codes', [ProductController::class, 'index'])->name('product-codes');
    Route::get('product-codes/listing', [ProductController::class, 'listPagination'])->name('product-codes/listing');
    Route::get('product-codes/download-template', [ProductController::class, 'downloadTemplate'])->name('product-codes/download-template');

    Route::post('product-codes/create', [ProductController::class, 'store'])->name('product-codes/create');
    Route::post('product-codes/update', [ProductController::class, 'update'])->name('product-codes/update');
    Route::post('product-codes/import-excel-data', [ProductController::class, 'importExcelData'])->name('product-codes/import-excel-data');

    /** segment code */
    Route::get('segment-codes', [SegmentController::class, 'index'])->name('segment-codes');
    Route::get('segment-codes/listing', [SegmentController::class, 'listPagination'])->name('segment-codes/listing');
    Route::get('segment-codes/download-template', [SegmentController::class, 'downloadTemplate'])->name('segment-codes/download-template');

    Route::post('segment-codes/create', [SegmentController::class, 'store'])->name('segment-codes/create');
    Route::post('segment-codes/update', [SegmentController::class, 'update'])->name('segment-codes/update');
    Route::post('segment-codes/import-excel-data', [SegmentController::class, 'importExcelData'])->name('segment-codes/import-excel-data');

    /** real branch code */
    Route::get('real-branches', [RealBranchController::class, 'index'])->name('real-branches');
    Route::get('real-branches/listing', [RealBranchController::class, 'listPagination'])->name('real-branches/listing');
    Route::get('real-branches/download-template', [RealBranchController::class, 'downloadTemplate'])->name('real-branches/download-template');

    Route::post('real-branches/create', [RealBranchController::class, 'store'])->name('real-branches/create');
    Route::post('real-branches/update', [RealBranchController::class, 'update'])->name('real-branches/update');
    Route::post('real-branches/import-excel-data', [RealBranchController::class, 'importExcelData'])->name('real-branches/import-excel-data');

    /** Payment method */
    Route::get('payment-methods', [PaymentMethodController::class, 'index'])->name('payment-methods');
    Route::get('payment-methods/listing', [PaymentMethodController::class, 'listPagination'])->name('payment-methods/listing');

    Route::post('payment-methods/create', [PaymentMethodController::class, 'store'])->name('payment-methods/create');
    Route::post('payment-methods/update', [PaymentMethodController::class, 'update'])->name('payment-methods/update');

    /** Supplier */
    Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers');
    Route::get('suppliers/listing', [SupplierController::class, 'listPagination'])->name('suppliers/listing');
    Route::get('suppliers/download-template', [SupplierController::class, 'downloadTemplate'])->name('suppliers/download-template');

    Route::post('suppliers/create', [SupplierController::class, 'store'])->name('suppliers/create');
    Route::post('suppliers/update', [SupplierController::class, 'update'])->name('suppliers/update');
    Route::post('suppliers/import-excel-data', [SupplierController::class, 'importExcelData'])->name('suppliers/import-excel-data');

    /** DE UPLOAD */
    Route::match(array('GET','POST'),'DE-uploads', [DEUploadController::class, 'index'])->name('DE-uploads');
    Route::get('DE-uploads/export-voucher/{formType}/{cryptedString}', [DEUploadController::class, 'exportExcel'])->name('DE-uploads/export-voucher');
    Route::post('DE-uploads/exports-voucher', [DEUploadController::class, 'exportMultiple'])->name('DE-uploads/exports-voucher');
    // DE UPLOAD journal
    Route::match(array('GET','POST'),'DE-uploads-journal', [DEUploadController::class, 'indexJournal'])->name('DE-uploads-journal');
    Route::get('DE-uploads/export-journal-voucher/{formType}/{cryptedString}', [DEUploadController::class, 'exportExcelJournal'])->name('DE-uploads/export-journal-voucher');
    Route::post('DE-uploads/exports-journal-voucher', [DEUploadController::class, 'exportMultipleJournal'])->name('DE-uploads/exports-journal-voucher');
    // DE UPLOAD journal
    Route::match(array('GET','POST'),'DE-uploads-bank-receipt', [DEUploadController::class, 'indexBankReceipt'])->name('DE-uploads-bank-receipt');
    Route::get('DE-uploads/export-bank-receipt-voucher/{formType}/{cryptedString}', [DEUploadController::class, 'exportExcelBankReceipt'])->name('DE-uploads/export-bank-receipt-voucher');
    Route::post('DE-uploads/exports-bank-receipt-voucher', [DEUploadController::class, 'exportMultipleBankReceipt'])->name('DE-uploads/exports-bank-receipt-voucher'); 
    // DE UPLOAD bankVoucher
    Route::match(array('GET','POST'),'DE-uploads-bank', [DEUploadController::class, 'indexBank'])->name('DE-uploads-bank');
    Route::get('DE-uploads/export-bank-voucher/{formType}/{cryptedString}', [DEUploadController::class, 'exportExcelBank'])->name('DE-uploads/export-bank-voucher');
    Route::post('DE-uploads/exports-bank-voucher', [DEUploadController::class, 'exportMultipleBank'])->name('DE-uploads/exports-bank-voucher'); 
    // DE UPLOAD cash payment
    // Route::get('DE-uploads-cash-payment', [DEUploadController::class, 'indexCashPayment'])->name('DE-uploads-cash-payment');
    Route::match(array('GET','POST'),'DE-uploads-cash-payment', [DEUploadController::class, 'indexCashPayment'])->name('DE-uploads-cash-payment');
    Route::get('DE-uploads/export-cash-payment-voucher/{formType}/{cryptedString}', [DEUploadController::class, 'exportExcelindexCashPayment'])->name('DE-uploads/export-cash-payment-voucher');
    Route::post('DE-uploads/exports-cash-payment-voucher', [DEUploadController::class, 'exportMultipleindexCashPayment'])->name('DE-uploads/exports-cash-payment-voucher'); 
    Route::get('get-cash-payment-voucher-deupload-data',[DEUploadController::class,'DEUploadData'])->name('get-cash-payment-voucher-deupload-data');
    // DE UPLOAD cash receipt
    Route::match(array('GET','POST'),'DE-uploads-cash-receipt', [DEUploadController::class, 'indexCashReceipt'])->name('DE-uploads-cash-receipt');
    Route::get('DE-uploads/export-cash-receipt-voucher/{formType}/{cryptedString}', [DEUploadController::class, 'exportExcelindexCashReceipt'])->name('DE-uploads/export-cash-receipt-voucher');
    Route::post('DE-uploads/exports-cash-receipt-voucher', [DEUploadController::class, 'exportMultipleindexCashReceipt'])->name('DE-uploads/exports-cash-receipt-voucher'); 
    // Block form by date   
    Route::get('block_form', [BlockFormController::class, 'index'])->name('block_form');
    Route::post('block_form/store', [BlockFormController::class, 'store'])->name('block_form/store');
    Route::post('block_form/update', [BlockFormController::class, 'update'])->name('block_form/update');

    // export data to excel
    Route::post('export/department',[ExportController::class,'departmentExport'])->name('export/department');
    Route::post('export/branch',[ExportController::class,'branchExport'])->name('export/branch');
    Route::post('export/budge-code',[ExportController::class,'budgetCodeExport'])->name('export/budge-code');
    Route::post('export/gl',[ExportController::class,'budgetGl'])->name('export/gl');
    Route::post('export/tax',[ExportController::class,'budgetTax'])->name('export/tax');
    Route::post('export/supplier',[ExportController::class,'budgetSupplier'])->name('export/supplier');
    Route::post('export/group',[ExportController::class,'budgetGroup'])->name('export/group');
    Route::post('export/special',[ExportController::class,'budgetSpecial'])->name('export/special');
    Route::post('export/product',[ExportController::class,'budgetProduct'])->name('export/product');
    Route::post('export/segment',[ExportController::class,'budgetSegment'])->name('export/segment');
    
    // Log info UI
    Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);
});

Route::group(['middleware' => ['guest']], function () {
    Route::get('/', function () {
        return Redirect('login');
    })->name('/');
    Route::post('signin', [ActivedirectoryController::class, 'login'])->name('signin');
});
