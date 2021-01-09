@extends('template.dashboard')

@section('title', 'Ballots Detail')

@section('content')
<main role="main" class="container">
    <div class="my-5">
        <h1>Ballots for election "{{ $data['title'] }}"</h1>
        <h3>Election ID : {{ md5($data['election_public_key']) }}</h3>
        <h3>Ballot UUID : {{ $data['ballot_uuid'] }}</h3>
        <div>
            @php
            // $priv = App\Http\Controllers::dec($)
            // dd($data['election_public_key_dec']);
            $election_data = explode("_", $data['election_public_key_dec']);
            // dd($election_data);
            $data['pilihan'] = json_decode($data['pilihan'], true);
            $data['answer'] = json_decode($data['answer'], true)[0];
            @endphp
            @foreach($data['pilihan'] as $val)
                <div class="form-check">
                    <input class="form-check-input" type="radio" id="{{ $val }}" {{ $val === $data['answer'] ? 'checked' : '' }} disabled>
                    <label class="form-check-label" for="{{ $val }}">
                        {{ $val }}
                    </label>
                </div>
            @endforeach
            <div class="form-group">
                <label>Voted On</label>
                <input type="text" class="form-control" value="{{ $data['voted_on'] }}" disabled />
            </div>
            <div class="form-group">
                <a href="{{ route('election.result', ['id' => base64_encode($election_data[0])]) }}"><button class="btn btn-info">Election Result</button></a>
                <a href="{{ route('ballot.view', ['id' => base64_encode($election_data[0])]) }}"><button class="btn btn-primary">Ballot</button></a>
            </div>
        </div>
    </div>
</main>
@endsection