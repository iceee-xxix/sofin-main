<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use App\Models\Preview as ModelsPreview;
use App\Models\PreviewFile;
use Illuminate\Http\Request;

class Preview extends Controller
{
    public function preview()
    {
        $data['function_key'] = __FUNCTION__;
        return view('preview.index', $data);
    }

    public function previewlistData()
    {
        $data = [
            'status' => false,
            'message' => '',
            'data' => []
        ];
        $preview = ModelsPreview::with('category')->get();

        if (count($preview) > 0) {
            $info = [];
            foreach ($preview as $rs) {
                $action = '<a href="' . route('previewEdit', $rs->id) . '" class="btn btn-sm btn-outline-primary" title="แก้ไข"><i class="bx bx-edit-alt"></i></a>
                <button type="button" data-id="' . $rs->id . '" class="btn btn-sm btn-outline-danger deleteMenu" title="ลบ"><i class="bx bxs-trash"></i></button>';
                $info[] = [
                    'name' => $rs->name,
                    'category' => $rs['category']->name,
                    'detail' => $rs->detail,
                    'action' => $action
                ];
            }
            $data = [
                'data' => $info,
                'status' => true,
                'message' => 'success'
            ];
        }
        return response()->json($data);
    }

    public function previewCreate()
    {
        $data['function_key'] = 'preview';
        $data['category'] = Categories::get();
        return view('preview.create', $data);
    }

    public function previewEdit($id)
    {
        $function_key = 'menu';
        $info = ModelsPreview::with('category')->find($id);
        $category = Categories::get();

        return view('preview.edit', compact('info', 'function_key', 'category'));
    }

    public function previewDelete(Request $request)
    {
        $data = [
            'status' => false,
            'message' => 'ลบข้อมูลไม่สำเร็จ',
        ];
        $id = $request->input('id');
        if ($id) {
            $delete = ModelsPreview::find($id);
            if ($delete->delete()) {
                $data = [
                    'status' => true,
                    'message' => 'ลบข้อมูลเรียบร้อยแล้ว',
                ];
            }
        }

        return response()->json($data);
    }

    public function previewSave(Request $request)
    {
        $input = $request->input();
        if (!isset($input['id'])) {
            $preview = new ModelsPreview();
            $preview->name = $input['name'];
            $preview->categories_id = $input['categories_id'];
            $preview->detail = $input['detail'];
            $preview->link_attachments = $input['link_attachments'];
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('image', $filename, 'public');
                $preview->image = $path;
            }
            if ($preview->save()) {
                if ($request->hasFile('file')) {
                    foreach ($request->file('file') as $file) {
                        $filename = time() . '_' . $file->getClientOriginalName();
                        $path = $file->storeAs('image', $filename, 'public');

                        $preview_file = new PreviewFile();
                        $preview_file->preview_id = $preview->id;
                        $preview_file->file = $path;
                        $preview_file->save();
                    }
                }
                return redirect()->route('preview')->with('success', 'บันทึกรายการเรียบร้อยแล้ว');
            }
        } else {
            $preview = ModelsPreview::find($input['id']);
            $preview->name = $input['name'];
            $preview->categories_id = $input['categories_id'];
            $preview->detail = $input['detail'];
            $preview->link_attachments = $input['link_attachments'];
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('image', $filename, 'public');
                $preview->image = $path;
            }
            if ($preview->save()) {
                if ($request->hasFile('file')) {
                    foreach ($request->file('file') as $file) {
                        $filename = time() . '_' . $file->getClientOriginalName();
                        $path = $file->storeAs('image', $filename, 'public');

                        $preview_file = new PreviewFile();
                        $preview_file->preview_id = $preview->id;
                        $preview_file->file = $path;
                        $preview_file->save();
                    }
                }
                return redirect()->route('preview')->with('success', 'บันทึกรายการเรียบร้อยแล้ว');
            }
        }
        return redirect()->route('preview')->with('error', 'ไม่สามารถบันทึกข้อมูลได้');
    }
}
