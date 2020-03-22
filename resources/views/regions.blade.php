@extends('layout')

@section('content')
<h2>Krok 2: E-mail odběrnému místu</h2>
<h4>Adresáti</h4>
<ul>
	<li>{{$json['odberne misto']['email']}}</li>
	@foreach ($json['koordinatori'] as $coordinator)
	<li>{{ $coordinator }}</li>
	@endforeach
</ul>

<form method="POST" action="/send-regions">
	<h4>Předmět</h4>
	<div class="form-group">
		<input name="subjectRegions" type="text" class="form-control" id="subjectRegions"
			value="DISTRIBUCE OOP MZ ČR {{$date}}: OM - {{$json['odberne misto']['nazev']}}" required>
	</div>
	<h4>Tělo e-mailu:</h4>
	<div class="form-group">
		<textarea name="topBodyRegions" class="form-control" id="topBodyRegions" rows="5"
			placeholder="vrchní část textu" required>Vážení,

z rozhodnutí Vlády ČR budou na odběrové místo {{$json['odberne misto']['nazev']}} dne {{$date}} distribuovány osobní ochranné pomůcky (OOP), které jsou určeny subjektům dle níže uvedeného rozpisu:
		</textarea>
	</div>
	<div class="form-group">
		<table class="table table-striped">
			<thead>
				<th scope="col">Příjemce</th>
				<th scope="col">Položka</th>
				<th scope="col">Množství</th>
				<th scope="col">E-mail</th>
				<th scope="col">Telefon</th>
			</thead>
			<tbody>
				@foreach ($json['polozky'] as $item)
				<tr>
					<td>{{$item['organizace']}}</td>
					<td>{{$item['polozka']}}</td>
					<td>{{$item['mnozstvi']}}</td>
					<td>{{$item['email']}}</td>
					<td>{{$item['telefon']}}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	<div class="form-group">
		<textarea name="bottomBodyRegions" class="form-control" id="bottomBodyRegions" rows="10"
			placeholder="spodní část textu" required>
Za zásobování uvedených subjektů je zodpovědné Ministerstvo zdravotnictví. Uvedené subjekty byly požádány, aby Vás stran vyzvednutí OPP kontaktovaly <span style="color:red">během zítřejšího dne</span>.

Hodnoty jsou přibližné a mohou se lišit dle konkrétní velikosti balení. Prosím o potvrzení doručení zásilky a zaslání scanu dodacího listu odpovědí na tuto zprávu na adresu distribuce@mzcr.cz.

Děkujeme za spolupráci.
S pozdravem
Distribuční tým OOP MZ ČR
			</textarea>
	</div>
	<div class="form-group clearfix">
		<input type="submit" name="send" class="btn btn-primary float-left" value="Odeslat">
		<input type="submit" name="skip" class="btn btn-secondary float-right" value="Přeskočit">
	</div>


</form>
@endsection