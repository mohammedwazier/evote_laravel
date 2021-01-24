@extends('template.dashboard')

@section('title', 'Evote Dashboard')

@section('content')
<main role="main" class="container">
    <div class="my-5">
        @if(Session::get('user')->is_manager === '1')
        <div class="my-4">
            <h1>Create a new election</h1>
            <a class="btn btn-primary" href="{{ route('dashboard.election.create') }}">Create Election</a>
        </div>
        <div class="my-4">
                <h1>Eelction Managed By You</h1>
                <table class="table">
                    @foreach ($election as $key => $val)
                    @php
                    $id_election = base64_encode(explode("_", $val->private_key)[0]);
                    @endphp
                        <tr>
                            <td>{{ $val->title }}</td>
                            <td>Created On {{ $val->created_at }}</td>
                            <td>
                                <div class="nav-item dropdown">
                                    <a class="nav-link  dropdown-toggle" href="#" data-toggle="dropdown">  Action  </a>
                                    <ul class="dropdown-menu">
                                        @if($val->closed === '0')
                                            <li><a class="dropdown-item" href="{{ route('dashboard.election.edit', ['id' => $id_election]) }}"> Edit Election</a></li>
                                            @if($val->is_active === '0')
                                                <li><a class="dropdown-item" href="{{ route('dashboard.election.start', ['id' => $id_election]) }}"> Start Election </a></li>
                                            @endif
                                            <li><a class="dropdown-item" href="{{ route('dashboard.election.close', ['id' => $id_election]) }}"> Close Election</a></li>
                                            <li><a class="dropdown-item" href="{{ route('ballot.view', ['id' => $id_election]) }}"> Reminder</a></li>
                                        @endif
                                        <li><a class="dropdown-item" href="{{ route('ballot.view', ['id' => $id_election]) }}"> Ballot</a></li>
                                        <li><a class="dropdown-item" href="{{ route('election.result', ['id' => $id_election]) }}"> Result</a></li>
                                        <li><a class="dropdown-item" href="{{ route('dashboard.election.delete', ['id' => $id_election]) }}"> Delete Election</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif
        <div class="my-4">
            <h1>Elections requiring your vote</h1>
        </div>


    </div>

</main>
@endsection

@push('js')
@endpush