@extends('emails.template')

@section('preheader', 'Verify your email address')

@section('content')
	<p>
		Please verify your email.
	</p>
	<p>
		@include('emails.partials.button', [
			'link' => $link,
			'text' => 'Verify'
		])
	</p>
	<p>
		Or copy and paste this link into your browser:
	</p>
	<p>
		{!! $link !!}
	</p>
	<p>
		Thank you.
	</p>
	<p>
		- One More Video
	</p>
@stop
