@extends('layouts.app')

@section('title', 'Display Pengguna')

@section('content')
<h1 class="text-center mb-4">Live Scores</h1>
<div class="row g-4">
  @foreach ($matches as $match)
    @php
      $class = $match->sport == 'sepakbola' ? 'bg-primary' : ($match->sport == 'basket' ? 'bg-success' : 'bg-warning');
      $class_text = $match->sport == 'sepakbola' ? 'text-white' : ($match->sport == 'basket' ? 'text-white' : 'text-dark');
      $text = $match->sport == 'sepakbola' ? 'Sepak Bola' : ($match->sport == 'basket' ? 'Basket' : 'Voli');
    @endphp
    <div class="col-md-4">
        <div class="card">
            <div class="card-header text-center {{ $class }} {{ $class_text }}">
                {{ $text }}
            </div>
            <div class="card-body text-center">
                <h2>{{ $match->team1_name }} vs {{ $match->team2_name }}</h2>
                <h3>{{ $match->scores->team1_score }} - {{ $match->scores->team2_score }}</h3>
            </div>
        </div>
    </div>
  @endforeach
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
                $('body').append(`<div class="alert alert-success alert-dismissible fade show" role="alert">${response.message}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>`);
                loadMatches();
            });
        });

        function loadMatches() {
            $.get('/operator/matches', function(response) {
                let cards = '';
                response.data.forEach(match => {
                    cards += `<div class="col-md-4">
                        <div class="card">
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
                $('body').append(`<div class="alert alert-success alert-dismissible fade show" role="alert">${response.message}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>`);
                loadMatches();
            });
        });

        $(document).on('click', '.finishMatch', function() {
            let id = $(this).data('id');
            $.post(`/operator/matches/${id}/finish`, {
                _token: csrfToken
            }, function(response) {
                $('body').append(`<div class="alert alert-success alert-dismissible fade show" role="alert">${response.message}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>`);
                loadMatches();
            });
        });
    });
</script>
@endsection
