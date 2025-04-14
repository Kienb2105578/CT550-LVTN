<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Ajax\DashboardController as AjaxDashboardController;
use App\Http\Controllers\Ajax\ChatbotController as AjaxChatbotController;
use App\Http\Controllers\Ajax\AttributeController as AjaxAttributeController;
use App\Http\Controllers\Ajax\MenuController as AjaxMenuController;
use App\Http\Controllers\Ajax\SlideController as AjaxSlideController;
use App\Http\Controllers\Ajax\ProductController as AjaxProductController;
use App\Http\Controllers\Ajax\CartController as AjaxCartController;
use App\Http\Controllers\Ajax\OrderController as AjaxOrderController;
use App\Http\Controllers\Ajax\ReviewController as AjaxReviewController;
use App\Http\Controllers\Ajax\StockController as AjaxStockController;
use App\Http\Controllers\Ajax\PurchaseOrderController as AjaxPurchaseOrderController;
use App\Http\Controllers\Ajax\CustomerController as AjaxCustomerController;
use App\Http\Controllers\Ajax\LocationController;

use App\Http\Controllers\Admin\User\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\User\UserController;
use App\Http\Controllers\Admin\User\UserCatalogueController;
use App\Http\Controllers\Admin\User\PermissionController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\Post\PostCatalogueController;
use App\Http\Controllers\Admin\Post\PostController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\SlideController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\Product\ProductCatalogueController;
use App\Http\Controllers\Admin\Product\ProductController;
use App\Http\Controllers\Admin\Attribute\AttributeCatalogueController;
use App\Http\Controllers\Admin\Attribute\AttributeController;
use App\Http\Controllers\Admin\SystemController;


use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\RouterController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\ContactController as FeContactController;
use App\Http\Controllers\Frontend\AuthController as FeAuthController;
use App\Http\Controllers\Frontend\CustomerController as FeCustomerController;
use App\Http\Controllers\Frontend\PostController as FePostController;
use App\Http\Controllers\Frontend\ProductCatalogueController as FeProductCatalogueController;
use App\Http\Controllers\Frontend\MyOrder\MyOrderController;


use App\Http\Controllers\Frontend\Payment\VnpayController;
use App\Http\Controllers\Frontend\Payment\MomoController;
use App\Http\Controllers\Frontend\Payment\PaypalController;


