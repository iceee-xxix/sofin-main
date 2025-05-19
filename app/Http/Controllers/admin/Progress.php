<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Progresses;
use App\Models\ProgressesFile;
use App\Models\User;
use Illuminate\Http\Request;

class Progress extends Controller
{
    public function progress()
    {
        $data['function_key'] = __FUNCTION__;
        return view('progress.index', $data);
    }

    public function progresslistData()
    {
        $data = [
            'status' => false,
            'message' => '',
            'data' => []
        ];
        $table = User::where('role', 'user')->get();

        if (count($table) > 0) {
            $info = [];
            foreach ($table as $rs) {
                $action = '<a href="' . route('progressDetail', $rs->id) . '" class="btn btn-sm btn-outline-primary" title="รายงานผล"><i class="bx bx-message-rounded-dots"></i></a>';
                $info[] = [
                    'name' => $rs->name,
                    'email' => $rs->email,
                    'tel' => $rs->tel,
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

    public function progressDetail($id)
    {
        $data['function_key'] = 'progress';
        $data['id'] = $id;
        return view('progress.detail', $data);
    }

    public function progressDetaillistData(Request $request)
    {
        $users_id = $request->post('users_id');
        $data = [
            'status' => false,
            'message' => '',
            'data' => []
        ];
        $table = Progresses::where('users_id', $users_id)->get();

        if (count($table) > 0) {
            $info = [];
            foreach ($table as $rs) {
                $action = '<a href="' . route('progressDetailEdit', $rs->id) . '" class="btn btn-sm btn-outline-primary" title="รายงานผล"><i class="bx bx-edit-alt"></i></a>
                <button type="button" data-id="' . $rs->id . '" class="btn btn-sm btn-outline-danger deleteTable" title="ลบ"><i class="bx bxs-trash"></i></button>';
                $info[] = [
                    'link_url' => $rs->link_url,
                    'remark' => $rs->remark,
                    'date' => ($rs->date) ? $this->DateTimeThai($rs->date) : '',
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

    public function progressDetailCreate($id)
    {
        $data['function_key'] = 'progress';
        $data['id'] = $id;
        return view('progress.create', $data);
    }

    public function progressDetailEdit($id)
    {
        $function_key = 'progress';
        $info = Progresses::find($id);

        return view('progress.edit', compact('info', 'function_key'));
    }

    public function ProgressSave(Request $request)
    {
        $input = $request->input();
        if (!isset($input['id'])) {
            $table = new Progresses();
            $table->users_id = $input['users_id'];
            $table->link_url = $input['link_url'];
            $table->remark = $input['remark'];
            $table->date = $input['date'];
            if ($table->save()) {
                if ($request->hasFile('file')) {
                    $file = $request->file('file');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('image', $filename, 'public');

                    $file_table = new ProgressesFile();
                    $file_table->progresses_id = $table->id;
                    $file_table->file = $path;
                    $file_table->save();
                }
                return redirect()->route('progressDetail', $input['users_id'])->with('success', 'บันทึกรายการเรียบร้อยแล้ว');
            }
        } else {
            $table = Progresses::find($input['id']);
            $table->link_url = $input['link_url'];
            $table->remark = $input['remark'];
            $table->date = $input['date'];
            if ($table->save()) {
                if ($request->hasFile('file')) {
                    $file_table = ProgressesFile::where('progresses_id', $input['id'])->delete();

                    $file = $request->file('file');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('image', $filename, 'public');

                    $file_table = new ProgressesFile();
                    $file_table->progresses_id = $table->id;
                    $file_table->file = $path;
                    $file_table->save();
                }
                return redirect()->route('progressDetail', $table->users_id)->with('success', 'บันทึกรายการเรียบร้อยแล้ว');
            }
        }
        return redirect()->route('progress')->with('error', 'ไม่สามารถบันทึกข้อมูลได้');
    }

    public function ProgressDelete(Request $request)
    {
        $data = [
            'status' => false,
            'message' => 'ลบข้อมูลไม่สำเร็จ',
        ];
        $id = $request->input('id');
        if ($id) {
            $delete = Progresses::find($id);
            if ($delete->delete()) {
                $data = [
                    'status' => true,
                    'message' => 'ลบข้อมูลเรียบร้อยแล้ว',
                ];
            }
        }

        return response()->json($data);
    }

    function DateTimeThai($strDate)
    {
        $strYear = date("Y", strtotime($strDate)) + 543;
        $strMonth = date("n", strtotime($strDate));
        $strDay = date("j", strtotime($strDate));
        $strMonthCut = array("", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม");
        $strMonthThai = $strMonthCut[$strMonth];
        return "$strDay $strMonthThai $strYear";
    }
}
