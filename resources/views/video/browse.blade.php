@extends('template')

@section('title', 'Site')

@section('content')
	<section id="videoBrowse">
		<div class="row small-up-2 medium-up-3 large-up-4">
			@foreach ($submissions as $submission)
				<div class="column column-block">
					<a class="thumbnail video" href="{{ $submission->url() }}">
						<h3 title="{{ $submission->title }}">{{ $submission->title }}</h3>
						<img src="https://img.youtube.com/vi/{{ $submission->video->youtube_id }}/mqdefault.jpg" />
					</a>
				</div>
			@endforeach
			@foreach ($submissions as $submission)
				<div class="column column-block">
					<a class="thumbnail video" href="{{ $submission->url() }}">
						<h3 title="{{ $submission->title }}">{{ $submission->title }}</h3>
						<img src="https://img.youtube.com/vi/{{ $submission->video->youtube_id }}/mqdefault.jpg" />
					</a>
				</div>
			@endforeach
			@foreach ($submissions as $submission)
				<div class="column column-block">
					<a class="thumbnail video" href="{{ $submission->url() }}">
						<h3 title="{{ $submission->title }}">{{ $submission->title }}</h3>
						<img src="https://img.youtube.com/vi/{{ $submission->video->youtube_id }}/mqdefault.jpg" />
					</a>
				</div>
			@endforeach
		</div>
	</section>
@endsection