@extends('layout')

@section('content')
<h2>Krok 1</h2>
<form method="POST" action="/upload-json">
	<div class="form-group">
		<label for="json">Vložte JSON kód:</label>
		<textarea name="json" class="form-control" id="json" rows="20" required></textarea>
	</div>
	<button type="submit" class="btn btn-primary">Pokračovat</button>
</form>
@endsection