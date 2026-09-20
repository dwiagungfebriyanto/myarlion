<?php

namespace App\Http\Controllers;

use App\DataTables\SpecificationsDataTable;
use App\Models\Main_Category;
use App\Models\Specification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SpecificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SpecificationsDataTable $dataTable)
    {
        return $dataTable->render('pages.product.specification.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'main_category' => Main_Category::all(),
        ];

        return view('pages.product.specification.modals.create-modal', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
        if (Specification::where('main_category_id', $request->main_category_id)->count() == 999) {
            return response()->json(['success' => false, 'message' => 'You have reached the maximum number of specification.']);
        }

        $rules = [
            'main_category_id'             => 'required|exists:\App\Models\Main_Category,id',
            'specification_name'           => 'required|string|max:255|unique:specifications,specification_name,NULL,id,main_category_id,' . $request->main_category_id,
        ];

        $request->validate($rules);

        $specification = new Specification;
        $specification->main_category_id = $request->main_category_id;
        $specification->specification_name = $request->specification_name;

        $specification_code =
            Specification::where('main_category_id', $request->main_category_id)
            ->orderBy('code', 'asc')
            ->get()
            ->toArray();
        $next_code = generateCode($specification_code,1);
        // if ($next_code == 0) {
        //     $next_code = 1;
        // }
        $next_code =
        str_pad($next_code, 3, '0', STR_PAD_LEFT);
        $specification->code = $next_code;

        $specification->save();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $specification = Specification::findOrFail($id);
        $main_catefory = Main_Category::findOrFail($specification->main_category_id);

        $data = [
            'main_category' => $main_catefory,
            'specification' => $specification,
        ];

        return view('pages.product.specification.modals.edit-modal', $data);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Specification $specification){

        $rules = [
            // 'main_category_id'             => 'required|exists:\App\Models\Main_Category,id',
            'specification_name'           => 'required|string|max:255|unique:specifications,specification_name,' . $specification->id . ',id,main_category_id,' . $request->main_category_id,
        ];

        $request->validate($rules);

        $specification->main_category_id = $request->main_category_id;
        $specification->specification_name = $request->specification_name;

        $specification->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id){
        //
    }
}
