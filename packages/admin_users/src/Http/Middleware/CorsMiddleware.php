<?php

namespace DreamTeam\AdminUser\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class CorsMiddleware
{
    protected $settings = array(
        'origin' => ['*', 'https://h5.zdn.vn'], // Chỉ định domain Mini Zalo App
        'allowMethods' => 'GET,HEAD,PUT,POST,DELETE,PATCH,OPTIONS',
        'allowHeaders' => '*',
        'allowCredentials' => true, // BẬT LÊN
    );

    protected function setOrigin($req, $rsp)
    {
        $origin = $req->header("Origin");

        if (in_array($origin, $this->settings['origin'])) {
            $rsp->headers->set('Access-Control-Allow-Origin', $origin);
        } else {
            $rsp->headers->set('Access-Control-Allow-Origin', $this->settings['origin'][0]);
        }
    }

    protected function setExposeHeaders($req, $rsp)
    {
        if (isset($this->settings['exposeHeaders'])) {
            $exposeHeaders = $this->settings['exposeHeaders'];
            if (is_array($exposeHeaders)) {
                $exposeHeaders = implode(", ", $exposeHeaders);
            }

            $rsp->header('Access-Control-Expose-Headers', $exposeHeaders);
        }
    }

    protected function setMaxAge($req, $rsp)
    {
        if (isset($this->settings['maxAge'])) {
            $rsp->header('Access-Control-Max-Age', $this->settings['maxAge']);
        }
    }

    protected function setAllowCredentials($req, $rsp)
    {
        if (isset($this->settings['allowCredentials']) && $this->settings['allowCredentials'] === True) {
            $rsp->header('Access-Control-Allow-Credentials', 'true');
        }
    }

    protected function setAllowMethods($req, $rsp)
    {
        if (isset($this->settings['allowMethods'])) {
            $allowMethods = $this->settings['allowMethods'];
            if (is_array($allowMethods)) {
                $allowMethods = implode(", ", $allowMethods);
            }

            $rsp->header('Access-Control-Allow-Methods', $allowMethods);
        }
    }

    protected function setAllowHeaders($req, $rsp)
    {
        if (isset($this->settings['allowHeaders'])) {
            $allowHeaders = $this->settings['allowHeaders'];
            if (is_array($allowHeaders)) {
                $allowHeaders = implode(", ", $allowHeaders);
            }
        } else {  // Otherwise, use request headers
            $allowHeaders = $req->header("Access-Control-Request-Headers");
        }
        if (isset($allowHeaders)) {
            $rsp->header('Access-Control-Allow-Headers', $allowHeaders);
        }
    }

    protected function setCorsHeaders($req, $rsp)
    {
        // http://www.html5rocks.com/static/images/cors_server_flowchart.png
        // Pre-flight
        if ($req->isMethod('OPTIONS')) {
            $this->setOrigin($req, $rsp);
            $this->setMaxAge($req, $rsp);
            $this->setAllowCredentials($req, $rsp);
            $this->setAllowMethods($req, $rsp);
            $this->setAllowHeaders($req, $rsp);
        } else {
            $this->setOrigin($req, $rsp);
            $this->setExposeHeaders($req, $rsp);
            $this->setAllowCredentials($req, $rsp);
        }
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->isMethod('OPTIONS')) {
            $response = new Response("", 200);
        } else {
            $response = $next($request);
        }
        $this->setCorsHeaders($request, $response);
        return $response;
    }
}
