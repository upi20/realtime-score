@extends('layouts.app')

@section('title', 'Operator Panel')

@section('content')
    <h2 class="text-center">Operator Panel</h2>
    
    @include('operator.components.match_form')
    
    <div id="matchList" class="row g-3"></div>

    <!-- Modal untuk menampilkan histori skor -->
    <div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="historyModalLabel">Histori Skor</h5>
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
<script>
    $(document).ready(function() {
        let csrfToken = '{{ csrf_token() }}';
        
        loadMatches();

        $('#addMatch').click(function() {
            $.post('/operator/matches', {
                _token: csrfToken,
                team1_name: $('#team1_name').val(),
                team2_name: $('#team2_name').val(),
                sport: $('#sport').val()
            }, function(response) {
                const idClose = Math.floor(Date.now() / 1000);
                setTimeout(() => { $(`#notif-${idClose}`).click(); }, 10000);
                $('body').append(`<div class="alert alert-success alert-dismissible fade show" role="alert">${response.message}<button id="notif-${idClose}" type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>`);
                loadMatches();
            });
        });

        function loadMatches() {
            $.get('/operator/matches', function(response) {
                let cards = '';
                response.data.forEach(match => {
                    cards += `<div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">${match.team1_name} vs ${match.team2_name}</h5>
                                <h6 class="card-subtitle mb-2 text-muted">${match.sport}</h6>
                                <p class="card-text">
                                    Skor: 
                                    <input type="number" class="score-input" data-id="${match.id}" data-team="1" value="${match.scores ? match.scores.team1_score : 0}"> - 
                                    <input type="number" class="score-input" data-id="${match.id}" data-team="2" value="${match.scores ? match.scores.team2_score : 0}">
                                </p>
                                <button class="btn btn-success updateScore" data-id="${match.id}">Update</button>
                                <button class="btn btn-danger finishMatch" data-id="${match.id}">Finish</button>
                                <button class="btn btn-info viewHistory" data-id="${match.id}">View History</button>
                            </div>
                        </div>
                    </div>`;
                });
                $('#matchList').html(cards);
            });
        }

        $(document).on('click', '.viewHistory', function() {
            let matchId = $(this).data('id');

            // Dapatkan histori skor berdasarkan ID pertandingan
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
        });

        $(document).on('click', '.updateScore', function() {
            let id = $(this).data('id');
            let team1_score = $(`input[data-id='${id}'][data-team='1']`).val();
            let team2_score = $(`input[data-id='${id}'][data-team='2']`).val();
            
            $.post(`/operator/matches/${id}/update-score`, {
                _token: csrfToken,
                team1_score: team1_score,
                team2_score: team2_score
            }, function(response) {
                const idClose = Math.floor(Date.now() / 1000);
                setTimeout(() => { $(`#notif-${idClose}`).click(); }, 10000);
                $('body').append(`<div class="alert alert-success alert-dismissible fade show" role="alert">${response.message}<button id="notif-${idClose}" type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>`);
                loadMatches();
            });
        });

        $(document).on('click', '.finishMatch', function() {
            let id = $(this).data('id');
            $.post(`/operator/matches/${id}/finish`, {
                _token: csrfToken
            }, function(response) {
                const idClose = Math.floor(Date.now() / 1000);
                setTimeout(() => { $(`#notif-${idClose}`).click(); }, 10000);
                $('body').append(`<div class="alert alert-success alert-dismissible fade show" role="alert">${response.message}<button id="notif-${idClose}" type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>`);
                loadMatches();
            });
        });
    });
</script>
@endsection
