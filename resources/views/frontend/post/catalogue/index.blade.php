@extends('frontend.homepage.layout')
@section('content')
    <div class="post-catalogue page-wrapper intro-wrapper">
        @if (!empty($postCatalogue->image))
            <span class="image img-cover"><img src="{{ image($postCatalogue->image) }}" alt=""></span>
        @endif
        <div class="page-breadcrumb background">
            <div class="uk-container uk-container-center">
                <ul class="uk-list uk-clearfix">
                    <li><a href="/"><i class="fi-rs-home mr5"></i>{{ __('frontend.home') }}</a></li>
                    <li><a href="{{ route('post.main') }}" title="Bài viết">Bài Viết</a></li>
                    <li><a href="{{ write_url($postCatalogue->canonical) }}"
                            title="{{ $postCatalogue->name }}">{{ $postCatalogue->name }}</a></li>
                </ul>
            </div>
        </div>
        <div class="uk-container uk-container-center">
            <div class="post-container">
                <h1 class="heading-1 post-detail-detail"><span class="post-detail-detail">{{ $postCatalogue->name }}</span>
                </h1>
                @if (!is_null($posts))
                    <div class="uk-grid uk-grid-medium">
                        @foreach ($posts as $key => $post)
                            @php
                                $name = $post->name;
                                $description = $post->description;
                                $image = image($post->image);
                                $canonical = write_url($post->canonical);
                            @endphp
                            @if ($post->publish == 2)
                                <div class="uk-width-medium-1-1 uk-width-large-1-2 mb20">
                                    <div class="blog-item uk-clearfix">
                                        <div class="uk-grid uk-grid-small mb20" uk-grid>
                                            <div class="uk-width-1-3">
                                                <a href="{{ $canonical }}" class="image img-cover img-post">
                                                    <img src="{{ $image }}" alt="{{ $name }}"
                                                        class="uk-width-1-1">
                                                </a>
                                            </div>
                                            <div class="uk-width-2-3">
                                                <div class="blog-item">
                                                    <h3 class="title" style="text-align: justify; ">
                                                        <a href="{{ $canonical }}"
                                                            title="{{ $name }}">{{ $name }}</a>
                                                    </h3>
                                                    <div class="description" style="text-align: justify; ">
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
