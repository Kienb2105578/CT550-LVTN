@if (!is_null($asidePost))
    <aside class="aside">
        <div class="aside-news">
            <div class="aside-heading">Tin tức mới nhất</div>
            <div class="aside-body">
                @foreach ($asidePost as $key => $post)
                    @php
                        $name = $post->name;
                        $description = $post->description;
                        $canonical = write_url($post->canonical);
                        $image = image($post->image);
                        if ($key > 10) {
                            break;
                        }
                    @endphp

                    @if ($post->publish == 2)
                        <div class="aside-post-item clearfix d-flex mb-3">
                            <a href="{{ $canonical }}" class="image img-cover me-3">
                                <img src="{{ $image }}" alt="{{ $name }}" class="img-fluid">
                            </a>
                            <div class="info">
                                <h3 class="title" style="text-align: justify; font-size: 13px;">
                                    <a href="{{ $canonical }}" title="{{ $name }}">{{ $name }}</a>
                                </h3>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </aside>

@endif
