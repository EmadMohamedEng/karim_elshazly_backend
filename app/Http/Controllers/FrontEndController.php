<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Setting ; 
use App\Rbt ; 
use App\Post ; 
use App\Content ; 
use App\Type ;  
use Carbon\Carbon;

class FrontEndController extends Controller
{

    private $PAGINATION_NUMBER = 3; 
    public function __construct()
    {
        $settings = Setting::where('key','LIKE','%pagination_number%')->first() ; 
        if($settings)
            $this->PAGINATION_NUMBER = (int) $settings->value; 
    }

    public function homepage(Request $request)
    {
        // STEPS 
        // 1. get homepage image from settings 
        // 2. get facebook,twitter,instagram, and youtube links from settings
        // 3. if not found return default values 
        $op_id = $request['op_id'] ;
        $homepage_image = Setting::where('key','LIKE','%image%')->first();
        $facebook_link = Setting::where('key','LIKE','%facebook%')->first() ; 
        $twitter_link = Setting::where('key','LIKE','%twitter%')->first() ; 
        $instagram_link = Setting::where('key','LIKE','%instagram%')->first() ;
        $youtube_link = Setting::where('key','LIKE','%youtube%')->first() ; 
        $slogan  = Setting::where('key','LIKE','%slogan%')->first() ;

        if(! $homepage_image)
            $homepage_image = url('img/home.jpg');
        else
            $homepage_image = $homepage_image->value ; 

        if(! $facebook_link)
            $facebook_link = "https://www.facebook.com/karem.alshazley/" ; 
        else
            $facebook_link = $facebook_link->value ; 

        if(! $twitter_link)
            $twitter_link = "https://twitter.com/karim_alshazley?lang=ar" ; 
        else
            $twitter_link = $twitter_link->value ; 
            
        if(! $instagram_link)
            $instagram_link = "https://www.instagram.com/karim_alshazley/" ; 
        else
            $instagram_link = $instagram_link->value ; 

        if(! $youtube_link)
            $youtube_link = "https://www.youtube.com/channel/UC9Gx0kQ94C2tyzuLzzYy9VQ" ; 
        else
            $youtube_link = $youtube_link->value ; 

        if(! $slogan)
            $slogan = "كاتب مصري معاصر له العديد من الكتب" ; 
        else
            $slogan = $slogan->value ; 

        $title = "الرئيسية" ; 

        return view('front_end.index',compact('slogan','op_id','title','youtube_link','homepage_image','facebook_link','twitter_link','instagram_link'))  ;
    }
    

    public function audiosPaginate(Request $request)
    {
        $op_id = $request['op_id'] ; 
        if(isset($op_id)&&is_numeric($op_id))
        {
            // list from posts
            $rbts = Rbt::where('operator_id',$op_id)
            ->where('published',1)
            ->join('contents','rbts.content_id','=','contents.id')
            ->join('types','contents.type_id','=','types.id')
            ->where('types.title','LIKE','%audio%')
            ->select('contents.*')
            ->paginate($this->PAGINATION_NUMBER)  ;
        }
        else{
            // list from rbts  
            $rbts = Type::where('types.title','LIKE','%audio%')
            ->join('contents','types.id','=','contents.type_id')
            ->paginate($this->PAGINATION_NUMBER) ; 
        }
        return $rbts ; 
    }

    public function audios(Request $request)
    {
        // 1. get audios from posts table 
        // 2. if no operator listing will be from audio
        $title = "الصوتيات" ; 
        $op_id = $request['op_id'] ;  
        $rbts = $this->audiosPaginate($request) ; 
        $view = 'audio_page' ; 
        if(count($rbts)==0)
        {
            $view = "error" ; 
        }
        return view('front_end.'.$view,compact('title','rbts','op_id')) ;
    }
    

    public function videosPaginate(Request $request)
    {
        $op_id = $request['op_id'] ; 
        if(isset($op_id)&&is_numeric($op_id))
        {
            // list from posts
            $videos = Post::where('operator_id',$op_id)
            ->where('Published',1) 
            ->where('Published_Date','<=',Carbon::now()->format("Y-m-d"))
            ->join('contents','posts.content_id','=','contents.id')
            ->join('types','contents.type_id','=','types.id')
            ->where('types.title','LIKE','%video%')
            ->select('posts.*','contents.*')
            ->paginate($this->PAGINATION_NUMBER)  ;
        }
        else{
            // list from content  
            $videos = Type::where('types.title','LIKE','%video%')
            ->join('contents','types.id','=','contents.type_id')
            ->paginate($this->PAGINATION_NUMBER) ; 
        }
        return $videos ; 
    }

    public function videos(Request $request)
    {
        $title = "الفيديوهات" ; 
        $op_id = $request['op_id'] ; 
        $videos = $this->videosPaginate($request) ; 
        $view = 'videos' ; 
        
        if(count($videos)==0)
        {
            $view = "error" ; 
        }
        return view('front_end.'.$view,compact('title','videos','op_id')) ;
    }
    

