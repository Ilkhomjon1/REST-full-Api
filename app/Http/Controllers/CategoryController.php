<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Providers\SlugSeviceProvider;
use App\Library\Services\CategorySlug;
use App\Models\Category;
use Validator;

class CategoryController extends Controller
{
    public function store(Request $request){
      $data = $request->all();
      $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'unique:categories'
        ]);

        if ($validator->fails()) {
          return response()->json(array(
                   'code'      =>  422,
                   'message'   =>  $validator->errors()
               ), 422);
        }

        $category = new Category;
        if (empty($data["slug"])) {
            $slug = new CategorySlug();
            $data["slug"] = $slug->createSlug($data["name"]);
        }
        if(empty($data["description"])){
          $data["description"] = null;
        }
        if(empty($data["keywords"])){
          $data["keywords"] = null;
        }
        $category->name = $data["name"];
        $category->description = $data["description"];
        $category->keywords = $data["keywords"];
        $category->slug = $data["slug"];
        $category->save();

        if($category){
          return response()->json($category, 201);
        }else{
          return response()->json(array(
                   'code'      =>  422,
                   'message'   =>  'Error'
               ), 422);
        }
    }


    public function index(){
      $categories = Category::all();
      return response()->json($categories, 200);
    }


    public function category($id){
      $category = Category::find($id);

      if($category!=null){
        return response()->json($category, 200);
      }else{
        return response()->json(array(
                 'code'      =>  404,
                 'message'   =>  'Нет данных'
             ), 404);
      }
    }


    public function update($id, Request $request){
      $data = $request->all();
      $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'unique:categories'
        ]);

        if ($validator->fails()) {
          return response()->json(array(
                   'code'      =>  422,
                   'message'   =>  $validator->errors()
               ), 422);
        }

        $category = Category::find($id)->update($data);

        if($category){
          return response()->json($category, 200);
        }else{
          return response()->json(array(
                   'code'      =>  422,
                   'message'   =>  'Error'
               ), 422);
        }
    }


    public function delete($id){
      $category = Category::with('products')->find($id);
      $result = false;
      if($category!=null){
        if(!$category->products()->exists()){
          $result = Category::find($id)->delete();
        }
      }


      if($result){
        return response()->json('Успешно удалено', 200);
      }else{
        if($category==null){
          return response()->json(array(
                   'code'      =>  404,
                   'message'   =>  'Не найдено соответствующего каталого'
               ), 404);
        }else{
          return response()->json(array(
                   'code'      =>  412,
                   'message'   =>  'Категория приклеплена товару'
               ), 412);
        }

      }
    }
}
