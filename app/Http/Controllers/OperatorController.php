<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GameMatch;
use App\Models\Score;
use App\Models\Event;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class OperatorController extends Controller
{
    public function view()
    {
        return view('operator.index');
    }

    public function getMatches($webChannel = false)
    {
        $matches = GameMatch::with('scores')
            ->where('status', 'ongoing')
            ->orderBy('created_at', 'desc')
            ->get()->map(function ($match) {
                $match->class = $match->sport == 'sepakbola' ? 'bg-primary' : ($match->sport == 'basket' ? 'bg-success' : 'bg-warning');
                $match->class_text = $match->sport == 'sepakbola' ? 'text-white' : ($match->sport == 'basket' ? 'text-white' : 'text-dark');
                $match->text = $match->sport == 'sepakbola' ? 'Sepak Bola' : ($match->sport == 'basket' ? 'Basket' : 'Voli');
                return $match;
            });
        if($webChannel){
            return $matches;
        }
        return response()->json([
            'status' => 200,
            'data' => $matches,
        ]);
    }

    public function index()
    {
        $matches = GameMatch::with('scores')
            ->where('status', 'ongoing')
            ->get();
        return response()->json([
            'status' => 200,
            'data' => $matches,
        ]);
    }

    public function createMatch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sport' => 'required|string|in:sepakbola,basket,voli',
            'team1_name' => 'required|string|max:100',
            'team2_name' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors(),
            ]);
        }

        DB::beginTransaction();

        try {
            $match = new GameMatch();
            $match->sport = $request->sport;
            $match->team1_name = $request->team1_name;
            $match->team2_name = $request->team2_name;
            $match->status = 'ongoing';
            $match->save();

            $score = new Score();
            $score->game_match_id = $match->id;
            $score->team1_score = 0;
            $score->team2_score = 0;
            $score->save();

            DB::commit();

            // update realtime display
            $this->updateScoreRaealTimeDisplay();

            return response()->json([
                'status' => 200,
                'message' => 'Match created successfully',
                'data' => $match,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 500,
                'message' => 'Failed to create match',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function updateScore(Request $request, $game_match_id)
    {
        $validator = Validator::make($request->all(), [
            'team1_score' => 'required|integer|min:0',
            'team2_score' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors(),
            ]);
        }

        DB::beginTransaction();

        try {
            $match = GameMatch::find($game_match_id);

            if (!$match) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Match not found',
                ]);
            }

            $score = Score::where('game_match_id', $game_match_id)->first();

            if (!$score) {
                $score = new Score();
                $score->game_match_id = $game_match_id;
            }

            $old_team1_score = $score->team1_score;
            $old_team2_score = $score->team2_score;

            $score->team1_score = $request->team1_score;
            $score->team2_score = $request->team2_score;
            $score->save();

            if ($request->team1_score > $old_team1_score) {
                $event = new Event();
                $event->game_match_id = $game_match_id;
                $event->scoring_team = 'team1';
                $event->point_type = 'manual_update';
                $event->points = $request->team1_score - $old_team1_score;
                $event->save();
            }

            if ($request->team2_score > $old_team2_score) {
                $event = new Event();
                $event->game_match_id = $game_match_id;
                $event->scoring_team = 'team2';
                $event->point_type = 'manual_update';
                $event->points = $request->team2_score - $old_team2_score;
                $event->save();
            }

            DB::commit();
            // update realtime display
            $this->updateScoreRaealTimeDisplay();
            return response()->json([
                'status' => 200,
                'message' => 'Score updated successfully',
                'data' => $score,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 500,
                'message' => 'Failed to update score',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function finishMatch($game_match_id)
    {
        $match = GameMatch::find($game_match_id);

        if (!$match) {
            return response()->json([
                'status' => 404,
                'message' => 'Match not found',
            ]);
        }

        $match->update(['status' => 'finished']);
        $this->updateScoreRaealTimeDisplay();
        return response()->json([
            'status' => 200,
            'message' => 'Match has been finished',
        ]);
    }

    public function getMatchHistory($game_match_id)
    {
        $events = Event::with(['gameMatch'])->where('game_match_id', $game_match_id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($event) {
                $event->scoring_team = $event->gameMatch->{"{$event->scoring_team}_name"};
                $event->formatted_created_at = $event->created_at->format('d-m-Y H:i:s');
                return $event;
            });

        if ($events->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'No history found',
            ]);
        }

        return response()->json([
            'status' => 200,
            'data' => $events,
        ]);
    }

    private function updateScoreRaealTimeDisplay()
    {
        $options = array(
            'cluster' => 'ap1',
            'useTLS' => true
        );
        $pusher = new \Pusher\Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );

        $data = $this->getMatches(true);
        $pusher->trigger('live-score', 'change-score', $data);
    }
}
