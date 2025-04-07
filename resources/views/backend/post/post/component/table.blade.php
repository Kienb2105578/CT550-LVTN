<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th style="width:50px;">
                    <input type="checkbox" value="" id="checkAll" class="input-checkbox">
                </th>
                <th>{{ __('messages.tableName') }}</th>

                <th class="text-center" style="width:100px;">{{ __('messages.tableStatus') }}</th>
                <th class="text-center" style="width:100px;">{{ __('messages.tableAction') }}</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($posts) && is_object($posts))
                @foreach ($posts as $post)
                    <tr id="{{ $post->id }}">
                        <td>
                            <input type="checkbox" value="{{ $post->id }}" class="input-checkbox checkBoxItem">
                        </td>
                        <td>
                            <div class="uk-flex uk-flex-middle">
                                <div class="image mr5">
                                    <div class="img-cover image-post"><img src="{{ image($post->image) }}"
                                            alt="">
                                    </div>
                                </div>
                                <div class="main-info">
                                    <div class="name"><span class="maintitle">{{ $post->name }}</span></div>
                                    <div class="catalogue">
                                        <span class="text-danger">{{ __('messages.tableGroup') }} </span>
                                        @foreach ($post->array_post_catalogue_name as $catalogue)
                                            <a href="{{ route('post.index', ['post_catalogue_id' => $catalogue['id']]) }}"
                                                title="">
                                                {{ $catalogue['name'] }}
                                            </a>
                                        @endforeach
                                    </div>


                                </div>
                            </div>
                        </td>

                        <td class="text-center js-switch-{{ $post->id }}">
                            <input type="checkbox" value="{{ $post->publish }}" class="js-switch status "
                                data-field="publish" data-model="{{ $config['model'] }}"
                                {{ $post->publish == 2 ? 'checked' : '' }} data-modelId="{{ $post->id }}" />
                        </td>
                        <td class="text-center">
                            <a href="{{ route('post.edit', $post->id) }}" class="btn btn-info btn-outline"><i
                                    class="fa fa-edit"></i></a>
                            <a href="{{ route('post.delete', $post->id) }}" class="btn btn-danger btn-outline"><i
                                    class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
<div class="pagination-wrapper">
    {{ $posts->links('pagination::bootstrap-4') }}
</div>
