<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
// use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
// use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
// use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Notification;
use App\Models\Category;
use App\Models\Mark;
use App\Models\Theme;
use Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class User extends Model implements AuthenticatableContract/*implements
    AuthenticatableContract,
    AuthorizableContract*/
{
    use HasFactory;
    //use Authenticatable;
    use Authenticatable;

    protected $fillable = ['address','nickname','sid'];
    
    public const STATUS_STANDARD = 0;
    public const STATUS_STAFF = 1;
    public const STATUS_MOD = 3;
    public const STATUS_BANNED = 2;
    
    public static function getUser($ip = null)
    {
        if(!$ip)
            $ip = Request::ip();
        
        $user = User::firstOrCreate([
            'address' => $ip,
        ],[
            'nickname' => Str::random(10),
            'sid' => hash('sha256',$ip),
            'theme' => Theme::DEFAULT
        ]);

        Auth::login($user,true);

        return $user;
    }
    
    function getLastComment()
    {
        return Post::where(['userid' => $this->id,['postid','<>',null]])->latest()->first();
    }

    public function ban($hours)
    {
        $this->status = Self::STATUS_BANNED;
        Log::info($hours);
        $time = date_create("now +{$hours} hours");
        $bantime = date_format($time,'Y-m-d H:i:s');
        $this->unban_date = $bantime;
        $this->save();
    }
    
    public function unBan()
    {
        $this->status = Self::STATUS_STANDARD;
        $this->unban_date = null;
        $this->save();
    }
    
    public function getNotifications()
    {
        return Notification::where(['userid' => $this->id])->get();
    }
    
    public function removeNotification($post)
    {
        Notification::where(['userid' => $this->id,'commentidowner' => $post->id])->delete();
    }
    
    public function isCategoryHidden($categoryid)
    {
        return Mark::where(['categoryid' => $categoryid,'userid' => $this->id,'type' => Mark::TYPE_HIDDED])->exists();
    }
    
    public function isBanned()
    {
        // if(date_diff(date_create('now'),date_create($this->unban_date))->invert === 1)
        // {
            // $this->unban_date = null;
            // $this->status === Self::STATUS_STANDARD;
            // $this->save();
        // }
        return $this->status === Self::STATUS_BANNED;
    }
    
    public function getThemeUrl()
    {
        if(Theme::where(['id' => $this->theme])->exists())
            return Theme::where(['id' => $this->theme])->first()->path;
        else
            return Theme::find(Theme::DEFAULT)->path;
    }
    
    public function isAdmin()
    {
        return $this->status === Self::STATUS_STAFF;
    }
    
    public function isModerator()
    {
        return $this->status === Self::STATUS_MOD;
    }

    public function isStaff()
    {
        return $this->isAdmin() || $this->isModerator();
    }
}
