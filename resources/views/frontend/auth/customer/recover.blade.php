@php
    // dd(get_defined_vars());

    $rawEmail = request()->query('email');
    $email = base64_encode($rawEmail);
@endphp
<div class="uk-container uk-container-center">
    <table cellspacing="0" border="0" cellpadding="0" style="margin-bottom: 200px" width="100%" bgcolor="#f2f3f8"
        style="@import url(https://fonts.googleapis.com/css?family=Rubik:300,400,500,700|Open+Sans:300,400,600,700); font-family: 'Open Sans', sans-serif;">
        <tr>
            <td>
                <table style="background-color: #f2f3f8; max-width:670px;  margin:0 auto;" width="100%" border="0"
                    align="center" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="height:20px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0"
                                style="max-width:670px;background:#fff; border-radius:3px; text-align:center;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);">
                                <tr>
                                    <td style="height:40px;">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="padding:0 35px;">
                                        <h1
                                            style="color:#1e1e2d; font-weight:500; margin:0;font-size:32px;font-family:'Rubik',sans-serif;">
                                            YÊU CẦU ĐẶT LẠI MẬT KHẨU THÀNH CÔNG</h1>
                                        <span
                                            style="display:inline-block; vertical-align:middle; margin:10px 0 10px; border-bottom:1px solid #cecece; width:100px;"></span>
                                        <p style="color:#455056; font-size:15px;line-height:24px; margin:0;">
                                            <strong> Lưu ý:</strong> Vì lý do bảo mật, chúng tôi không thể gửi lại mật
                                            khẩu cũ của bạn.
                                            Thay vào đó, chúng tôi đã tạo một liên kết duy nhất để bạn đặt lại mật khẩu.
                                            Hãy nhấp vào liên kết bên dưới trong vòng <strong style="color: #e92222">30
                                                phút</strong> để đặt lại mật khẩu của
                                            bạn:
                                        </p>
                                        <a href="{{ route('customer.update.password', ['email' => $email, 'token' => $token]) }}"
                                            style="background:linear-gradient(to right, #003366, #3399ff);text-decoration:none !important; font-weight:500; margin-top:35px; color:#fff;text-transform:uppercase; font-size:14px;padding:10px 24px;display:inline-block;border-radius:50px;">
                                            Thay đổi mật khẩu
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height:40px;">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    <tr>
                </table>
            </td>
        </tr>
    </table>
</div>
