@if($errors->any())
    <ul>
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

<form method="POST" action="">
    @csrf
    <input type="hidden" name="id" value="{{ $user[0]['id'] }}">
    <input type="password" name="password" placeholder="Nova senha">
    <br><br>
    <input type="password" name="password_confirmation" placeholder="Confirmar senha">
    <br><br>
    <input type="submit" value="Enviar">

</form>
