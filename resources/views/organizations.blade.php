@extends('layout')

@section('content')
<h2>Krok 3: E-mail koordinátorům organizací</h2>
<h5>Organizace {{$step + 1}}/{{count($json)}}: {{$json[$key]['organization']}}</h5>
<h5>Adresát: {{$key}}</h5>


<form method="POST" action="/send-organization">
	<h4>Předmět</h4>
	<div class="form-group">
		<input name="subjectOrganization" type="text" class="form-control" id="subjectOrganization"
			value="{{$json[$key]['organization']}}: distribuce OOP dne XX. XX. 2020" required>
	</div>
	<h4>Tělo e-mailu:</h4>
	<div class="form-group">
		<textarea name="topBodyRegions" class="form-control" id="topBodyRegions" rows="5"
			placeholder="vrchní část textu" required>Vážení,

z rozhodnutí Vlády ČR budou pro {{$region}} dne XX. XX. 2020 rozvezeny ochranné pomůcky. Počet OOP pro Vaší organizaci nejdete v níže uvedeném rozpisu:
		</textarea>
	</div>
	<div class="form-group">
		<table class="table table-striped">
			<thead>
				<th scope="col">Položka</th>
				<th scope="col">Množství</th>
			</thead>
			<tbody>
				@foreach ($json[$key]['items'] as $item)
				<tr>
					<td>{{$item['item']}}</td>
					<td>{{$item['amount']}}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	<div class="form-group">
		<textarea name="bottomBodyRegions" class="form-control" id="bottomBodyRegions" rows="10"
			placeholder="spodní část textu" required>
Stran vyzvednutí zásilky prosím kontaktujte odběrové místo Vašeho kraje: JMÉNO, ČÍSLO, MAIL. Prosím o potvrzení
doručení zásilky a zaslání scanu dodacího listu na adresu distribuce@mzcr.cz.
Krajský koordinátor a odběrové místo byli o alokaci OOP pro Vaši instituci informováni.

Moc děkuji za spolupráci.
S pozdravem
Distribuční tým OOP MZ ČR
				</textarea>
	</div>
	<input type="hidden" name="json" value="{{json_encode($json)}}">
	<input type="hidden" name="step" value="{{$step}}">
	<input type="hidden" name="region" value="{{$region}}">
	<div class="form-group clearfix">
		<input type="submit" name="send" class="btn btn-primary float-left" value="Odeslat">
		<input type="submit" name="skip" class="btn btn-secondary float-right" value="Přeskočit">
	</div>

</form>

@endsection