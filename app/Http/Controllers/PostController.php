<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{


    //Get All posts
    public function index()
    {
        return response([
            'posts' => Post::orderBy('created_at', 'desc')->with('user:id,name,image')->withCount('coments', 'likes')->get()
        ], 200);
    }

    // create a post
    public function store(Request $request)
    {
        //validate fields
        $fields = $request->validate([
            'body' => 'required|string'
        ]);
        $post = Post::create([
            'body' => $fields['body'],
            'user_id' => auth()->user()->id
        ]);

        return response([
            'message' => 'Post Created',
            'post' => $post
        ], 200);
    }

    //get a single post
    public function show(Post $post)
    {
        return response([
            'post' => Post::where('id', $id)->withCount('coments', 'likes')->get()
        ], 200);
    }


    //update a post
    public function update(Request $request, $id)
    {

        $post = Post::find($id);

        if (!$post) {
            return response([
                'message' => 'Post not found'
            ], 403);
        }
        if ($post->user_id != auth()->user()->id) {
            return response([
                'message' => 'Permission denied'
            ], 403);
        }

        //validate fields
        $fields = $request->validate([
            'body' => 'required|string'
        ]);

        $post->update([
            'body' => $fields['body']
        ]);

        return response([
            'message' => 'Post Updated',
            'post' => $post
        ], 200);
    }
    
    //delete a post
    public function destroy($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response([
                'message' => 'Post not found'
            ], 403);
        }
        if ($post->user_id != auth()->user()->id) {
            return response([
                'message' => 'Permission denied'
            ], 403);
        }

        $post->comments()->delete();
        $post->likes()->delete();
        $post->delete();

        return response([
            'message' => 'Post deleted'
        ], 200);
    }
}
