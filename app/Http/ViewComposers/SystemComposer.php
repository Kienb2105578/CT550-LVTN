<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Repositories\Interfaces\SystemRepositoryInterface  as SystemRepository;
use App\Models\System;

class SystemComposer
{
    protected $systemRepository;

    public function __construct(
        SystemRepository $systemRepository,
    ) {
        $this->systemRepository = $systemRepository;
    }

    public function compose(View $view)
    {
        $systemArray =  convert_array(System::all(), 'keyword', 'content');
        $view->with('system', $systemArray);
    }
}
