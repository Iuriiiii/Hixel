<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use App\Models\Post;

class Notification extends Model
{
    use HasFactory;
    
    public const TYPE_COMMENT_NOTIFICATION = 1;
    public const TYPE_RESPONSE_NOTIFICATION = 2;
    
    protected $fillable  = ['postid','commentid','commentidowner','userid','type','counter'];
    
    public function getPostUrl()
    {
        return $this->getPost()->getUrl();
    }
    
    public function getPost()
    {
        return Post::find($this->postid);
    }
    
    public function getComment()
    {
        return Post::find($this->commentid);
    }
}
