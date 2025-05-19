<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Http\Controllers\Controller;
use App\Models\Categories;
use App\Models\Config;
use App\Models\LogStock;
use App\Models\Menu;
use App\Models\MenuStock;
use App\Models\OrderPay;
use App\Models\Orders;
use App\Models\OrdersDetails;
use App\Models\Preview;
use App\Models\Progresses;
use App\Models\Promotion;
use App\Models\Stock;
use App\Models\User;
use App\Models\UsersAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use PromptPayQR\Builder;

class Delivery extends Controller
{
    public function index(Request $request)
    {
        $table_id = $request->input('table');
        if ($table_id) {
            session(['table_id' => $table_id]);
        }
        $promotion = Promotion::where('is_status', 1)->get();
        $category = Categories::has('menu')->with('files')->get();
        $category_preview = Categories::has('preview')->with('files')->get();
        return view('delivery.main_page', compact('category', 'promotion', 'category_preview'));
    }

    public function login()
    {
        return view('userslogin');
    }

    public function detail($id)
    {
        $menu = Menu::where('categories_id', $id)->with('files', 'option')->orderBy('created_at', 'asc')->get();
        return view('delivery.detail_page', compact('menu'));
    }

    public function order()
    {
        $address = [];
        if (Session::get('user')) {
            $address = UsersAddress::where('users_id', Session::get('user')->id)->get();
        }
        return view('delivery.list_page', compact('address'));
    }

    public function SendOrder(Request $request)
    {
        $data = [
            'status' => false,
            'message' => 'สั่งซื้อไม่สำเร็จ',
        ];
        if (Session::get('user')) {
            $orderData = $request->input('orderData');
            $remark = $request->input('remark');
            $item = array();
            $total = 0;
            foreach ($orderData as $order) {
                foreach ($order as $rs) {
                    $item[] = [
                        'id' => $rs['id'],
                        'price' => $rs['price'],
                        'option' => $rs['option'],
                        'qty' => $rs['qty'],
                    ];
                    $total = $total + ($rs['price'] * $rs['qty']);
                }
            }

            if (!empty($item)) {
                $order = new Orders();
                $order->users_id = Session::get('user')->id;
                $order->total = $total;
                $order->remark = $remark;
                $order->status = 1;
                if ($order->save()) {
                    foreach ($item as $rs) {
                        $orderdetail = new OrdersDetails();
                        $orderdetail->order_id = $order->id;
                        $orderdetail->menu_id = $rs['id'];
                        $orderdetail->option_id = $rs['option'];
                        $orderdetail->quantity = $rs['qty'];
                        $orderdetail->price = $rs['price'];
                        $orderdetail->save();
                    }
                }
                event(new OrderCreated(['📦 มีการสั่งแพ็กเกจใหม่']));
                $data = [
                    'status' => true,
                    'message' => 'สั่งซื้อเรียบร้อยแล้ว',
                ];
            }
        } else {
            $data = [
                'status' => false,
                'message' => 'กรุณาล็อกอินเพื่อสั่งแพ็กเกจ',
            ];
        }
        return response()->json($data);
    }

    public function sendEmp()
    {
        event(new OrderCreated(['ลูกค้าเรียกจากโต้ะที่ ' . session('table_id')]));
    }

    public function users()
    {
        $address = UsersAddress::where('users_id', Session::get('user')->id)->get();
        return view('delivery.users', compact('address'));
    }

    public function usersSave(Request $request)
    {
        $input = $request->post();
        $users = User::find(Session::get('user')->id);
        $users->name = $input['name'];
        $users->email = $input['email'];
        $users->tel = $input['tel'];
        if ($users->save()) {
            Session::put('user', $users);
            return redirect()->route('users.users')->with('success', 'เพิ่มที่อยู่เรียบร้อยแล้ว');
        }
        return redirect()->route('users.users')->with('error', 'ไม่สามารถเพิ่มที่อยู่ได้');
    }

    public function listorder()
    {
        $orderlist = [];
        if (Session::get('user')) {
            $orderlist = Orders::select('orders.*', 'users.name', 'users.tel')
                ->where('users_id', Session::get('user')->id)
                ->leftJoin('rider_sends', 'orders.id', '=', 'rider_sends.order_id')
                ->leftJoin('users', 'rider_sends.rider_id', '=', 'users.id')
                ->get();
        }
        return view('delivery.order', compact('orderlist'));
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

    public function register()
    {
        return view('usersRegister');
    }

    public function UsersRegister(Request $request)
    {
        $input = $request->input();
        $users = new User;
        $users->name = $input['name'];
        $users->tel = $input['tel'];
        $users->email = $input['email'];
        $users->password = Hash::make($input['password']);
        $users->email_verified_at = now();
        if ($users->save()) {
            return redirect()->route('users.login')->with('success', 'สมัครสมาชิกเรียบร้อยแล้ว');
        }
        return redirect()->route('users.register')->with('error', 'สมัครสมาชิกไม่สำเร็จ');
    }

    public function pay($id)
    {
        $qr_code = '';
        $config = Config::first();
        $orderlist = Orders::select('orders.*', 'users.name', 'users.tel')
            ->where('orders.id', $id)
            ->leftJoin('rider_sends', 'orders.id', '=', 'rider_sends.order_id')
            ->leftJoin('users', 'rider_sends.rider_id', '=', 'users.id')
            ->first();
        if ($config->promptpay != '') {
            $qr_code = Builder::staticMerchantPresentedQR($config->promptpay)->setAmount($orderlist->total)->toSvgString();
        } else {
            $qr_code = '<img width="100%" src="' . url('storage/' . $config->image_qr) . '">';
        }

        return view('delivery.pay', compact('orderlist', 'id', 'qr_code'));
    }

    public function paySave(Request $request)
    {
        $input = $request->post();

        $order = Orders::find($input['id']);
        $order->status = 2;
        if ($order->save()) {
            if ($request->hasFile('silp')) {
                $file = $request->file('silp');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('image', $filename, 'public');
            }
            $pay = new OrderPay();
            $pay->order_id = $input['id'];
            $pay->users_id = Session::get('user')->id;
            $pay->image = $path;
            if ($pay->save()) {
                return redirect()->route('users.listorder')->with('success', 'บันทึกรายการเรียบร้อยแล้ว');
            }
        }
    }

    public function progresslist()
    {
        $list = Progresses::where('users_id', Session::get('user')->id)->orderBy('id', 'desc')->with('files')->get();
        return view('delivery.progresslist', compact('list'));
    }

    public function preview($id)
    {
        $preview = Preview::where('categories_id', $id)->with('files')->orderBy('created_at', 'asc')->get();
        return view('delivery.preview', compact('preview'));
    }


    public function preview_detail($id)
    {
        $preview = Preview::with('files')->find($id);
        return view('delivery.preview_detail', compact('preview'));
    }
}
