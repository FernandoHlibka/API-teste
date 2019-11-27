<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Routing\Response;

class RouteController extends Controller
{
    const validRoutes = [
        'A',
        'B',
        'C',
        'D',
        'E'
    ];
    
    protected $routes = [
        'AB' => 10,
        'BD' => 15,
        'AC' => 20,
        'CD' => 30,
        'BE' => 50,
        'DE' => 30,
    ];

    protected $error;

    protected $a;

    protected $b;

    protected $gas;

    protected $con;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->error = [
            'status' => '400',
            'msg' => 'Parametros informados incorretamente'
        ];
        $this->a = '';
        $this->b = '';
        $this->gas = 0;
        $this->con = 1;
        $this->routes;
    }

    public function getRoutes()
    {
        return response()->json([ 'routes' => self::validRoutes], 200);
    }

    // Get params and calc best route
    public function bestRoute($routeA, $routeB, $cons, $gasPrice)
    {

        $this->a = strtoupper($routeA);

        $this->b = strtoupper($routeB);

        $validA = in_array($this->a, self::validRoutes);

        $validB = in_array($this->b, self::validRoutes);

        if($validA && $validB && isset($cons, $gasPrice))
        {

            $this->gas = (float)str_replace(':', '.', $gasPrice);

            $this->con = (float)str_replace(':', '.', $cons);
         
            $this->getInverseRoutes();

            $route = $this->calcRoute();

            return response()->json($route, 200);

        }

        $this->setError($validB, $validA);

        return response()->json($this->error, 404);
    }

    // inverse the routes
    private function getInverseRoutes()
    {
        
        foreach($this->routes as $key => $value)
        {
            $this->routes[strrev($key)] = $value;
        }

        return $this->routes;
    }

    
    // get route calc

    private function calcRoute()
    {

        if($this->a === $this->b)
        {
            return $this->getResponseJson(0, '|');
        }

        $pre = $this->isPreDefinedRoute();

        if(!$pre)
        {

            $this->bestA = array_keys(array_filter($this->routes, function($key) {
                return preg_match("/{$this->a}/", $key);
            }, ARRAY_FILTER_USE_KEY));
            
            $this->keysA = str_replace($this->a, $this->b, $this->bestA);
            
            $bestB = array_filter(array_values($this->keysA), function($key) {
                return array_key_exists($this->keysA[$key], $this->routes) ?? null;
            }, ARRAY_FILTER_USE_KEY);
    
            $rtA = $this->keysA[array_key_first($this->keysA)];
    
            if(empty($bestB))
            {
                
                $km = (($this->routes[$rtA]
                + $this->routes[$this->bestA[array_key_first($this->bestA)]]) 
                / $this->con) 
                * $this->gas;
    
                $this->getResponseJson($km, $rtA);
            }
    
            $keyB = str_replace($this->b, $this->a, $bestB);
    
            $rtB = $keyB[array_key_first($keyB)];
            
            $km = (($this->routes[$rtB]
            + $this->routes[$bestB[array_key_first($bestB)]]) / $this->con) 
            * $this->gas;

            return $this->getResponseJson($km, $rtB . $rtA);
        } 

        return $pre;

    }

    private function isPreDefinedRoute()
    {
        // verify if route A is pre-defined
        if(array_key_exists($this->a . $this->b, $this->routes))
        {
            $km = ($this->routes[$this->a . $this->b] / $this->con) * $this->gas;

            return $this->getResponseJson($km, $this->a . ' ' . $this->b, true);
        }
        // verify if route B is pre-defined
        else if(array_key_exists($this->b . $this->a, $this->routes)) 
        {
            $km = ($this->routes[$this->b . $this->a] / $this->con) * $this->gas;

            return $this->getResponseJson($km, $this->b . ' ' . $this->a, true);
        }

        return false;
    }

    // if have error
    private function setError($a, $b)
    {   
        if($a)
        {
            $routeError = 'Ponto de partida invalido.';
        }
        else if($b)
        {
            $routeError = 'Destino invalido.';
        }
        else 
        {
            $routeError = 'Partida e destino invalidos.';
        }

        $this->error['status'] = 404;
        $this->error['msg'] = $routeError;
    }

    // get best route response
    private function getResponseJson($cost, $route, $pre = false)
    {
        $formattedCost = number_format($cost, 2, ',', '.');
        if(!$pre)
        {
            $parsedRoute = substr(str_replace($this->b, '', str_replace($this->a, '', $route)), 0, 1);

            return [ 
                'route' => $this->a . ' ' . $parsedRoute . ' ' . $this->b,
                'cost' => $formattedCost
            ];

        }

        return [ 
            'route' => $route,
            'cost' => $formattedCost
        ];
    }

}
