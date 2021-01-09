@extends('template.dashboard')

@section('title', 'Ballots')

@section('content')
<main role="main" class="container">
    <div class="my-5">
        <div>
            @php
            $election_ballot = json_decode($data['election_model']);
            $id_election = explode("_",$data['election_public_key_dec'])[0];
            @endphp
            <form method="POST" action="{{ route('dashboard.vote.voted', ['id' => $id_election, 'key' => $data['ballot_signature'], 'blt_key' => base64_encode($election_ballot->key)]) }}">
                @csrf
                <h3>Election '{{ $data['title'] }}'</h3>
                <h4>Ballot : {{ $data['ballot_uuid'] }}</h4>
                <h5>Nama : {{ ucfirst($data['first_name']) }} {{ ucfirst($data['last_name']) }}</h5>
                @foreach($election_ballot->answer as $val)
                    <div class="form-check">
                        <input class="form-check-input" name="{{ md5($election_ballot->key) }}" type="radio" id="{{ $val }}" value="{{ $val }}">
                        <label class="form-check-label" for="{{ $val }}">
                            {{ $val }}
                        </label>
                    </div>

                @endforeach
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" />
                </div>
            </form>
            {{-- <table class="table">
                <tr>
                    <th>Ballot ID</th>
                    <th>Voted On (UTC/GMT)</th>
                </tr>
                @foreach($ballot as $val)
                <tr>
                    <td>
                        <a href="{{ route('ballot.details', ['id' => $id, 'ballot_uid' => $val->ballot_uuid]) }}">
                            <button class="btn btn-primary">
                                {{ $election->id }}-{{ $val->ballot_uuid }}
                            </button>
                        </a>
                    </td>
                    <td>
                        {{ $val->voted_on ? $val->voted_on : 'BLANK' }}
                    </td>
                </tr>
                @endforeach
            </table> --}}
        </div>
    </div>
</main>
@endsection