<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Tag;
use App\Mail\CommentNotification;
use Illuminate\Support\Facades\Mail;
use App\Comment;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Post::where('published', 1)->orderBy('date', 'asc')->get();
        $tags = Tag::all();

        return view('guest.index', compact('posts', 'tags'));
    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)->first();
        $tags = Tag::all();
        
        return view('guest.show', compact('post', 'tags'));
    }

    public function addComment(Request $request, Post $post)
    {
        $request->validate([
            'name' => 'nullable|string|max:100',
            'content' =>'required|string'
        ]);

        $newComment = new Comment();
        $newComment->name = $request->name;
        $newComment->content = $request->content;
        $newComment->post_id = $post->id;

        $newComment->save();

        //invio della mail
        //se avessi un user prendere la sua email con $post->user->email
        Mail::to('daniele@gmail.com')->send(new CommentNotification($post));

        return back();
    }

    public function filterTag($slug)
    {
        $tags = Tag::all();

        $tag = Tag::where('slug', $slug)->first();
        if($tag == null){
            abort(404);
        }

        $posts = $tag->posts()->where('published', 1)->get();

        return view('guest.index', compact('posts', 'tags'));
    }
}
