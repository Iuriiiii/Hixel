<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use App\Models\Audio;
use App\Models\ReCaptcha;
use App\Models\Notification;
use App\Models\Response;
use App\Models\Mark;
use App\Models\Front;
use App\Models\ImageHosting;
use App\Models\ImageCompressor;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    private const POST_CHUNK_SIZE = 30;
    
    private const ATTACH_TYPE_IMAGE = 1;
    private const ATTACH_TYPE_VIDEO = 2;
    
    
    public function testImgUpload()
    {
        return ImageHosting::upload('C:/laragon/www/resources/app/wave-mid.png');
    }
    
    private function isEmpty($content)
    {
        if($content === '') return true;
        return ctype_space($content);
    }
    
    private function checkContent($content,&$error = '')
    {
        if($this->isEmpty($content))
        {
            $error = 'El post está vacío.';
            return false;
        }
        
        return true;//!$this->isEmpty(preg_replace($this->bbfind,['','','','','','','','','',''],$content));
    }
    
    private function getResponeses($content)
    {
        if($content === null)
        {
            return false;
        }
        
        $text = strip_tags($content);
        $pattern = '/(?:&gt;|>)(\w+)/';
        
		if(preg_match_all($pattern,$text,$matches))
		{
			return $matches;
		}
        
        return false;
    }
    
    private function isVideoServer(&$url)
    {
        return in_array($url,[
            'www.youtube.com'
        ]);
    }
    
    private function isImageServer(&$url)
    {
        return in_array($url,[
            'i.imgur.com',
            'i.pinimg.com'
        ]);
    }
    
    private function isValidUrlForAttach(&$url,&$type = 0)
    {
        if($this->isVideoServer($url))
        {
            $type = self::ATTACH_TYPE_VIDEO;
        }
        else if($this->isImageServer($url))
        {
            $type = self::ATTACH_TYPE_IMAGE;
        }
        else
        {
            return false;
        }
        
        return true;
    }
    
    private function extractAttachUrl(&$content)
    {
        $text = strip_tags($content);
        $pattern = '~\[attach\](.*?)\[/attach\]~s';
        
		if(preg_match($pattern,$text,$matches) === 1)
		{
			$content = preg_replace($pattern,"",$content,1);
			
			return $matches[1];
		}
        
        return false;
    }
    
    
    
    // public function getPosts(Request $request)
    // {
        // $validator = Validator::make($request->all(),[
            // 'category' => 'required|integer|filled',
            // 'page' => 'integer|required|filled',
            // 'last_post' => 'optional|nullable|date'
        // ],[
            // 'category.required' => 'Se requiere una categoría para recuperar.',
            // 'page.required' => 'Se esperaba una página.'
        // ]);
        
        // if($validator->fails())
        // {
            // foreach($validator->errors()->all() as $message)
            // {
                // return ['success' => false,'description' => $message];
            // }
        // }
        
        // $category = Category::findOrFail($request->category);
        // $official_posts = $highlight_posts = [];
        
        // if($category->id === 1)
        // {
            // $posts = Post::where(['status' => Post::STATUS_STANDARD,'postid' => 0,['categoryid','<>',6]]);
        // }
        // else
        // {
            // $posts = Post::where(['status' => Post::STATUS_STANDARD,'categoryid' => $category->id,'postid' => 0]);
        // }

        // $posts = $posts->orderBy('last_update','desc')->orderBy('likes','asc')->paginate(Self::POST_CHUNK_SIZE);
        
        // if($request->page <= 1) //count($posts)
        // {
            // $official_posts = Post::where(['status' => Post::STATUS_OFFICIAL,'postid' => 0])->orderBy('created_at','desc')->get();
            // $highlight_posts = Post::where(['status' => Post::STATUS_HIGHLIGHT,'postid' => 0])->orderBy('created_at','desc')->get();
        // }
        
        // return view('posts',[
            // 'official_posts' => $official_posts,
            // 'highlight_posts' => $highlight_posts,
            // 'posts' => $posts,
            // 'onindex' => true,
            // 'category' => $category->diminutive,
            // 'categoryid' => $category->id
        // ]);
    // }

    public function changeTheme(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'themeid' => 'required|integer|filled',
        ],[
            'themeid.required' => 'Tema inválido.'
        ]);
        
        if($validator->fails())
        {
            foreach($validator->errors()->all() as $message)
            {
                return ['success' => false,'description' => $message];
            }
        }

        $user = User::getUser();
        $user->theme = $request->themeid;
        $user->save();

        return ['success' => true];
    }

    public function markPost(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'postid' => 'required|integer|filled',
            'type' => 'required|integer|filled'
        ],[
            'postid.required' => 'Publicación inválida.',
            'type.required' => 'Tipo inválido.'
        ]);
        
        
        if($validator->fails())
        {
            foreach($validator->errors()->all() as $message)
            {
                return ['success' => false,'description' => $message];
            }
        }

        $user = User::getUser();

        $w = [
            'userid' => $user->id,
            'postid' => $request->postid,
            'type' => $request->type
        ];

        if(Mark::where($w)->exists())
        {
            Mark::where($w)->delete();
            return ['success' => true,'newtype' => 0];
        }

        $mark = new Mark;
        $mark->userid = $user->id;
        $mark->postid = $request->postid;
        $mark->type = $request->type;
        $mark->save();
        
        return ['success' => true,'newtype' => $request->type];
    }
    
    public function toggleCategory(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'categoryid' => 'required|integer|filled',
        ],[
            'categoryid.required' => 'Categoría inválida.'
        ]);
        
        
        if($validator->fails())
        {
            foreach($validator->errors()->all() as $message)
            {
                return ['success' => false,'description' => $message];
            }
        }
        
        $user = User::getUser();
        
        if($user->isCategoryHidden($request->categoryid))
        {
            Mark::where(['categoryid' => $request->categoryid,'userid' => $user->id])->delete();
        }
        else
        {
            $mark = new Mark;
            $mark->userid = $user->id;
            $mark->categoryid = $request->categoryid;
            $mark->type = Mark::TYPE_HIDDED;
            $mark->save();
        }
        
        return ['success' => true,'hidden' => Mark::where(['categoryid' => $request->categoryid,'userid' => $user->id,'type' => Mark::TYPE_HIDDED])->exists()];
    }
    
    public function getPosts2(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'category' => 'required|integer|filled',
            'page' => 'integer|required|filled',
            'last_time' => 'optional|nullable|date'
        ],[
            'category.required' => 'Se requiere una categoría para recuperar.',
            'page.required' => 'Se esperaba una página.'
        ]);
        
        if($validator->fails())
        {
            foreach($validator->errors()->all() as $message)
            {
                return ['success' => false,'description' => $message];
            }
        }

        $user = User::getUser();
        $category = Category::findOrFail($request->category);

        if($category->id === 1)
        {
            // Mientras sea una publicación estandar y no sea un comentario...
            
            $posts = Post::whereExists(function($query){
                // Mientras la categoría sea pública
               $query->select(DB::Raw(1))->from('categories')->whereRaw('categories.id = posts.categoryid AND categories.access = ' . Category::ACCESS_PUBLIC . ' AND categories.status = ' . Category::STATUS_PUBLIC);
            })->whereNotExists(function($query) use ($user){
                // Buscamos si la categoría de la publicación está escondida por el usuario
                $query->select(DB::Raw(1))->from('marks')->whereRaw('marks.userid = ' . $user->id . ' AND marks.categoryid = posts.categoryid AND marks.type = ' . Mark::TYPE_HIDDED);
                // Buscamos si la publicación está escondida por el usuario
                $query->select(DB::Raw(1))->from('marks')->whereRaw('marks.userid = ' . $user->id . ' AND marks.postid = posts.id AND marks.type = ' . Mark::TYPE_HIDDED);
            })->where([['status','<>',Post::STATUS_HIGHLIGHT],['status','<>',Post::STATUS_OFFICIAL],'postid' => null]);
            
            //$posts = Post::whereRaw("exists (select 1 from `categories` where categories.id = posts.categoryid AND categories.access = " . Category::ACCESS_PUBLIC . " AND categories.status = " . Category::STATUS_PUBLIC . ") and not exists (select 1 from `marks` where marks.userid = {$user->id} AND marks.categoryid = posts.categoryid AND marks.type = " . Mark::TYPE_HIDDED . " and marks.userid = {$user->id} AND marks.postid = posts.id AND marks.type = " . Mark::TYPE_HIDDED . ") and (`status` <> " . Post::STATUS_HIGHLIGHT . " and `status` <> " . Post::STATUS_OFFICIAL . " and `postid` is null)");
            //$post->toSql();
            //Log::info($posts->toSql());
            //return ['success' => false,'description' => $posts->toSql()];
        }
        else if($category->status === Category::STATUS_MY)
        {
            $posts = Post::where(['userid' => $user->id,'postid' => null]);
        }
        else if($category->status === Category::STATUS_FAVORITES)
        {
            $posts = Post::whereExists(function($query){
                // Mientras la categoría sea pública
                $query->select(DB::Raw(1))->from('categories')->whereRaw('categories.id = posts.categoryid AND categories.access = ' . Category::ACCESS_PUBLIC . ' AND categories.status = ' . Category::STATUS_PUBLIC);
            })->whereExists(function($query) use ($user) {
                // Mientras la publicación sea favorita
                $query->select(DB::Raw(1))->from('marks')->whereRaw('marks.userid = ' . $user->id . ' AND marks.postid = posts.id AND marks.type = ' . Mark::TYPE_FAVORITE);
            })->where([['status','<>',Post::STATUS_HIGHLIGHT],['status','<>',Post::STATUS_OFFICIAL],'postid' => null]);
        }
        else if($category->status === Category::STATUS_HIDDED)
        {
            $posts = Post::whereExists(function($query){
                // Mientras la categoría sea pública
                $query->select(DB::Raw(1))->from('categories')->whereRaw('categories.id = posts.categoryid AND categories.access = ' . Category::ACCESS_PUBLIC . ' AND categories.status = ' . Category::STATUS_PUBLIC);
            })->whereExists(function($query) use ($user) {
                // Mientras la publicación sea favorita
                $query->select(DB::Raw(1))->from('marks')->whereRaw('marks.userid = ' . $user->id . ' AND marks.postid = posts.id AND marks.type = ' . Mark::TYPE_HIDDED);
            })->where([['status','<>',Post::STATUS_HIGHLIGHT],['status','<>',Post::STATUS_OFFICIAL],'postid' => null]);
            $omitehidded = !$omitehidded; 
        }
        else if($category->status === Category::STATUS_REPORTS)
        {
            $posts = Post::whereExists(function($query){
                // Mientras la categoría sea pública
                $query->select(DB::Raw(1))->from('marks')->whereRaw('marks.postid = posts.id AND marks.type = ' . Mark::TYPE_REPORTED);
            });
        }
        else
        {
            $w = array_merge($w,['status' => Post::STATUS_STANDARD,'categoryid' => $category->id,'postid' => null]);
            $posts = Post::where($w);
        }
    }

    public function getPosts(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'category' => 'required|integer|filled',
            'page' => 'integer|required|filled',
            'last_time' => 'optional|nullable|date'
        ],[
            'category.required' => 'Se requiere una categoría para recuperar.',
            'page.required' => 'Se esperaba una página.'
        ]);
        
        if($validator->fails())
        {
            foreach($validator->errors()->all() as $message)
            {
                return ['success' => false,'description' => $message];
            }
        }
        
        $user = User::getUser();
        $category = Category::findOrFail($request->category);
        $w = [];
        $omitehidded = false;
        
        if($category->id === 1)
        {
            // Mientras sea una publicación estandar y no sea un comentario...
            
            $posts = Post::whereExists(function($query){
                // Mientras la categoría sea pública
               $query->select(DB::Raw(1))->from('categories')->whereRaw('categories.id = posts.categoryid AND categories.access = ' . Category::ACCESS_PUBLIC . ' AND categories.status = ' . Category::STATUS_PUBLIC);
            })->whereNotExists(function($query) use ($user){
                // Buscamos si la categoría de la publicación está escondida por el usuario
                $query->select(DB::Raw(1))->from('marks')->whereRaw('marks.userid = ' . $user->id . ' AND marks.categoryid = posts.categoryid AND marks.type = ' . Mark::TYPE_HIDDED);
                // Buscamos si la publicación está escondida por el usuario
                $query->select(DB::Raw(1))->from('marks')->whereRaw('marks.userid = ' . $user->id . ' AND marks.postid = posts.id AND marks.type = ' . Mark::TYPE_HIDDED);
            })->where([['status','<>',Post::STATUS_HIGHLIGHT],['status','<>',Post::STATUS_OFFICIAL],'postid' => null]);
            
            //$posts = Post::whereRaw("exists (select 1 from `categories` where categories.id = posts.categoryid AND categories.access = " . Category::ACCESS_PUBLIC . " AND categories.status = " . Category::STATUS_PUBLIC . ") and not exists (select 1 from `marks` where marks.userid = {$user->id} AND marks.categoryid = posts.categoryid AND marks.type = " . Mark::TYPE_HIDDED . " and marks.userid = {$user->id} AND marks.postid = posts.id AND marks.type = " . Mark::TYPE_HIDDED . ") and (`status` <> " . Post::STATUS_HIGHLIGHT . " and `status` <> " . Post::STATUS_OFFICIAL . " and `postid` is null)");
            //$post->toSql();
            //Log::info($posts->toSql());
            //return ['success' => false,'description' => $posts->toSql()];
        }
        else if($category->status === Category::STATUS_MY)
        {
            $posts = Post::where(['userid' => $user->id,'postid' => null]);
        }
        else if($category->status === Category::STATUS_FAVORITES)
        {
            $posts = Post::whereExists(function($query){
                // Mientras la categoría sea pública
                $query->select(DB::Raw(1))->from('categories')->whereRaw('categories.id = posts.categoryid AND categories.access = ' . Category::ACCESS_PUBLIC . ' AND categories.status = ' . Category::STATUS_PUBLIC);
            })->whereExists(function($query) use ($user) {
                // Mientras la publicación sea favorita
                $query->select(DB::Raw(1))->from('marks')->whereRaw('marks.userid = ' . $user->id . ' AND marks.postid = posts.id AND marks.type = ' . Mark::TYPE_FAVORITE);
            })->where([['status','<>',Post::STATUS_HIGHLIGHT],['status','<>',Post::STATUS_OFFICIAL],'postid' => null]);
        }
        else if($category->status === Category::STATUS_HIDDED)
        {
            $posts = Post::whereExists(function($query){
                // Mientras la categoría sea pública
                $query->select(DB::Raw(1))->from('categories')->whereRaw('categories.id = posts.categoryid AND categories.access = ' . Category::ACCESS_PUBLIC . ' AND categories.status = ' . Category::STATUS_PUBLIC);
            })->whereExists(function($query) use ($user) {
                // Mientras la publicación sea favorita
                $query->select(DB::Raw(1))->from('marks')->whereRaw('marks.userid = ' . $user->id . ' AND marks.postid = posts.id AND marks.type = ' . Mark::TYPE_HIDDED);
            })->where([['status','<>',Post::STATUS_HIGHLIGHT],['status','<>',Post::STATUS_OFFICIAL],'postid' => null]);
            $omitehidded = !$omitehidded; 
        }
        else if($category->status === Category::STATUS_REPORTS)
        {
            $posts = Post::whereExists(function($query){
                // Mientras la categoría sea pública
                $query->select(DB::Raw(1))->from('marks')->whereRaw('marks.postid = posts.id AND marks.type = ' . Mark::TYPE_REPORTED);
            });
        }
        else
        {
            $w = array_merge($w,['status' => Post::STATUS_STANDARD,'categoryid' => $category->id,'postid' => null]);
            $posts = Post::where($w);
        }
        
        if(!$omitehidded)
        {
            $posts = $posts->whereNotExists(function($query) use ($user){
                $query->select(DB::Raw(1))->from('marks')->whereRaw('marks.userid = ' . $user->id . ' AND marks.postid = posts.id AND marks.type = ' . Mark::TYPE_HIDDED);
            });
        }

        if($request->last_time)
        {
            $posts = $posts->where(['last_update','<',$request->last_time]);
        }
        
        $posts = Post::getArrayFromPosts($posts->orderBy('last_update','desc')->paginate(Self::POST_CHUNK_SIZE));

        if($request->page <= 1) //count($posts)
        {
            $official_posts = Post::getArrayFromPosts(Post::where(['status' => Post::STATUS_OFFICIAL,'postid' => null])->orderBy('created_at','desc')->get());
            $highlight_posts = Post::getArrayFromPosts(Post::where(['status' => Post::STATUS_HIGHLIGHT,'postid' => null])->orderBy('created_at','desc')->get());
            $posts = array_merge($official_posts,$highlight_posts,$posts);
        }
        
        return [
            'success' => true,
            $posts
        ];
    }
    
    private function parseAttach(&$post,&$content,&$error = '')
    {
        if(($attachurl = $this->extractAttachUrl($content)) !== false)
        {
            if($this->isValidUrlForAttach($attachurl,$type = 0))
            {
                $attach = new Attach;
                $attach->url = $attachurl;
                if(($attach->type = $type) == Self::ATTACH_TYPE_IMAGE)
                {
                    
                }
                
            }
            else
            {
                $error = 'URL inválida';
            }
        }
        
        return $error === '';
    }
    
    private function fixContent($content)
    {
        $content = str_replace("\r\n",'[br]',$content);
        return $this->replaceBB(htmlspecialchars($content));
    }
    
    private function videoType($url) {
        if (strpos($url, 'youtube') > 0) {
            return 'youtube';
        } elseif (strpos($url, 'vimeo') > 0) {
            return 'vimeo';
        } else {
            return 'unknown';
        }
    }

    private function hasBannedWords($content)
    {
        $badwords = [
            'oscarcitoca',
            'Los Copihues 1054',
            'oscarcasas',
            'casas oscar',
            '40101127',
            'oscarcasasnqn@gmail.com',
            'Oscar Casas',
            '+54.2994678607',
            ' 8607',
            '2994678607',
            '4678607',
            'www.publicdomainregistry.com',
            'Los Copihues',
            'Domain Name: HIXEL.NET',
            'Registrant Postal Code: 8300',
            /*'R O Z E D . c_LUB',
            'c_LUB!!!!',
            'ROZ_-E',
            '---------',
            '________',
            '_-_-_-_-'*/
        ];

        foreach($badwords as $badword)
        {
            if(stristr($content,$badword) !== false)
                return true;
        }

        return false;
    }

    private function makePostByReq(Request &$request,&$category = null,&$user = null,&$error = null)
    {
        $user = User::getUser();

        if($this->hasBannedWords($request->content))
        {
            $user->ban(9999999);
            return;
        }

        $post = new Post;
        $post->categoryid = $request->category ?? 1;
        $post->sid = Str::random(10);
        $post->title = $request->title;
        $post->content = $request->content;
        $post->userid = $user->id;
        $post->postid = $request->postid;
        $post->rps = random_int(1,3);
        
        do
        {
            if($request->has('audiodata'))
            {
                if(gettype($request->audiodata) === "string")
                    break;
                
                if($request->audiodata->getSize())
                {
                    $fname = 'audio_' . time() . '.ogg';
                    $request->audiodata->move(public_path('audio'),$fname);
                    $post->audioid = DB::table('audios')->insertGetId([
                        'file' => '/public/audio/' . $fname
                    ]);
                }
            }
        }while(false);

        if($request->has('fronturl'))
        {
            switch($request->fronttype)
            {
                case 'i': // direct img url
                    
                    break;
                case 'ytb':
                    // $front = Front::find($post->front);
                    if(Front::where(['extra' => $request->fronturl])->exists())
                    {
                        $post->front = Front::where(['extra' => $request->fronturl])->first()->id;
                        break;
                    }

                    $front = new Front;
                    $front->thumbnail_sha256 = $front->preview_sha256 = $front->path_sha256 = $front->original_sha256 = $front->thumbnail = $front->preview = $front->path = '';
                    $front->externpath = "https://img.youtube.com/vi/{$request->fronturl}/hqdefault.jpg";
                    $front->externpreview = "https://img.youtube.com/vi/{$request->fronturl}/mqdefault.jpg";
                    $front->externthumbnail = "https://img.youtube.com/vi/{$request->fronturl}/sddefault.jpg";
                    $front->extra = $request->fronturl;
                    $front->type = Front::TYPE_YOUTUBE_VIDEO;
                    if($request->has('censorefront'))
                        $front->status = Front::STATUS_CENSORED;
                    $front->save();
                    $post->front = $front->id;
                    break;
            }
        }else if($request->has('front'))
        {
            if(($post->front = Front::saveImage($request->front,$error,$request->has('censorefront'))) === false)
                return false;
        }
        
        if($request->has('status'))
        {
            if($user->isAdmin())
                $post->status = $request->status;
        }
        
        return $post;
    }
    
    public function removeNotification(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'notifyid' => 'required|integer|filled'
        ]);
        
        if($validator->fails())
        {
            foreach($validator->errors()->all() as $message)
            {
                return ['success' => false,'description' => $message];
            }
        }
        
        $user = User::getUser();
        
        Notification::where('id',$request->notifyid)->delete();
        
        return ['success' => true];
    }

    public function getLastComment()
    {
        return User::getUser()->getLastComment()->getArray(true);
    }
    
    public function submitComment(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'postid' => 'required|integer|filled'
        ]);
        
        if($validator->fails())
        {
            foreach($validator->errors()->all() as $message)
            {
                return ['success' => false,'description' => $message];
            }
        }
        
        if (!$request->filled('content')) {
            if(!$request->hasAny(['audiodata','front','fronturl']))
            {
                return ['success' => false,'description' => 'Se esperaba algún tipo de contenido.'];
            }
        }
        
        $comment = $this->makePostByReq($request,$category,$user,$error);
        
        if($error !== null)
            return ['success' => false,'description' => $error];

        if($user->isBanned())
            return ['success' => false,'description' => 'Used está baneado hasta: '. $user->unban_date . '.'];
        
        // if(ReCaptcha::hasReCaptcha())
        // {
            // if(!$request->has('g-recaptcha-response'))
                // return ['success' => false,'description' => 'Se require captcha a verificar.'];
            
            // if(!ReCaptcha::isValidResponse($request->input('g-recaptcha-response')))
                // return ['success' => false,'description' => 'Captcha inválido.'];
        // }
        
        $comment->save();
        
        $colorrnd = rand(1,101);
        
        switch(true)
        {
            case in_array($colorrnd,range(1,90)):
                $comment->extra = rand(Post::COLOR_RED,Post::COLOR_PINK);
                break;
            case in_array($colorrnd,range(91,96)):
                $comment->extra = Post::COLOR_MULTICOLOR;
                break;
            case in_array($colorrnd,range(97,99)):
                $comment->extra = Post::COLOR_INVERTED;
                break;
            case $colorrnd === 100:
                $comment->extra = Post::COLOR_WHITE;
                break;
            case $colorrnd === 101:
                $comment->extra = Post::COLOR_FRAMING;
                break;
        }

        $post = Post::find($comment->postid);
        $post->notificate($comment);
        $post->bump();
        
        if($responses = $this->getResponeses($comment->content))
        {
            foreach($responses[1] as $index => $response)
            {
                $where = ['sid' => $response];
                
                if(Post::where($where)->exists())
                {
                    $referenced = Post::where($where)->first();
                    $referenced->notificate($comment);
                    
                    $rps = new Response;
                    $rps->postid1 = $referenced->id;
                    $rps->postid2 = $comment->id;
                    $rps->save();
                    //Response::create(['postid1' => $referenced->id,'postid2' => $comment->id])->save();
                    
                    

                    //$comment->content = preg_replace("#(\s?{$responses[0][$index]}\s?)#","",$comment->content);
                    $comment->content = str_replace($responses[0][$index],'',$comment->content);
                    //$comment->content = str_replace($responses[0][$index],"[self={$referenced->getUrl()}]&gt;{$response}[/self]",$comment->content);
                }
            }
        }
        
        $comment->content = ltrim($comment->content);

        $comment->save();

        return ['success' => true,'url' => $comment->getUrl()];
    }
    
    public function getNotifications($user)
    {
        return Notification::where('userid',$user->id)->get();
    }
    
    public function postAction(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'action' => 'required|string|filled|max:12',
            'postid' => 'required|integer|filled'
        ],[
            'action.required' => 'Se requiere acción.',
            'postid.required' => 'Se requiere postid.'
        ]);
        
        if($validator->fails())
        {
            foreach($validator->errors()->all() as $message)
            {
                return ['success' => false,'description' => $message];
            }
        }
        
        $user = User::getUser();
        
        if(!($user->isAdmin() || $user->isModerator()))
            return ['success' => false,'description' => 'Usted no es parte del STAFF.']; 
        
        $post = Post::find($request->postid);
        
        switch($request->action)
        {
            case 'move':
                if(!$request->has('category'))
                    return ['success' => false,'description' => 'Se esperaba categoría.'];

                $post->categoryid = $request->category;
                $post->save();
                break;
            case 'uncensoreimg':
                if($post->front <= 1)
                    break;
                
                $front = Front::find($post->front);
                
                if($front->status === Front::STATUS_CENSORED)
                    $front->status = Front::STATUS_NORMAL;

                $front->save();
                break;
            case 'censoreimg':
                if($post->front <= 1)
                    break;
            
                $front = Front::find($post->front);
                
                if($front->status === Front::STATUS_NORMAL)
                    $front->status = Front::STATUS_CENSORED;

                $front->save();
                break;
            case 'bananddel':
                $userid = $post->userid;
                $post_user = User::find($post->userid);
                $post_user->ban($request->bantime);
                $post->clear();
                break;
            case 'delallpost':
                foreach(Post::where(['userid' => $post->userid])->get() as $fpost)
                {
                    $fpost->clear();
                }
                break;
            case 'delimage':
                if($post->front === 1)
                    return ['success' => false,'description' => 'Esta imagen no se puede eliminar.'];

                $front = Front::find($post->front);
                $front->deleteWithImage();
                
                $post->front = 1;
                $post->save();
                break;
            case 'banimage':
                if($post->front === 1)
                    return ['success' => false,'description' => 'Esta imagen no se puede banear.'];
                
                $front = Front::find($post->front);
                $front->banMe();

                $post->front = 1;
                $post->save();
                break;
            case 'bananddelimage':
                if($post->front === 1)
                    return ['success' => false,'description' => 'Esta imagen no se puede banear.'];
            
                $front = Front::find($post->front);
                $front->banMe();

                $post->front = 1;
                $post->save();
                break;
            case 'cstatus':
                if(!$request->has('status'))
                    return ['success' => false,'description' => 'Se esperaba estado.'];

                    $post->status = $request->status;
                    $post->save();
                break;
            case 'modifypost':
                if((!$request->has('status')) || (!$request->has('category')))
                    return ['status' => false,'description' => 'Se esperaba nuevo estado y categoría.'];
                
                $post->status = $request->status;
                $post->categoryid = $request->category;
                $post->save();
                break;
            case 'delete':
                Post::where('postid',$post->id)->delete();
                Notification::where('postid',$post->id)->delete();
                if($post->front !== 1)
                    Front::where('id',$post->front)->first()->deleteWithImage();
                $post->clear();
                break;
            case 'ban':
                if(!$request->has('bantime'))
                    return ['status' => false,'description' => 'Se esperaba tiempo de baneo.'];

                $post_user = User::find($post->userid);
                $post_user->ban($request->bantime);
                break;
            case 'highlight':
                $post->status = Post::STATUS_HIGHLIGHT;
                $post->save();
                break;
            case 'makeofficial':
                $post->status = Post::STATUS_OFFICIAL;
                $post->save();
                break;
            default:
                return ['success' => false,'description' => 'Acción inválida.'];
        }
        
        return ['success' => true];
    }
    
    public function submitPost(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'content' => 'max:2000',
            'title' => 'required|string|filled|max:100',
            'category' => 'required|integer|filled'
        ],[
            'title.required' => 'Se necesita un título para la publicación.',
            'category.required' => 'La publicación necesita una categoría.'
        ]);
        
        if($validator->fails())
        {
            foreach($validator->errors()->all() as $message)
            {
                return ['success' => false,'description' => $message];
            }
        }
        
        if(ReCaptcha::hasReCaptcha())
        {
            if(!$request->has('g-recaptcha-response'))
                return ['success' => false,'description' => 'Se requiere captcha a verificar.'];
            
            if(!ReCaptcha::isValidResponse($request->input('g-recaptcha-response'))['success'])
                return ['success' => false,'description' => 'Captcha inválido.'];
        }
        
        //return ['success' => false,'description' => $request->audiodata];
        
        if(!$request->filled('content')){
            if(!$request->hasAny(['audiodata','front','fronturl']))
            {
                return ['success' => false,'description' => 'Se esperaba algún tipo de contenido.'];
            }
        }
        
        $post = $this->makePostByReq($request,$category,$user,$error);

        if($error !== null)
            return ['success' => false,'description' => $error];

        if($user->isBanned())
            return ['success' => false,'description' => 'Used está baneado hasta: '. $user->unban_date . '.'];
        
        $post->last_update = date('Y-m-d H:i:s');
        
        $post->save();
        
        return ['success' => true,'url' => $post->getUrl()];
    }
}
