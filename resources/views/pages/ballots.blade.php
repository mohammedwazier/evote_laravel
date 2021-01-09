@extends('template.dashboard')

@section('title', 'Ballots')

@section('content')
<main role="main" class="container">
    <div class="my-5">
        <h1>Ballots for election "{{ $election->title }}"</h1>
        <h3>Total Ballots/Voters: {{ count($ballot) }} </h3>
        <h3>Used Ballots/Votes: {{ $ballot_used }}</h3>
        <h3>End of Election : {{ $election->deadline }}</h3>
        <h3>Status Election : {{ $election->closed === '0' ? 'Open' : 'Closed' }}</h3>
        <div>
            <table class="table">
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
                <tr>
                    <td colspan="2">
                        {{-- <button class="btn btn-info">Election Result</button> --}}
                        <a href="{{ route('election.result', ['id' => $dec_data]) }}"><button class="btn btn-info">Election Result</button></a>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</main>
@endsection