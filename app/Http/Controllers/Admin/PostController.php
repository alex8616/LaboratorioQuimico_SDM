<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Models\Category;
use App\Models\Image as ModelsImage;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.posts.index')->only('index');
        $this->middleware('can:admin.posts.create')->only('create', 'store');
        $this->middleware('can:admin.posts.edit')->only('edit', 'update');
        $this->middleware('can:admin.posts.destroy')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.posts.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::pluck('name', 'id');
        $tags = Tag::all();
        return view('admin.posts.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {

        // dd($request->file);
        $post = Post::create($request->all());
        if($request->file[0]){
            foreach($request->file as $file){
                $nombre = Str::random(8) . $file->getClientOriginalName();
                $ruta = storage_path() . '\app\public\posts/' . $nombre;
                Image::make($file)
                    ->resize(800, null, function($constraint){
                        $constraint->aspectRatio();
                    })
                    ->save($ruta);
                $url = 'posts/' . $nombre;
                $post->image()->create([
                    'url' => $url
                ]);
            }
        }
        if($request->tags){
            $post->tags()->attach($request->tags);
        }
        return redirect()->route('admin.posts.edit', $post);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  Post $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return view('admin.posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  Post $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        $this->authorize('author', $post);
        $categories = Category::pluck('name', 'id');
        $tags = Tag::all();
        return view('admin.posts.edit', compact('categories', 'tags', 'post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  Post $post
     * @return \Illuminate\Http\Response
     */
    public function update(PostRequest $request, Post $post)
    {
        $this->authorize('author', $post);
        $post->update($request->all());
        if($request->file('file')){
            $url = Storage::put('posts', $request->file('file'));
            if($post->image){
                Storage::delete($post->image->url);
                $post->image->update([
                    'url' => $url
                ]);
            }else{
                $post->image()->create([
                    'url' => $url
                ]);
            }
        }
        if($request->tags){
            $post->tags()->sync($request->tags);
        }
        return redirect()->route('admin.posts.edit', $post)->with('info', 'El Post se actualizo con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  Post $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $this->authorize('author', $post);
        $images = ModelsImage::where('imageable_id', '=', $post->id)->get();
        foreach($images as $image){
            $ruta = storage_path().'/app/public/' . $image->url;
            // dd($ruta);
            // Storage::delete($ruta);
            unlink($ruta);
        }
        $post->image()->delete();
        $post->delete();
        return redirect()->route('admin.posts.index')->with('info', 'El Post se elimino con exito');
    }
}
