@extends('layouts.applibrary')

@section('head_resources')

	@parent
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
	<!-- <link href="{{ asset('vendor/dropzone/dropzone.min.css') }}" rel="stylesheet"> -->

@endsection

@section('content')
<div class="container-fluid">
	@include('layouts.modallibrary')
	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="col-md-12 mb-4">
				<button type="button" class="btn btn-primary btn-ico" data-toggle="modal" data-target="#uploaderModal"><i class="fa fa-files-o"></i> {{ __('File Upload') }}</button>
			</div>
		</div>
	</div>
</div>
@endsection

@section('footer_resources')
	@parent
		<!-- Scripts -->
        <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
		<!-- <script src="{{ asset('vendor/dropzone/dropzone.min.js') }}" defer></script> -->
		<script src="{{ asset('js/file_upload.js') }}" defer></script>

		<script>
			var home_url = "{{env('APP_URL') }}";
			var deleteAction = '{{ route("file-delete") }}';
			var generalTS =  document.getElementById('dataTS').value;
			var generalDATE = document.getElementById('dataDATE').value;
			var token = '{!! csrf_token() !!}';
		</script>

@endsection
