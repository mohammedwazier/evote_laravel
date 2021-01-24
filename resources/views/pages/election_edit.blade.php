@extends('template.dashboard')

@section('title', 'Create/Edit Election')

@section('content')
<main role="main" class="container pb-5">
    <div class="my-5">
        <h1>Create/Edit Election</h1>

        <form method="POST" action="{{ route('dashboard.election.store') }}">
            @csrf
            <div class="form-group">
                <h4>Election Title</h4>
                <input type="text" name="title" class="form-control" placeholder="Enter Election Name" required>
            </div>

            <hr />
            <h4>Ballot</h4>
            <div class="form-group">
                <input type="text" name="ballot_description" class="form-control" placeholder="Ballot Description">
            </div>
            <div class="form-group" id="questionList">
                <div class="row my-2">
                    <div class="col">
                        <input type="text" name="question[]" class="form-control" placeholder="Question 1">
                    </div>
                    <div class="col">
                        <button class="btn btn-danger deleteQuestion">Delete Question</button>
                    </div>
                </div>

                <div class="row my-2">
                    <div class="col">
                        <input type="text" name="question[]" class="form-control" placeholder="Question 2">
                    </div>
                    <div class="col">
                        <button class="btn btn-danger deleteQuestion">Delete Question</button>
                    </div>
                </div>

                <div class="row my-2">
                    <div class="col">
                        <input type="text" name="question[]" class="form-control" placeholder="Question 3">
                    </div>
                    <div class="col">
                        <button class="btn btn-danger deleteQuestion">Delete Question</button>
                    </div>
                </div>
                <div id="appendQuestion" class="my-2"></div>
                <div>
                    <button class="btn btn-primary" id="addQuestion">Add Question</button>
                </div>
            </div>
            <hr />

            <div class="form-group row">
                <div class="col">
                    <h4>Voters</h4>
                    List of emails of voters.
                    <div>
                        <select class="form-control" id="voters">
                            <option value="">--CHOOSE VOTER--</option>
                            @foreach ($user as $key => $val)
                                <option value="{{ $val->email }}">{{ $val->email }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group my-3">
                        <textarea class="form-control" name="voters" id="listVoters">{{ Session::get('user')->email }}</textarea>
                    </div>
                </div>
                <div class="col">
                    <h4>Managers</h4>
                    List of emails of election managers.
                    <div>
                        <select class="form-control" id="manager">
                            <option value="">--CHOOSE MANAGER--</option>
                            @foreach ($user as $key => $val)
                                @if($val->is_manager === '1')
                                    <option value="{{ $val->email }}">{{ $val->email }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group my-3">
                        <textarea class="form-control" name="manager" id="listManager">{{ Session::get('user')->email }}</textarea>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <h4>Election Period</h4>
                <input type="datetime-local" name="electionPeriod" class="form-control deadline" required />
            </div>

            <div class="form-group">
                <h4>Text of email with voting link</h4>
                <textarea name="voteForm" rows="6" class="form-control">{=title}

Link to vote: {=link}
Link to ballots: {=link_ballots}
Link to results: {=link_results}</textarea>
            </div>

            <div class="form-group">
                <h4>Text of email for voting receipt</h4>
                <textarea name="voteSuccess" rows="10" class="form-control">{=title}

You have voted and your vote has been registered. Thank you!
Here is your ballot.

Your ballot: {=link}
A copy is also attached.

Please keep it to verify the integrity of the election.</textarea>
            </div>
            <div class="form-group">
                <h4>Email sender</h4>
                <input type="text" disabled value="{{ env('MAIL_FROM_ADDRESS') }}" class="form-control"/>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" />
                <a href="{{ route('dashboard.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>

</main>
{{ dd($data) }}
@endsection

@push('js')
<script>
    $(document).ready(function(){
        $('#voters').on('change', function(e){
            e.preventDefault()
            let ListVoter = $('#listVoters').val();
            let append = true;
            ListVoter.split("\n").map(val => {
                if(val === e.target.value){
                    append = false
                }
            })
            if(append && e.target.value.length > 0){
                let newData = ListVoter+'\n'+e.target.value
                $('#listVoters').val(newData);
            }
        })

        $('#manager').on('change', function(e){
            e.preventDefault()
            let ListVoter = $('#listManager').val();
            let append = true;
            ListVoter.split("\n").map(val => {
                if(val === e.target.value){
                    append = false
                }
            })
            if(append && e.target.value.length > 0){
                let newData = ListVoter+'\n'+e.target.value
                $('#listManager').val(newData);
            }
        })

        $('body').on('click', '.deleteQuestion', function(e){
            let Number = 1;
            e.preventDefault();
            $(this).closest('.row').remove()
            let El = $('#questionList').find('.row')
            El.map(d => {
                $(El[d]).find('input').attr('placeholder', `Question ${d + 1}`)
            })
        })
        $('#addQuestion').on('click', function(e){
            e.preventDefault();
            let QuestionNum = $('#questionList').find('.row').length + 1;
            $('#appendQuestion').append(`
            <div class="row my-2">
                    <div class="col">
                        <input type="text" name="question[]" class="form-control" placeholder="Question ${QuestionNum}">
                    </div>
                    <div class="col">
                        <button class="btn btn-danger deleteQuestion">Delete Question</button>
                    </div>
                </div>
            `)
        })
    })
</script>
@endpush