@extends('layout')

@section('content')
<h2>Krok 2: E-mail krajským koordinátorům</h2>
<h4>Adresáti</h4>
<ul>
	@foreach ($json['koordinatori'] as $coordinator)
	<li>{{ $coordinator }}</li>
	@endforeach
</ul>

<form method="POST" action="/send-regions">
	<h4>Předmět</h4>
	<div class="form-group">
		<input name="subjectRegions" type="text" class="form-control" id="subjectRegions" value="Distribuce" required>
	</div>
	<h4>Tělo e-mailu:</h4>
	<div class="form-group">
		<textarea name="topBodyRegions" class="form-control" id="topBodyRegions" rows="3"
			placeholder="vrchní část textu"></textarea>
	</div>
	<div class="form-group">
		<table class="table table-striped">
			<thead>
				<th scope="col">Příjemce</th>
				<th scope="col">Položka</th>
				<th scope="col">Množství</th>
			</thead>
			<tbody>
				@foreach ($json['polozky'] as $item)
				<tr>
					<td>{{$item['organizace']}}</td>
					<td>{{$item['polozka']}}</td>
					<td>{{$item['mnozstvi']}}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	<div class="form-group">
		<textarea name="bottomBodyRegions" class="form-control" id="bottomBodyRegions" rows="3"
			placeholder="spodní část textu"></textarea>
	</div>
	<input type="hidden" name="json" value="{{json_encode($json)}}">
	<div class="form-group clearfix">
		<input type="submit" name="send" class="btn btn-primary float-left" value="Odeslat">
		<input type="submit" name="skip" class="btn btn-secondary float-right" value="Přeskočit">
	</div>


</form>
@endsection