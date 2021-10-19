<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TweetRequest;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Models\Tweet;

class TweetController extends Controller
{
   
    public function store(TweetRequest $request)
    {
        $user = $request->user();
        $tweet = $user->tweets()->create([
            'text' => $request->text,
            'description' => $request->description
        ]);
        
        return response()->json([
            'tweet' => $tweet
        ]);
    }

    public function timeline(Request $request)
    {
        return response()->json([
            'tweets' => app(UserRepository::class)->getUserTweets()
        ]);
    }
}
