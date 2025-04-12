@extends('frontend.homepage.layout')
@section('content')
    <div class="contact-page">
        <div class="page-breadcrumb background">
            <div class="container">
                <ul class="list-unstyled d-flex flex-wrap mb-0">
                    <li class="me-2"><a href="/"><i class="mr5"></i>{{ __('frontend.home') }}</a></li>
                    <li><a href="{{ route('fe.contact.index') }}" title="Liên hệ">Liên hệ</a></li>
                </ul>
            </div>
        </div>

        <div class="container">
            <div class="contact-container-1">
                <div class="mape mt20">
                    {!! $system['contact_map'] !!}
                </div>

                <div class="row mt20">
                    <div class="col-lg-6">
                        <form onsubmit="return false;" action="" method="post" class="form contact-form">
                            <div class="heading-form mb20">Liên hệ ngay</div>
                            <div class="row g-3">
                                <div class="col-md-6 mb20">
                                    <input type="text" name="fullname" class="form-control input-text"
                                        placeholder="Tên của bạn">
                                </div>
                                <div class="col-md-6 mb20">
                                    <input type="text" name="phone" class="form-control input-text"
                                        placeholder="Số điện thoại của bạn">
                                </div>
                                <div class="col-md-6 mb20">
                                    <input type="text" name="email" class="form-control input-text"
                                        placeholder="Email của bạn">
                                </div>
                                <div class="col-md-6 mb20">
                                    <input type="text" name="subject" class="form-control input-text"
                                        placeholder="Chủ đề">
                                </div>
                                <div class="col-12 mb20">
                                    <textarea name="message" class="form-control" placeholder="Nội dung" rows="5" style="padding:10px;"></textarea>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-login" type="submit" name="send" value="create">Liên Hệ
                                        Ngày</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-lg-6">
                        <div class="contact-infor contact-form">
                            <div class="footer-contact">
                                <p class="address">Văn Phòng: {{ $system['contact_address'] }}</p>
                                <p class="phone">Hotline: {{ $system['contact_hotline'] }}</p>
                                <p class="email">Email: {{ $system['contact_email'] }}</p>
                            </div>
                            <div class="short pt20">
                                {!! $system['homepage_short_intro'] !!}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
