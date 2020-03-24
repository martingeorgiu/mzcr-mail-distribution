@extends('layout')

@section('content')
<h2>Krok 3: E-mail koncovým odběratelům</h2>
@include('alert', ['success' => $success])
<h5>Organizace {{$step + 1}}/{{count($json)}}: {{$json[$key]['organization']}}</h5>
<h5>Adresát: {{$key}}</h5>


<form method="POST" action="/send-organization">
	<h4>Předmět</h4>
	<div class="form-group">
		<input name="subjectOrganization" type="text" class="form-control" id="subjectOrganization"
			value="DISTRIBUCE OOP MZ ČR {{$date}}: {{$json[$key]['organization']}}" required>
	</div>
	<h4>Tělo e-mailu:</h4>
	<div class="form-group">
		<textarea name="topBodyRegions" class="form-control" id="topBodyRegions" rows="5"
			placeholder="vrchní část textu" required>Vážení,

z rozhodnutí Vlády ČR obdrží {{$json[$key]['organization']}} dne {{$date}} osobní ochranné pomůcky (OOP) dle níže uvedeného rozpisu:
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
Rozvoz OOP probíhá během dnešního dopoledne. Kontaktujte, prosím, odběrné místo stran předání uvedených OOP <span style="color:red">během dnešního odpoledne po 16. hodině</span>. Nyní ještě nebude mít k dispozici informace o předání.

Kontakt odběrného místa:
{{$om['nazev']}}
{{$om['kontakt']}}
{{$om['misto']}}
{{$om['email']}}

Hodnoty jsou přibližné a mohou se lišit dle konkrétní velikosti balení. Prosím o potvrzení doručení zásilky a zaslání scanu dodacího listu odpovědí na tuto zprávu na adresu distribuce@mzcr.cz.

Děkujeme za spolupráci.
S pozdravem
{{$json[$key]['signature']}}
				</textarea>
	</div>
	<input type="hidden" name="step" value="{{$step}}">
	<div class="form-group clearfix">
		<input type="submit" name="send" class="btn btn-primary float-left" value="Odeslat">
		<input type="submit" name="skip" class="btn btn-secondary float-right" value="Přeskočit">
	</div>

</form>

@endsection