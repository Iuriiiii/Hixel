<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use App\Models\Category;
Use App\Models\User;
Use App\Models\Audio;
Use App\Models\Notification;
use Illuminate\Support\Facades\Log;
use App\Models\ImageHosting;
use App\Models\Response;
use App\Models\Front;
use App\Models\Mark;
// Eventos
use App\Events\PostCreated;

class Post extends Model
{
    use HasFactory;
    
    public $dispatchesEvents = [
        'saved' => PostCreated::class,
    ];

    public const COLOR_RED = 0;
    public const COLOR_GREEN = 1;
    public const COLOR_YELLOW = 2;
    public const COLOR_BROWN = 3;
    public const COLOR_ORANGE = 4;
    public const COLOR_PURPLE = 5;
    public const COLOR_PINK = 6;
    
    public const COLOR_MULTICOLOR = 10;
    public const COLOR_INVERTED = 11;
    public const COLOR_WHITE = 12;
    public const COLOR_FRAMING = 13;
    
    public const STATUS_STANDARD = 0;
    public const STATUS_HIGHLIGHT = 1;
    public const STATUS_OFFICIAL = 2;
    public const STATUS_SOLVED = 3;
    public const STATUS_VERIFIED = 4;
    public const STATUS_SUBNORMAL = 5;
    public const STATUS_GENIUS = 6;
    public const STATUS_THIS = 7;
    public const STATUS_CONFIRMED = 8;
    public const STATUS_FAKE = 9;
    public const STATUS_BAIT = 10;
    public const STATUS_OUTRAGED = 11;
    public const STATUS_IMPORTANT = 12;
    
    private const IMAGE_BB_CODE = '~\[img\](https://[^"><]*?\.(?:jpg|jpeg|gif|png|bmp|webp))\[/img\]~s';

    private $bbfind =[
        '#\[center\](.+?)\[/center\]#',
        '#\[right\](.+?)\[/right\]#',
        '~\[b\](.+?)\[/b\]~s',
        '~\[i\](.+?)\[/i\]~s',
        '~\[u\](.+?)\[/u\]~s',
        '~\[quote\]([^"><]+?)\[/quote\]~s',
        '~\[size=([0-9]+)\](.+?)\[/size\]~s',
        '~\[color=(\#[0-9a-fA-F]{1,6})\](.+?)\[/color\]~s',
        //Self::IMAGE_BB_CODE,
        '~\[spoiler\](.+?)\[/spoiler\]~s',
        '~\[t\](.+?)\[/t\]~s',
        '#^>?(?:https://)?www\.youtube\.com/watch\?v=([a-zA-Z0-9_-]+)(?:.*)?#m',
        '#^>?(?:https://)?www\.dailymotion\.com/video/([a-zA-Z0-9_-]+)(?:.*)?#m',
        '#^>?(?:https://)?[www\.]?twitter\.com/([a-zA-Z0-9_-]+)/status/([0-9]+)(?:.*)?#m',
        '#^>?(?:https?://)?[www\.]?(?:youtu|y2u)\.be/([a-zA-Z0-9_-]+)(?:.*)?#m',
        '~(\n)~s',
        '#>(?xi)\b((?:https?://|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`\!()\[\]{};:\'".,<>?«»“”‘’]))#i',
        '#\[code\](.+?)\[/code\]#',
        '#\[code="?([a-zA-Z0-9_-]+)"?\](.+?)\[/code\]#',
        '#^>?(?:https?://)?[www\.]hixel.net/[a-zA-Z0-9+]/([a-zA-Z0-9+])#m',
    ];

