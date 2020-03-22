@if ($success > 0)
<div class="alert alert-success" role="alert">
	E-mail byl úspěšně odeslán {{$success}} {{$success > 1 ? 'adresátům' : 'adresátovi'}}.
</div>
@endif