@extends('frontend.homepage.layout')
@section('content')
    <div class="post-catalogue page-wrapper intro-wrapper">
        <div class="page-breadcrumb background">
            <div class="container">
                <ul class="list-unstyled d-flex flex-wrap mb-0">
                    <li><a href="/"><i class="mr5"></i>{{ __('frontend.home') }}</a></li>
                    <li><a href="{{ route('post.main') }}" title="Bài viết">Bài Viết</a></li>
                </ul>
            </div>
        </div>

        <div class="container mt20">
            <div class="post-container">
                @if (!is_null($posts))
                    <div class="row g-3">
                        @foreach ($posts as $key => $post)
                            @php
                                $name = $post->name;
                                $description = $post->description;
                                $image = image($post->image);
                                $canonical = write_url($post->canonical);
                            @endphp
                            @if ($post->publish == 2)
                                <div class="col-md-12 col-lg-6 mb20">
                                    <div class="blog-item clearfix">
                                        <div class="row g-2 mb20">
                                            <div class="col-4">
                                                <a href="{{ $canonical }}" class="image img-cover img-post">
                                                    <img src="{{ $image }}" alt="{{ $name }}" class="w-100">
                                                </a>
                                            </div>
                                            <div class="col-8">
                                                <div class="blog-item">
                                                    <h3 class="title" style="text-align: justify;">
                                                        <a href="{{ $canonical }}"
                                                            title="{{ $name }}">{{ $name }}</a>
                                                    </h3>
                                                    <div class="description"
                                                        style="text-align: justify; font-size: 13px; font-weight: 400 !important;">
                                                        {!! $description !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
