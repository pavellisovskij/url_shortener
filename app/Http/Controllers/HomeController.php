<?php

namespace App\Http\Controllers;

use App\Url;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
//    public function __construct()
//    {
//        $this->middleware('auth');
//    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function shortening(Request $request)
    {
        $short_url = DB::table('urls')->
            where(['url' => $request->link])->
            get(['short_url']);

        if (!isset($short_url[0]->short_url)) {
            $short_url = Str::random(8);
            if (Str::is('https://*', $request->link) == false) {
                $link = 'https://' . $request->link;
            }
            else {
                $link = $request->link;
            }
            DB::table('urls')->insert([
                'url' => $link,
                'short_url' => $short_url
            ]);
        }
        else {
            $short_url = $short_url[0]->short_url;
        }

        return response(['short_url' => $short_url],200);
    }

    public function error404() {
        return view(404);
    }

    public function redirector($id) {
        $url = DB::table('urls')->
            where(['short_url' => $id])->
            get(['url']);

        if (isset($url[0]->url)) {
            return redirect($url[0]->url);
        }
        else {
            return redirect('/404');
        }

    }
}
