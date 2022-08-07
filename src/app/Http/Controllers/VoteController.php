<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Vote;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.verify')->only([
            'store'
        ]);

        $this->middleware(function ($request, $next) {
            $user = auth()->user();

            if ($user->isAdmin() || $user->isNormalUser()) {
                return $next($request);
            } else {
                return response('Not enough access', 403);
            }

        })->only([
            'store'
        ]);

    }

    public function store()
    {
        $vote = Vote::create([
            'rating'   => request('rating'),
            'movie_id' => request('movie_id'),
            'user_id'  => request('user_id')
        ]);

        $avg = Vote::where('movie_id', request('movie_id'))->pluck('rating')->avg();

        Movie::find(request('movie_id'))->update([
            'rating' => $avg
        ]);

        return $vote;

    }
}
