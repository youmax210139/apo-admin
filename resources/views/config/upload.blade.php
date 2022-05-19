@extends('layouts.base')

@section('title','配置管理')

@section('function','配置管理')
@section('function_link', '/config/')

@section('here','图片上传')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
    <div class="main animsition">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">图片上传</h3>
                        </div>
                        <div class="panel-body">
                            @include('partials.errors')
                            @include('partials.success')
                            <form id="upload-form" action="/upload" method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <input type="hidden" name="_method" value="post">
                                <div class="form-group">
                                    　　　<input type="file" class="form-control-file" id="upload" name="pic" accept="image/png, image/jpeg, image/gif"/>
                                </div>
                                <div class="form-group">

                                    <button type="button" class="btn btn-warning btn-md" onclick="history.back()">
                                        <i class="fa fa-minus-circle"></i>
                                        取消
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-md">
                                        <i class="fa fa-plus-circle"></i>
                                        上传
                                    </button>

                                </div>
                                　
                            </form>


                            <div class="row">
                                @foreach($data as $pic)
                                    <div class="col-xs-6 col-md-2">
                                        <a href="{{$url.'/storage/'.$pic}}"
                                           class="thumbnail">
                                            <img alt="100%x180" style="height: 120px; width: 100%; display: block;"
                                                 src="{{$url.'/storage/'.$pic}}"
                                                 data-holder-rendered="true">
                                        </a>
                                        <div class="caption">
                                            <p style="word-break: break-all">{{$pic}}</p>
                                            <a href="/upload/Delpic?pic_name={{$pic}}" onClick="return confirm('确定删除?');" class="btn btn-danger btn-md">
                                                <i class="fa fa-minus-circle"></i>
                                                删除
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop