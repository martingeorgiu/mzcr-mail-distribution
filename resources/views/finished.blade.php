@extends('layout')

@section('content')
<h2>Hotovo</h2>
@include('alert', ['success' => $success])
<h5>
	vytvořil <a href="mailto:martin@georgiu.cz">martin@georgiu.cz</a>
	z <a href="https://bindworks.eu/">bindworks.eu</a>
	v rámci dobrovolné bezplatné pomoci
</h5>
@endsection