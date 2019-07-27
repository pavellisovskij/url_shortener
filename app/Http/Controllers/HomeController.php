<?php

namespace App\Http\Controllers;

use App\Stat;
use App\Url;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;

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
        if (Str::is('https://www.*', $request->link) == true) {
            $link = substr($request->link, 12);
        } elseif (Str::is('http://www.*', $request->link) == true) {
            $link = substr($request->link, 11);
        } elseif (Str::is('www.*', $request->link) == true) {
            $link = substr($request->link, 4);
        } elseif (Str::is('http://*', $request->link) == true) {
            $link = substr($request->link, 7);
        } elseif (Str::is('https://*', $request->link) == true) {
            $link = substr($request->link, 8);
        } else {
            $link = $request->link;
        }

        $url = Url::where(['url' => $link])->first();

        if (!isset($url)) {
            $url = new Url();
            $url->url       = $link;
            $url->short_url = Str::random(8);
            $url->save();
        }

        return response(['short_url' => $url->short_url],200);
    }

    public function error404() {
        return view(404);
    }

    public function redirector($path) {
        $url = Url::where(['short_url' => $path])->first();

        if (isset($url->url)) {
            $agent = new Agent();
            $data = json_decode(file_get_contents('http://free.ipwhois.io/json/' . Url::getIP()), true);

            $stat = new Stat();
            $stat->id_url       = $url->id;
            $stat->browser      = $agent->browser();
            $stat->br_version   = $agent->version($agent->browser());
            $stat->os           = $agent->platform();
            $stat->ip           = Url::getIP();
            $stat->city         = $data['city'];
            $stat->country      = $data['country'];
            $stat->clicked_at   = date_format(date_create(now()), 'F Y');
            $stat->save();
            return redirect('https://' . $url->url);
        }
        else {
            return redirect('/404');
        }
    }

    public function dashboard($path) {
        $link = Url::where('short_url', $path)->first();

        return view('dashboard', [
            'id'            => $link['id'],
            'url'           => $link['url'],
            'short_url'     => $link['short_url'],
            'created_at'    => $link['created_at'],
        ]);
    }

    public function data1(Request $request) {
        $count = Stat::where('id_url', $request->id)
            ->count();

        $dates = DB::table('statistics')
            ->select(['clicked_at'])
            ->where('id_url', '=', $request->id)
            ->distinct()
            ->get();

        $clicks = [];
        $labels = [];

        foreach ($dates as $date) {
            $data = DB::table('statistics')
                ->select('clicked_at')
                ->where([
                    ['id_url', '=', $request->id],
                    ['clicked_at', '=', $date->clicked_at]
                ])
                ->count();

            array_push($clicks, $data);
            array_push($labels, $date->clicked_at);
        }

        return response([
            'count' => $count,
            'chart' => array([
                'labels'    => $labels,
                'datasets'  => array([
                    'label'             => 'Переходы по ссылке',
                    'backgroundColor'   => '#F26202',
                    'data'              =>  $clicks
                ])
            ])
        ], 200);
    }

    public function data2(Request $request) {
        $locations = DB::table('statistics')
            ->select(['country', 'city'])
            ->where('id_url', '=', $request->id)
            ->distinct()
            ->get();

        $clicks = [];
        $labels = [];

        foreach ($locations as $location) {
            $data = DB::table('statistics')
                ->select(['country', 'city'])
                ->where([
                    ['id_url', '=', $request->id],
                    ['country', '=', $location->country],
                    ['city', '=', $location->city],
                ])
                ->count();

            array_push($clicks, $data);
            array_push($labels, $location->country . ', ' . $location->city);
        }

        return response([
            'labels'    => $labels,
            'datasets'  => array([
                'label'             => 'Переходы по ссылке',
                'backgroundColor'   => '#00B8D0',
                'data'              =>  $clicks
            ])
        ], 200);
    }
}
