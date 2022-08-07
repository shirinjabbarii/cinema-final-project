<?php

namespace App\Http\Controllers;

use App\Models\Movie;

class MovieController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('jwt.verify')->only([
            'store',
            'update',
            'destroy'
        ]);
        $this->middleware(function ($request, $next) {
            $user = auth()->user();

            if ($user->isAdmin()) {
                return $next($request);
            } else {
                return response('Not enough access', 403);
            }

        })->only([
            'store',
            'update',
            'destroy'
        ]);
    }

    public function index()
    {
        return Movie::with(['comments' => function ($query) {
            return $query->where('approved', 1);
        }])->get();
    }

    public function store()
    {
        $movie = Movie::create([
            'name'        => request('name'),
            'description' => request('description')
        ]);

        return $movie;
    }

    public function update(Movie $movie)
    {
        $movie->update([
            'name'        => request('name'),
            'description' => request('description')
        ]);

        return $movie;
    }

    public function show(Movie $movie)
    {
        return $movie->with(['comments' => function ($query) {
            return $query->where('approved', 1);
        }])->first();
    }

    public function destroy(Movie $movie)
    {
        $movie->delete();

        return response('Movie Deleted');
    }
}
