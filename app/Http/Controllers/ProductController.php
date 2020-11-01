<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Providers\SlugSeviceProvider;
use App\Library\Services\ProductSlug;
use App\Models\Product;
use App\Models\Category;
use Validator;

class ProductController extends Controller
{
  public function store(Request $request){
    $data = $request->all();
    $validator = Validator::make($request->all(), [
          'name'        => 'required|min:2',
          'slug'        => 'unique:products',
          'category_id' => 'required|array|min:2|max:10',
          'price'       => "required",
          'status'      => "required"
      ]);

      if ($validator->fails()) {
        return response()->json(array(
                 'code'      =>  422,
                 'message'   =>  $validator->errors()
             ), 422);
      }

      $product = new Product;
      if (empty($data["slug"])) {
          $slug = new ProductSlug();
          $data["slug"] = $slug->createSlug($data["name"]);
      }
      if(empty($data["description"])){
        $data["description"] = null;
      }
      if(empty($data["keywords"])){
        $data["keywords"] = null;
      }
      $product->name = $data["name"];
      $product->description = $data["description"];
      $product->keywords = $data["keywords"];
      $product->price = $data["price"];
      $product->slug = $data["slug"];
      $product->save();


      foreach($data["category_id"] as $array){
          $product->categories()->attach($array);
      }

      if($product){
        return response()->json($product, 201);
      }else{
        return response()->json(array(
                 'code'      =>  422,
                 'message'   =>  'Error'
             ), 422);
      }
  }


  public function index(){
    $products = Product::with('categories')->get();

    return response()->json($products, 200);
  }


  public function product($id){
    $product = Product::with('categories')->find($id);

    if($product==null){
    return response()->json(array(
             'code'      =>  404,
             'message'   =>  'Нет данных'
         ), 404);
    }else{
      return response()->json($product, 200);
    }
  }


  public function update($id, Request $request){
    $data = $request->all();
    $validator = Validator::make($request->all(), [
          'name'        => 'required|min:2',
          'slug'        => 'unique:products',
          'category_id' => 'required|array|min:2|max:10',
          'price'       => "required",
          'status'      => "required"
      ]);

      if ($validator->fails()) {
        return response()->json(array(
                 'code'      =>  422,
                 'message'   =>  $validator->errors()
             ), 422);
      }
    Product::find($id)->update($data);
    $product = Product::find($id);
    $product->categories()->detach();

    foreach ($data["category_id"] as $category_id) {
      $product->categories()->attach($category_id);
    }

    if($product){
      return response()->json($product, 200);
    }else{
      return response()->json(array(
               'code'      =>  422,
               'message'   =>  'Error'
           ), 422);
    }
  }


  public function search(Request $request){
    if(isset($request->category_id)){
      $products = Product::whereHas('categories' ,   function ($query) {
                                $query->whereIn('category_id', [3]);
                            });
    }else{
      $products = Product::where('created_at', '!=', null);
    }
    if(isset($request->name)){
      $products = $products->where('name', 'LIKE', "%{$request->name}%");
    }
    if(isset($request->price_from)){
      $products = $products->where('price', '>=', $request->price_from);
    }
    if(isset($request->price_to)){
      $products = $products->where('price', '<=', $request->price_to);
    }
    if(isset($request->status)){
      $products = $products->where('status', $request->status);
    }


    $products = $products->with('categories')->get()->all();
    if($products!=null){
      return response()->json($products, 200);
    }else{
      return response()->json(array(
               'code'      =>  404,
               'message'   =>  'Не найдено сооьветствующих товаров'
           ), 404);
    }
  }


  public function delete($id){
    $product = Product::find($id);
    $result = false;
    if($product){
      $product->categories()->detach();
      $result = $product->delete();
    }
    if($result){
      return response()->json('Успешно удалено', 200);
    }else{
      return response()->json(array(
               'code'      =>  404,
               'message'   =>  'Не найдено соответствующего товара'
           ), 404);
    }
  }



}
