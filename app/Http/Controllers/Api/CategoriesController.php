<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\GeneralTrait;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    use GeneralTrait;

    public function __construct(){}

    public function index()
    {
        $data = Category::selection()->get();
        return $this->returnData($data);
    }

    public function getCategory(Request $request)
    {
        $data = Category::selection()->find($request->id);
        if (!$data)
            return $this->returnError('001', 'هذا العنصر غير موجود');
        return $this->returnData($data);
    }

    public function changeStatus(Request $request)
    {
        $data = Category::selection()->find($request->id);
        $data->update(['active'=>$request->active]);

        if (!$data)
            return $this->returnError('001', 'هذا العنصر غير موجود');
        return $this->returnSuccessMessage('تم تغيير الحالة بنجاح');
    }
}