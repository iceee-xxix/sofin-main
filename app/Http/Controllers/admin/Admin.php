<?php

namespace App\Http\Controllers\admin;

use App\Events\OrderCreated;
use App\Http\Controllers\Controller;
use App\Models\Categories;
use App\Models\Config;
use App\Models\Menu;
use App\Models\OrderPay;
use App\Models\Orders;
use App\Models\OrdersDetails;
use App\Models\RiderSend;
use App\Models\User;
use BaconQrCode\Encoder\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PromptPayQR\Builder;

class Admin extends Controller
{
    public function dashboard()
    {
        $data['function_key'] = __FUNCTION__;
        $data['orderday'] = Orders::select(DB::raw("SUM(total)as total"))->where('status', 3)->whereDay('created_at', date('d'))->first();
        $data['ordermouth'] = Orders::select(DB::raw("SUM(total)as total"))->where('status', 3)->whereMonth('created_at', date('m'))->first();
        $data['orderyear'] = Orders::select(DB::raw("SUM(total)as total"))->where('status', 3)->whereYear('created_at', date('Y'))->first();
        $data['ordertotal'] = Orders::count();
        $data['rider'] = User::where('is_rider', 1)->get();

        $menu = Menu::select('id', 'name')->get();
        $item_menu = array();
        $item_order = array();
        if (count($menu) > 0) {
            foreach ($menu as $rs) {
                $item_menu[] = $rs->name;
                $menu_order = OrdersDetails::Join('orders', 'orders.id', '=', 'orders_details.order_id')->where('orders.status', 3)->where('menu_id', $rs->id)->groupBy('menu_id')->count();
                $item_order[] = $menu_order;
            }
        }

        $item_mouth = array();
        for ($i = 1; $i < 13; $i++) {
            $query = Orders::select(DB::raw("SUM(total)as total"))->where('status', 3)->whereMonth('created_at', date($i))->first();
            $item_mouth[] = $query->total;
        }
        $data['item_menu'] = $item_menu;
        $data['item_order'] = $item_order;
        $data['item_mouth'] = $item_mouth;
        $data['config'] = Config::first();
        return view('dashboard', $data);
    }

    public function ListOrder()
    {
        $data = [
            'status' => false,
            'message' => '',
            'data' => []
        ];
        $order = Orders::orderBy('created_at', 'desc')->with('users')->get();

        if (count($order) > 0) {
            $info = [];
            foreach ($order as $rs) {
                $status = '';
                $pay = '';
                if ($rs->status == 1) {
                    $status = '<button class="btn btn-sm btn-primary">รอชำระเงิน</button>';
                }
                if ($rs->status == 2) {
                    $status = '<button class="btn btn-sm btn-success">รอตรวจสอบการชำระเงิน</button>';
                    $pay = '<button data-id="' . $rs->id . '" type="button" class="btn btn-sm btn-outline-primary modalPay m-1">รายละเอียดชำระเงิน</button>';
                }
                if ($rs->status == 3) {
                    $status = '<button class="btn btn-sm btn-success">ชำระเงินเรียบร้อยแล้ว</button>';
                }

                $action = '<button data-id="' . $rs->id . '" type="button" class="btn btn-sm btn-outline-primary modalShow m-1">รายละเอียด</button>' . $pay;
                $info[] = [
                    'name' => $rs['users']->name,
                    'tel' => $rs['users']->tel,
                    'order_id' => $rs->id,
                    'total' => $rs->total,
                    'remark' => $rs->remark,
                    'status' => $status,
                    'created' => $this->DateThai($rs->created_at),
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

    public function listOrderDetail(Request $request)
    {
        $orders = OrdersDetails::select('menu_id')
            ->where('order_id', $request->input('id'))
            ->groupBy('menu_id')
            ->get();

        if (count($orders) > 0) {
            $info = '';
            foreach ($orders as $key => $value) {
                $order = OrdersDetails::where('order_id', $request->input('id'))
                    ->where('menu_id', $value->menu_id)
                    ->with('menu', 'option')
                    ->get();
                $info .= '<div class="card text-white bg-primary mb-3"><div class="card-body"><h5 class="card-title text-white">' . $order[0]['menu']->name . '</h5><p class="card-text">';
                foreach ($order as $rs) {
                    $info .= '' . $rs['menu']->name . ' (' . $rs['option']->type . ') จำนวน ' . $rs->quantity . ' ราคา ' . ($rs->quantity * $rs->price) . ' บาท <br>';
                }
                $info .= '</p></div></div>';
            }
        }
        echo $info;
    }

    public function config()
    {
        $data['function_key'] = __FUNCTION__;
        $data['config'] = Config::first();
        return view('config', $data);
    }

    public function ConfigSave(Request $request)
    {
        $input = $request->input();
        $config = Config::find($input['id']);
        $config->name = $input['name'];
        $config->color1 = $input['color1'];
        $config->color2 = $input['color2'];
        $config->color_font = $input['color_font'];
        $config->color_category = $input['color_category'];
        $config->promptpay = $input['promptpay'];

        if ($request->hasFile('image_bg')) {
            $file = $request->file('image_bg');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('image', $filename, 'public');
            $config->image_bg = $path;
        }
        if ($request->hasFile('image_qr')) {
            $file = $request->file('image_qr');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('image', $filename, 'public');
            $config->image_qr = $path;
        }
        if ($config->save()) {
            return redirect()->route('config')->with('success', 'บันทึกรายการเรียบร้อยแล้ว');
        }
        return redirect()->route('config')->with('error', 'ไม่สามารถบันทึกข้อมูลได้');
    }

    public function confirm_pay(Request $request)
    {
        $data = [
            'status' => false,
            'message' => 'ชำระเงินไม่สำเร็จ',
        ];
        $id = $request->input('id');
        if ($id) {
            $order = Orders::find($id);
            $order->status = 3;
            if ($order->save()) {
                $data = [
                    'status' => true,
                    'message' => 'ชำระเงินเรียบร้อยแล้ว',
                ];
            }
        }
        return response()->json($data);
    }


    function DateThai($strDate)
    {
        $strYear = date("Y", strtotime($strDate)) + 543;
        $strMonth = date("n", strtotime($strDate));
        $strDay = date("j", strtotime($strDate));
        $time = date("H:i", strtotime($strDate));
        $strMonthCut = array("", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม");
        $strMonthThai = $strMonthCut[$strMonth];
        return "$strDay $strMonthThai $strYear" . " " . $time;
    }

    public function paymentConfirm(Request $request)
    {
        $id = $request->post('id');
        $pay = OrderPay::where('order_id', $id)->first();

        echo '<img src="' . url('storage/' . $pay->image) . '">';
    }

    public function confirm_rider(Request $request)
    {
        $data = [
            'status' => false,
            'message' => 'ส่งข้อมูลไปยังไรเดอร์ไม่สำเร็จ',
        ];
        $input = $request->input();
        if ($input['id']) {
            $order = Orders::find($input['id']);
            $order->status = 2;
            if ($order->save()) {
                $rider_save = new RiderSend();
                $rider_save->order_id = $input['id'];
                $rider_save->rider_id = $input['rider_id'];
                if ($rider_save->save()) {
                    $data = [
                        'status' => true,
                        'message' => 'ส่งข้อมูลไปยังไรเดอร์เรียบร้อยแล้ว',
                    ];
                }
            }
        }
        return response()->json($data);
    }
}
