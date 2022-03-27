<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
Use App\Models\Post;
Use App\Models\User;
Use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class IndexController extends Controller
{
    
    public function readPost(Request $request,$category,$post)
    {
        $category = Category::where('diminutive',$category)->firstOrFail();
        $user = User::getUser();

        if($category->access === Category::ACCESS_STAFF && !$user->isStaff())
            return redirect(env('APP_URL'));

        $post = Post::where(['sid' => $post,'postid' => null])->firstOrFail();
        $official_comments = Post::where(['postid' => $post->id,'status' => Post::STATUS_OFFICIAL])->get();
        $highlight_comments = Post::where(['postid' => $post->id,'status' => Post::STATUS_HIGHLIGHT])->get();
        $comments = Post::where(['postid' => $post->id,['status','<>',Post::STATUS_OFFICIAL],['status','<>',Post::STATUS_HIGHLIGHT]])->orderBy('created_at','desc')->get();
        
        
        $user->removeNotification($post);

        $post = $post->getArray(true);

        return view('post',[
            'post' => $post,
            'comments' => array_merge(Post::toParsedArray($official_comments,true),Post::toParsedArray($highlight_comments,true),Post::toParsedArray($comments,true)),
            'category' => $category
        ]);
    }
    
    public function boards()
    {
        if(User::getUser()->isStaff())
            return Category::all();
        else
            return Category::where('status','<>',Category::STATUS_TRASH)->get();
    }
    
    public function index(Request $request,$category = 'g')
    {
        $category = Category::where('diminutive',$category)->firstOrFail();
        $user = User::getUser();

        if($category->access === Category::ACCESS_STAFF && !$user->isStaff())
            return redirect(env('APP_URL'));

        return view('index',[
            'category' => $category
        ]);
    }

    public function searchPosts(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'words' => 'required|string|filled'
        ]);
        
        if($validator->fails())
        {
            foreach($validator->errors()->all() as $message)
            {
                return ['success' => false,'description' => $message];
            }
        }

        $category = Category::where('id',1)->firstOrFail();
        $words = explode(' ', $request->words);
        
        $posts = Post::where(function($query) use ($words){
            foreach ($words as $word) {
                $query->orWhere('title', 'like', "%{$word}%");
            }
        })->get();
        //return ['success' => false,'description' => $message];
        return view('search',[
            'posts' => Post::toParsedArray($posts),
            'category' => $category
        ]);
    }
}
