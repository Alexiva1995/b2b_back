<?php

namespace App\Http\Controllers;

use App\Models\CategoryLearning;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryLearningsController extends Controller
{
    public function getAll($type)
    {
        switch ($type) {
            case 'video':
                return CategoryLearning::where([['status', 1], ['type', CategoryLearning::VIDEO]])->get();
                break;
            case 'link':
                return CategoryLearning::where([['status', 1], ['type', CategoryLearning::LINK]])->get();
                break;
            case 'document':
                return CategoryLearning::where([['status', 1], ['type', CategoryLearning::DOCUMENT]])->get();
                break;
        }
    }

    public function get($id)
    {
        return CategoryLearning::find($id);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            switch ($request->type) {
                case 'video':
                  $type = CategoryLearning::VIDEO;
                    break;
                case 'link':
                    $type = CategoryLearning::LINK;
                    break;
                case 'document':
                    $type = CategoryLearning::DOCUMENT;
                    break;
            }
            $category = new CategoryLearning();

            if(!is_null($request->preview)){
                $file = $request->file('preview');
                $name = str_replace(" ", "_", $file->getClientOriginalName());
                $file->move(public_path('storage/categories/'), $name);
                $category->preview = 'storage/categories/'.$name;
            }


            $category->name = $request->name;
            $category->description = $request->description;
            $category->type = $type;

            if($category->save()){
                DB::commit();
                return response()->json(['message' => 'Category create successful']);
            }

            throw new Exception("Error create Category");


        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }

    public function  update(Request $request)
    {
        DB::beginTransaction();
        try {

            $category = CategoryLearning::find($request->id);

            $category->name = $request->name;
            $category->status = $request->status;

            if($category->save()){
                DB::commit();
                return response()->json(['message' => 'Category update successful']);
            }

            throw new Exception("Error update Category");


        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $category = CategoryLearning::find($id);


            if($category->delete()){
                DB::commit();
                return response()->json(['message' => 'Category Delete successful']);
            }

            throw new Exception("Error Delete Category");


        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }
}
