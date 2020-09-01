@extends('layouts.app')

@section('content')
<div class="row mb-5">
    <div class="col-lg-9 col-md-9 topic-list">
        <div class="alert alert-info" role="alert">
            
        </div>

        <div class="card">
            <div class="card-header bg-transparent">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link" href="">
                            最後回覆
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="">
                            最新發佈
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                {{--話題列表--}}

                {{--分頁--}}
                <div class="mt-5">

                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-3 sidebar">

    </div>
</div>
@endsection