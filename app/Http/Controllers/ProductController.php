<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Http\Services\ShortUrlService;
use Error;
use Mockery\Expectation;

class ProductController extends Controller
{
    public function __construct(ShortUrlService $shortUrlService)
    {
        $this->shortUrlService = $shortUrlService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        throw new Error('123');
        // $data = DB::table("products")->get();
        $data = json_decode(Redis::get('products'));
        return response($data);
    }
    
    public function checkProduct(Request $request)
    {
        $id = $request->all()['id']; // 找到前端ID
        $product = Product::find($id);
        if ($product->quantity > 0){
            return response(true);
        } else {
            return response(false);
        }
    }

    public function sheredUrl($id)
    {
        // $service = new ShortUrlService();
        $url = $this->shortUrlService->makeShortUrl("http://localhost:3000/products/$id");
        return response(['url' => $url]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
        $data = $this->getdata();
        $newdata = $request->all();
        $data->push(collect($newdata));
        return response($data);
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
        //
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
        $data = $this->getdata();
        $form = $request->all();
        $selectdata = $data->where('id', $id)->first();
        $selectdata = $selectdata->merge(collect($form));
        return response($selectdata);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = $this->getdata();
        $data = $data->filter(function($product) use ($id){
            return $product['id'] != $id;
        });
        return response($data->values());
    }

    public function getdata()
    {
        return collect([
            collect([ "id" => 0,
                "title" => "test1",
                "price" => 1000]),
            collect([   "id" => 1,
                "title" => "test2",
            "price" => 2000]) 
        ]);
    }
}