    private $basicreplace = [
        ':grin:' => '<img class="emoticon" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAiCAYAAABIiGl0AAAB50lEQVRYR81XMUvDQBT+blZodHFTdHPQf+Dg3P4AwdFFHJx1F/QX6OQm4g9oZxcHN8EuThUjIoq0FhQUhEjy8tLcyyV31TRtIbR3eX3f+7733eWi4P4JHEOVS5xTUJyoMmANKHg8BGbrQLcFfLTp+ukPrrg6tZ7hayRXxLhy4Agw6DaBmTrQaxGFpxPgLf7t0sSsAhpJE+PxAXueh17nDPj2gbudIfhlQ1M9tzOuGpgkfj6msi1MDe6N/hZc6qz9FxovbCTzEfM0/ZEAtzsEuLpVABxJfLMNfPlQy+da6cyEmUpmHCzv9z/pjteoGFgWxCqHUpPE93tRjFo8MvbKxtRmfdnrCQAWbrQxMEho9ITMw8oNGE8acN56ZSZBYH5aKkUrVbrfmXFZwHJZ5UqduDiHUaZ3MUM5z4zzXS16PHZg514KZWSPW9eUqbFP31apywK+alOmtd0BcJI77cJhpU7WtcXN6S1zMoDfm1RHbSqWJHtqLNzU2MW8fNjNhY/FMGNZwAzIBRQBGyV33bNlnDSTOHRoJxAN+OGChvNzf4POO3kkJjSkjTbf21O6s7JkBw7l7H8ALGvoD/+VfCIltgLLXhfBlwWsSc4Dlp5Z8TyPQ2lr08DmQabE/707VQksS6/sNXWkwL9W9iAodeOx3wAAAABJRU5ErkJggg==">',
        ':fucku:' => '<img class="emoticon" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAABz0lEQVRYR+2WLZPCMBCGU9c6cNQViUTy/38AuNa1Dhw46rh5M2zYbPM9nbsTdCYHHGH3ybtfqdQ6z4uZqXJMZm0OGP4zAO349fr4rypzpqTDJW2KnfwL8FXgtxXgJafz898ADMOgDocDFU1ShSVtelu0av52u6m2bS0FLpeLOh6Pvqp1+ioGmKZJ7fd7C+DxeKjtdlsMIGMr4aIKSFXmeVZN0wSBuJPVATiQg0L7xh/teBxH1XWd2efo6ZYCXG6qAqlARBEb4Hq9qt1ulwyg6d+DhwAkVOjz+/AfBZ7Pp3Ze17WlFlNiUfOxuS8BREiWCsAgV4GfkpzxppML4OoTiySEElIFl9wx5/geVcBVFX3CKGAOhzcyF0xbE/FOAZB7YgAWiJRaJlwJgC8HpK3FNas0BNKwaEyLEFgK+HKh5OT4DSoCADQ/eBk6Fej7XjcmV0LmQsAxAGCLzQqvAqsrIYaUNWNC09C06M1mo7BKHp/0proSjGqQ+/2eDQHpkfmQXsY+G+B8PusuKTul7wDknLorG8vJIUgqTx8ApMeiSwtlvdxffCMKhY7ijtfYHTEbAOWJmMI4HkpOKldIz9fpdAqGexUAcggIWvhfKPmI6ge2clwwG3lPEAAAAABJRU5ErkJggg==">',
        ':heart:' => '<i style="color:#1967d2;" class="fa fa-heart"></i>'
    ];
    
    public function bump()
    {
        $this->last_update = date('Y-m-d H:i:s');
        $this->save();
    }
    
    private function replaceBB($text,$replace)
    {
        $text = strip_tags($text);

        while(true)
        {
            $rplc = preg_replace($this->bbfind,$replace,$text);

            if($text === $rplc)
            {
                break;
            }
            
            $text = $rplc;
        }
        
        $text = str_replace(array_keys($this->basicreplace),array_values($this->basicreplace),$text);

        return $text;
    }
    
    public function getPreviewOfContentAsHTML($len = 100)
    {
        return substr($this->replaceBB($this->content, [
            '$1',
            '$1',
            '',
            '',
            '',
            '$1',
            '$1',
            '$1',
            '$1',
            '$2',
            '$2',
            '$1',
            '$2',
            '$2',
            '',
            '',
            '<br>',
            '$1',
            ''
        ]),0,$len);
    }
    
