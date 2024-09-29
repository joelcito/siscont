@extends('layouts.app')
@section('css')

@endsection

@section('content')
<!--begin::Row-->
<div class="row g-5 gx-xl-10 mb-5 mb-xl-10">

    <div class="col-xxl-12">
        <div class="card card-flush h-md-100">
            <div class="card-body d-flex flex-column justify-content-between mt-9 bgi-no-repeat bgi-size-cover bgi-position-x-center pb-0" style="background-position: 100% 50%; background-image:url('assets/media/stock/900x600/42.png')">
                <div class="mb-10">
                    <div class="fs-2hx fw-bold text-gray-800 text-center mb-13">
                    <span class="me-2">Sistema de Facturacion en Linea.
                    <br />
                    <span class="position-relative d-inline-block text-danger">
                        <span class="position-absolute opacity-15 bottom-0 start-0 border-4 border-danger border-bottom w-100"></span>
                    </span></span>Computarizada en Linea / Electronica en Linea</div>
                </div>
                @if ($empresa)
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="text-center text-success">{{ $empresa->nombre }}</h3>
                        </div>
                    </div>
                    @if ($empresa->logo)
                        <img class="mx-auto h-150px h-lg-200px theme-light-show" src="{{ asset('assets/img')."/".$empresa->logo }}" alt="" />
                    @else
                        <img class="mx-auto h-150px h-lg-200px theme-light-show" src="{{ asset('assets/media/illustrations/misc/upgrade.svg') }}" alt="" />
                        <img class="mx-auto h-150px h-lg-200px theme-dark-show" src="{{ asset('assets/media/illustrations/misc/upgrade-dark.svg') }}" alt="" />
                    @endif
                @else
                    <img class="mx-auto h-150px h-lg-200px theme-light-show" src="{{ asset('assets/media/illustrations/misc/upgrade.svg') }}" alt="" />
                    <img class="mx-auto h-150px h-lg-200px theme-dark-show" src="{{ asset('assets/media/illustrations/misc/upgrade-dark.svg') }}" alt="" />
                @endif
            </div>
        </div>
    </div>
</div>
@stop
@section('js')
@endsection
