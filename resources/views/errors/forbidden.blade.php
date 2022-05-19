@extends('layouts.base')

@section('title','Forbidden')

@section('content')
    <div class="error-page">
        <div class="error-content" ip="{{ $client_ip }}" user-agent="{{ $user_agent }}">
            <h3><i class="fa fa-warning text-yellow"></i> Forbidden 禁止访问 </h3>
        </div>
    </div>
@stop

@section('css')
<style>
.content-header {display: none;}
</style>
@stop
