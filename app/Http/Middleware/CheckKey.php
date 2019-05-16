<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use \Illuminate\Database\QueryException;
class CheckKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
   //     return response()->json(array('status'=>'success'));
        try
        {
            $key = $request->headers->get('key');
            $secret_key='$2y$10$KPYNFfDXrD96EB2uQaHXEebRAEmsbD2ZphhyDjtRvmsGJ1lBPQzz2';
            if($secret_key!= $key)
            {   
                return response()->json(array('status'=>'failure','error'=>'Wrong Secret Key'));
            }
            return $next($request);
        }
        catch(QueryException $e)
        {
            return response()->json(array('status'=>'failure','detail'=>$e->getMessage()));
        }
        catch(Exception $e)
        {
            return response()->json(array('status'=>'failure','detail'=>$e->getMessage()));
        }
    }
}
