@extends('layouts.app')

@section('title', 'Display Pengguna')

@section('content')
<h1 class="text-center mb-4">Live Scores</h1>
<div class="row g-4" id="matchList"></div>
@endsection

@section('styles')
<style>
    .score-input {
        width: 80px;
        padding: 0.5rem;
        font-size: 1rem;
        border: 1px solid #ced4da;
        border-radius: 4px;
        text-align: center;
    }
    .score-input:focus {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }
</style>
@endsection

@section('scripts')
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>

    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}", {
        cluster: 'ap1'
    });

    var channel = pusher.subscribe('live-score');
    channel.bind('change-score', function(data) {
        renderList(data);
    });
</script>

<script>
    $(document).ready(function() {
        loadMatches();

        function loadMatches() {
            $.get('/operator/matches', function(response) {
                renderList(response.data);
            });
        }
    });

    function renderList(data){
        let cards = '';
        if(data.length == 0){
            $('#matchList').html('Data tidak tersedia');
            return;
        }

        data.forEach(match => {
            cards += `<div class="col-md-4">
                        <div class="card">
                            <div class="card-header text-center ${match.class} ${match.class_text}">
                                ${match.text}
                            </div>
                            <div class="card-body text-center">
                                <h2>${match.team1_name} vs ${match.team2_name}</h2>
                                <h3>${match.scores.team1_score} - ${match.scores.team2_score}</h3>
                            </div>
                        </div>
                    </div>`;
        });
        $('#matchList').html(cards);
    }
</script>
@endsection
