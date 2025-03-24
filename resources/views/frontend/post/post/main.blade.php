@extends('frontend.homepage.layout')
@section('content')
    <div class="post-catalogue page-wrapper intro-wrapper">
        <div class="page-breadcrumb background">
            <div class="uk-container uk-container-center">
                <ul class="uk-list uk-clearfix">
                    <li><a href="/"><i class="fi-rs-home mr5"></i>{{ __('frontend.home') }}</a></li>
                    <li><a href="{{ route('post.main') }}" title="Bài viết">Bài Viết</a></li>
                </ul>
            </div>
        </div>
        <div class="uk-container uk-container-center" style="margin-top:20px;">
            <div class="post-container">
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
                                                    <h3 class="title">
                                                        <a href="{{ $canonical }}"
                                                            title="{{ $name }}">{{ $name }}</a>
                                                    </h3>
                                                    <div class="description">
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