Route::group(['middleware' => 'license'], function () {

   /* BACKEND ROUTES */
   Route::group(['middleware' => ['admin', 'locale', 'backend_default_locale']], function () {
      Route::get('dashboard/index', [DashboardController::class, 'index'])->name('dashboard.index');
      Route::get('/error', function () {
         return view('vendor.error');
      })->name('vendor.error');

      /* USER */
      Route::group(['prefix' => 'user'], function () {
         Route::get('index', [UserController::class, 'index'])->name('user.index');
         Route::get('create', [UserController::class, 'create'])->name('user.create');
         Route::post('store', [UserController::class, 'store'])->name('user.store');
         Route::get('{id}/edit', [UserController::class, 'edit'])->where(['id' => '[0-9]+'])->name('user.edit');
         Route::post('{id}/update', [UserController::class, 'update'])->where(['id' => '[0-9]+'])->name('user.update');
         Route::delete('{id}/destroy', [UserController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('user.destroy');
      });


      Route::group(['prefix' => 'user/catalogue'], function () {
         Route::get('index', [UserCatalogueController::class, 'index'])->name('user.catalogue.index');
         Route::get('create', [UserCatalogueController::class, 'create'])->name('user.catalogue.create');
         Route::post('store', [UserCatalogueController::class, 'store'])->name('user.catalogue.store');
         Route::get('{id}/edit', [UserCatalogueController::class, 'edit'])->where(['id' => '[0-9]+'])->name('user.catalogue.edit');
         Route::post('{id}/update', [UserCatalogueController::class, 'update'])->where(['id' => '[0-9]+'])->name('user.catalogue.update');
         Route::delete('{id}/destroy', [UserCatalogueController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('user.catalogue.destroy');
         Route::get('permission', [UserCatalogueController::class, 'permission'])->name('user.catalogue.permission');
         Route::post('updatePermission', [UserCatalogueController::class, 'updatePermission'])->name('user.catalogue.updatePermission');
      });

      Route::group(['prefix' => 'customer'], function () {
         Route::get('index', [CustomerController::class, 'index'])->name('customer.index');
         Route::get('create', [CustomerController::class, 'create'])->name('customer.create');
         Route::post('store', [CustomerController::class, 'store'])->name('customer.store');
         Route::get('{id}/edit', [CustomerController::class, 'edit'])->where(['id' => '[0-9]+'])->name('customer.edit');
         Route::post('{id}/update', [CustomerController::class, 'update'])->where(['id' => '[0-9]+'])->name('customer.update');
         Route::delete('{id}/destroy', [CustomerController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('customer.destroy');
      });

      Route::group(['prefix' => 'supplier'], function () {
         Route::get('index', [SupplierController::class, 'index'])->name('supplier.index');
         Route::get('create', [SupplierController::class, 'create'])->name('supplier.create');
         Route::post('store', [SupplierController::class, 'store'])->name('supplier.store');
         Route::get('{id}/edit', [SupplierController::class, 'edit'])->where(['id' => '[0-9]+'])->name('supplier.edit');
         Route::post('{id}/update', [SupplierController::class, 'update'])->where(['id' => '[0-9]+'])->name('supplier.update');
         Route::delete('{id}/destroy', [SupplierController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('supplier.destroy');
      });


      Route::group(['prefix' => 'purchase-order'], function () {
         Route::get('index', [PurchaseOrderController::class, 'index'])->name('purchase-order.index');
         Route::get('create', [PurchaseOrderController::class, 'create'])->name('purchase-order.create');
         Route::post('store', [PurchaseOrderController::class, 'store'])->name('purchase-order.store');
         Route::get('{id}/edit', [PurchaseOrderController::class, 'edit'])->where(['id' => '[0-9]+'])->name('purchase-order.edit');
         Route::post('{id}/update', [PurchaseOrderController::class, 'update'])->where(['id' => '[0-9]+'])->name('purchase-order.update');
         Route::delete('{id}/destroy', [PurchaseOrderController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('purchase-order.destroy');
      });

      Route::group(['prefix' => 'stock'], function () {
         Route::get('report/index', [StockController::class, 'report'])->name('stock.report.index');
         Route::get('stock-taking/index', [StockController::class, 'stockTaking'])->name('stock.stock-taking.index');
         Route::get('create', [StockController::class, 'createStockTaking'])->name('stock.stock-taking.create');
         Route::post('store', [StockController::class, 'storecreateStockTaking'])->name('stock.stock-taking.store');
         Route::get('inventory/index', [StockController::class, 'inventory'])->name('stock.inventory.index');
         Route::get('report/exportFile', [StockController::class, 'exportFile'])->name('stock.report.exportFile');
      });



      Route::group(['prefix' => 'system'], function () {
         Route::get('index', [SystemController::class, 'index'])->name('system.index');
         Route::post('store', [SystemController::class, 'store'])->name('system.store');
      });

      Route::group(['prefix' => 'review'], function () {
         Route::get('index', [ReviewController::class, 'index'])->name('review.index');
         Route::delete('{id}/delete', [ReviewController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('review.destroy');
      });

      Route::group(['prefix' => 'menu'], function () {
         Route::get('index', [MenuController::class, 'index'])->name('menu.index');
         Route::get('create', [MenuController::class, 'create'])->name('menu.create');
         Route::post('store', [MenuController::class, 'store'])->name('menu.store');
         Route::get('{id}/edit', [MenuController::class, 'edit'])->where(['id' => '[0-9]+'])->name('menu.edit');
         Route::get('{id}/editMenu', [MenuController::class, 'editMenu'])->where(['id' => '[0-9]+'])->name('menu.editMenu');
         Route::post('{id}/update', [MenuController::class, 'update'])->where(['id' => '[0-9]+'])->name('menu.update');
         Route::delete('{id}/destroy', [MenuController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('menu.destroy');
         Route::get('{id}/children', [MenuController::class, 'children'])->where(['id' => '[0-9]+'])->name('menu.children');
         Route::post('{id}/saveChildren', [MenuController::class, 'saveChildren'])->where(['id' => '[0-9]+'])->name('menu.save.children');
      });


      Route::group(['prefix' => 'post/catalogue'], function () {
         Route::get('index', [PostCatalogueController::class, 'index'])->name('post.catalogue.index');
         Route::get('create', [PostCatalogueController::class, 'create'])->name('post.catalogue.create');
         Route::post('store', [PostCatalogueController::class, 'store'])->name('post.catalogue.store');
         Route::get('{id}/edit', [PostCatalogueController::class, 'edit'])->where(['id' => '[0-9]+'])->name('post.catalogue.edit');
         Route::post('{id}/update', [PostCatalogueController::class, 'update'])->where(['id' => '[0-9]+'])->name('post.catalogue.update');
         Route::delete('{id}/destroy', [PostCatalogueController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('post.catalogue.destroy');
      });

      Route::group(['prefix' => 'post'], function () {
         Route::get('index', [PostController::class, 'index'])->name('post.index');
         Route::get('create', [PostController::class, 'create'])->name('post.create');
         Route::post('store', [PostController::class, 'store'])->name('post.store');
         Route::get('{id}/edit', [PostController::class, 'edit'])->where(['id' => '[0-9]+'])->name('post.edit');
         Route::post('{id}/update', [PostController::class, 'update'])->where(['id' => '[0-9]+'])->name('post.update');
         Route::delete('{id}/destroy', [PostController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('post.destroy');
      });

      Route::group(['prefix' => 'permission'], function () {
         Route::get('index', [PermissionController::class, 'index'])->name('permission.index');
         Route::get('create', [PermissionController::class, 'create'])->name('permission.create');
         Route::post('store', [PermissionController::class, 'store'])->name('permission.store');
         Route::get('{id}/edit', [PermissionController::class, 'edit'])->where(['id' => '[0-9]+'])->name('permission.edit');
         Route::post('{id}/update', [PermissionController::class, 'update'])->where(['id' => '[0-9]+'])->name('permission.update');
         Route::delete('{id}/destroy', [PermissionController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('permission.destroy');
      });

      Route::group(['prefix' => 'slide'], function () {
         Route::get('index', [SlideController::class, 'index'])->name('slide.index');
         Route::get('create', [SlideController::class, 'create'])->name('slide.create');
         Route::post('store', [SlideController::class, 'store'])->name('slide.store');
         Route::get('{id}/edit', [SlideController::class, 'edit'])->where(['id' => '[0-9]+'])->name('slide.edit');
         Route::post('{id}/update', [SlideController::class, 'update'])->where(['id' => '[0-9]+'])->name('slide.update');
         Route::delete('{id}/destroy', [SlideController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('slide.destroy');
      });

      Route::group(['prefix' => 'promotion'], function () {
         Route::get('index', [PromotionController::class, 'index'])->name('promotion.index');
         Route::get('create', [PromotionController::class, 'create'])->name('promotion.create');
         Route::post('store', [PromotionController::class, 'store'])->name('promotion.store');
         Route::get('{id}/edit', [PromotionController::class, 'edit'])->where(['id' => '[0-9]+'])->name('promotion.edit');
         Route::post('{id}/update', [PromotionController::class, 'update'])->where(['id' => '[0-9]+'])->name('promotion.update');
         Route::delete('{id}/destroy', [PromotionController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('promotion.destroy');
      });

      Route::group(['prefix' => 'product/catalogue'], function () {
         Route::get('index', [ProductCatalogueController::class, 'index'])->name('product.catalogue.index');
         Route::get('create', [ProductCatalogueController::class, 'create'])->name('product.catalogue.create');
         Route::post('store', [ProductCatalogueController::class, 'store'])->name('product.catalogue.store');
         Route::get('{id}/edit', [ProductCatalogueController::class, 'edit'])->where(['id' => '[0-9]+'])->name('product.catalogue.edit');
         Route::post('{id}/update', [ProductCatalogueController::class, 'update'])->where(['id' => '[0-9]+'])->name('product.catalogue.update');
         Route::delete('{id}/destroy', [ProductCatalogueController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('product.catalogue.destroy');
      });
      Route::group(['prefix' => 'product'], function () {
         Route::get('index', [ProductController::class, 'index'])->name('product.index');
         Route::get('create', [ProductController::class, 'create'])->name('product.create');
         Route::post('store', [ProductController::class, 'store'])->name('product.store');
         Route::get('{id}/edit', [ProductController::class, 'edit'])->where(['id' => '[0-9]+'])->name('product.edit');
         Route::post('{id}/update', [ProductController::class, 'update'])->where(['id' => '[0-9]+'])->name('product.update');
         Route::delete('{id}/destroy', [ProductController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('product.destroy');
      });
      Route::group(['prefix' => 'attribute/catalogue'], function () {
         Route::get('index', [AttributeCatalogueController::class, 'index'])->name('attribute.catalogue.index');
         Route::get('create', [AttributeCatalogueController::class, 'create'])->name('attribute.catalogue.create');
         Route::post('store', [AttributeCatalogueController::class, 'store'])->name('attribute.catalogue.store');
         Route::get('{id}/edit', [AttributeCatalogueController::class, 'edit'])->where(['id' => '[0-9]+'])->name('attribute.catalogue.edit');
         Route::post('{id}/update', [AttributeCatalogueController::class, 'update'])->where(['id' => '[0-9]+'])->name('attribute.catalogue.update');
         Route::get('{id}/delete', [AttributeCatalogueController::class, 'delete'])->where(['id' => '[0-9]+'])->name('attribute.catalogue.delete');
         Route::delete('{id}/destroy', [AttributeCatalogueController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('attribute.catalogue.destroy');
      });

      Route::group(['prefix' => 'attribute'], function () {
         Route::get('index', [AttributeController::class, 'index'])->name('attribute.index');
         Route::get('create', [AttributeController::class, 'create'])->name('attribute.create');
         Route::post('store', [AttributeController::class, 'store'])->name('attribute.store');
         Route::get('{id}/edit', [AttributeController::class, 'edit'])->where(['id' => '[0-9]+'])->name('attribute.edit');
         Route::post('{id}/update', [AttributeController::class, 'update'])->where(['id' => '[0-9]+'])->name('attribute.update');
         Route::get('{id}/delete', [AttributeController::class, 'delete'])->where(['id' => '[0-9]+'])->name('attribute.delete');
         Route::delete('{id}/destroy', [AttributeController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('attribute.destroy');
      });

      Route::group(['prefix' => 'order'], function () {
         Route::get('index', [OrderController::class, 'index'])->name('order.index');
         Route::get('create', [OrderController::class, 'create'])->name('order.create');
         Route::post('store', [OrderController::class, 'store'])->name('order.store');
         Route::get('{id}/detail', [OrderController::class, 'detail'])->where(['id' => '[0-9]+'])->name('order.detail');
         Route::get('{id}/invoice', [OrderController::class, 'exportPdf'])
            ->where(['id' => '[0-9]+'])
            ->name('order.exportPdf');
         Route::get('invoices', [OrderController::class, 'exportMultiplePdf'])->name('order.exportMultiplePdf');
      });



      Route::group(['prefix' => 'report'], function () {
         Route::get('time', [ReportController::class, 'time'])->name('report.time');
         Route::get('product', [ReportController::class, 'product'])->name('report.product');
         Route::get('exportFileProduct', [ReportController::class, 'exportFileProduct'])->name('report.exportFileProduct');
         Route::get('exportFileTime', [ReportController::class, 'exportFileTime'])->name('report.exportFileTime');
      });

      /* AJAX */

      Route::post('ajax/dashboard/changeStatus', [AjaxDashboardController::class, 'changeStatus'])->name('ajax.dashboard.changeStatus');
      Route::post('ajax/dashboard/changeStatusAll', [AjaxDashboardController::class, 'changeStatusAll'])->name('ajax.dashboard.changeStatusAll');
      Route::get('ajax/dashboard/getMenu', [AjaxDashboardController::class, 'getMenu'])->name('ajax.dashboard.getMenu');
      Route::get('ajax/dashboard/DasnboardchartRevenueAndCost', [AjaxDashboardController::class, 'DasnboardchartRevenueAndCost'])->name('ajax.dashboard.DasnboardchartRevenueAndCost');

      Route::get('ajax/dashboard/findPromotionObject', [AjaxDashboardController::class, 'findPromotionObject'])->name('ajax.dashboard.findPromotionObject');
      Route::get('ajax/dashboard/getPromotionConditionValue', [AjaxDashboardController::class, 'getPromotionConditionValue'])->name('ajax.dashboard.getPromotionConditionValue');
      Route::get('ajax/attribute/getAttribute', [AjaxAttributeController::class, 'getAttribute'])->name('ajax.attribute.getAttribute');
      Route::get('ajax/attribute/loadAttribute', [AjaxAttributeController::class, 'loadAttribute'])->name('ajax.attribute.loadAttribute');
      Route::post('ajax/menu/createCatalogue', [AjaxMenuController::class, 'createCatalogue'])->name('ajax.menu.createCatalogue');
      Route::post('ajax/menu/drag', [AjaxMenuController::class, 'drag'])->name('ajax.menu.drag');
      Route::post('ajax/menu/deleteMenu', [AjaxMenuController::class, 'deleteMenu'])->name('ajax.menu.deleteMenu');
      Route::post('ajax/slide/order', [AjaxSlideController::class, 'order'])->name('ajax.slide.order');
      Route::get('ajax/product/loadProductPromotion', [AjaxProductController::class, 'loadProductPromotion'])->name('ajax.loadProductPromotion');
      Route::post('ajax/product/updateProductQuantity', [AjaxProductController::class, 'updateProductQuantity'])->name('ajax.updateProductQuantity');


      Route::post('ajax/purchaseOrder/getProductDetails', [AjaxPurchaseOrderController::class, 'getProductDetails'])->name('ajax.purchaseOrder.getProductDetails');
      Route::get('ajax/purchaseOrder/loadExistingProducts/{orderId}', [AjaxPurchaseOrderController::class, 'loadExistingProducts'])
         ->name('ajax.purchaseOrder.loadExistingProducts');

      Route::get('ajax/stock/getInventoryWithPurchase', [AjaxStockController::class, 'getInventoryWithPurchase'])->name('ajax.stock.getInventoryWithPurchase');
      Route::post('ajax/stock/changeStatus', [AjaxStockController::class, 'changeStatus'])->name('ajax.stock.changeStatus');
      Route::get('ajax/stock/getInventoryWithProduct', [AjaxStockController::class, 'getInventoryWithProduct'])->name('ajax.stock.getInventoryWithProduct');
      Route::get('ajax/stock/getInventoryWithTime', [AjaxStockController::class, 'getInventoryWithTime'])->name('ajax.stock.getInventoryWithTime');

      Route::get('ajax/stock/getReport', [AjaxStockController::class, 'getReport'])->name('ajax.stock.getReport');

      Route::post('ajax/order/update', [AjaxOrderController::class, 'update'])->name('ajax.order.update');
      Route::get('ajax/order/chart', [AjaxOrderController::class, 'chart'])->name('ajax.order.chart');
      Route::get('ajax/order/chartDoughnutChart', [AjaxOrderController::class, 'chartDoughnutChart'])->name('ajax.order.chartDoughnutChart');
      Route::get('ajax/order/chartPolarChart', [AjaxOrderController::class, 'chartPolarChart'])->name('ajax.order.charPolarChart');
      Route::get('ajax/order/chartRevenueAndCost', [AjaxOrderController::class, 'chartRevenueAndCost'])->name('ajax.order.chartRevenueAndCost');
      Route::get('ajax/order/getVariantByProduct', [AjaxOrderController::class, 'getVariantByProduct'])->name('ajax.order.getVariantByProduct');
      Route::get('ajax/order/getProduct', [AjaxOrderController::class, 'getProduct'])->name('ajax.order.getProduct');

      Route::post('ajax/construct/createCustomer', [AjaxCustomerController::class, 'createCustomer'])->name('ajax.construct.createCustomer');

      Route::get('ajax/dashboard/findInformationObject', [AjaxDashboardController::class, 'findInformationObject'])->name('ajax.findInformationObject');
   });


   Route::get('admin', [AuthController::class, 'index'])->name('auth.admin')->middleware('login');
   Route::get('logout', [AuthController::class, 'logout'])->name('auth.logout');
   Route::post('login', [AuthController::class, 'login'])->name('auth.login');



   /* FRONTEND ROUTES  */
   Route::get('/', [HomeController::class, 'index'])->name('home.index');

   Route::get('tim-kiem' . config('apps.general.suffix'), [FeProductCatalogueController::class, 'search'])->name('product.catalogue.search');
   Route::get('lien-he' . config('apps.general.suffix'), [FeContactController::class, 'index'])->name('fe.contact.index');
   Route::get('bai-viet' . config('apps.general.suffix'), [FePostController::class, 'main'])->name('post.main');
   Route::get('san-pham' . config('apps.general.suffix'), [FeProductCatalogueController::class, 'main'])->name('product.catalogue.main');
   Route::post('tim-kiem-bang-anh' . config('apps.general.suffix'), [FeProductCatalogueController::class, 'searchProductByImage'])->name('product.catalogue.searchProductByImage');

   /* CUSTOMER  */
   Route::get('customer/login' . config('apps.general.suffix'), [FeAuthController::class, 'index'])->name('fe.auth.login');
   Route::get('customer/check/login' . config('apps.general.suffix'), [FeAuthController::class, 'login'])->name('fe.auth.dologin');

   Route::get('customer/password/forgot' . config('apps.general.suffix'), [FeAuthController::class, 'forgotCustomerPassword'])->name('forgot.customer.password');
   Route::get('customer/password/email' . config('apps.general.suffix'), [FeAuthController::class, 'verifyCustomerEmail'])->name('customer.password.email');
   Route::get('customer/register' . config('apps.general.suffix'), [FeAuthController::class, 'register'])->name('customer.register');
   Route::post('customer/reg' . config('apps.general.suffix'), [FeAuthController::class, 'registerAccount'])->name('customer.reg');

   Route::get('customer/password/update' . config('apps.general.suffix'), [FeAuthController::class, 'updatePassword'])->name('customer.update.password');
   Route::post('customer/password/change' . config('apps.general.suffix'), [FeAuthController::class, 'changePassword'])->name('customer.password.reset');

   Route::get('/don-hang-cua-toi', [MyOrderController::class, 'index'])->name('my-order.index');
   Route::get('/don-hang-cua-toi/{id}', [MyOrderController::class, 'detail'])->name('my-order.detail')->where(['id' => '[0-9]+']);
   Route::delete('/don-hang-cua-toi/{id}/huy', [MyOrderController::class, 'cancel'])->name('my-order.cancel')->where(['id' => '[0-9]+']);

   Route::group(['middleware' => ['customer']], function () {
      Route::get('customer/profile' . config('apps.general.suffix'), [FeCustomerController::class, 'profile'])->name('customer.profile');
      Route::post('customer/profile/update' . config('apps.general.suffix'), [FeCustomerController::class, 'updateProfile'])->name('customer.profile.update');
      Route::get('customer/password/reset' . config('apps.general.suffix'), [FeCustomerController::class, 'passwordForgot'])->name('customer.password.change');
      Route::post('customer/password/recovery' . config('apps.general.suffix'), [FeCustomerController::class, 'recovery'])->name('customer.password.recovery');
      Route::get('customer/logout' . config('apps.general.suffix'), [FeCustomerController::class, 'logout'])->name('customer.logout');
   });

   Route::get('thanh-toan' . config('apps.general.suffix'), [CartController::class, 'checkout'])->name('cart.checkout');
   Route::get('{canonical}' . config('apps.general.suffix'), [RouterController::class, 'index'])->name('router.index')->where('canonical', '[a-zA-Z0-9-]+');
   Route::get('{canonical}/trang-{page}' . config('apps.general.suffix'), [RouterController::class, 'page'])->name('router.page')->where('canonical', '[a-zA-Z0-9-]+')->where('page', '[0-9]+');
   Route::post('cart/create', [CartController::class, 'store'])->name('cart.store');
   Route::get('cart/{code}/success' . config('apps.general.suffix'), [CartController::class, 'success'])->name('cart.success')->where(['code' => '[0-9]+']);

   /* FRONTEND SYSTEM */
   Route::post('ajax/chatbot/create', [AjaxChatbotController::class, 'create'])->name('ajax.chatbot.create');
   Route::get('/ajax/get-gemini-key', [AjaxChatbotController::class, 'getGeminiKey']);


   /* VNPAY */
   Route::get('return/vnpay' . config('apps.general.suffix'), [VnpayController::class, 'vnpay_return'])->name('vnpay.momo_return');
   Route::get('return/vnpay_ipn' . config('apps.general.suffix'), [VnpayController::class, 'vnpay_ipn'])->name('vnpay.vnpay_ipn');

   Route::get('return/momo' . config('apps.general.suffix'), [MomoController::class, 'momo_return'])->name('momo.momo_return');
   Route::get('return/ipn' . config('apps.general.suffix'), [MomoController::class, 'momo_ipn'])->name('momo.momo_ipn');

   Route::get('paypal/success' . config('apps.general.suffix'), [PaypalController::class, 'success'])->name('paypal.success');
   Route::get('paypal/cancel' . config('apps.general.suffix'), [PaypalController::class, 'cancel'])->name('paypal.cancel');


   /* FRONTEND AJAX ROUTE */
   Route::post('ajax/review/create', [AjaxReviewController::class, 'create'])->name('ajax.review.create');
   Route::post('ajax/review/reply', [AjaxReviewController::class, 'reply'])->name('ajax.review.reply');
   Route::get('ajax/product/loadVariant', [AjaxProductController::class, 'loadVariant'])->name('ajax.loadVariant');
   Route::get('ajax/product/filter', [AjaxProductController::class, 'filter'])->name('ajax.filter');
   Route::post('ajax/cart/create', [AjaxCartController::class, 'create'])->name('ajax.cart.create');
   Route::post('ajax/cart/update', [AjaxCartController::class, 'update'])->name('ajax.cart.update');
   Route::post('ajax/cart/change-mini-cart', [AjaxCartController::class, 'changeMinyCartQuantity'])->name('ajax.cart.change-mini-cart');
   Route::post('ajax/cart/delete', [AjaxCartController::class, 'delete'])->name('ajax.cart.delete');
   Route::get('ajax/location/getLocation', [LocationController::class, 'getLocation'])->name('ajax.location.index');
   Route::post('ajax/order/update-cancle', [AjaxOrderController::class, 'updateCancle'])->name('ajax.order.update-cancle');
   Route::post('ajax/order/update-return', [AjaxOrderController::class, 'updateReturn'])->name('ajax.order.update-return');
   Route::post('ajax/order/getMyOrder', [AjaxOrderController::class, 'getMyOrder'])->name('ajax.order.getMyOrder');
   Route::get('ajax/dashboard/findModelObject', [AjaxDashboardController::class, 'findModelObject'])->name('ajax.dashboard.findModelObject');

   Route::post('ajax/product/checkQuantity', [AjaxProductController::class, 'checkQuantity'])->name('ajax.checkQuantity');
   Route::post('ajax/product/checkQuantityCart', [AjaxProductController::class, 'checkQuantityCart'])->name('ajax.checkQuantityCart');
});


Route::get('/license/license', function () {
   return view('vendor.license.index');
})->name('license');
