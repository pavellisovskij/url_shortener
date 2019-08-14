<?php

namespace App\Http\Controllers;

use App\Stat;
use App\Url;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
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
    public function __construct()
    {
        $this->middleware('auth');
    }

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
     *
     * Проверяет есть ли такой же URL в базе
     * true - возвращает в LinkShortonerComponent старую запись из бд
     * false - создает новую и записывает туда сокращенную ссылку
     */
    public function shortening(Request $request)
    {
        $link = Auth::user()->url()->where('url', Url::cutLink($request->link))->first();

        if (isset($link->url)) {
            return response(['short_url' => $link->short_url],200);
        }
        else {
            $newUrl = new Url();
            $newUrl->user_id    = Auth::id();
            $newUrl->url        = Url::cutLink($request->link);
            $newUrl->created_at = now();
            $newUrl->save();

            $newUrl->short_url = URL::getShortLinkById($newUrl->id);
            $newUrl->save();

            return response(['short_url' => $newUrl->short_url],200);
        }
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     *
     * Передает в LinkListComponent список ссылок для аутентифицированного пользователя
     */
    public function getList() {
        $list = Auth::user()->url()->get();
        if (!empty($list[0])) {
            return response(['list' => $list],200);
        }
        else return '';

    }

    public function error404() {
        return view(404);
    }

    /**
     * @param $path
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     *
     * получает короткую ссылку, получает данные из базы если они есть, если нет -> 404
     * записывает в бд данные о пользователе перешедшем по ссылке
     * осуществляет перенаправление
     */
    public function redirector($path) {
        $url = Url::where('short_url', $path)->first();

        if (isset($url->url)) {
            $agent = new Agent();
            $data = json_decode(file_get_contents('http://free.ipwhois.io/json/' . Url::getIP()), true);

            $stat = new Stat();
            $stat->url_id       = $url->id;
            $stat->browser      = $agent->browser();
            $stat->br_version   = $agent->version($agent->browser());
            $stat->os           = $agent->platform();
            $stat->ip           = Url::getIP();
            $stat->city         = $data['city'];
            $stat->country      = $data['country'];
            $stat->clicked_at   = date_format(date_create(now()), 'F Y');
            $stat->save();

            if (Str::is('ftp://*', $url->url) == true) {
                return redirect()->away($url->url);
            }
            else {
                return redirect()->away('https://' . $url->url);
            }
        }
        else {
            return redirect('/404');
        }
    }

    /**
     * @param $path
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * выводит информацию о хранящейся в бд ссылке
     */
    public function dashboard($path) {
        $link = Auth::user()->url()->where('short_url', $path)->first();

        return view('dashboard', [
            'id'            => $link['id'],
            'url'           => $link['url'],
            'short_url'     => $link['short_url'],
            'created_at'    => $link['created_at'],
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     *
     * получает и передает данные для графика отображающего клики по датам в StatisticsComponent
     */
    public function data1(Request $request) {
        $count = Stat::where('url_id', $request->id)
            ->count();

        $dates = DB::table('statistics')
            ->select(['clicked_at'])
            ->where('url_id', '=', $request->id)
            ->distinct()
            ->get();

        $clicks = [];
        $labels = [];

        foreach ($dates as $date) {
            $data = DB::table('statistics')
                ->select('clicked_at')
                ->where([
                    ['url_id', '=', $request->id],
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

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     *
     * получает и передает данные для графика кликов по городам в StatisticsComponent
     */
    public function data2(Request $request) {
        $locations = DB::table('statistics')
            ->select(['country', 'city'])
            ->where('url_id', '=', $request->id)
            ->distinct()
            ->get();

        $clicks = [];
        $labels = [];

        foreach ($locations as $location) {
            $data = DB::table('statistics')
                ->select(['country', 'city'])
                ->where([
                    ['url_id', '=', $request->id],
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

    public function deleteUrl($id) {
        $url = Url::findOrFail($id);
        $stat = Stat::where('url_id', $id)->delete();
        $url->delete();

        return '';
    }
}
