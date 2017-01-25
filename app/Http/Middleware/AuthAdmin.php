<?php

namespace App\Http\Middleware;

use App\Http\Logics\PrivilegeLogic;
use App\Http\Models\UserModel;
use Closure;
use Session;
use Redirect;
use Request;
use DB;


class AuthAdmin
{
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

        // 権限確認
        $user_id = Session::get("user_id");
        //$privilegeLogic = new PrivilegeLogic;
        //$role_id = $privilegeLogic->getData($user_id);

        $role_id = DB::table('user')->where('user_id', [$user_id])->value('user_role');

        if ((int)$role_id > UserModel::ROLE_STAFF) {
            return Redirect::to("/mypage");
        }
        return $next($request);
    }
}
