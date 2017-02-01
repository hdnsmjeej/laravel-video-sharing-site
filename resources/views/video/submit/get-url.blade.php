@extends('template')

@section('title', 'Videos')

@section('content')
	<section id="videoSubmitUrl">
		<form action="{{ route('video.submit.url.process') }}" method="POST" autocomplete="off">
			{!! csrf_field() !!}
			<div class="row">
				<div class="column small-12">
					<h1>Submit Video</h1>
					<p>First, paste the YouTube URL below.</p>
				</div>
			</div>
			<div class="row">
				<div class="columns medium-6 end">
					<label for="youtube_url" class="{{ ($errors->has('youtube_url')) ? 'is-invalid-label' : '' }}">
						Youtube URL
						<input type="text" placeholder="Youtube URL" name="youtube_url" value="{{ old('youtube_url') }}" class="{{ ($errors->has('youtube_url')) ? 'is-invalid-input' : '' }}" autofocus required>
						@foreach ($errors->get('youtube_url') as $error)
			        <span class="form-error is-visible">
				        {!! $error !!}
			        </span>
			      @endforeach
					</label>
					<div class="text-right">
						<button type="submit" class="button">
							Next Step
						</button>
					</div>
				</div>
			</div>
		</form>
	</section>
@endsection
