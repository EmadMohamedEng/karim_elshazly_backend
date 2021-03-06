<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\RouteModel ;
use App\Role ;
use DB;
use App\RoleRoute ; 
use Validator;
class RouteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */ 
    
 
    public function index()
    { 
        $routes = RouteModel::all() ;  
        return view('route.index',compact('routes')) ; 
    }
 
    public function buildroutes()
    {
        $roles = Role::all();
        $role_routes = RoleRoute::all();
        $routegroupArray = array(); // Routes which have auth ..
        $unauthgroup = array(); // Routes which haven't auth ..
        $roleroutes = array();
        $unauthroutes = DB::table('routes')->whereNotIn('id', function($q){
        $q->select('route_id')->from('role_route');
        })->get();
        $routes = DB::table('routes')
            ->join('role_route', 'routes.id', '=', 'role_route.route_id')
            ->join('roles','role_route.role_id','=','roles.id')
            ->select('routes.*','role_route.*','roles.*')
            ->get();
         $total = "";
         $isfound = false ;
         $count = 0 ;
        
         //Grouping Roles Based On Routes .. 
         foreach($routes as $route => $items)
         { 
            $count = 0 ; 
            foreach($routes as $sroute => $items2)
            {
               if($items2->route_id == $items->route_id)
               {
                   $count = $count + 1 ;
                   if($count > 1)
                   {
                        $total = "|".$items2->name;
                        $items->name .= $total;
                        unset($routes[$sroute]);
                   }

               }
             
             }
         }
        
        //Getting All Roles On Array For Writing .. 
        foreach($routes as $route)
        {
            $roleroutes[] = $route->name;
        }
        
        $roleroutes = array_unique($roleroutes); 
        $singleq = "'";
        $myfile = fopen("routes.php", "w");
        $starttext = "<?php \n /*Generated Route File @iVAS*/ \n Mail : karim.ahmed@ivas.com.eg \n \n/*
|--------------------------------------------------------------------------

| Application Routes

|--------------------------------------------------------------------------

|

| Here is where you can register all of the routes for an application.

| It's a breeze. Simply tell Laravel the URIs it should respond to

| and give it the controller to call when that URI is requested.

|
*/\n \n";
        fwrite($myfile, $starttext);

        foreach($roleroutes as $roleroute)
        {
            $authtext = 'Route::group(['.$singleq.'middleware'.$singleq.'=>['.$singleq.'auth'.$singleq.','.$singleq.'role:'.$roleroute.$singleq.']],'.' function () {';
            fwrite($myfile, $authtext);
            $newline = "\n";
            fwrite($myfile, $newline);
            
            foreach($routes as $route)
            {
                if($roleroute == $route->name)
                {
                                   $conroute = 'Route::'.$route->method.'('.$singleq.$route->route.$singleq.','.$singleq.$route->controller_name.'@'.$route->function_name.$singleq.');';
                fwrite($myfile, $conroute."\n"); 
                }

            }
        $authtext = '});';
        fwrite($myfile, $authtext."\n \n");
        
        }
           
        //For unauth Routes 
        foreach($unauthroutes as $unauthroute)
        {
            $conroute = 'Route::'.$unauthroute->method.'('.$singleq.$unauthroute->route.$singleq.','.$singleq.$unauthroute->controller_name.'@'.$unauthroute->function_name.$singleq.');';
            $unauthgroup[] = $conroute;
        }
        
        if(count($unauthgroup) > 0)
        {
            $newline = "\n";
            fwrite($myfile, $newline);
        
            for($i=0;$i<count($unauthgroup);$i++)
            {
                $text = $unauthgroup[$i];
                fwrite($myfile, $text."\n");
            }
        }
            fclose($myfile);
            if("routes.php")
            {
              header('Content-disposition: attachment; filename=routes.php');
              header('Content-type: application/php');
              readfile('routes.php');
            }
        
    }

    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $roles = Role::all() ; 
        $route = null ; 
        $controllers = $this->get_controllers() ; // in main controller  
        $methods = array() ;
        if(isset($request['role']))
        {
            $id = $request['role']; 
            $query = "SELECT * FROM routes JOIN role_route ON routes.id = role_route.route_id JOIN roles ON role_route.role_id = roles.id WHERE roles.id = $id ORDER BY routes.controller_name" ; // order by here to sort them as the file system sorting
            $methods = \DB::select($query) ;       
        } 
        return view('route.create',compact('roles','route','controllers','methods')) ;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'method' => 'required|unique_with:routes,method,route',
            'route' => 'required|unique_with:routes,method,route',
            'controller_name' => 'required' , 
            'function_name' => 'required'
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $route['method'] = $request['method'] ;
        $route['route'] = $request['route'] ; 
        $route['controller_name'] = $request['controller_name'] ;
        $route['function_name'] = $request['function_name'] ; 
        $added = RouteModel::create($route) ;
        if(isset($request['role']))
        {
            foreach($request['role'] as $item)
            {
                $role_route['role_id'] = $item ;
                $role_route['route_id'] = $added->id ; 
                RoleRoute::create($role_route) ; 
            }       
        }
 
        \Session::flash('success',\Lang::get('messages.custom-messages.created'));
        return redirect('routes') ;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $roles = Role::all() ; 
        $route = RouteModel::findOrFail($id) ; 
        $controllers = $this->get_controllers() ;
        return view('route.create',compact('roles','route','controllers')) ;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'method' => 'required|unique_with:routes,method,route,'.$id,
            'route' => 'required|unique_with:routes,method,route,'.$id,
            'controller_name' => 'required' , 
            'function_name' => 'required'
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $old = RouteModel::findOrFail($id) ; 
        $old['method'] = $request['method'] ;
        $old['route'] = $request['route'] ; 
        $old['controller_name'] = $request['controller_name'] ;
        $old['function_name'] =  $request['function_name'] ; 
        $old->save()  ; 
        
        RoleRoute::where('route_id',$id)->delete() ; 
        if(isset($request['role']))
        {
            foreach($request['role'] as $item)
            {
                $role_route['role_id'] = $item ;
                $role_route['route_id'] = $id ; 
                RoleRoute::create($role_route) ; 
            }            
        } 
        \Session::flash('success',\Lang::get('messages.custom-messages.updated'));
        return redirect('routes') ;        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        RouteModel::destroy($id) ;
        \Session::flash('success',\Lang::get('messages.custom-messages.deleted'));
        return redirect('routes') ;        
    }


    public function index_v2(Request $request)
    {
        $controller_name = $request['controller_name'] ;  
        $selected_routes = RouteModel::where('controller_name',$controller_name)->get() ; 
        $methods = $this->get_controllers()[$controller_name] ;
        $roles = Role::all() ;  
        $method_types = $this->form_methods ; 
        return view('route.index_v2',compact('selected_routes','method_types','methods','controller_name','roles')) ;
    }

    public function create_v2(Request $request)
    {
        
        $controllers = $this->get_controllers() ; // in main controller  
    
        return view('route.create_v2',compact('controllers')) ; 
    }

    public function get_methods_for_selected_controller(Request $request)
    {
        $controller = $request['controller'] ; 
        $methods = $this->get_controllers()[$controller] ; 
        return response()->json(["methods"=>$methods]) ; 
    }

    public function store_v2(Request $request)
    {
        $route_size = count($request['route']);   
        for($i = 0 ; $i < $route_size ; $i++)
        {
            if(isset($request['route'][$i][0]) && ! empty($request['route'][$i][0]) 
                && isset($request['route'][$i][1]) && ! empty($request['route'][$i][1])
                && isset($request['route'][$i][2]) && ! empty($request['route'][$i][2]))
            {
                $route['function_name'] = $request['route'][$i][0] ;
                $route['route'] = $request['route'][$i][1] ; 
                $route['method'] = $request['route'][$i][2] ;
                $route['controller_name'] = $request['controller_name'] ;

                $check_route = RouteModel::where('controller_name',$route['controller_name'])
                ->where('function_name',$route['function_name'])
                ->first() ; 

                $checker = false ; 
                if($check_route)
                {
                    $check_route->update($route) ; 
                    $checker = true ; 
                }
                else {
                    $check_route = RouteModel::create($route) ; 
                }
                
                $IDs = [] ; 

                for($j = 3 ; $j < $route_size ; $j++ )
                {
                    if(isset($request['route'][$i][$j]))
                    {
                        $holder = 
                        [
                            'role_id'  => $request['route'][$i][$j],
                            'route_id' => $check_route->id
                        ]  ;
                        array_push($IDs,$holder) ; 
                    }
                }
                $check_route->roles()->sync($IDs) ; 
            }
        }
        $request->session()->flash('success',"Route Added Successfully");
        return back() ; 
    }

}
