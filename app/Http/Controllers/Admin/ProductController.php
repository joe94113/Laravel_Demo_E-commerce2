<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Imports\ProductsImport;
use App\Notifications\ProductDelivery;
use InvalidArgumentException;
use Maatwebsite\Excel\Facades\Excel;
use phpDocumentor\Reflection\PseudoTypes\True_;

class Productcontroller extends Controller
{
    public function index(Request $request)
    {
        $productCount = Product::count();  // 訂單總數
        $dataPerPage = 2;
        $productPages = ceil($productCount / $dataPerPage);
        $currentPage = isset($request->all()['page']) ? $request->all()['page'] : 1;
        $products = Product::orderBy('id', 'asc')
                        ->offset($dataPerPage * ($currentPage - 1))
                        ->limit($dataPerPage)
                        ->get();
        return view('admin.products.index', ['products' => $products,
                                            'productCount' => $productCount,
                                            'productPages' => $productPages]);                             
    }

    public function uploadImage(Request $request)
    {
        $file = $request->file('product_image');
        $productId = $request->input('product_id');
        if (is_null($productId)) {
            return redirect()->back()->withErrors(['msg' => '參數錯誤']); // 返回上一頁
        }
        $product = Product::find($productId);

        try {
             $path = $file->store('public/images');
        } catch (InvalidArgumentException $error) {
            return redirect()->back()->withErrors(['msg' => '檔案太大']);
        }

        $product->images()->create([
            'filename' => $file->getClientOriginalName(), // 取得上傳檔案原始名稱
            'path' => $path,
        ]);
        return redirect()->back();
    }

    public function import(Request $request)
    {
        $file = $request->file('excel');
        Excel::import(new ProductsImport, $file);

        return redirect()->back();
    }
}
