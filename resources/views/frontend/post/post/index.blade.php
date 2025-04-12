@extends('frontend.homepage.layout')
@section('content')
    <div class="post-detail">
        @if (!empty($postCatalogue->image))
            <span class="image img-cover">
                <img src="{{ image($postCatalogue->image) }}" alt="">
            </span>
        @endif

        <div class="page-breadcrumb background">
            <div class="container">
                <ul class="list-unstyled d-flex flex-wrap mb-0">
                    <li><a href="/"><i class="mr5"></i>{{ __('frontend.home') }}</a></li>
                    <li><a href="{{ route('post.main') }}" title="Bài viết">Bài Viết</a></li>
                    <li><a href="{{ write_url($postCatalogue->canonical) }}"
                            title="{{ $postCatalogue->name }}">{{ $postCatalogue->name }}</a></li>
                </ul>
            </div>
        </div>

        <div class="container mt30">
            <div class="row g-3">
                <div class="col-lg-3">
                    @include('frontend.component.post-aside')
                </div>
                <div class="col-lg-9">
                    <div class="detail-wrapper">
                        <h1 class="post-title">{{ $post->name }}</h1>
                        <div class="description" style="text-align: justify; line-height: 1.5; font-size: 15px !important;">
                            {!! $post->description !!}
                        </div>
                        <div class="content" style="text-align: justify; line-height: 1.5;">
                            {!! $post->content !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
