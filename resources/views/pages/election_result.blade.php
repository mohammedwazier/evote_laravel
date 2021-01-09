@extends('template.dashboard')

@section('title', 'Election Result')

@section('content')
<main role="main" class="container">
    <div class="my-5">
        <h1>Results for election "{{ $data['title'] }}"</h1>
        <h3>Election ID : {{ md5($data['election_public_key']) }}</h3>
        <div>
            @php
            $election_data = explode("_", $data['election_public_key_dec']);
            @endphp
            <table class="table table-bordered">
                @foreach($data['counted'] as $key => $val)
                <tr>
                    <th>{{ $key }}</th>
                    <td>{{ $val }}</td>
                </tr>
                @endforeach
            </table>
            <div>
                Election is completely re-counted every time this page is visited. You can see the individual ballots which have been counted and their associated tokens here
            </div>
            <div class="form-group">
                <a href="{{ route('ballot.view', ['id' => base64_encode($election_data[0])]) }}"><button class="btn btn-primary">Ballot</button></a>
            </div>
        </div>
    </div>
</main>
@endsection