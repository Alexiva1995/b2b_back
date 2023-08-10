<?php

namespace App\Imports;

use App\Http\Controllers\UserController;
use App\Models\MarketPurchased;
use App\Models\Order;
use App\Models\Prefix;
use App\Models\ReferalLink;
use App\Models\User;
use App\Services\BonusService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;


class ImportUsers implements ToModel, WithHeadingRow
{
    use Importable;

    /**
    * @param Collection $collection
    */
    public function model(array $users)
    {
        $pais = Prefix::where('pais', $users['pais'])->first();
       $data = [
        'user_name' => $users['nombre'],
      'user_lastname'=> $users['apellido'],
      'phone' => $users['telefono'],
      'prefix_id' => $pais->id,
      'type_service' => 2,
      "email" => $users['correo'],
       ];

       $pass = Str::random(12);
       $datas = [
           'name' => $data['user_name'],
           'last_name' => $data['user_lastname'],
           'password' => $pass,
           'password_confirmation' => $pass,
           'email' => $data['email'],
           'verify' => true,
       ];

       $user = User::create([
           'name' => $data['user_name'],
           'last_name' => $data['user_lastname'],
           'binary_id' => 1,
           'email' => $data['email'],
           'email_verified_at' => now(),
           'binary_side' => 'L',
           'buyer_id' => 1,
           'prefix_id' => $data['prefix_id'],
           'status' => '1',
           'phone' => $data['phone'],
           'father_cyborg_purchased_id' => null,
           'type_service' => $data['type_service'] == 'product' ? 0 : 2,
       ]);

       $user->user_name = strtolower(explode(" ", $data['user_name'])[0][0] . "" . explode(" ", $data['user_lastname'])[0]) . "#" . $user->id;
       $user->save();
       $url = config('services.backend_auth.base_uri');

       $response = Http::withHeaders([
           'apikey' => config('services.backend_auth.key'),
       ])->post("{$url}register-manual", $datas);

       if ($response->successful()) {
           $res = $response->object();
          // $user->update(['id' => $res->user->id]);

           ReferalLink::create([
               'user_id' => $user->id,
               'link_code' => $this->generateCode(),
               'cyborg_id' => 1,
           ]);

          $order =  Order::create([
               'user_id' => $user->id,
               'amount' => 50,
               'type' => 'inicio',
               'status' => '1',
               'cyborg_id' => 1,
           ]);

           MarketPurchased::create([
               'user_id' => $user->id,
               'order_id' => $order->id,
               'cyborg_id' => 1,
           ]);
           $bonusService = new BonusService;
           $bonusService->generateFirstComission(20,$user, $order, $buyer = $user, $level = 2, $user->id);

           $dataEmail = [
               'email' => $user->email,
               'password' => $pass,
               'user' => $user->name. ' '. $user->last_name,
           ];

          /*  Mail::send('mails.newUser',  ['data' => $dataEmail], function ($msj) use ($data) {
               $msj->subject('Welcome to B2B.');
               $msj->to($data['email']);
           }); */
       }
    }

    private function generateCode()
    {
        $code = Str::random(6);
        if(!ReferalLink::where('link_code', $code)->exists()){
            return $code;
        }
        $this->generateCode();
    }
}
