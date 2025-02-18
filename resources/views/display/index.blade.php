@extends('layouts.app')

@section('title', 'Display Pengguna')

@section('content')
<h1 class="text-center mb-4">Live Scores</h1>
<div class="row g-4" id="matchList"></div>

    <!-- Modal untuk menampilkan Detail Skor -->
    <div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="historyModalLabel">Detail Skor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul id="historyList" class="list-group">
                        <!-- Histori akan ditampilkan di sini -->
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
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
    Pusher.logToConsole = false;

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
                        <div class="card border-0 shadow-sm">
                            <div class="card-header text-center ${match.class} ${match.class_text}">
                                ${match.text}
                            </div>
                            <div class="card-body">
                                <div class="text-center">
                                    <h2>${match.team1_name} vs ${match.team2_name}</h2>
                                    <h3>${match.scores.team1_score} - ${match.scores.team2_score}</h3>
                                </div>
                                <div class="text-end">
                                    <small class="badge bg-primary" onclick="detail(${match.id})" style="cursor:pointer">Detail</small>
                                </div>
                            </div>
                        </div>
                    </div>`;
        });
        $('#matchList').html(cards);
    }

    function detail(matchId) {
        // Dapatkan Detail Skor berdasarkan ID pertandingan
        $.get(`/operator/matches/${matchId}/history`, function(response) {
            let historyList = '';
            if(response.status == 404){
                $('#historyList').html(`<li class="list-group-item">Belum ada data histori</li>`);
                return $('#historyModal').modal('show');
            }

            response.data.forEach(event => {
                historyList += `<li class="list-group-item">
                    ${event.formatted_created_at}: ${event.scoring_team} mendapatkan ${event.points} poin.
                </li>`;
            });
            $('#historyList').html(historyList);
            $('#historyModal').modal('show');
        });
    };
</script>
@endsection
