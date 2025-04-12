@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['create']['title']])
@php
    $url =
        isset($config['method']) && $config['method'] == 'translate'
            ? route('system.save.translate', ['languageId' => $languageId])
            : route('system.store');
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">

        @foreach ($systemConfig as $key => $val)
            <div class="row">
                <div class="col-lg-5">
                    <div class="panel-head">
                        <div class="panel-title">{{ $val['label'] }}</div>
                        <div class="panel-description">
                            {{ $val['description'] }}
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="ibox">
                        @if (count($val['value']))
                            <div class="ibox-content">
                                @foreach ($val['value'] as $keyVal => $item)
                                    @php
                                        $name = $key . '_' . $keyVal;
                                    @endphp
                                    <div class="row mb15">
                                        <div class="col-lg-12">
                                            <div class="form-row">
                                                <label for="" class="uk-flex uk-flex-space-between">
                                                    <span>{{ $item['label'] }}</span>
                                                    @if (isset($item['link']))
                                                        <a class="system-link" target="{{ $item['link']['target'] }}"
                                                            href="{{ $item['link']['href'] }}">
                                                            {{ $item['link']['text'] }}
                                                        </a>
                                                    @endif

                                                    @if (isset($item['title']))
                                                        <span class="system-link text-danger">
                                                            {{ $item['title'] }}
                                                        </span>
                                                    @endif

                                                </label>

                                                @switch($item['type'])
                                                    @case('text')
                                                        <input type="text" name="config[{{ $name }}]"
                                                            value="{{ old($name, $systems[$name] ?? '') }}" class="form-control"
                                                            placeholder="" autocomplete="off">
                                                    @break

                                                    @case('images')
                                                        <input type="text" name="config[{{ $name }}]"
                                                            value="{{ old($name, $systems[$name] ?? '') }}"
                                                            class="form-control upload-image" placeholder="" autocomplete="off">
                                                    @break

                                                    @case('textarea')
                                                        <textarea name="config[{{ $name }}]" class="form-control system-textarea">{{ old($name, $systems[$name] ?? '') }}</textarea>
                                                    @break

                                                    @case('select')
                                                        <select name="config[{{ $name }}]" class="form-control">
                                                            @foreach ($item['option'] as $key => $val)
                                                                <option value="{{ $key }}"
                                                                    {{ isset($systems[$name]) && $key == $systems[$name] ? 'selected' : '' }}>
                                                                    {{ $val }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @break

                                                    @case('editor')
                                                        <textarea name="config[{{ $name }}]" id="{{ $name }}" class="form-control system-textarea ck-editor">{{ old($name, $systems[$name] ?? '') }}</textarea>
                                                    @break
                                                @endswitch


                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <hr>
        @endforeach

        <div class="text-right mb15">
            <button class="btn btn-primary" type="submit" name="send" value="send">Lưu lại</button>
        </div>
    </div>
</form>
