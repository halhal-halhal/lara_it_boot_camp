<?php

namespace App\Http\Middleware;

use App\Http\Logics\UserLogic;
use App\Http\Logics\AuthLogic;
use Closure;
use Session;
use Redirect;

class AuthMypage
{
        private $userLogic;
        private $authLogic;

        /**
         * コンストラクタ
         *
         */
        public function __construct()
        {
            $this->userLogic = new UserLogic();
            $this->authLogic = new AuthLogic();
        }
    /**
     * ログインが必要なページヘの非ログインユーザのアクセスを拒否
     *
     */
    public function handle($request, Closure $next)
    {
        // ログイン確認
        if (Session::has("user_id") !== true) {
            // ログイン後に元のページが表示されるように保持
            if (isset($_SERVER["REQUEST_URI"]) && !empty($_SERVER["REQUEST_URI"])) {
                Session::set("route_request_uri", $_SERVER["REQUEST_URI"]);
            } else {
                Session::set("route_request_uri", "/");
            }

            return Redirect::to("/logout");
        }
        // ログイン時間を記録
        $this->userLogic->insertLoginTime(Session::get("user_id"));
        return $next($request);
    }
}
