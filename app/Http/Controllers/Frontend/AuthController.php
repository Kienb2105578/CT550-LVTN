<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Services\Interfaces\CustomerServiceInterface  as CustomerService;

use App\Services\Interfaces\CartServiceInterface  as CartService;
use App\Repositories\Interfaces\CustomerRepositoryInterface as CustomerRepository;
use App\Repositories\Interfaces\CartRepositoryInterface as CartRepository;
use App\Http\Requests\AuthRegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use App\Models\Customer;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

use Gloudemans\Shoppingcart\Facades\Cart;

class AuthController extends FrontendController
{
    protected $customerService;
    protected $customerRepository;

    protected $cartService;
    protected $cartRepository;
    public function __construct(
        CustomerService $customerService,

        CustomerRepository $customerRepository,
        CartService $cartService,
        CartRepository $cartRepository,
    ) {
        $this->customerService = $customerService;

        $this->customerRepository = $customerRepository;
        $this->cartService = $cartService;
        $this->cartRepository = $cartRepository;
        parent::__construct();
    }

    public function index()
    {
        $system = $this->system;
        $carts = Cart::instance('shopping')->content();
        $seo = [
            'meta_title' => 'Trang đăng nhập - Hệ thống website ' . $this->system['homepage_company'],
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_image' => '',
            'canonical' => route('fe.auth.login')
        ];
        return view('frontend.auth.index', compact(
            'seo',
            'system',
            'carts'
        ));
    }

    public function register()
    {
        $seo = [
            'meta_title' => 'Đăng ký tài khoản',
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_image' => '',
            'canonical' => route('customer.profile')
        ];
        $system = $this->system;
        $carts = Cart::instance('shopping')->content();
        return view('frontend.auth.customer.register', compact(
            'seo',
            'system',
            'carts'
        ));
    }

    public function registerAccount(AuthRegisterRequest $request)
    {
        if ($this->customerService->create($request)) {
            return redirect()->route('fe.auth.login')->with('success', 'Đăng kí tài khoản thành công');
        }
        return redirect()->route('customer.register')->with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function forgotCustomerPassword()
    {
        $seo = [
            'meta_title' => 'Quên mật khẩu',
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_image' => '',
            'canonical' => route('forgot.customer.password')
        ];
        $route = 'customer.password.email';
        $system = $this->system;
        $carts = Cart::instance('shopping')->content();
        return view('frontend.auth.components.forgotPassword', compact(
            'seo',
            'system',
            'route',
            'carts'
        ));
    }

    public function verifyCustomerEmail(Request $request)
    {
        $emailReset = $request->input('email');
        $customer = Customer::where('email', $emailReset)->first();
        if (!is_null($customer)) {
            $token = Str::random(60);
            DB::table('customer_password_resets')->updateOrInsert(
                ['email' => $emailReset],
                [
                    'token' => $token,
                    'created_at' => Carbon::now(),
                    'expires_at' => Carbon::now()->addMinutes(30)
                ],

            );
            $data = [
                'email' => $emailReset,
                'token' => $token
            ];

            Mail::to($emailReset)->send(new ResetPasswordMail($data));
            return redirect()->route('fe.auth.login')
                ->with('success', 'Gửi yêu cầu cập nhật mật khẩu thành công, vui lòng truy cập email của bạn để cập nhật mật khẩu mới');
        }
        return redirect()->route('forgot.customer.password')->with('error', 'Gửi yêu cầu cập nhật mật khẩu không thành công, email không tồn tại trong hệ thống');
    }


    public function updatePassword(Request $request)
    {
        $email = base64_decode($request->query('email'));
        $token = $request->query('token');
        $resetRecord = DB::table('customer_password_resets')
            ->where('email', $email)
            ->where('token', $token)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$resetRecord) {
            return redirect()->route('forgot.customer.password')->with('error', 'Token không hợp lệ hoặc đã hết hạn.');
        }

        $seo = [
            'meta_title' => 'Thay đổi mật khẩu',
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_image' => '',
            'canonical' => route('customer.profile')
        ];
        $system = $this->system;
        $carts = Cart::instance('shopping')->content();
        $route = 'customer.password.reset';
        return view('frontend.auth.components.updatePassword', compact(
            'system',
            'seo',
            'route',
            'email',
            'carts'
        ));
    }

    public function changePassword(Request $request)
    {
        // $email = base64_decode(rtrim(urldecode($request->getQueryString('email')), '='));
        $email = $request->query('email');
        $customer = Customer::where('email', $email)->first();

        if ($this->customerService->update($customer->id, $request)) {
            return redirect()->route('fe.auth.login')->with('success', 'Cập nhật mật khẩu mới thành công');
        }
        return redirect()->route('customer.update.password')->with('error', 'Cập nhật mật khẩu không thành công. Hãy thử lại');
    }


    public function login(Request $request)
    {
        $credentials = [
            'email' => $request->input('email'),
            'password' => $request->input('password')
        ];

        if (Auth::guard('customer')->attempt($credentials)) {
            $user = Auth::guard('customer')->user();

            if ($user->publish != 2) {
                Auth::guard('customer')->logout();
                return redirect()->route('home.index')->with('error', 'Tài khoản chưa được duyệt hoặc bị khóa');
            }
            $request->session()->regenerate();
            $carts = Cart::instance('shopping')->content();
            $this->cartService->saveCartToDatabase($carts);

            return redirect()->route('home.index')->with('success', 'Đăng nhập thành công');
        }

        return redirect()->route('home.index')->with('error', 'Email hoặc Mật khẩu không chính xác');
    }
}
