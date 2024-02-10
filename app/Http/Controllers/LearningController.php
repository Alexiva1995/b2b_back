<?php

namespace App\Http\Controllers;

use App\Models\CategoryLearning;
use App\Models\Learning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Document;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LearningController extends Controller
{
    public function learnings() {

        $learning = Learning::orderBy('id', 'DESC')->get();
        return response()->json($learning, 200);
    }
    public function learningsType($type, $category) {

        $learning = CategoryLearning::where('name', $category)->first()->$type()->orderBy('id', 'DESC')->get();;
        return response()->json($learning, 200);
    }
    public function deleteLearning (Request $request) {
        try {
            $learning = Learning::find($request->id);
            $learning->delete();
            return response()->json($learning, 200);
        } catch (\Throwable $th) {
            return response()->json($learning, 400);
        }
    }
    // public function editLearning (Request $request) {
    //     $rules = [
    //         'id' => 'required',
    //         'title' => 'required',
    //         'description' => 'required'
    //     ];

    //     $message = [
    //         'title.required' => 'The title is required and must be a pdf file',
    //         'description.required' => 'The description is required and must be a pdf file',
    //     ];
    //     $validator = Validator::make($request->all(), $rules, $message);
    //     try {
    //         $learning = Learning::find($request->id);
    //         $learning->title = $request->title;
    //         $learning->description = $request->description;

    //         return response()->json($learning, 200);
    //     } catch (\Throwable $th) {
    //         return response()->json($learning, 400);
    //     }
    // }
    public function videos () {
        $learning = Learning::where('type', '1')->get();
        return response()->json($learning, 200);
    }
    public function links () {
        $learning = Learning::where('type', '2')->get();
        return response()->json($learning, 200);
    }
    public function documents () {
        $learning = Learning::where('type', '0')->get();
        return response()->json($learning, 200);
    }
    public function documentStore(Request $request) {
        $rules = [
            'document' => 'required|mimes:pdf|max:512000',
            'title' => 'required',
           // 'description' => 'required'
        ];

        $message = [
            'document.required' => 'The document is required',
            'title.required' => 'The title is required and must be a pdf file',
            'description.required' => 'The description is required and must be a pdf file',
        ];

        $validator = Validator::make($request->all(), $rules, $message);
        if( $validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $document = new Learning();
        $document->title = $request->title;
        $document->description = $request->description;

        if(!is_null($request->preview)){
            $file2 = $request->file('preview');
            $name2 = str_replace(" ", "_", $file2->getClientOriginalName());
            $file2->move(public_path('storage/documents/preview'), $name2);
            $document->preview  = 'storage/documents/preview/'.$name2;
        }

        $file = $request->file('document');
        $name = str_replace(" ", "_", $file->getClientOriginalName());
        $file->move(public_path('storage/documents/'), $name);
        $document->file_name = $name;
        $document->path = 'storage/documents/'.$name;
        $document->type = 0;
        $document->category_learning_id = CategoryLearning::where('name', $request->category)->first()->id;
        $document->save();

        return response()->json(['message' => 'Document registered successfully'], 200);
    }
    public function videoStore(Request $request) {

        $rules = [
            'video' => 'required',
            'title' => 'required',
            'description' => 'required'
        ];

        $message = [
            'video.required' => 'The video is required',
            'title.required' => 'the title is required and must be a pdf file',
            'description.required' => 'The description is required and must be a pdf file',
        ];

        $validator = Validator::make($request->all(), $rules, $message);
        if( $validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $link = explode('/', $request->video);
        $document = new Learning();
        $document->title = $request->title;
        $document->description = $request->description;

        if(!is_null($request->preview)){
            $file2 = $request->file('preview');
            $name2 = str_replace(" ", "_", $file2->getClientOriginalName());
            $file2->move(public_path('storage/video/preview'), $name2);
            $document->preview  = 'storage/video/preview/'.$name2;
        }
        $document->file_name = $request->title;
        $document->path = "$link[3]?h=$link[4]";
        $document->type = 1;
        $document->category_learning_id = CategoryLearning::where('name', $request->category)->first()->id;
        $document->save();

        return response()->json(['message' => 'Video registered successfully'], 200);
    }
    public function linkStore(Request $request) {
        $rules = [
            'link' => 'required',
            'title' => 'required',
            'description' => 'required'
        ];

        $message = [
            'link.required' => 'The link is required',
            'title.required' => 'the title is required and must be a pdf file',
            'description.required' => 'The description is required and must be a pdf file',
        ];

        $validator = Validator::make($request->all(), $rules, $message);
        if( $validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $document = new Learning();
        $document->title = $request->title;
        $document->description = $request->description;
        $document->file_name = $request->link;
        $document->path =  $request->link;
        $document->type = 2;
        $document->category_learning_id = CategoryLearning::where('name', $request->category)->first()->id;
        $document->save();
        return response()->json(['message' => 'Link registered successfully'], 200);
    }
    public function download(Request $request)
    {
        try {
            $document = Learning::findOrFail($request->id);
            $path = public_path($document->path);
            return  response()->download($path, $document->file_name);

        } catch (\Throwable $th) {
            return back()->with('warning', 'The file you want to download was not found');
        }
    }

    public function getTopBest()
    {
        $categories = Learning::where([['is_top', 1], ['type', '!=', 1]])->get();
        return response()->json($categories);
    }

    public function updateLearning(Request $request)
    {
        try {

            $learningReq = json_decode($request->learning);
            $learning = Learning::find($learningReq->id);
            $learning->title = $learningReq->title;
            $learning->description = $learningReq->description;
            $type = $this->getType($learning->type);

            if (!is_null($request->preview)) {
                $file2 = $request->file('preview');
                $name2 = str_replace(" ", "_", $file2->getClientOriginalName());
                $file2->move(public_path("storage/$type/preview"), $name2);
                $learning->preview  = "storage/$type/preview/" . $name2;
            }
            if(!is_null($request->file('document'))){
                $file = $request->file('document');
                $name = str_replace(" ", "_", $file->getClientOriginalName());
                $file->move(public_path("storage/$type/"), $name);
                $learning->file_name = $name;
                $learning->path = "storage/$type/" . $name;
            }
            if(!is_null($request->link)){
                $learning->file_name = $request->link;
                $learning->path =  $request->link;
            }

            if ($learning->save()) {
                DB::commit();
                $type = ucwords($type);

                return response()->json(['message' => "$type Update successful"]);
            }

            throw new Exception("Error update ucwords($type)");
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }

    private function getType($type)
    {
        switch ($type) {
            case 0:
                return 'documents';
                break;
            case  1 :
                return 'video';
                break;
            case '2':
                return 'links';
                break;
        }
    }

    public function changeTop($id)
    {
        $learning = Learning::find($id);
        if ($learning->is_top == 1) {
            $learning->is_top = 0;
            $learning->date_top = null;
            $learning->save();
            return  response()->json(['status' => 'success', 'message' => 'Learning changed in highlights']);
        }
        if ($learning->is_top == 0) {
            $date = CarbonImmutable::today();
            $count = Learning::where([['is_top', 1]])->whereBetween('date_top', [$date->startOfMonth(), $date])->count();
            if ($count >= 7) return response()->json(['status' => 'error', 'message' => 'Has reached the maximum number of highlights']);
            $learning->is_top = 1;
            $learning->date_top = Carbon::now();
            $this->deleteTop();
            $learning->save();
            return  response()->json(['status' => 'success', 'message' => 'Learning changed in highlights']);
        }
    }


    private function deleteTop()
    {
        $date = CarbonImmutable::today();
        $start_date = $date->startOfMonth()->subMonth();
        $end_date = $date->subMonth()->lastOfMonth();
        $categories = CategoryLearning::where([['is_top', 1]])->whereBetween('date_top', [$start_date, $end_date])->orderBy('date_top', 'ASC')->get();
        if ($categories->count() > 0) {
            $category = $categories->first();
            $category->is_top = 0;
            $category->date_top = null;
            $category->save();
        }
    }
}
