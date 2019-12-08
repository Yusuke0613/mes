<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\ProductionExe;
use App\Http\Resources\ProductionExeResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Events\DashBordEvent;
use PDF;

class ProductionExeController extends Controller
{

     /**
     * Create a new AuthController instance.
     *
     * @return void
     */

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Respo
     * nse
     */
    public function index()
    {
        return ProductionExeResource::collection(ProductionExe::get());
    }

    public function listPdf() {
        
        $data = ProductionExeResource::collection(ProductionExe::get());
        $cont = ProductionExe::count();

        $pdf = PDF::loadView('pdf.articulospdf',['articulos'=>$data,'cont'=>$cont]);
        return $pdf->download('articulos.pdf');
        
      // $pdf = PDF::loadHTML('<h1>Hello World</h1>');

    	//return $pdf->stream();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $lineMaster = new ProductionExe();
        $lineMaster->orderCode     = $request->orderCode;
        $lineMaster->lineCode      = $request->lineCode;
        $lineMaster->ratio         = $request->ratio;
        $lineMaster->priorityFlag  = $request->priorityFlag;

        $lineMaster->save();
      
        return response($request, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $comentNum
     * @return \Illuminate\Http\Response
     */
    public function show($comentNum)
    {
        return  ProductionExe::find($comentNum);
    }


    /**
     * Display the specified resource.
     *
     * @param  string  $comentNum
     * @return \Illuminate\Http\Response
     */
    public function tag($request)
    {
        return  ProductionExe::find($request);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\ProcessMaster  $dashBordUser
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductionExe $productionExe)
    {
        
        DB::beginTransaction();
        //broadcast(new dashBordEvent($request->all()))->toOthers();
        if ($request->status == 2) {
            ProductionExe::where('id', $request->id)->update([
                'ID'      =>$request->id,
                'status'  =>$request->status,
                'startDate' =>date("Y/m/d H:i:s")
            ]);
        } 

        if ($request->status == 3) {
            ProductionExe::where('id', $request->id)->update([
                'ID'        =>$request->id,
                'status'    =>$request->status,
                'endDate' =>date("Y/m/d H:i:s")
            ]);
        } 

        if ($request->status != 2 || $request->status != 3) {
            ProductionExe::where('id', $request->id)->update([
                'ID'      =>$request->id,
                'status'  =>$request->status,
                
            ]);
        } 
      
        event(new dashBordEvent($request->all()));
        DB::commit();
        return response($request, Response::HTTP_ACCEPTED);
    }

  
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function convert_to_utf8_recursively($dat){
        if( is_string($dat) ){
           return mb_convert_encoding($dat, 'UTF-8', 'UTF-8');
        }
        elseif( is_array($dat) ){
           $ret = [];
           foreach($dat as $i => $d){
             $ret[$i] = convert_to_utf8_recursively($d);
           }
           return $ret;
        }
        else{
           return $dat;
        }
  }
}
