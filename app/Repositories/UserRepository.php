<?php
/**
 * Created by PhpStorm.
 * User: monica
 * Date: 19/10/21
 * Time: 05:41 م
 */

namespace App\Repositories;


class UserRepository
{
    public function isUserInFollowings($userId, $followings)
    {
        return $followings->where('user_id', $userId)->exists();
    }

    public function getUserTweets()
    {
        return request()->user()->followings()->with('tweets')->get()->pluck('tweets')->flatten();
    }
}