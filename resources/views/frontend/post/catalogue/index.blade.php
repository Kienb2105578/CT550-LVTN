@extends('frontend.homepage.layout')
@section('content')
    <div class="post-catalogue page-wrapper intro-wrapper">
        @if (!empty($postCatalogue->image))
            <span class="image img-cover"><img src="{{ image($postCatalogue->image) }}" alt=""></span>
        @endif
        <div class="page-breadcrumb background">
            <div class="container">
                <ul class="list-unstyled d-flex flex-wrap mb-0">
                    <li class="me-2"><a href="/"><i class="mr5"></i>{{ __('frontend.home') }}</a></li>
                    <li class="me-2"><a href="{{ route('post.main') }}" title="Bài viết">Bài Viết</a></li>
                    <li><a href="{{ write_url($postCatalogue->canonical) }}"
                            title="{{ $postCatalogue->name }}">{{ $postCatalogue->name }}</a></li>
                </ul>
            </div>
        </div>
        <div class="container mt-4">
            <div class="post-container">
                <h1 class="heading-1 post-detail-detail">
                    <span class="post-detail-detail">{{ $postCatalogue->name }}</span>
                </h1>
                @if (!is_null($posts))
                    <div class="row gx-4 gy-3">
                        @foreach ($posts as $key => $post)
                            @php
                                $name = $post->name;
                                $description = $post->description;
                                $image = image($post->image);
                                $canonical = write_url($post->canonical);
                            @endphp
                            @if ($post->publish == 2)
                                <div class="col-12 col-lg-6 mb20">
                                    <div class="blog-item clearfix">
                                        <div class="row g-2 mb20 align-items-stretch">
                                            <div class="col-4">
                                                <a href="{{ $canonical }}" class="image img-cover img-post">
                                                    <img src="{{ $image }}" alt="{{ $name }}"
                                                        class="img-fluid">
                                                </a>
                                            </div>
                                            <div class="col-8">
                                                <div class="blog-item">
                                                    <h3 class="title" style="text-align: justify;">
                                                        <a href="{{ $canonical }}"
                                                            title="{{ $name }}">{{ $name }}</a>
                                                    </h3>
                                                    <div class="description" style="text-align: justify;">
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

                @include('frontend.component.pagination', ['model' => $posts])
            </div>
        </div>
    </div>
@endsection