    public function getContentAsHTML()
    {
        return $this->replaceBB($this->content, [
            '<div style="text-align:center;">$1</div>',
            '<div style="text-align:right;">$1</div>',
            '<b>$1</b>',
            '<i>$1</i>',
            '<span style="text-decoration:underline;">$1</span>',
            '<pre>$1</pre>',
            '<span style="front-size:$1px;">$2</span>',
            '<span style="color:$1;">$2</span>',
            //'<details><summary>Imagen</summary><img src="$1" alt="" /></details>',
            '<div class="spoiler">$1</div>',
            '<del>$1</del>',
            '<div class="video-bb"><img src="https://img.youtube.com/vi/$1/mqdefault.jpg"><div class="play-button" data-urltype="ytb" data-v="$1" onclick="playVideo(this)"><i class="fa fa-play-circle"></i></div></div>',
            '<div class="video-bb" data-urltype="dlm" data-v="$1" onclick="playVideo(this)"><img src="https://www.dailymotion.com/thumbnail/video/$1"><div class="play-button"><i class="fa fa-play-circle"></i></div></div>',
            '',
            '<div class="video-bb"><img src="https://img.youtube.com/vi/$1/mqdefault.jpg"><div class="play-button" data-urltype="ytb" data-v="$1" onclick="playVideo(this)"><i class="fa fa-play-circle"></i></div></div>',
            '<br>',
            '<a href="$1" target="_blank"><i class="fa fa-external-link"> link</i></a>',
            '<pre><code>$1</code></pre>',
            '<pre><code class="$1">$2</code></pre>',
        ]);
    }
    
    static function getArrayFromPosts($allposts)
    {
        $r = [];
        foreach($allposts as $post)
        {
            $r[] = $post->getArray();
        }
        
        return $r;
    }
    
    public function getUrl()
    {
        if($this->postid)
        {
            $post = Post::find($this->postid);
            if(!$post)
                return '';
            $category = Category::find($post->categoryid)->diminutive;
            return url("/{$category}/{$post->sid}#{$this->sid}");
        }
        else
        {
            $category = Category::find($this->categoryid ?? 1)->diminutive;
            return url("/{$category}/{$this->sid}");
        }
    }
    
    public function getUser()
    {
        return User::find($this->userid);
    }
    
    public function getUserNickname()
    {
        return User::find($this->userid)->nickname;
    }
    
    public function isComment()
    {
        return $this->postid !== 0 && $this->postid !== null;
    }
    
    public function getCommentsCounter()
    {
        return Post::where('postid',$this->id)->count();
    }
    
    public function getFather()
    {
        return Post::find($this->postid);
    }
    
    public function notificate($comment)
    {
        if($comment->userid !== $this->userid)
        {
            $type = ($this->isComment() ? Notification::TYPE_RESPONSE_NOTIFICATION : Notification::TYPE_COMMENT_NOTIFICATION);
            $where = ['userid' => $this->userid,'postid' => $this->id,'type' => $type];

            Notification::firstOrCreate($where,['userid' => $this->userid,'postid' => $this->id,'commentid' => $comment->id,'commentidowner' => ( (!$this->isComment()) ? $this->id : $this->postid ),'type' => $type,'counter' => 0])->increment('counter');
        }
    }
    
    public function getImageUrl($preview = true)
    {
        // if(strstr($this->front,ImageHosting::hostPrefix()))
        // {
            // $img = str_replace(ImageHosting::hostPrefix(),'',$this->front);
            // return 'https://iili.io/' . $img . '.webp';
        // }
        
        // if($preview)
            // return url('public/img/l/' . $this->front);
        // else
            // return url('public/img/' . $this->front);
            
        $front = Front::find($this->front);

        if($preview)
            return $front->preview;
        else
            return $front->path;
    }
    
    public function getThumbnailUrl()
    {
        // if(strstr($this->front,ImageHosting::hostPrefix()))
        // {
            // $img = str_replace(ImageHosting::hostPrefix(),'',$this->front);
            // return 'https://iili.io/' . $img . '.th.webp';
        // }
        
        // if(strlen($this->front))
            // return url('public/img/t/' . $this->front);
        // else
            // return url('public/img/t/default.webp');
        
        return Front::find($this->front)->thumbnail;
    }
    
    public function getTimeDiff()
    {
        $df = date_diff(date_create('now'),date_create($this->created_at),true);
        if($df->y != 0)
            return $df->y . 'A';
            
        elseif($df->m != 0)
            return $df->m . 'M';
            
        elseif($df->d != 0)
            return $df->d . 'd';
        
        elseif($df->h != 0)
            return $df->h . 'hrs';
            
        elseif($df->i != 0)
            return $df->i . 'min';
        
        elseif($df->s != 0)
            return $df->s . 'seg';
        
        else
            return 'Ahora';
    }
    
