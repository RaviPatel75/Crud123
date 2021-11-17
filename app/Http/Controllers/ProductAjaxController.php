<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use DataTables;

class ProductAjaxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $alphabet = array_merge(range('A', 'Z'));
        // dd($request->get('alpha_search'));

        if ($request->ajax()) {
            if ($request->get('alpha_search')!= "" )
            {
                $data = Product::select('*');    
            }
            else
            {
                $data = Product::latest()->get();
            }
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                           $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editProduct">Edit</a>';
                           $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteProduct">Delete</a>';
                            return $btn;
                    })
                    ->filter(function ($instance) use ($request){
                        if (!empty($request->get('alpha_search')))
                        {
                            
                            $instance->where(function($w) use($request){
                                $upper = $request->get('alpha_search');
                                $lower = strtolower($request->get('alpha_search'));
                                
                                $w->orWhere('name', 'LIKE', "$upper%")
                                ->orWhere('name', 'LIKE', "$lower%");
                            });
                        }
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('productAjax');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Product::updateOrCreate(['id' => $request->product_id],['name' => $request->name, 'detail' => $request->detail]);        
        return response()->json(['success'=>'Product saved successfully.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::find($id);
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Product::find($id)->delete();
        return response()->json(['success'=>'Product deleted successfully.']);
    }
}
