<?php

namespace App\Http\Controllers;

use App\Models\Comment;

class CommentController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('jwt.verify')->only([
            'update',
            'store',
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
            'update',
            'destroy'
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
        $comment = Comment::create([
            'comment'  => request('comment'),
            'user_id'  => auth()->user()->id,
            'movie_id' => request('movie_id')
        ]);

        return $comment;
    }

    public function update(Comment $comment)
    {
        $comment->update([
            'comment'  => request('comment'),
            'user_id'  => auth()->user()->id,
            'movie_id' => request('movie_id'),
            'approved' => request('approved')
        ]);

        return $comment;
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return response('Comment Deleted');

    }
}
