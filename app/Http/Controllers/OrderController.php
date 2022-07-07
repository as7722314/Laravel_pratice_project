<?php

namespace App\Http\Controllers;

use App\Http\Resources\ItemCollection;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use Carbon\Carbon;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use TsaiYiHua\ECPay\Checkout;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected $checkout;

    public function __construct(Checkout $checkout)
    {
        $this->checkout = $checkout;
        $this->checkout->setReturnUrl(url('/api/checkout/callback'));
    }

    public function getByIdOrder(int $id)
    {
        // return new ItemCollection(Item::with('orders')->get());
        return new OrderCollection(Order::with('items')->where('id', $id)->get());
        // $orders = Order::find($id);
        // return new OrderResource($orders->load('items'));
    }

    //結帳路由方法
    public function checkout(Request $request)
    {
        //建立訂單&明細
        $items = Item::all();
        $TotalAmount = 0;
        $orderItems = [];
        foreach ($items as $key => $item) {
            if ($key <= 1) {
                $TotalAmount += ($item->price * $item->id);
                $orderItems[] = [
                    'item_id' => $item->id,
                    'quantity' => $item->id
                ];
            }
        }
        $orderData = [
            'user_id' => $request->user()->id,
            'receiver' => 'TEST收件人',
            'receiverTitle' => 'Test',
            'receiverMobile' => '0978196729',
            'receiverEmail' => 'as7722314@gmail.com',
            'receiverAddress' => '高雄市',
            'message' => 'Test',
            'couponCode' => '1234567890',
            'subtotal' => $TotalAmount,
            'shipCost' => 0,
        ];
        try {
            DB::beginTransaction();

            $order = Order::create($orderData);
            $formData = [
                'UserId' => 1, // 用戶ID , 非必須
                'MerchantTradeNo' => 'Goblin' . $order->id, //特店訂單編號
                'ItemDescription' => 'testProduct', //商品描述，可自己修改
                'ItemName' => 'Goblin Shop Items', //商品名稱，可自己修改
                'TotalAmount' => $TotalAmount, //訂單總金額
                'PaymentMethod' => 'Credit', // ALL, Credit, ATM, WebATM
                'CustomField1' => $order->id //自定義欄位1
            ];
            $order->items()->attach($orderItems);

            DB::commit();

            //串接綠界金流做付款
            return $this->checkout->setPostData($formData)->send();
        } catch (\Throwable $th) {
            DB::rollBack();
        }
    }

    //綠界付完款轉址路由方法
    public function checkoutCallback(Request $request)
    {
        $response = $request->all();
        $order = Order::find($response['CustomField1']);
        if ($response['RtnCode'] == 1) {
            if ($response['PaymentType'] == 'Credit_CreditCard') {
                $order->pay_type = 'credit';
            }
            $order->trade_no = $response['TradeNo']; //綠界訂單編號
            $order->subtotal = $response['TradeAmt'];
            $order->pay_at = Carbon::now();
            $order->status = 'paid';
            $order->save();
            Log::info('訂單編號' . $order->id . '付款成功');
            Log::info($response);
        } else {
            Log::error('訂單編號' . $order->id . '付款失敗');
        }
        return response()->json(
            [
                'status' => true,
                'msg' => 'success'
            ]
        );
    }
}
