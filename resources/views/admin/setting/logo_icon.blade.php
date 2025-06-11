@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row justify-content-center">
                            <div class="form-group col-md-4 col-sm-6">
                                <label> @lang('Logo')</label>
                                <x-image-uploader name="logo" :imagePath="siteLogo() . '?' . time()" :size="false" class="w-100" id="uploadLogo" :required="false" :darkMode="true"/>
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label> @lang('Logo Dark')</label>
                                <x-image-uploader name="logo_dark" :imagePath="siteLogo('dark') . '?' . time()" :size="false" class="w-100" id="uploadLogo1" :required="false" />
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label> @lang('Favicon')</label>
                                <x-image-uploader name="favicon" :imagePath="siteFavicon() . '?' . time()" :size="false" class="w-100" id="uploadFavicon" :required="false" />
                            </div>
                        </div>
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
