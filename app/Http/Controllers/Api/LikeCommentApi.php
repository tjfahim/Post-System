<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LikeCommentApi extends Controller
{
    public function like(Post $post)
    {
        $user = Auth::user();
        if ($user->likes()->where('post_id', $post->id)->exists()) {
            $user->likes()->detach($post->id);
            $message = 'Post unliked successfully';
        } else {
            $user->likes()->attach($post->id);
            $message = 'Post liked successfully';
        }
    
        return response()->json(['message' => $message], 200);
    }

    public function store(Request $request, Post $post)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
        ]);
           
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
       
        $comment = new Comment();
        $comment->user_id = Auth::id();
        $comment->post_id = $post->id;
        $comment->content = $request->content;
        $comment->status = 0;
        $comment->save();

        return response()->json(['message' => 'Comment pending', 'comment' => $comment], 201);
    }

    public function update(Request $request, Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $request->validate([
            'content' => 'required|string',
        ]);

        $comment->content = $request->content;
        $comment->save();

        return response()->json(['message' => 'Comment updated successfully', 'comment' => $comment], 200);
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully'], 200);
    }

    public function approve(Post $post, Comment $comment)
    {
        if ($post->user_id === Auth::id()) {
            $comment->status = $comment->status === 1 ? 0 : 1;
            $comment->save();

            $message = $comment->status === 1 ? 'Comment approved successfully' : 'Comment pending';
            return response()->json(['message' => $message], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

  
}