    public function getAudioUrl()
    {
        if($this->audioid == null)
            return '';
        return Audio::find($this->audioid)->file;
    }
    
    public function getComments()
    {
        $ret = [];
        
        foreach(Response::where(['postid1' => $this->id])->get() as $response)
        {
            $post = Post::find($response->postid2);
            $ret[] = ['sid' => $post->sid,'url' => $post->getUrl()];
        }
        
        return $ret;
    }
    
    public function getResponses()
    {
        $ret = [];
        
        foreach(Response::where(['postid2' => $this->id])->get() as $response)
        {
            if(!$post = Post::find($response->postid1))
            {
                $response->delete();
            }
            else
                $ret[] = ['sid' => $post->sid,'url' => $post->getUrl()];
        }
        
        return $ret;
    }
    
    static function toParsedArray($posts,$comment = false)
    {
        $r = [];
        
        foreach($posts as $post)
        {
            $r[] = $post->getArray($comment);
        }
        
        return $r;
    }
    
    public function clear()
    {
        Post::where(['postid' => $this->id])->delete();
        Response::where(['postid1' => $this->id])->orWhere(['postid2' => $this->id])->delete();
        Notification::where(['postid' => $this->id])->orWhere(['commentid' => $this->id])->delete();
        Post::where(['id' => $this->id])->delete();
    }

    public function isFavorite()
    {
        $user = User::getUser();

        return Mark::where([
            'postid' => $this->id,
            'userid' => $user->id,
            'type' => Mark::TYPE_FAVORITE
        ])->exists();
    }
    
    public function getArray($comment = false)
    {
        $only = 0;

        if(empty($this->content) && empty($this->audioid))
        {
            $only = 1; // Solo imagen - Only Image
        }
        else if(empty($this->content) && empty($this->front))
        {
           $only = 2; // Solo Audio
        }
        else if(empty($this->audioid) && ($this->front === 1))
        {
            $only = 3; // Solo contenido - Only Content
        }

        $front = Front::find($this->front);
        $preview = $this->status === Post::STATUS_OFFICIAL || $this->status === Post::STATUS_HIGHLIGHT  ? $front->path: $front->preview;
        $ret = [
            'id' => $this->id,
            'userid' => $this->userid,
            'fav' => $this->isFavorite(),
            'cc' => $this->getCommentsCounter(),
            'status' => $this->status,
            'userid' => $this->userid,
            'preview' => $front->type === Front::TYPE_YOUTUBE_VIDEO ? $front->externpreview : $preview,
            'title' => $this->title,
            'category' => Category::find($this->categoryid)->diminutive,
            'last_update' => $this->last_update,
            'url' => $this->getUrl(),
            'censoredf' => $front->status === Front::STATUS_CENSORED,
        ];
        
        if($comment)
        {
            unset($ret['censoredf']);
            unset($ret['last_update']);
            unset($ret['preview']);
            unset($ret['cc']);
            unset($ret['fav']);
            $ret['htmlcontent'] = $this->getContentAsHTML();
            $ret['comments'] = $this->getComments();
            $ret['responses'] = $this->getResponses();
            $ret['extra'] = $this->extra;
            $ret['sid'] = $this->sid;
            $ret['usernickname'] = $this->getUserNickname();//$this->getUser()->status == User::STATUS_STAFF ? $this->getUserNickname() : 'Anónimo';
            $ret['audio'] = ['exists' => $this->audioid > 0,'src' => $this->getAudioUrl()];
            $ret['iscomment'] = $this->isComment();
            $ret['timediff'] = $this->getTimeDiff();
            $ret['front'] = [
                'exists' => $this->front !== 1,
                'animated' => $front->animated,
                'type' => $front->type,
                'hq' => $front->path,
                'sq' => $preview,
                'lq' => $front->thumbnail,
                'ehq' => $front->externpath,
                'esq' => $front->externpreview,
                'elq' => $front->externthumbnail,
                'extra' => $front->extra,
                'censored' => $front->status === Front::STATUS_CENSORED
            ];
            $ret['only'] = $only;
        }
        
        return $ret;
    }
}