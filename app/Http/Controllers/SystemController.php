<?php

namespace App\Http\Controllers;

use App\DataTables\ActivityLogDataTable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SystemController extends Controller
{
    public function index()
    {
        return view('pages.system.index');
    }

    public function artisanCacheClear(Request $request)
    {
        // nilai 0 menandakan bahwa perintah berhasil dijalankan tanpa masalah
        if (Artisan::call('cache:clear') === 0) {
            $request->session()->flash('success', Artisan::output());
        } else {
            $request->session()->flash('danger', Artisan::output());
        }

        return redirect(route('system.index'));
    }

    public function log(ActivityLogDataTable $dataTable)
    {
        $data['causers'] = User::all();

        return $dataTable->render('pages.system.log', $data);
    }
}