    public function photos_paginate(Request $request)
    {
        $op_id = $request['op_id'] ; 
        if(isset($op_id)&&is_numeric($op_id))
        {
            // list from posts
            $photos = Post::where('operator_id',$op_id)
            ->where('Published',1) 
            ->where('Published_Date','<=',Carbon::now()->format("Y-m-d"))
            ->join('contents','posts.content_id','=','contents.id')
            ->join('types','contents.type_id','=','types.id')
            ->where('types.title','LIKE','%image%')
            ->select('posts.*','contents.*')
            ->paginate($this->PAGINATION_NUMBER)  ;
        }
        else{
            // list from content  
            $photos = Type::where('types.title','LIKE','%image%')
            ->join('contents','types.id','=','contents.type_id')
            ->paginate($this->PAGINATION_NUMBER) ; 
        }

        return $photos ; 
    }

    public function images(Request $request)
    {
        $title = "الصور" ; 
        $op_id = $request['op_id'] ; 
        $photos = $this->photos_paginate($request) ; 
        $view = 'images' ; 
        
        if(count($photos)==0)
        {
            $view = "error" ; 
        }
        return view('front_end.'.$view,compact('title','photos','op_id')) ;
    }
    

    public function audio_page(Request $request, $id)
    {
        $title = "الصوتيات" ; 
        $op_id = $request['op_id'] ; 
        if(isset($op_id)&&is_numeric($op_id))
        {
            // list from posts
            $track = Rbt::where('operator_id',$op_id)
            ->where('published',1)
            ->join('contents','rbts.content_id','=','contents.id')
            ->where('contents.id',$id) 
            ->first()  ;

            $related_audios = Content::join('types','types.id','=','contents.type_id')
            ->orderBy('created_at','DESC')
            ->where('types.title','LIKE','%audio%')
            ->where('contents.id','<>',$id)
            ->join('rbts','rbts.content_id','=','contents.id')
            ->where('rbts.operator_id',$op_id)
            ->select('contents.*')
            ->limit(6)
            ->get() ; 
     
        }
        else{
            // list from rbts  
            $track = Type::where('types.title','LIKE','%audio%')
            ->join('contents','types.id','=','contents.type_id')
            ->where('contents.id',$id) 
            ->select('contents.*')
            ->first()  ;

            $related_audios = Content::join('types','types.id','=','contents.type_id')
            ->orderBy('created_at','DESC')
            ->where('types.title','LIKE','%audio%')
            ->where('contents.id','<>',$id)
            ->select('contents.*')
            ->limit(6)
            ->get() ; 
        }
        
        $view = 'audio_page' ; 
        
        if(! $track)
        {
            $view = "error" ; 
        }
        return view('front_end.'.$view,compact('related_audios','track','op_id','title')) ;
    }

    public function video_page(Request $request,$id)
    {
        $title = "الفيديوهات" ; 
        $op_id = $request['op_id'] ; 
        if(isset($op_id)&&is_numeric($op_id))
        {
            // list from posts
            $track = Post::where('operator_id',$op_id)
            ->where('Published',1)
            ->where('Published_Date','<=',Carbon::now()->format("Y-m-d"))
            ->join('contents','posts.content_id','=','contents.id')
            ->where('contents.id',$id) 
            ->first()  ;

            $related_videos = Content::join('types','types.id','=','contents.type_id')
            ->join('posts','posts.content_id','=','contents.id')
            ->orderBy('created_at','DESC')
            ->where('types.title','LIKE','%video%')
            ->where('contents.id','<>',$id)
            ->where('posts.operator_id',$op_id)
            ->select('contents.*')
            ->limit(6)
            ->get() ; 
        }
        else{
            // list from rbts  
            $track = Type::where('types.title','LIKE','%video%')
            ->join('contents','types.id','=','contents.type_id')
            ->where('contents.id',$id) 
            ->select('contents.*')
            ->first()  ;
        
            $related_videos = Content::join('types','types.id','=','contents.type_id')
            ->orderBy('created_at','DESC')
            ->where('types.title','LIKE','%video%')
            ->where('contents.id','<>',$id)
            ->select('contents.*')
            ->limit(6)
            ->get() ; 
        }
        $view = 'video_page' ; 

        if(! $track)
        {
            $view = "error" ; 
        }

        return view('front_end.'.$view,compact('related_videos','track','op_id','title')) ;
    }


    public function faq(Request $request)
    {
        $title = "الإرشادات" ; 
        $op_id = $request['op_id']  ;
        return view('front_end.faq',compact('op_id','title')) ; 
    }

    public function login(Request $request)
    {
        $title = "دخول" ;
        $op_id = $request['op_id']  ;
        return view('front_end.login',compact('op_id','title')) ; 
    }
    public function register(Request $request)
    {
        $title = "تسجيل حساب" ;
        $op_id = $request['op_id']  ;
        return view('front_end.register',compact('op_id','title')) ; 
    }



}
