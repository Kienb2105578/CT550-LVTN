<div class="category-children">
    <ul class="uk-list uk-clearfix uk-flex uk-flex-middle">
        @foreach ($category->object as $key => $val)
            @php
                $name = $val->name;
                $canonical = write_url($val->canonical);
            @endphp
            <li class=""><a href="{{ $canonical }}" title="{{ $name }}">{{ $name }}</a></li>
        @endforeach
    </ul>
</div>
