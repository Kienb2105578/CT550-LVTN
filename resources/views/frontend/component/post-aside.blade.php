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
                        <div class="aside-post-item uk-clearfix">
                            <a href="{{ $canonical }}" class="image img-cover"><img src="{{ $image }}"
                                    alt="{{ $name }}"></a>
                            <div class="info">
                                <h3 class="title"><a href="{{ $canonical }}"
                                        title="{{ $name }}">{{ $name }}</a></h3>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </aside>
@endif
