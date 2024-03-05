<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class PostControllerApi extends Controller
{
    public function index()
    {
        $posts = Post::with(['images', 'likes', 'comments' => function ($query) {
            $query->where('status', 1); // Filter comments by status = 1 (active)
        }])
        ->withCount('likes')
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    return response()->json(['posts' => $posts], 200);
    }

    public function store(Request $request)
    {
     $validator = Validator::make($request->all(), [
        'title' => 'required',
        'description' => 'required',
        'images.*' => 'required|image|mimes:png,jpg,jpeg,webp',
    ]);
       
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        $post = new Post();
        $post->user_id = auth()->user()->id;
        $post->title = $request->title;
        $post->description = $request->description;
        $post->save();

//multiple images field name is images[]

        if($request->hasFile("images")){
            $files=$request->file("images");
            foreach($files as $file){
                $imageName=time().'_'.$file->getClientOriginalName();
                $request['post_id']=$post->id;
                $request['image']=$imageName;
                $file->move(\public_path("/images"),$imageName);
                PostImage::create($request->all());

            }
        }
       
        return response()->json(['message' => 'Post created successfully'], 201);
    }

    public function show($id)
    {
        $post = Post::with(['images', 'likes', 'comments' => function ($query) {
            $query->where('status', 1); // Filter comments by status = 1 (active)
        }])
        ->withCount('likes')->findOrFail($id);
        return response()->json(['post' => $post], 200);
    }

    public function update(Request $request, $id)
    {
        $post = Post::where('id', $id)
                    ->where('user_id', auth()->user()->id)
                    ->firstOrFail();
        
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'images.*' => 'image|mimes:png,jpg,jpeg,webp',
        ]);
        
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }
            $post = Post::findOrFail($id);
            $post->title = $request->title;
            $post->description = $request->description;
            $post->save();

        if ($request->hasFile("images")) {
            $deleteImages = PostImage::where("post_id", $post->id)->get();
            foreach ($deleteImages as $image) {
                if (File::exists(public_path("images/".$image->image))) {
                    File::delete(public_path("images/".$image->image));
                }
                $image->delete();
            }

            foreach ($request->file("images") as $file) {
                $imageName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path("images"), $imageName);
                PostImage::create([
                    "post_id" => $post->id,
                    "image" => $imageName
                ]);
            }
        }

        return response()->json(['message' => 'Post updated successfully'], 200);
    }

    public function destroy($id)
    {
        $posts=Post::findOrFail($id);

         $images=PostImage::where("post_id",$posts->id)->get();
         foreach($images as $image){
         if (File::exists("images/".$image->image)) {
            File::delete("images/".$image->image);
        }
         }
         $posts->delete();

        return response()->json(['message' => 'Post deleted successfully'], 200);
    }



}
